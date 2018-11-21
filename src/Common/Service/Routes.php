<?php

/**
 * Manage app routes
 *
 * @package     Nails
 * @subpackage  common
 * @category    Service
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Traits\ErrorHandling;
use Nails\Components;
use Nails\Factory;
use Symfony\Component\Console\Output\OutputInterface;

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
    const ROUTES_DIR = CACHE_PATH;

    /**
     * The name to give the routes file
     */
    const ROUTES_FILE = 'routes_app.php';

    // --------------------------------------------------------------------------

    /**
     * Constructs the Service
     */
    public function __construct()
    {
        //  Set Defaults
        $this->bCanWriteRoutes = null;
        $this->aRoutes         = [];

        if (!$this->canWriteRoutes()) {
            $this->sCantWriteReason = $this->lastError();
            $this->clearErrors();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update routes
     *
     * @param  string          $sModule For which module to restrict the route update
     * @param  OutputInterface $oOutput A Symfony OutputInterface to write logs to
     *
     * @return boolean
     * @throws \Exception
     */
    public function update($sModule = null, $oOutput = null)
    {
        if (!$this->bCanWriteRoutes) {
            $this->setError($this->sCantWriteReason);
            return false;
        }

        // --------------------------------------------------------------------------

        //  Look for modules who wish to write to the routes file
        $aModules = Components::modules();

        //  Append the app
        $aModules['app'] = (object) [
            'slug'      => 'app',
            'name'      => 'APP',
            'namespace' => 'App\\',
        ];

        foreach ($aModules as $oModule) {

            if (!empty($sModule) && $sModule !== $oModule->slug) {
                continue;
            }

            $sClass = $oModule->namespace . 'Routes';
            if (class_exists($sClass)) {

                if (!is_null($oOutput)) {
                    $oOutput->write('Generating routes for <info>' . $oModule->slug . '</info>... ');
                }

                $sInterface = 'Nails\\Common\\Interfaces\\RouteGenerator';
                if (!classImplements($sClass, $sInterface)) {
                    throw new \Exception(
                        'Routes generator ' . $sClass . ' does not implement ' . $sInterface
                    );
                }

                $this->aRoutes = array_merge(
                    $this->aRoutes,
                    ['// BEGIN ' . $oModule->name => ''],
                    $sClass::generate(),
                    ['// END ' . $oModule->name => '']
                );

                if (!is_null($oOutput)) {
                    $oOutput->writeln('<info>done!</info>');
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Write the file
        if (!is_null($oOutput)) {
            $oOutput->write('Writing routes to file... ');
        }

        if ($this->writeFile()) {

            if (!is_null($oOutput)) {
                $oOutput->writeln('<info>done!</info>');
            }
            return true;

        } else {

            if (!is_null($oOutput)) {
                $oOutput->writeln('<error>failed!</error>');
            }
            return false;
        }
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
        $sData .= "\n";

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
    public function canWriteRoutes()
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
