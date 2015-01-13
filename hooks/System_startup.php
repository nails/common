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
class NAILS_System_startup
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
        if (!defined('NAILS_PACKAGE_NAME'))  define('NAILS_PACKAGE_NAME',  'Nails');
        if (!defined('NAILS_PACKAGE_URL'))   define('NAILS_PACKAGE_URL',   'http://nailsapp.co.uk/');

        // --------------------------------------------------------------------------

        //  Environment
        if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'DEVELOPMENT');

        // --------------------------------------------------------------------------

        //  Cache Directory
        if (!defined('DEPLOY_CACHE_DIR')) define('DEPLOY_CACHE_DIR', FCPATH . APPPATH . 'cache/');

        // --------------------------------------------------------------------------

        //  Default Error Handler
        if (!defined('DEPLOY_ERROR_REPORTING_HANDLER')) define('DEPLOY_ERROR_REPORTING_HANDLER', 'NAILS');

        // --------------------------------------------------------------------------

        //  Check routes_app.php exists
        if (!defined('NAILS_STARTUP_GENERATE_APP_ROUTES')) {

            if (is_file(DEPLOY_CACHE_DIR . 'routes_app.php')) {

                define('NAILS_STARTUP_GENERATE_APP_ROUTES', FALSE);

            } else {

                //  Not found, crude hook seeing as basically nothing has loaded yet
                define('NAILS_STARTUP_GENERATE_APP_ROUTES', TRUE);
            }
        }

        // --------------------------------------------------------------------------

        //  Database
        if (!defined('DEPLOY_DB_HOST'))     define('DEPLOY_DB_HOST',     'localhost');
        if (!defined('DEPLOY_DB_USERNAME')) define('DEPLOY_DB_USERNAME', '');
        if (!defined('DEPLOY_DB_PASSWORD')) define('DEPLOY_DB_PASSWORD', '');
        if (!defined('DEPLOY_DB_DATABASE')) define('DEPLOY_DB_DATABASE', '');

        // --------------------------------------------------------------------------

        /**
         * Default app constants (if not already defined)
         * These should be specified in config/app.php
         */

        if (!defined('NAILS_DB_PREFIX'))        define('NAILS_DB_PREFIX',        'nails_');
        if (!defined('APP_PRIVATE_KEY'))        define('APP_PRIVATE_KEY',        '');
        if (!defined('APP_NAME'))               define('APP_NAME',               'Untitled');
        if (!defined('APP_STAGING_USERPASS'))   define('APP_STAGING_USERPASS',   serialize(array()));
        if (!defined('APP_SSL_ROUTING'))        define('APP_SSL_ROUTING',        FALSE);
        if (!defined('APP_NATIVE_LOGIN_USING')) define('APP_NATIVE_LOGIN_USING', 'BOTH'); //    [EMAIL|USERNAME|BOTH]

        // --------------------------------------------------------------------------

        /**
         * Deployment specific constants (if not already defined). These should be
         * specified in config/deploy.php
         */

        if (!defined('DEPLOY_SYSTEM_TIMEZONE')) define('DEPLOY_SYSTEM_TIMEZONE', 'UTC');

        //  If this is changed, update CORE_NAILS_Log.php too
        if (!defined('DEPLOY_LOG_DIR')) define('DEPLOY_LOG_DIR', FCPATH . APPPATH . 'logs/');

        // --------------------------------------------------------------------------

        //  Email
        if (!defined('APP_DEVELOPER_EMAIL')) define('APP_DEVELOPER_EMAIL', '');
        if (!defined('EMAIL_DEBUG'))         define('EMAIL_DEBUG',         FALSE);
        if (!defined('EMAIL_OVERRIDE'))      define('EMAIL_OVERRIDE',      '');
        if (!defined('DEPLOY_EMAIL_HOST'))   define('DEPLOY_EMAIL_HOST',   '127.0.0.1');
        if (!defined('DEPLOY_EMAIL_USER'))   define('DEPLOY_EMAIL_USER',   '');
        if (!defined('DEPLOY_EMAIL_PASS'))   define('DEPLOY_EMAIL_PASS',   '');
        if (!defined('DEPLOY_EMAIL_PORT'))   define('DEPLOY_EMAIL_PORT',   '');

        // --------------------------------------------------------------------------

        //  CDN
        if (!defined('APP_CDN_DRIVER'))   define('APP_CDN_DRIVER',   'LOCAL');
        if (!defined('DEPLOY_CDN_MAGIC')) define('DEPLOY_CDN_MAGIC', '');
        if (!defined('DEPLOY_CDN_PATH'))  define('DEPLOY_CDN_PATH',  FCPATH . 'assets/uploads/');

        /**
         * Define how long CDN items should be cached for, this is a maximum age in seconds
         * According to http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html this shouldn't be
         * more than 1 year.
         */

        if (!defined('APP_CDN_CACHE_MAX_AGE')) define('APP_CDN_CACHE_MAX_AGE', '31536000'); // 1 year

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

            if (APP_SSL_ROUTING && page_is_secure()) {

                define('NAILS_URL', SECURE_BASE_URL . 'vendor/nailsapp/');

            } else {

                define('NAILS_URL', BASE_URL . 'vendor/nailsapp/');
            }
        }

        // --------------------------------------------------------------------------

        //  Set the NAILS_ASSETS_URL
        if (!defined('NAILS_ASSETS_URL')) {

            define('NAILS_ASSETS_URL', NAILS_URL . 'module-asset/asset/assets/');
        }

        // --------------------------------------------------------------------------

        //  Database Debug
        if (!defined('DEPLOY_DB_DEBUG')) {

            if (strtoupper(ENVIRONMENT) == 'PRODUCTION') {

                define('DEPLOY_DB_DEBUG', FALSE);

            } else {

                define('DEPLOY_DB_DEBUG', TRUE);
            }
        }
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

        // --------------------------------------------------------------------------

        /**
         * Set the module locations, by default we include the App's path and then
         * follow it up with all known official Nails modules.
         */

        $modules = _NAILS_GET_POTENTIAL_MODULES();

        /**
         * Note: Key is full path, value is relative path from the application controllers
         * directory to where the modules are.
         */

        //  Firstly, App module locations
        $this->moduleLocations[FCPATH . APPPATH . 'modules/'] = '../modules/';

        //  Nails Common should be included too
        $this->moduleLocations[FCPATH . 'vendor/nailsapp/common/'] = '../../vendor/nailsapp/common/';

        //  Individual "official" Nails module locations
        foreach ($modules as $module) {

            $this->moduleLocations[FCPATH . 'vendor/' . $module . '/'] = '../../vendor/' . $module . '/';
        }

        // --------------------------------------------------------------------------

        /**
         * This hook happens before the config class loads (but the config file has
         * already been loaded).CI provides an interface to pass items to the config
         * file via the index.php file; we're going to leverage that here to set
         * the module locations.
         */

        global $assign_to_config;

        //  Underscore casing is important
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

// --------------------------------------------------------------------------

/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * The following block of code makes it simple to extend one of the core admin
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if (!defined('NAILS_ALLOW_EXTENSION_SYSTEM_STARTUP')) {

    class System_startup extends NAILS_System_startup
    {
    }
}
