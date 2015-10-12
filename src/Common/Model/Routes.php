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

class Routes extends BAse
{
    protected $canWriteRoutes;
    protected $routesDir;
    protected $routesFile;
    protected $routes;

    public $cantWriteReason;

    // --------------------------------------------------------------------------

    /**
     * Constructs the model
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        //  Set Defaults
        $this->routesDir      = DEPLOY_CACHE_DIR;
        $this->routesFile     = 'routes_app.php';
        $this->canWriteRoutes = null;
        $this->routes         = array();

        if (!$this->canWriteRoutes()) {

            $this->cantWriteReason = $this->last_error();
            $this->clear_errors();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update routes
     * @param  string $which For which model to restrict the route update
     * @return boolean
     */
    public function update($which = null)
    {
        if (!$this->canWriteRoutes) {

            $this->_set_error($this->cantWriteReason);
            return false;
        }

        // --------------------------------------------------------------------------

        //  Look for modules who wish to write to the routes file
        $modules = _NAILS_GET_MODULES();

        foreach ($modules as $module) {

            $routesFile = $module->path . 'routes/Routes.php';

            if (file_exists($routesFile)) {

                include_once $routesFile;

                $routesClass = 'Nails\\Routes\\' . ucfirst(strtolower($module->moduleName)) . '\\Routes';
                $instance    = new $routesClass();

                if (is_callable(array($instance, 'getRoutes'))) {

                    $this->routes['//BEGIN ' . $module->name] = '';
                    $this->routes = $this->routes + (array) $instance->getRoutes();
                    $this->routes['//END ' . $module->name] = '';
                }
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
        //  Routes are writeable, apparently, give it a bash
        $_data = '<?php' . "\n\n";
        $_data .= '/**' . "\n";
        $_data .= ' * THIS FILE IS CREATED/MODIFIED AUTOMATICALLY'."\n";
        $_data .= ' * ==========================================='."\n";
        $_data .= ' *'."\n";
        $_data .= ' * Any changes you make in this file will be overwritten the' . "\n";
        $_data .= ' * next time the app routes are generated.'."\n";
        $_data .= ' *'."\n";
        $_data .= ' * See Nails docs for instructions on how to utilise the' . "\n";
        $_data .= ' * routes_app.php file'."\n";
        $_data .= ' *'."\n";
        $_data .= ' **/' . "\n\n";

        // --------------------------------------------------------------------------

        foreach ($this->routes as $key => $value) {

            if (preg_match('#^//.*$#', $key)) {

                //  This is a comment
                $_data .= $key . "\n";

            } else {

                //  This is a route
                $_data .= '$route[\'' . $key . '\']=\'' . $value . '\';' . "\n";
            }
        }

        $_data .= "\n" . '//LAST GENERATED: ' . date('Y-m-d H:i:s');

        // --------------------------------------------------------------------------

        $_fh = @fopen($this->routesDir . $this->routesFile, 'w');

        if (!$_fh) {

            $this->_set_error('Unable to open routes file for writing.<small>Located at: ' . $this->routesDir . $this->routesFile . '</small>');
            return false;
        }

        if (!fwrite($_fh, $_data)) {

            fclose($_fh);
            $this->_set_error('Unable to write data to routes file.<small>Located at: ' . $this->routesDir . $this->routesFile . '</small>');
            return false;
        }

        fclose($_fh);

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Determine whether or not the routes can be written
     * @return boolean
     */
    public function canWriteRoutes()
    {
        if (!is_null($this->canWriteRoutes)) {

            return $this->canWriteRoutes;
        }

        //  First, test if file exists, if it does is it writable?
        if (file_exists($this->routesDir . $this->routesFile)) {

            if (is_really_writable($this->routesDir . $this->routesFile)) {

                $this->canWriteRoutes = true;
                return true;

            } else {

                //  Attempt to chmod the file
                if (@chmod($this->routesDir . $this->routesFile, FILE_WRITE_MODE)) {

                    $this->canWriteRoutes = true;
                    return true;

                } else {

                    $this->_set_error('The route config exists, but is not writeable. <small>Located at: ' . $this->routesDir . $this->routesFile . '</small>');
                    $this->canWriteRoutes = false;
                    return false;
                }
            }

        } elseif (is_really_writable($this->routesDir)) {

            $this->canWriteRoutes = true;
            return true;

        } else {

            //  Attempt to chmod the directory
            if (@chmod($this->routesDir, DIR_WRITE_MODE)) {

                $this->canWriteRoutes = true;
                return true;

            } else {

                $this->_set_error('The route directory is not writeable. <small>' . $this->routesDir . '</small>');
                $this->canWriteRoutes = false;
                return false;
            }
        }
    }
}
