<?php

/**
 * Manage app routes
 *
 * @package     Nails
 * @subpackage  common
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

class NAILS_Routes_model extends NAILS_Model
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

// --------------------------------------------------------------------------

/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core
 * models. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if (!defined('NAILS_ALLOW_EXTENSION_ROUTES_MODEL')) {

    class Routes_model extends NAILS_Routes_model
    {
    }
}
