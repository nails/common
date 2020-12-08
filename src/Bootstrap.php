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
use Nails\Common\Service\FileCache;
use Nails\Common\Service\Profiler;
use Nails\Common\Service\Routes;

/**
 * Class Bootstrap
 *
 * @package Nails
 */
final class Bootstrap
{
    /**
     * Whether the app has been bootstrapped already
     *
     * @var bool
     */
    private static $sBootstrapped = false;

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
     * @deprecated
     */
    public static $aNailsControllerData = [];

    // --------------------------------------------------------------------------

    /**
     * @param $sEntryPoint
     *
     * @throws Common\Exception\EnvironmentException
     * @throws Common\Exception\FactoryException
     */
    public static function run($sEntryPoint)
    {
        if (static::$sBootstrapped) {
            return;
        } else {
            static::$sBootstrapped = true;
        }

        Profiler::mark('BOOTSTRAPPING:START');

        self::setEntryPoint($sEntryPoint);
        self::setBaseDirectory($sEntryPoint);

        /*
         *---------------------------------------------------------------
         * Bootstrapper: preSystem
         *---------------------------------------------------------------
         * Allows the app to execute code very early on in the app's lifecycle.
         * All events after this event can be handled by the native event handler.
         */
        if (class_exists('\App\Events') && is_callable('\App\Events::preSystem')) {
            \App\Events::preSystem();
        }

        //  @todo (Pablo - 2020-03-02) - Remove; app and deploy config files are deprecated
        self::loadConfig('app');
        self::loadConfig('deploy');
        self::setNailsConstants();
        self::setCodeIgniterConstants();
        self::setErrorHandling();
        self::setRuntime();
        self::loadFunctions();
        self::setupModules();

        Factory::setup();
        Factory::autoload();

        self::checkRoutes();

        Profiler::mark(Events::SYSTEM_STARTUP);
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
        self::$sEntryPoint = $sEntryPoint;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the entry point
     *
     * @return string
     */
    public static function getEntryPoint()
    {
        return self::$sEntryPoint;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the base directory
     *
     * @param string $sEntryPoint The entry point
     */
    public static function setBaseDirectory($sEntryPoint)
    {
        self::$sBaseDirectory = dirname($sEntryPoint) . '/';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the base directory
     *
     * @return string
     */
    public static function getBaseDirectory()
    {
        return self::$sBaseDirectory;
    }

    // --------------------------------------------------------------------------

    /**
     * Load app config files
     *
     * @param string $sFile The config file to load
     */
    private static function loadConfig($sFile)
    {
        $sPath = self::$sBaseDirectory . 'config/' . $sFile . '.php';
        if (file_exists($sPath)) {
            require_once $sPath;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set Nails constants
     *
     * @throws Common\Exception\EnvironmentException
     */
    public static function setNailsConstants()
    {
        //  Generic and branding constants
        Config::default('NAILS_PACKAGE_NAME', 'Nails');
        Config::default('NAILS_PACKAGE_URL', 'https://nailsapp.co.uk/');
        Config::default('NAILS_BRANDING', true);

        //  Paths
        Config::default('NAILS_PATH', self::$sBaseDirectory . 'vendor/nails/');
        Config::default('NAILS_APP_PATH', self::$sBaseDirectory);
        Config::default('NAILS_COMMON_PATH', Config::get('NAILS_PATH') . 'common/');
        Config::default('NAILS_CI_APP_PATH', self::$sBaseDirectory . 'vendor/codeigniter/framework/application/');
        Config::default('NAILS_CI_SYSTEM_PATH', self::$sBaseDirectory . 'vendor/codeigniter/framework/system/');

        //  So CodeIgniter configures itself correctly
        Config::default('BASEPATH', Config::get('NAILS_CI_SYSTEM_PATH'));

        //  URLs
        Config::default('DOMAIN', '/');
        Config::default('DEFAULT_PROTOCOL', 'https');
        Config::default(
            'BASE_URL',
            Config::get('DOMAIN') !== '/'
             ? sprintf(
                    '%s://%s/',
                    Config::get('DEFAULT_PROTOCOL'),
                    Config::get('DOMAIN')
                )
             : Config::get('DOMAIN')
        );
        Config::default('SECURE_BASE_URL', preg_replace('/^http:/', 'https:', Config::get('BASE_URL')));
        Config::default('NAILS_URL', (Functions::isPageSecure() ? Config::get('SECURE_BASE_URL') : Config::get('BASE_URL')) . 'vendor/nails/');

        //  @todo (Pablo - 2018-11-16) - Move these into the asset service
        Config::default('NAILS_ASSETS_URL', Config::get('NAILS_URL') . 'module-asset/assets/');
        Config::default('NAILS_ASSETS_PATH', Config::get('NAILS_PATH') . 'module-asset/assets/');

        //  Environment
        Config::default('ENVIRONMENT', Environment::ENV_DEV);
        Config::default('NAILS_TIMEZONE', 'UTC');

        /**
         * Test the environment is valid
         * If ENVIRONMENT is empty then CI will roll over with unhelpful issues as it tries to look
         * up directory names etc with a missing segment.
         */
        Environment::isValid(Config::get('ENVIRONMENT'));

        //  Database
        //  @todo (Pablo - 2018-11-16) - Move these to the database service

        //  Consistent between deployments
        Config::default('APP_DB_DRIVER', 'mysqli');
        Config::default('APP_DB_GLOBAL_PREFIX', '');
        Config::default('APP_DB_PCONNECT', true);
        Config::default('APP_DB_CACHE', false);
        Config::default('APP_DB_CHARSET', 'utf8mb4');
        Config::default('APP_DB_DBCOLLAT', 'utf8mb4_unicode_ci');
        Config::default('APP_DB_STRICT', true);
        Config::default('NAILS_DB_PREFIX', 'nails_');
        Config::default('APP_DB_PREFIX', 'app_');

        //  Potentially vary between deployments
        //  @todo (Pablo - 2020-03-02) - Remove, kept for backwards compatibility
        Config::default('DEPLOY_DB_HOST', '127.0.0.1');
        Config::default('DEPLOY_DB_USERNAME');
        Config::default('DEPLOY_DB_PASSWORD');
        Config::default('DEPLOY_DB_DATABASE');
        Config::default('DEPLOY_DB_PORT', 3306);

        Config::default('DB_HOST', Config::get('DEPLOY_DB_HOST'));
        Config::default('DB_USERNAME', Config::get('DEPLOY_DB_USERNAME'));
        Config::default('DB_PASSWORD', Config::get('DEPLOY_DB_PASSWORD'));
        Config::default('DB_DATABASE', Config::get('DEPLOY_DB_DATABASE'));
        Config::default('DB_PORT', Config::get('DEPLOY_DB_PORT'));

        //  App
        Config::default('PRIVATE_KEY', '');
        Config::default('APP_NAME', 'Nails');
        Config::default('APP_NATIVE_LOGIN_USING', 'BOTH');   //  [EMAIL|USERNAME|BOTH]

        //  Logging
        Config::default('LOG_DIR', Config::get('NAILS_APP_PATH') . implode(DIRECTORY_SEPARATOR, ['application', 'logs', '']));

        //  Profiling constants
        Config::default('PROFILER_ENABLED', false);
        if (!PROFILER_ENABLED) {
            Profiler::disable();
        }

        //  Ensure the app's constants file is also loaded
        //  @todo (Pablo - 2018-11-16) - Remove reliance on this feature
        if (is_file(self::$sBaseDirectory . 'application/config/constants.php')) {
            require_once self::$sBaseDirectory . 'application/config/constants.php';
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
     * - The working directory is set to self::$sBaseDirectory
     * - Calls to __FILE__ have been replaced with self::$sEntryPoint
     * - Calls to define() have been replaced with calls to Config::default()
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
            $system_path = self::$sBaseDirectory . 'vendor/codeigniter/framework/system';
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
            chdir(self::$sBaseDirectory);
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
        Config::default('SELF', pathinfo(self::$sEntryPoint, PATHINFO_BASENAME));

        // Path to the system directory
        Config::default('BASEPATH', $system_path);

        // Path to the front controller (this file) directory
        Config::default('FCPATH', dirname(self::$sEntryPoint) . DIRECTORY_SEPARATOR);

        // Name of the "system" directory
        Config::default('SYSDIR', basename(BASEPATH));

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

        Config::default('APPPATH', $application_folder . DIRECTORY_SEPARATOR);

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

        Config::default('VIEWPATH', $view_folder . DIRECTORY_SEPARATOR);
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
        date_default_timezone_set(Config::get('NAILS_TIMEZONE'));
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
     * Another part of the system to act upon
     *
     * @todo (Pablo - 2018-11-16) - Rework this approach
     */
    private static function checkRoutes()
    {
        /** @var Routes $oRoutesService */
        $oRoutesService = Factory::service('Routes');
        if (is_file($oRoutesService->getRoutesFile())) {
            Config::default('NAILS_STARTUP_GENERATE_APP_ROUTES', false);
        } else {
            //  Not found, crude hook seeing as basically nothing has loaded yet
            Config::default('NAILS_STARTUP_GENERATE_APP_ROUTES', true);
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
                ['..', '..', 'vendor', $oModule->name, '']
            );
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
     * self::$aNailsControllerData is an array populated by $this->data in controllers,
     * this function provides an easy interface to this array when it's not in scope.
     *
     * @return array
     * @deprecated
     **/
    public static function &getControllerData()
    {
        return self::$aNailsControllerData;
    }

    // --------------------------------------------------------------------------

    /**
     * self::$aNailsControllerData is an array populated by $this->data
     * in controllers, this function provides an easy interface to populate this
     * array when it's not in scope.
     *
     * @param string $sKey   The key to populate
     * @param mixed  $mValue The value to assign
     *
     * @return  void
     * @deprecated
     **/
    public static function setControllerData($sKey, $mValue)
    {
        self::$aNailsControllerData[$sKey] = $mValue;
    }

    // --------------------------------------------------------------------------

    /**
     * Handles system shutdown
     *
     * @throws Common\Exception\FactoryException
     * @throws Common\Exception\NailsException
     */
    public static function shutdown()
    {
        Profiler::mark(Events::SYSTEM_SHUTDOWN);
        Factory::service('Event')
            ->trigger(Events::SYSTEM_SHUTDOWN);

        if (Profiler::isEnabled()) {
            /** @var Profiler $oProfiler */
            $oProfiler = Factory::service('Profiler');
            echo $oProfiler->generateReport();
        }
    }
}
