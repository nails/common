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
        $this->defineConstants();
        $this->setModuleLocations();
        $this->setupFactory();
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

        defineConst('BASE_URL', '/');

        //  Ensure the app's constants file is also loaded
        require_once APPPATH . 'config/constants.php';

        //  Generic Nails constants
        defineConst('NAILS_PACKAGE_NAME', 'Nails');
        defineConst('NAILS_PACKAGE_URL', 'http://nailsapp.co.uk/');
        defineConst('NAILS_BRANDING', true);

        //  Environment
        defineConst('ENVIRONMENT', 'DEVELOPMENT');

        //  Cache Directories
        defineConst('CACHE_PATH', FCPATH . 'cache' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR);
        defineConst('CACHE_PUBLIC_PATH', FCPATH . 'cache' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
        defineConst('CACHE_PUBLIC_URL', rtrim(BASE_URL, '/') . '/cache/public/');

        //  Database
        //  Consistent between deployments
        defineConst('APP_DB_DRIVER', 'mysqli');
        defineConst('APP_DB_GLOBAL_PREFIX', '');
        defineConst('APP_DB_PCONNECT', true);
        defineConst('APP_DB_CACHE', false);
        defineConst('APP_DB_CHARSET', 'utf8mb4');
        defineConst('APP_DB_DBCOLLAT', 'utf8mb4_unicode_ci');
        defineConst('APP_DB_STRICT', true);
        defineConst('NAILS_DB_PREFIX', 'nails_');
        defineConst('APP_DB_PREFIX', '');

        //  Potentially vary between deployments
        defineConst('DEPLOY_DB_HOST', 'localhost');
        defineConst('DEPLOY_DB_USERNAME', '');
        defineConst('DEPLOY_DB_PASSWORD', '');
        defineConst('DEPLOY_DB_DATABASE', '');

        //  App Constants
        defineConst('APP_PRIVATE_KEY', '');
        defineConst('APP_NAME', 'Untitled');
        defineConst('APP_NATIVE_LOGIN_USING', 'BOTH');   //  [EMAIL|USERNAME|BOTH]

        //  Deploy constants
        defineConst('DEPLOY_SYSTEM_TIMEZONE', 'UTC');
        defineConst('DEPLOY_LOG_DIR', APPPATH . 'logs' . DIRECTORY_SEPARATOR);

        //  Email constants
        defineConst('APP_DEVELOPER_EMAIL', '');
        defineConst('EMAIL_OVERRIDE', '');
        defineConst('DEPLOY_EMAIL_HOST', '127.0.0.1');
        defineConst('DEPLOY_EMAIL_USER', '');
        defineConst('DEPLOY_EMAIL_PASS', '');
        defineConst('DEPLOY_EMAIL_PORT', 25);

        //  Check routes_app.php exists
        //  @todo - don't like this at all
        if (is_file(CACHE_PATH . 'routes_app.php')) {
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
        defineConst(
            'NAILS_ASSETS_PATH',
            implode(DIRECTORY_SEPARATOR, [rtrim(NAILS_PATH), 'module-asset', 'assets']) . DIRECTORY_SEPARATOR
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Sets all the module locations so that CodeIgniter can pick them up
     */
    protected function setModuleLocations()
    {
        if (empty($this->moduleLocations)) {
            $this->moduleLocations = [];
        }

        //  Discover Nails modules
        $aModules = _NAILS_GET_MODULES();

        /**
         * Note: Key is full path, value is relative path from the application controllers
         * directory to where the modules are.
         */

        $aAbsolutePaths = [
            [rtrim(APPPATH, DIRECTORY_SEPARATOR), 'modules'],
            [rtrim(FCPATH, DIRECTORY_SEPARATOR), 'vendor', 'nailsapp', 'common'],
        ];

        $aRelativePaths = [
            ['..', 'modules'],
            ['..', '..', 'vendor', 'nailsapp', 'common'],
        ];

        array_walk(
            $aAbsolutePaths,
            function (&$aItem) {
                $aItem = implode(DIRECTORY_SEPARATOR, $aItem) . DIRECTORY_SEPARATOR;
            }
        );

        array_walk(
            $aRelativePaths,
            function (&$aItem) {
                $aItem = implode(DIRECTORY_SEPARATOR, $aItem) . DIRECTORY_SEPARATOR;
            }
        );

        $this->moduleLocations = array_merge($this->moduleLocations, array_combine($aAbsolutePaths, $aRelativePaths));

        //  Discovered Nails modules
        foreach ($aModules as $oModule) {
            $this->moduleLocations[$oModule->path] = implode(
                DIRECTORY_SEPARATOR,
                ['..', '..', 'vendor', $oModule->name]
            ) . DIRECTORY_SEPARATOR;
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
            $assign_to_config = [];
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

    // --------------------------------------------------------------------------

    protected function setupFactory()
    {
        Factory::setup();
        Factory::autoload();
    }
}
