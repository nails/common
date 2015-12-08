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
     * @return void
     */
    public function init()
    {
        $this->defineConstants();
        $this->setAutoloading();
        $this->setModuleLocations();
    }

    // --------------------------------------------------------------------------

    /**
     * Defines Nails constants if they are not already defined
     * @return void
     */
    protected function defineConstants()
    {
        //  Define some generic Nails constants, allow dev to override these - just in case
        if (!defined('NAILS_PACKAGE_NAME')) {
            define('NAILS_PACKAGE_NAME', 'Nails');
        }
        if (!defined('NAILS_PACKAGE_URL')) {
            define('NAILS_PACKAGE_URL', 'http://nailsapp.co.uk/');
        }
        if (!defined('NAILS_BRANDING')) {
            define('NAILS_BRANDING', true);
        }

        // --------------------------------------------------------------------------

        //  Environment
        if (!defined('ENVIRONMENT')) {
            define('ENVIRONMENT', 'DEVELOPMENT');
        }

        // --------------------------------------------------------------------------

        //  Cache Directory
        if (!defined('DEPLOY_CACHE_DIR')) {
            define('DEPLOY_CACHE_DIR', FCPATH . APPPATH . 'cache/');
        }

        // --------------------------------------------------------------------------

        //  Check routes_app.php exists
        if (!defined('NAILS_STARTUP_GENERATE_APP_ROUTES')) {

            if (is_file(DEPLOY_CACHE_DIR . 'routes_app.php')) {

                define('NAILS_STARTUP_GENERATE_APP_ROUTES', false);

            } else {

                //  Not found, crude hook seeing as basically nothing has loaded yet
                define('NAILS_STARTUP_GENERATE_APP_ROUTES', true);
            }
        }

        // --------------------------------------------------------------------------

        //  Database
        if (!defined('DEPLOY_DB_HOST')) {
            define('DEPLOY_DB_HOST', 'localhost');
        }
        if (!defined('DEPLOY_DB_USERNAME')) {
            define('DEPLOY_DB_USERNAME', '');
        }
        if (!defined('DEPLOY_DB_PASSWORD')) {
            define('DEPLOY_DB_PASSWORD', '');
        }
        if (!defined('DEPLOY_DB_DATABASE')) {
            define('DEPLOY_DB_DATABASE', '');
        }

        // --------------------------------------------------------------------------

        /**
         * Default app constants (if not already defined)
         * These should be specified in config/app.php
         */

        if (!defined('NAILS_DB_PREFIX')) {
            define('NAILS_DB_PREFIX', 'nails_');
        }
        if (!defined('APP_PRIVATE_KEY')) {
            define('APP_PRIVATE_KEY', '');
        }
        if (!defined('APP_NAME')) {
            define('APP_NAME', 'Untitled');
        }
        if (!defined('APP_STAGING_USERPASS')) {
            define('APP_STAGING_USERPASS', json_encode(array()));
        }
        if (!defined('APP_SSL_ROUTING')) {
            define('APP_SSL_ROUTING', false);
        }
        if (!defined('APP_NATIVE_LOGIN_USING')) {
            //  [EMAIL|USERNAME|BOTH]
            define('APP_NATIVE_LOGIN_USING', 'BOTH');
        }

        // --------------------------------------------------------------------------

        /**
         * Deployment specific constants (if not already defined). These should be
         * specified in config/deploy.php
         */

        if (!defined('DEPLOY_SYSTEM_TIMEZONE')) {
            define('DEPLOY_SYSTEM_TIMEZONE', 'UTC');
        }

        //  If this is changed, update CORE_NAILS_Log.php too
        if (!defined('DEPLOY_LOG_DIR')) {
            define('DEPLOY_LOG_DIR', FCPATH . APPPATH . 'logs/');
        }

        // --------------------------------------------------------------------------

        //  Email
        if (!defined('APP_DEVELOPER_EMAIL')) {
            define('APP_DEVELOPER_EMAIL', '');
        }
        if (!defined('EMAIL_OVERRIDE')) {
            define('EMAIL_OVERRIDE', '');
        }
        if (!defined('DEPLOY_EMAIL_HOST')) {
            define('DEPLOY_EMAIL_HOST', '127.0.0.1');
        }
        if (!defined('DEPLOY_EMAIL_USER')) {
            define('DEPLOY_EMAIL_USER', '');
        }
        if (!defined('DEPLOY_EMAIL_PASS')) {
            define('DEPLOY_EMAIL_PASS', '');
        }
        if (!defined('DEPLOY_EMAIL_PORT')) {
            define('DEPLOY_EMAIL_PORT', '');
        }

        // --------------------------------------------------------------------------

        /**
         * SSL
         * If a SECURE_BASE_URL is not defined then assume the secure URL is simply `https://BASE_URL`
         */

        if (!defined('SECURE_BASE_URL')) {
            //  Not defined, play it safe and just copy the BASE_URL
            define('SECURE_BASE_URL', BASE_URL);
        }

        // --------------------------------------------------------------------------

        /**
         * Set NAILS_URL here as it's dependent on knowing whether SSL is set or not
         * and if the current page is secure.
         */

        if (!defined('NAILS_URL')) {

            if (APP_SSL_ROUTING && isPageSecure()) {

                define('NAILS_URL', SECURE_BASE_URL . 'vendor/nailsapp/');

            } else {

                define('NAILS_URL', BASE_URL . 'vendor/nailsapp/');
            }
        }

        // --------------------------------------------------------------------------

        //  Set the NAILS_ASSETS_URL
        if (!defined('NAILS_ASSETS_URL')) {
            define('NAILS_ASSETS_URL', NAILS_URL . 'module-asset/assets/');
        }

        //  Set the NAILS_ASSETS_PATH
        if (!defined('NAILS_ASSETS_PATH')) {
            define('NAILS_ASSETS_PATH', NAILS_PATH . 'module-asset/assets/');
        }

        // --------------------------------------------------------------------------

        //  Database Debug
        if (!defined('DEPLOY_DB_DEBUG')) {

            if (strtoupper(ENVIRONMENT) == 'PRODUCTION') {

                define('DEPLOY_DB_DEBUG', false);

            } else {

                define('DEPLOY_DB_DEBUG', true);
            }
        }
    }

    // --------------------------------------------------------------------------

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
