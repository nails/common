<?php

/**
 * Manage app routes
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

use Nails\Factory;
use Nails\Common\Traits\ErrorHandling;

class Routes
{
    use ErrorHandling;

    // --------------------------------------------------------------------------

    /**
     * Whether the routes can be written
     * @var boolean
     */
    protected $bCanWriteRoutes;

    /**
     * The reason, if any, why routes cannot be written
     * @var string
     */
    public $sCantWriteReason;

    /**
     * The routes to write
     * @var array
     */
    protected $aRoutes;

    /**
     * Where to store the routes file
     */
    const ROUTES_DIR = DEPLOY_CACHE_DIR;

    /**
     * The name to give the routes file
     */
    const ROUTES_FILE = 'routes_app.php';

    // --------------------------------------------------------------------------

    /**
     * Constructs the model
     */
    public function __construct()
    {
        //  Set Defaults
        $this->bCanWriteRoutes = null;
        $this->aRoutes         = [];

        if (!$this->bCanWriteRoutes()) {
            $this->sCantWriteReason = $this->lastError();
            $this->clearErrors();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update routes
     *
     * @param  string $sModule For which module to restrict the route update
     * @return boolean
     */
    public function update($sModule = null)
    {
        if (!$this->bCanWriteRoutes) {

            $this->setError($this->sCantWriteReason);

            return false;
        }

        // --------------------------------------------------------------------------

        //  Look for modules who wish to write to the routes file
        $aModules = _NAILS_GET_MODULES();

        foreach ($aModules as $oModule) {

            if (!empty($sModule) && $sModule !== $oModule->slug) {
                continue;
            }

            $sPath = $oModule->path . 'routes/Routes.php';

            if (file_exists($sPath)) {

                require $sPath;
                $sRoutesClass = 'Nails\\Routes\\' . ucfirst(strtolower($oModule->moduleName)) . '\\Routes';
                $oInstance    = new $sRoutesClass();

                $this->aRoutes['//BEGIN ' . $oModule->name] = '';
                $this->aRoutes                              = $this->aRoutes + (array) $oInstance->getRoutes();
                $this->aRoutes['//END ' . $oModule->name]   = '';
            }
        }

        // --------------------------------------------------------------------------

        //  Write the file
        return $this->writeFile();
    }

    // --------------------------------------------------------------------------

    /**
     * Write the routes file
     * @return boolean
     */
    protected function writeFile()
    {
        //  Routes are writable, apparently, give it a bash
        $sData = '<?php' . "\n\n";
        $sData .= '/**' . "\n";
        $sData .= ' * THIS FILE IS CREATED/MODIFIED AUTOMATICALLY' . "\n";
        $sData .= ' * ===========================================' . "\n";
        $sData .= ' *' . "\n";
        $sData .= ' * Any changes you make in this file will be overwritten the' . "\n";
        $sData .= ' * next time the app routes are generated.' . "\n";
        $sData .= ' *' . "\n";
        $sData .= ' * See Nails docs for instructions on how to utilise the' . "\n";
        $sData .= ' * routes_app.php file' . "\n";
        $sData .= ' *' . "\n";
        $sData .= ' **/' . "\n\n";

        // --------------------------------------------------------------------------

        foreach ($this->aRoutes as $sKey => $sValue) {

            if (preg_match('#^//.*$#', $sKey)) {

                //  This is a comment
                $sData .= $sKey . "\n";

            } else {

                //  This is a route
                $sData .= '$route[\'' . $sKey . '\']=\'' . $sValue . '\';' . "\n";
            }
        }

        $oDate = Factory::factory('DateTime');
        $sData .= "\n" . '//LAST GENERATED: ' . $oDate->format('Y-m-d H:i:s');

        // --------------------------------------------------------------------------

        $fHandle = @fopen(static::ROUTES_DIR . static::ROUTES_FILE, 'w');

        if (!$fHandle) {

            $this->setError('Unable to open routes file for writing.');

            return false;
        }

        if (!fwrite($fHandle, $sData)) {

            fclose($fHandle);
            $this->setError('Unable to write data to routes file.');

            return false;
        }

        fclose($fHandle);

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Determine whether or not the routes can be written
     * @return boolean
     */
    public function bCanWriteRoutes()
    {
        if (!is_null($this->bCanWriteRoutes)) {

            return $this->bCanWriteRoutes;
        }

        //  First, test if file exists, if it does is it writable?
        if (file_exists(static::ROUTES_DIR . static::ROUTES_FILE)) {

            if (is_writable(static::ROUTES_DIR . static::ROUTES_FILE)) {

                $this->bCanWriteRoutes = true;

                return true;

            } else {

                //  Attempt to chmod the file
                if (@chmod(static::ROUTES_DIR . static::ROUTES_FILE, FILE_WRITE_MODE)) {

                    $this->bCanWriteRoutes = true;

                    return true;

                } else {

                    $this->setError('The route config exists, but is not writable.');
                    $this->bCanWriteRoutes = false;

                    return false;
                }
            }

        } elseif (is_writable(static::ROUTES_DIR)) {

            $this->bCanWriteRoutes = true;

            return true;

        } else {

            //  Attempt to chmod the directory
            if (@chmod(static::ROUTES_DIR, DIR_WRITE_MODE)) {

                $this->bCanWriteRoutes = true;

                return true;

            } else {

                $this->setError('The route directory is not writable. <small>' . static::ROUTES_DIR . '</small>');
                $this->bCanWriteRoutes = false;

                return false;
            }
        }
    }
}
