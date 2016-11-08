<?php

/**
 * This hook is called very early on in the page's lifecycle. It defines many
 * constants used by Nails as well as detects all the module locations.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Hook
 * @author      Nails Dev Team
 * @link
 */

namespace Nails;

class Startup
{
    protected $moduleLocations;

    // --------------------------------------------------------------------------

    /**
     * Called by the hook, executes the individual startup methods as required.
     *
     * @return void
     */
    public function init()
    {
        $this->setAutoloading();
        $this->defineConstants();
        $this->setModuleLocations();
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up autoloading
     */
    protected function setAutoloading()
    {
        //  Include the composer autoloader
        if (!file_exists(FCPATH . 'vendor/autoload.php')) {

            $_ERROR = 'Composer autoloader not found; run <code>composer install</code> to install dependencies';
            include NAILS_COMMON_PATH . 'errors/startup_error.php';
        }

        require_once FCPATH . 'vendor/autoload.php';
    }

    // --------------------------------------------------------------------------

    /**
     * Defines Nails constants if they are not already defined
     *
     * @return void
     */
    protected function defineConstants()
    {
        /**
         * The following constants apply when the application is being used as either
         * a web or console application
         */

        //  Generic Nails constants
        defineConst('NAILS_PACKAGE_NAME', 'Nails');
        defineConst('NAILS_PACKAGE_URL', 'http://nailsapp.co.uk/');
        defineConst('NAILS_BRANDING', true);

        //  Environment
        defineConst('ENVIRONMENT', 'DEVELOPMENT');

        //  Cache Directory
        defineConst('DEPLOY_CACHE_DIR', FCPATH . APPPATH . 'cache/');

        //  Database
        defineConst('DEPLOY_DB_HOST', 'localhost');
        defineConst('DEPLOY_DB_USERNAME', '');
        defineConst('DEPLOY_DB_PASSWORD', '');
        defineConst('DEPLOY_DB_DATABASE', '');
        defineConst('NAILS_DB_PREFIX', 'nails_');

        if (Environment::is('PRODUCTION')) {
            defineConst('DEPLOY_DB_DEBUG', false);
        } else {
            defineConst('DEPLOY_DB_DEBUG', true);
        }

        //  App Constants
        defineConst('APP_PRIVATE_KEY', '');
        defineConst('APP_NAME', 'Untitled');
        defineConst('APP_NATIVE_LOGIN_USING', 'BOTH');   //  [EMAIL|USERNAME|BOTH]

        //  Deploy constants
        defineConst('DEPLOY_SYSTEM_TIMEZONE', 'UTC');
        defineConst('DEPLOY_LOG_DIR', FCPATH . APPPATH . 'logs/');

        //  Email constants
        defineConst('APP_DEVELOPER_EMAIL', '');
        defineConst('EMAIL_OVERRIDE', '');
        defineConst('DEPLOY_EMAIL_HOST', '127.0.0.1');
        defineConst('DEPLOY_EMAIL_USER', '');
        defineConst('DEPLOY_EMAIL_PASS', '');
        defineConst('DEPLOY_EMAIL_PORT', 25);

        //  Check routes_app.php exists
        //  @todo - don't like this at all
        if (is_file(DEPLOY_CACHE_DIR . 'routes_app.php')) {

            defineConst('NAILS_STARTUP_GENERATE_APP_ROUTES', false);

        } else {

            //  Not found, crude hook seeing as basically nothing has loaded yet
            defineConst('NAILS_STARTUP_GENERATE_APP_ROUTES', true);
        }

        //  URLs
        defineConst('BASE_URL', '/');
        defineConst('SECURE_BASE_URL', preg_replace('/^http:/', 'https:', BASE_URL));

        if (isPageSecure()) {

            defineConst('NAILS_URL', SECURE_BASE_URL . 'vendor/nailsapp/');

        } else {

            defineConst('NAILS_URL', BASE_URL . 'vendor/nailsapp/');
        }

        defineConst('NAILS_ASSETS_URL', NAILS_URL . 'module-asset/assets/');
        defineConst('NAILS_ASSETS_PATH', NAILS_PATH . 'module-asset/assets/');
    }

    // --------------------------------------------------------------------------

    /**
     * Sets all the module locations so that CodeIgniter can pick them up
     */
    protected function setModuleLocations()
    {
        if (empty($this->moduleLocations)) {
            $this->moduleLocations = array();
        }

        //  Discover Nails modules
        $modules = _NAILS_GET_MODULES();

        /**
         * Note: Key is full path, value is relative path from the application controllers
         * directory to where the modules are.
         */

        //  Firstly, App module locations
        $this->moduleLocations[FCPATH . APPPATH . 'modules/'] = '../modules/';

        //  Nails Common should be included too
        $this->moduleLocations[FCPATH . 'vendor/nailsapp/common/'] = '../../vendor/nailsapp/common/';

        //  Discovered Nails modules
        foreach ($modules as $module) {

            $this->moduleLocations[$module->path] = '../../vendor/' . $module->name . '/';
        }

        // --------------------------------------------------------------------------

        /**
         * This hook happens before the config class loads (but the config file has
         * already been loaded). CI provides an interface to pass items to the config
         * file via the index.php file; we're going to leverage that here to set
         * the module locations.
         */

        //  Underscore casing is important
        global $assign_to_config;
        $key = 'modules_locations';

        if (empty($assign_to_config)) {

            $assign_to_config = array();
        }

        if (isset($assign_to_config[$key]) && is_array($assign_to_config[$key])) {

            //  Already set, merge the arrays
            $assign_to_config[$key] = array_merge($assign_to_config[$key], $this->moduleLocations);

        } else {

            /**
             * Not set (or is set but not "proper", overwrite it as it's probably wrong anyway)
             */

            $assign_to_config[$key] = $this->moduleLocations;
        }
    }
}
