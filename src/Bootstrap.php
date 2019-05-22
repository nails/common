<?php

/**
 * The main Nails bootstrapper
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 */

namespace Nails;

use Nails\Common\Events;
use Nails\Common\Service\ErrorHandler;

final class Bootstrap
{
    /**
     * The entry point
     *
     * @var string
     */
    private static $sEntryPoint;

    /**
     * The directory of the entry point
     *
     * @var string
     */
    private static $sBaseDirectory;

    /**
     * This global variable will store all the information that
     * controllers set using $this->data. This allows us to reference
     * this variable outwith the scope of the controller, e.g in
     * models and libraries.
     *
     * @todo (Pablo - 2018-11-15) - Rework this approach
     * @var array
     */
    public static $aNailsControllerData = [];

    // --------------------------------------------------------------------------

    /**
     * @param $sEntryPoint
     */
    public static function run($sEntryPoint)
    {
        static::setEntryPoint($sEntryPoint);
        static::setBaseDirectory($sEntryPoint);
        static::loadConfig('app');
        static::loadConfig('deploy');
        static::setNailsConstants();
        static::setCodeIgniterConstants();
        static::setErrorHandling();
        static::setRuntime();
        static::loadFunctions();
        static::checkRoutes();
        static::setupModules();

        Factory::setup();
        Factory::autoload();
        Factory::service('Event')
            ->trigger(Events::SYSTEM_STARTUP);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the entry point
     *
     * @param string $sEntryPoint The entry point
     */
    public static function setEntryPoint($sEntryPoint)
    {
        static::$sEntryPoint = $sEntryPoint;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the base directory
     *
     * @param string $sEntryPoint The entry point
     */
    public static function setBaseDirectory($sEntryPoint)
    {
        static::$sBaseDirectory = dirname($sEntryPoint) . '/';
    }

    // --------------------------------------------------------------------------

    /**
     * Load app config files
     *
     * @param string $sFile The config file to load
     */
    private static function loadConfig($sFile)
    {
        $sPath = static::$sBaseDirectory . 'config/' . $sFile . '.php';
        if (file_exists($sPath)) {
            require_once $sPath;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set Nails constants
     */
    public static function setNailsConstants()
    {
        //  Generic and branding constants
        Functions::define('NAILS_PACKAGE_NAME', 'Nails');
        Functions::define('NAILS_PACKAGE_URL', 'http://nailsapp.co.uk/');
        Functions::define('NAILS_BRANDING', true);

        //  Paths
        Functions::define('NAILS_PATH', static::$sBaseDirectory . 'vendor/nails/');
        Functions::define('NAILS_APP_PATH', static::$sBaseDirectory);
        Functions::define('NAILS_COMMON_PATH', NAILS_PATH . 'common/');
        Functions::define('NAILS_CI_APP_PATH', static::$sBaseDirectory . 'vendor/codeigniter/framework/application/');
        Functions::define('NAILS_CI_SYSTEM_PATH', static::$sBaseDirectory . 'vendor/codeigniter/framework/system/');

        //  So CodeIgniter configures itself correctly
        Functions::define('BASEPATH', NAILS_CI_SYSTEM_PATH);

        //  URLs
        Functions::define('BASE_URL', '/');
        Functions::define('SECURE_BASE_URL', preg_replace('/^http:/', 'https:', BASE_URL));
        Functions::define('NAILS_URL', (Functions::isPageSecure() ? SECURE_BASE_URL : BASE_URL) . 'vendor/nails/');

        //  @todo (Pablo - 2018-11-16) - Move these into the asset service
        Functions::define('NAILS_ASSETS_URL', NAILS_URL . 'module-asset/assets/');
        Functions::define('NAILS_ASSETS_PATH', NAILS_PATH . 'module-asset/assets/');

        //  Environment
        Functions::define('ENVIRONMENT', Environment::ENV_DEV);
        Functions::define('NAILS_TIMEZONE', 'UTC');

        //  Cache constants
        //  @todo (Pablo - 2018-11-16) - Move these to the cache service
        Functions::define('CACHE_PATH', static::$sBaseDirectory . 'cache' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR);
        Functions::define('CACHE_PUBLIC_PATH', static::$sBaseDirectory . 'cache' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
        Functions::define('CACHE_PUBLIC_URL', rtrim(BASE_URL, '/') . '/cache/public/');

        //  Database
        //  @todo (Pablo - 2018-11-16) - Move these to the database service

        //  Consistent between deployments
        Functions::define('APP_DB_DRIVER', 'mysqli');
        Functions::define('APP_DB_GLOBAL_PREFIX', '');
        Functions::define('APP_DB_PCONNECT', true);
        Functions::define('APP_DB_CACHE', false);
        Functions::define('APP_DB_CHARSET', 'utf8mb4');
        Functions::define('APP_DB_DBCOLLAT', 'utf8mb4_unicode_ci');
        Functions::define('APP_DB_STRICT', true);
        Functions::define('NAILS_DB_PREFIX', 'nails_');
        Functions::define('APP_DB_PREFIX', '');

        //  Potentially vary between deployments
        Functions::define('DEPLOY_DB_HOST', 'localhost');
        Functions::define('DEPLOY_DB_USERNAME', '');
        Functions::define('DEPLOY_DB_PASSWORD', '');
        Functions::define('DEPLOY_DB_DATABASE', '');

        //  App Constants
        Functions::define('APP_PRIVATE_KEY', '');
        Functions::define('APP_NAME', 'Untitled');
        Functions::define('APP_NATIVE_LOGIN_USING', 'BOTH');   //  [EMAIL|USERNAME|BOTH]

        //  Log constants
        //  @todo (Pablo - 2018-11-16) - Move these to the log service
        Functions::define('DEPLOY_LOG_DIR', static::$sBaseDirectory . 'application/logs/');

        //  Email constants
        Functions::define('APP_DEVELOPER_EMAIL', '');
        Functions::define('EMAIL_OVERRIDE', '');
        Functions::define('EMAIL_WHITELIST', '');

        //  Specify these first for backwards compatability
        Functions::define('DEPLOY_EMAIL_HOST', '127.0.0.1');
        Functions::define('DEPLOY_EMAIL_USER', '');
        Functions::define('DEPLOY_EMAIL_PASS', '');
        Functions::define('DEPLOY_EMAIL_PORT', 25);

        Functions::define('EMAIL_HOST', DEPLOY_EMAIL_HOST);
        Functions::define('EMAIL_USERNAME', DEPLOY_EMAIL_USER);
        Functions::define('EMAIL_PASSWORD', DEPLOY_EMAIL_PASS);
        Functions::define('EMAIL_PORT', DEPLOY_EMAIL_PORT);

        //  Ensure the app's constants file is also loaded
        //  @todo (Pablo - 2018-11-16) - Remove reliance on this feature
        if (is_file(static::$sBaseDirectory . 'application/config/constants.php')) {
            require_once static::$sBaseDirectory . 'application/config/constants.php';
        }
    }

    // --------------------------------------------------------------------------

    /**
     * This method is largely a copy/pase of the CI index.php file. It has been
     * modified in the following ways:
     *
     * - The iniatal section considering environments has been removed
     * - The section detailing error reporting has been removed
     * - The $system_path variable has been updated to reflect its true location
     * - The working directory is set to static::$sBaseDirectory
     * - Calls to __FILE__ have been replaced with static::$sEntryPoint
     * - Calls to define() have been replaced with calls to Functions::define()
     * - Not kicking off CodeIgniter (as it needs to be called in the global scope)
     *
     * @param string $sSystemPath      The path to the CodeIgniter system directory
     * @param string $sApplicationPath The path to the CodeIgniter application directory
     */
    public static function setCodeIgniterConstants($sSystemPath = null, $sApplicationPath = null)
    {
        /*
         *---------------------------------------------------------------
         * SYSTEM DIRECTORY NAME
         *---------------------------------------------------------------
         *
         * This variable must contain the name of your "system" directory.
         * Set the path if it is not in the same directory as this file.
         */
        if (empty($sSystemPath)) {
            $system_path = static::$sBaseDirectory . 'vendor/codeigniter/framework/system';
        } else {
            $system_path = $sSystemPath;
        }

        /*
         *---------------------------------------------------------------
         * APPLICATION DIRECTORY NAME
         *---------------------------------------------------------------
         *
         * If you want this front controller to use a different "application"
         * directory than the default one you can set its name here. The directory
         * can also be renamed or relocated anywhere on your server. If you do,
         * use an absolute (full) server path.
         * For more info please see the user guide:
         *
         * https://codeigniter.com/user_guide/general/managing_apps.html
         *
         * NO TRAILING SLASH!
         */
        if (empty($sApplicationPath)) {
            $application_folder = 'application';
        } else {
            $application_folder = $sApplicationPath;
        }

        /*
         *---------------------------------------------------------------
         * VIEW DIRECTORY NAME
         *---------------------------------------------------------------
         *
         * If you want to move the view directory out of the application
         * directory, set the path to it here. The directory can be renamed
         * and relocated anywhere on your server. If blank, it will default
         * to the standard location inside your application directory.
         * If you do move this, use an absolute (full) server path.
         *
         * NO TRAILING SLASH!
         */
        $view_folder = '';

        /*
         * --------------------------------------------------------------------
         * DEFAULT CONTROLLER
         * --------------------------------------------------------------------
         *
         * Normally you will set your default controller in the routes.php file.
         * You can, however, force a custom routing by hard-coding a
         * specific controller class/function here. For most applications, you
         * WILL NOT set your routing here, but it's an option for those
         * special instances where you might want to override the standard
         * routing in a specific front controller that shares a common CI installation.
         *
         * IMPORTANT: If you set the routing here, NO OTHER controller will be
         * callable. In essence, this preference limits your application to ONE
         * specific controller. Leave the function name blank if you need
         * to call functions dynamically via the URI.
         *
         * Un-comment the $routing array below to use this feature
         */
        // The directory name, relative to the "controllers" directory.  Leave blank
        // if your controller is not in a sub-directory within the "controllers" one
        // $routing['directory'] = '';

        // The controller class file name.  Example:  mycontroller
        // $routing['controller'] = '';

        // The controller function you wish to be called.
        // $routing['function'] = '';

        /*
         * -------------------------------------------------------------------
         *  CUSTOM CONFIG VALUES
         * -------------------------------------------------------------------
         *
         * The $assign_to_config array below will be passed dynamically to the
         * config class when initialized. This allows you to set custom config
         * items or override any default config values found in the config.php file.
         * This can be handy as it permits you to share one application between
         * multiple front controller files, with each file containing different
         * config values.
         *
         * Un-comment the $assign_to_config array below to use this feature
         */
        // $assign_to_config['name_of_config_item'] = 'value of config item';

        // --------------------------------------------------------------------
        // END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
        // --------------------------------------------------------------------

        /*
         * ---------------------------------------------------------------
         *  Resolve the system path for increased reliability
         * ---------------------------------------------------------------
         */

        // Set the current directory correctly for CLI requests
        if (defined('STDIN')) {
            chdir(static::$sBaseDirectory);
        }

        if (($_temp = realpath($system_path)) !== false) {
            $system_path = $_temp . DIRECTORY_SEPARATOR;
        } else {
            // Ensure there's a trailing slash
            $system_path = strtr(
                    rtrim($system_path, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                ) . DIRECTORY_SEPARATOR;
        }

        // Is the system path correct?
        if (!is_dir($system_path)) {
            header('HTTP/1.1 503 Service Unavailable.', true, 503);
            echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: ' . pathinfo(__FILE__, PATHINFO_BASENAME);
            exit(3); // EXIT_CONFIG
        }

        /*
         * -------------------------------------------------------------------
         *  Now that we know the path, set the main path constants
         * -------------------------------------------------------------------
         */
        // The name of THIS file
        Functions::define('SELF', pathinfo(static::$sEntryPoint, PATHINFO_BASENAME));

        // Path to the system directory
        Functions::define('BASEPATH', $system_path);

        // Path to the front controller (this file) directory
        Functions::define('FCPATH', dirname(static::$sEntryPoint) . DIRECTORY_SEPARATOR);

        // Name of the "system" directory
        Functions::define('SYSDIR', basename(BASEPATH));

        // The path to the "application" directory
        if (is_dir($application_folder)) {
            if (($_temp = realpath($application_folder)) !== false) {
                $application_folder = $_temp;
            } else {
                $application_folder = strtr(
                    rtrim($application_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
            }
        } elseif (is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR)) {
            $application_folder = BASEPATH . strtr(
                    trim($application_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
        } else {
            header('HTTP/1.1 503 Service Unavailable.', true, 503);
            echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
            exit(3); // EXIT_CONFIG
        }

        Functions::define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);

        // The path to the "views" directory
        if (!isset($view_folder[0]) && is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR)) {
            $view_folder = APPPATH . 'views';
        } elseif (is_dir($view_folder)) {
            if (($_temp = realpath($view_folder)) !== false) {
                $view_folder = $_temp;
            } else {
                $view_folder = strtr(
                    rtrim($view_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
            }
        } elseif (is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR)) {
            $view_folder = APPPATH . strtr(
                    trim($view_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
        } else {
            header('HTTP/1.1 503 Service Unavailable.', true, 503);
            echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
            exit(3); // EXIT_CONFIG
        }

        Functions::define('VIEWPATH', $view_folder . DIRECTORY_SEPARATOR);
    }

    // --------------------------------------------------------------------------

    /**
     * Configures the error handler
     */
    private static function setErrorHandling()
    {
        ErrorHandler::init();
    }

    // --------------------------------------------------------------------------

    /**
     * Configure runtime
     */
    private static function setRuntime()
    {
        date_default_timezone_set(NAILS_TIMEZONE);
    }

    // --------------------------------------------------------------------------

    /**
     * Load the Nails Common function file
     *
     * @todo (Pablo - 2018-11-15) - Move these out of the global namespace
     */
    private static function loadFunctions()
    {
        if (!file_exists(NAILS_COMMON_PATH . 'src/Common/CodeIgniter/Core/Common.php')) {
            ErrorHandler::halt(
                'Could not find <code>Nails\Common\CodeIgniter\Core\Common()</code>, ensure Nails is set up correctly.'
            );
        }

        require_once NAILS_COMMON_PATH . 'src/Common/CodeIgniter/Core/Common.php';
    }

    // --------------------------------------------------------------------------

    /**
     * Checks whether the routes file has been generated and sets a constant for
     * Another part of the system to act upon/
     *
     * @todo (Pablo - 2018-11-16) - Rework this approach
     */
    private static function checkRoutes()
    {
        if (is_file(CACHE_PATH . 'routes_app.php')) {
            Functions::define('NAILS_STARTUP_GENERATE_APP_ROUTES', false);
        } else {
            //  Not found, crude hook seeing as basically nothing has loaded yet
            Functions::define('NAILS_STARTUP_GENERATE_APP_ROUTES', true);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for modules and configures CI
     */
    private static function setupModules()
    {
        $aModuleLocations = [];

        //  Discover Nails modules
        $aModules = Components::modules();

        /**
         * Note: Key is full path, value is relative path from the application controllers
         * directory to where the modules are.
         */
        $aAbsolutePaths = [
            [rtrim(APPPATH, DIRECTORY_SEPARATOR), 'modules'],
            [rtrim(FCPATH, DIRECTORY_SEPARATOR), 'vendor', 'nails', 'common'],
        ];

        $aRelativePaths = [
            ['..', 'modules'],
            ['..', '..', 'vendor', 'nails', 'common'],
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

        $aModuleLocations = array_merge(
            $aModuleLocations,
            array_combine($aAbsolutePaths, $aRelativePaths)
        );

        //  Discovered Nails modules
        foreach ($aModules as $oModule) {
            $aModuleLocations[$oModule->path] = implode(
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
            $assign_to_config[$key] = array_merge($assign_to_config[$key], $aModuleLocations);

        } else {

            // Not set (or is set but not "proper", overwrite it as it's probably wrong anyway)
            $assign_to_config[$key] = $aModuleLocations;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * static::$aNailsControllerData is an array populated by $this->data in controllers,
     * this function provides an easy interface to this array when it's not in scope.
     *
     * @return array
     **/
    public static function &getControllerData()
    {
        return static::$aNailsControllerData;
    }

    // --------------------------------------------------------------------------

    /**
     * static::$aNailsControllerData is an array populated by $this->data
     * in controllers, this function provides an easy interface to populate this
     * array when it's not in scope.
     *
     * @param string $sKey   The key to populate
     * @param mixed  $mValue The value to assign
     *
     * @return  void
     **/
    public static function setControllerData($sKey, $mValue)
    {
        static::$aNailsControllerData[$sKey] = $mValue;
    }

    // --------------------------------------------------------------------------

    /**
     * Handles system shutdown
     */
    public static function shutdown()
    {
        Factory::service('Event')
            ->trigger(Events::SYSTEM_SHUTOWN);
    }
}
