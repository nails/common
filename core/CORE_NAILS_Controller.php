<?php

/**
 * This class is the main execution point for all page requests. It
 * checks and configures the Nails environment.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

// use Monolog\Logger;
// use Monolog\Handler\StreamHandler;

class CORE_NAILS_Controller extends MX_Controller
{
    protected $data;
    protected $user;
    protected $nailsErrorHandler;
    protected $log;

    // --------------------------------------------------------------------------

    /**
     * Build the main framework. All autoloaded items have been loaded and
     * instantiated by this point and are safe to use.
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        //  Set up services
        \Nails\Factory::setup();

        // --------------------------------------------------------------------------

        //  Is Nails in maintenance mode?
        $this->maintenanceMode();

        // --------------------------------------------------------------------------

        //  Nails PHP Version Check
        $this->checkPhpVersion();

        // --------------------------------------------------------------------------

        //  Configure logging and error reporting
        // $this->setLogging();
        $this->setErrorReporting();

        // --------------------------------------------------------------------------

        //  Set the default content-type
        $this->output->set_content_type('text/html; charset=utf-8');

        // --------------------------------------------------------------------------

        //  Define data array (used extensively in views)
        $this->data =& getControllerData();

        // --------------------------------------------------------------------------

        //  Define all the packages
        $this->autoloadItems();

        // --------------------------------------------------------------------------

        /**
         * If we're on a staging environment then prompt for a password; but only if
         * a password has been defined in app.php
         */

        $this->staging();

        // --------------------------------------------------------------------------

        //  Test that the cache is writeable
        $this->testCache();

        // --------------------------------------------------------------------------

        //  Instanciate the user model
        $this->instantiateUser();

        // --------------------------------------------------------------------------

        //  Instanciate languages
        $this->instantiateLanguages();

        // --------------------------------------------------------------------------

        /**
         * Is the user suspended?
         * Executed here so that both the user and language systems are initialised
         * (so that any errors can be shown in the correct language).
         */

        $this->isUserSuspended();

        // --------------------------------------------------------------------------

        //  Instanciate DateTime
        $this->instantiateDateTime();

        // --------------------------------------------------------------------------

        //  Need to generate the routes_app.php file?
        if (defined('NAILS_STARTUP_GENERATE_APP_ROUTES') && NAILS_STARTUP_GENERATE_APP_ROUTES) {

            $this->load->model('routes_model');

            if (!$this->routes_model->update()) {

                //  Fall over, routes_app.php *must* be there
                $subject  = 'Failed To generate routes_app.php';
                $message  = 'routes_app.php was not found and could not be generated. ';
                $message .= $this->routes_model->last_error();

                showFatalError($subject, $message);

            } else {

                //  Routes exist now, instruct the browser to try again
                if ($this->input->post()) {

                    redirect($this->input->server('REQUEST_URI'), 'Location', 307);

                } else {

                    redirect($this->input->server('REQUEST_URI'));
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Set User Feedback alerts for the views
        $this->data['notice']  = $this->userFeedback->get('notice')  ?: $this->session->flashdata('notice');
        $this->data['message'] = $this->userFeedback->get('message') ?: $this->session->flashdata('message');
        $this->data['error']   = $this->userFeedback->get('error')   ?: $this->session->flashdata('error');
        $this->data['success'] = $this->userFeedback->get('success') ?: $this->session->flashdata('success');

        // --------------------------------------------------------------------------

        //  Other defaults
        $this->data['page']                   = new stdClass();
        $this->data['page']->title            = '';
        $this->data['page']->seo              = new stdClass();
        $this->data['page']->seo->title       = '';
        $this->data['page']->seo->description = '';
        $this->data['page']->seo->keywords    = '';

        // --------------------------------------------------------------------------

        /**
         * Forced maintenance mode?
         */
        if (app_setting('maintenance_mode_enabled', 'site')) {

            $this->maintenanceMode(true);
        }

        // --------------------------------------------------------------------------

        /**
         * Finally, set some meta tags which should be used on every site.
         */

        $this->meta->addRaw(array(
            'charset' => 'utf8'
        ));

        $this->meta->addRaw(array(
            'name'    => 'viewport',
            'content' => 'width=device-width, initial-scale=1'
        ));
    }

    // --------------------------------------------------------------------------

    /**
     * Checks that the version of PHP is sufficient to run all enabled modules
     * @return void
     */
    protected function checkPhpVersion()
    {
        /**
         * PHP Version Check
         * =================
         *
         * We need to loop through all available modules and have a look at what version
         * of PHP they require, we'll then take the highest version and set that as our
         * minimum supported value.
         *
         * To set a requirement, within the module's nails object in composer.json,
         * specify the minPhpVersion value. You should also specify the appropriate
         * constraint for composer in the "require" section of composer.json.
         *
         * e.g:
         *
         *  "extra":
         *  {
         *      "nails" :
         *      {
         *          "minPhpVersion": "5.4.0"
         *      }
         *  }
         */

        define('NAILS_MIN_PHP_VERSION', _NAILS_MIN_PHP_VERSION());

        if (version_compare(PHP_VERSION, NAILS_MIN_PHP_VERSION, '<')) {

            $subject  = 'PHP Version ' . PHP_VERSION . ' is not supported by Nails';
            $message  = 'The version of PHP you are running is not supported. Nails requires at least ';
            $message .= 'PHP version ' . NAILS_MIN_PHP_VERSION;

            if (function_exists('_NAILS_ERROR')) {

                _NAILS_ERROR($message, $subject);

            } else {

                echo '<h1>ERROR: ' . $subject . '</h1>';
                echo '<h2>' . $message . '</h2>';
                exit(0);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up and configures the global Nails log channel
     * @return  void
     */
    protected function setLogging()
    {
        /**
         * @todo Get centralised logging working throughout
         * - Disable CI's logging
         * - Remove the Logger, CORE_NAILS_Log and NAILS_Log classes
         * - Update all calls to $this->logger, _LOG(), etc
         * - Update areas which do log to do so more objectively, set as warning, debug etc
         */

        //  Using $GLOBALS so that it can be easily accessed from anywhere
        $GLOBALS['NAILS_LOG'] = new Logger('NAILS::MAIN');

        /**
         * @todo Work out a way to easily configure this if needed
         * @todo Utilise monolog's functionality like autorotating logs
         */

        $logFile = FCPATH . APPPATH . 'logs/' . ENVIRONMENT . '-' . date('Y-m-d') . '.php';

        $GLOBALS['NAILS_LOG']->pushHandler(new StreamHandler($logFile));

        //  Save a reference, for ease of access in the controllers
        $this->log = $GLOBALS['NAILS_LOG'];
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the appropriate error reporting values and handlers
     * @return void
     */
    protected function setErrorReporting()
    {
        /**
         * Configure how verbose PHP is; Everything except E_STRICT and E_ERROR;
         * we'll let the errorHandler pickup fatal errors
         */

        error_reporting(E_ALL ^ E_STRICT ^ E_ERROR);

        //  Configure whether errors are shown or no
        if (function_exists('ini_set')) {

            switch (strtoupper(ENVIRONMENT)) {

                case 'PRODUCTION' :

                    //  Suppress all errors on production
                    ini_set('display_errors', false);
                    break;

                default :

                    //  Show errors everywhere else
                    ini_set('display_errors', true);
                    break;
            }
        }

        $this->nailsErrorHandler = \Nails\Factory::service('ErrorHandler');
    }

    // --------------------------------------------------------------------------

    /**
     * Tests that the cache is writeable
     * @return void
     */
    protected function testCache()
    {
        if (is_writable(DEPLOY_CACHE_DIR)) {

            return true;

        } elseif (is_dir(DEPLOY_CACHE_DIR)) {

            //  Attempt to chmod the dir
            if (@chmod(DEPLOY_CACHE_DIR, FILE_WRITE_MODE)) {

                return true;

            } elseif (strtoupper(ENVIRONMENT) !== 'PRODUCTION') {

                show_error('The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" exists but is not writeable.');

            } else {

                $subject = 'Cache Dir is not writeable';
                $message = 'The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" exists but is not writeable.';
                showFatalError($subject, $message);
            }

        } elseif (@mkdir(DEPLOY_CACHE_DIR)) {

            return true;

        } elseif (strtoupper(ENVIRONMENT) !== 'PRODUCTION') {

            show_error('The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" does not exist and could not be created.');

        } else {

            $subject = 'Cache Dir is not writeable';
            $message = 'The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" does not exist and could not be created.';
            showFatalError($subject, $message);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Maintenance Mode is enabled, shows the holding page if so.
     * @param  boolean $force  Force maintenance mode on
     * @param  string  $sTitle Override the page title
     * @param  string  $sBody  Override the page body
     * @return void
     */
    protected function maintenanceMode($force = false, $sTitle = '', $sBody = '')
    {
        if ($force || file_exists(FCPATH . '.MAINTENANCE')) {

            /**
             * We're in maintenance mode. This can happen very early so we need to
             * make sure that we've loaded everything we need to load as we're
             * exiting whether we like it or not
             */

            try {

                //  Get the database so that the app_setting() functions will work
                $oDb = \Nails\Factory::service('Database');

                //  Set the package path (so helpers and libraries are loaded correctly)
                $this->load->add_package_path(NAILS_COMMON_PATH);

                //  Load the helpers
                \Nails\Factory::service('encrypt');
                \Nails\Factory::helper('app_setting');
                \Nails\Factory::helper('tools');

                $whitelistIp   = (array) app_setting('maintenance_mode_whitelist', 'site');
                $isWhiteListed = isIpInRange($this->input->ip_address(), $whitelistIp);

                //  Customisations
                $sMaintenanceTitle = $sTitle ? $sTitle : app_setting('maintenance_mode_title', 'site');
                $sMaintenanceBody  = $sBody ? $sBody : app_setting('maintenance_mode_body', 'site');

            } catch (\Exception $e) {

                //  No database, or it failed, defaults
                $isWhiteListed     = false;
                $sMaintenanceTitle = $sTitle;
                $sMaintenanceBody  = $sBody;
            }

            // --------------------------------------------------------------------------

            if (!$isWhiteListed) {

                if (!$this->input->is_cli_request()) {

                    header($this->input->server('SERVER_PROTOCOL') . ' 503 Service Temporarily Unavailable');
                    header('Status: 503 Service Temporarily Unavailable');
                    header('Retry-After: 7200');

                    // --------------------------------------------------------------------------

                    //  If the request is an AJAX request, or the URL is on the API then spit back JSON
                    if ($this->input->is_ajax_request() || $this->uri->segment(1) == 'api') {

                        header('Cache-Control: no-store, no-cache, must-revalidate');
                        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                        header('Content-type: application/json');
                        header('Pragma: no-cache');

                        $_out = array('status' => 503, 'error' => 'Down for Maintenance');

                        echo json_encode($_out);

                    //  Otherwise, render some HTML
                    } else {

                        //  Look for an app override
                        if (file_exists(FCPATH . APPPATH . 'views/maintenance/maintenance.php')) {

                            require FCPATH . APPPATH . 'views/maintenance/maintenance.php';

                        //  Fall back to the Nails maintenance page
                        } elseif (file_exists(NAILS_COMMON_PATH . 'views/maintenance/maintenance.php')) {

                            require NAILS_COMMON_PATH . 'views/maintenance/maintenance.php';

                        //  Fall back, back to plain text
                        } else {

                            echo '<h1>Down for maintenance</h1>';
                        }
                    }

                } else {

                    echo 'Down for Maintenance' . "\n";
                }
                exit(0);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if credentials should be requested for staging environments
     * @return void
     */
    protected function staging()
    {
        $users = @json_decode(APP_STAGING_USERPASS);

        if (strtoupper(ENVIRONMENT) == 'STAGING' && $users) {

            $users = (array) $users;

            if (!isset($_SERVER['PHP_AUTH_USER'])) {

                $this->stagingRequestCredentials();
            }

            if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

                //  Determine the users
                $users = array_filter($users);
                $isSet = isset($users[$_SERVER['PHP_AUTH_USER']]);
                $isNotEqual = $users[$_SERVER['PHP_AUTH_USER']] != md5(trim($_SERVER['PHP_AUTH_PW']));

                if (!$isSet || $isNotEqual) {

                    $this->stagingRequestCredentials();
                }

            } else {

                $this->stagingRequestCredentials();
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Requests staging credentials
     * @return void
     */
    protected function stagingRequestCredentials()
    {
        header('WWW-Authenticate: Basic realm="' . APP_NAME . ' Staging Area"');
        header($this->input->server('SERVER_PROTOCOL') . ' 401 Unauthorized');
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <title><?=APP_NAME?> - Unauthorised</title>
                <meta charset="utf-8">

                <!--    STYLES  -->
                <link href="<?=NAILS_ASSETS_URL?>css/nails.default.css" rel="stylesheet">

                <style type="text/css">

                    #main-col
                    {
                        text-align:center;
                        margin-top:100px;
                    }

                </style>

            </head>
            <body>
                <div class="container row">
                    <div class="six columns first last offset-by-five" id="main-col">
                        <h1>unauthorised</h1>
                        <hr />
                        <p>This staging environment restrticted to authorised users only.</p>
                    </div>
                </div>
            </body>
        </html>
        <?php
        exit(0);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up date & time handling
     * @return void
     */
    protected function instantiateDateTime()
    {
        //  Define default date format
        $oDefaultDateFormat = $this->datetime_model->getDateFormatDefault();

        if (empty($oDefaultDateFormat)) {

            showFatalError('No default date format has been set, or it\'s been set incorrectly.');
        }

        define('APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG', $oDefaultDateFormat->slug);
        define('APP_DEFAULT_DATETIME_FORMAT_DATE_LABEL', $oDefaultDateFormat->label);
        define('APP_DEFAULT_DATETIME_FORMAT_DATE_FORMAT', $oDefaultDateFormat->format);

        //  Define default time format
        $oDefaultTimeFormat = $this->datetime_model->getTimeFormatDefault();

        if (empty($oDefaultTimeFormat)) {

            showFatalError('No default time format has been set, or it\'s been set incorrectly.');
        }

        define('APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG', $oDefaultTimeFormat->slug);
        define('APP_DEFAULT_DATETIME_FORMAT_TIME_LABEL', $oDefaultTimeFormat->label);
        define('APP_DEFAULT_DATETIME_FORMAT_TIME_FORMAT', $oDefaultTimeFormat->format);

        // --------------------------------------------------------------------------

        /**
         * Set the timezones.
         * Choose the user's timezone - starting with their preference, followed by
         * the app's default.
         */

        if (activeUser('timezone')) {

            $sTimezoneUser = activeUser('timezone');

        } else {

            $sTimezoneUser = $this->datetime_model->getTimezoneDefault();
        }

        $this->datetime_model->setTimezones('UTC', $sTimezoneUser);

        // --------------------------------------------------------------------------

        //  Set the user date/time formats
        $sFormatDate = activeUser('datetime_format_date');
        $sFormatDate = $sFormatDate ? $sFormatDate : APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG;

        $sFormatTime = activeUser('datetime_format_time');
        $sFormatTime = $sFormatTime ? $sFormatTime : APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG;

        $this->datetime_model->setFormats($sFormatDate, $sFormatTime);

        // --------------------------------------------------------------------------

        //  Make sure the system and the database are running on UTC
        date_default_timezone_set('UTC');
        $this->db->query('SET time_zone = \'+0:00\'');
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up language handling
     * @return void
     */
    protected function instantiateLanguages()
    {
        //  Define default language
        $oDefault = $this->language_model->getDefault();

        if (empty($oDefault)) {

            showFatalError('No default language has been set, or it\'s been set incorrectly.');
        }

        define('APP_DEFAULT_LANG_CODE', $oDefault->code);
        define('APP_DEFAULT_LANG_LABEL', $oDefault->label);

        // --------------------------------------------------------------------------

        /**
         * Set any global preferences for this user, e.g languages, fall back to the
         * app's default language (defined in config.php).
         */

        $sUserLangCode = activeUser('language');

        if (!empty($sUserLangCode)) {

            define('RENDER_LANG_CODE', $sUserLangCode);

        } else {

            define('RENDER_LANG_CODE', APP_DEFAULT_LANG_CODE);
        }

        //  Set the language config item which codeigniter will use.
        $this->config->set_item('language', RENDER_LANG_CODE);

        //  Load the Nails. generic lang file
        $this->lang->load('nails');
    }

    // --------------------------------------------------------------------------

    /**
     * Autoloads all items (helpers, models, libraries etc) that we'll need
     * @return void [description]
     */
    protected function autoloadItems()
    {
        /**
         * This is an important part. Here we are defining all the packages to load.
         * this translates as "where CodeIgniter will look for stuff".
         *
         * We have to do a few manual hacks to ensure things work as expected, i.e.
         * the load/check order is:
         *
         * 1. The Application
         * 2. Available modules
         * 3. Nails Common
         */

        //  Reset
        $this->config->_config_paths = array();

        $aPaths = array();

        //  Nails Common
        $aPaths[] = NAILS_COMMON_PATH;

        //  Available Modules
        $aAvailableModules = _NAILS_GET_MODULES();

        foreach ($aAvailableModules as $oModule) {

            $aPaths[] = $oModule->path;
        }

        //  The Application
        $aPaths[] = FCPATH . APPPATH;

        foreach ($aPaths as $sPath) {

            $this->load->add_package_path($sPath);
        }

        // --------------------------------------------------------------------------

        //  Load the user helper
        \Nails\Factory::helper('user');
        \Nails\Factory::helper('app_setting');
        \Nails\Factory::helper('app_notification');
        \Nails\Factory::helper('date');
        \Nails\Factory::helper('url');
        \Nails\Factory::helper('cookie');
        \Nails\Factory::helper('form');
        \Nails\Factory::helper('html');
        \Nails\Factory::helper('tools');
        \Nails\Factory::helper('debug');
        \Nails\Factory::helper('language');
        \Nails\Factory::helper('text');
        \Nails\Factory::helper('exception');
        \Nails\Factory::helper('typography');
        \Nails\Factory::helper('event');
        \Nails\Factory::helper('log');

        /**
         * Module specific helpers
         */

        $aModules = _NAILS_GET_MODULES();
        foreach ($aModules as $oModule) {

            if (!empty($oModule->autoload->helpers) && is_array($oModule->autoload->helpers)) {
                foreach ($oModule->autoload->helpers as $sHelper) {
                    \Nails\Factory::helper($sHelper, $oModule->name);
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Fairly sure load order is important here.
        $aModels   = array();
        $aModels[] = 'app_setting_model';
        $aModels[] = 'auth/user_model';
        $aModels[] = 'auth/user_group_model';
        $aModels[] = 'auth/user_password_model';
        $aModels[] = 'datetime_model';
        $aModels[] = 'language_model';

        foreach ($aModels as $sModel) {

            $this->load->model($sModel);
        }

        // --------------------------------------------------------------------------

        /**
         * Common libraries
         * @note: We have to load this way so that the property is taken up by the CI
         * super object and therefore more reliably accessible (e.g in CMS module).
         * @todo  reduce this coupling
         * @todo  implement userFeedback library throughout
         */

        $oCi               =& get_instance();
        $oCi->db           = \Nails\Factory::service('Database');
        $oCi->meta         = \Nails\Factory::service('Meta');
        $oCi->asset        = \Nails\Factory::service('Asset');
        $oCi->userFeedback = \Nails\Factory::service('UserFeedback');
        $oCi->session      = \Nails\Factory::service('Session');
        $oCi->encrypt      = \Nails\Factory::service('Encrypt');
        $oCi->logger       = \Nails\Factory::service('Logger');
        $oCi->event        = \Nails\Factory::service('Event', 'nailsapp/module-event');
        $oCi->emailer      = \Nails\Factory::service('Emailer', 'nailsapp/module-email');
    }

    // --------------------------------------------------------------------------

    /**
     * Set up the active user
     * @return void
     */
    protected function instantiateUser()
    {
        /**
         * Find a remembered user and initialise the user model; this routine checks
         * the user's cookies and set's up the session for an existing or new user.
         */

        $this->user_model->init();

        // --------------------------------------------------------------------------

        //  Inject the user object into the user_group, user_password & datetime models
        $this->user_group_model->setUserObject($this->user_model);
        $this->user_password_model->setUserObject($this->user_model);
        $this->datetime_model->setUserObject($this->user_model);

        // --------------------------------------------------------------------------

        //  Shortcut/backwards compatibility
        $this->user = $this->user_model;

        //  Set a $user variable (for the views)
        $this->data['user'] = $this->user_model;
        $this->data['user_group'] = $this->user_group_model;
        $this->data['user_password'] = $this->user_password_model;
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether the active user is suspended and, if so, logs them out.
     * @return void
     */
    protected function isUserSuspended()
    {
        //  Check if this user is suspended
        if ($this->user_model->isLoggedIn() && activeUser('is_suspended')) {

            //  Load models and langs
            $this->load->model('auth/auth_model');
            $this->lang->load('auth/auth');

            //  Log the user out
            $this->auth_model->logout();

            //  Create a new session
            $this->session->sess_create();

            //  Give them feedback
            $this->session->set_flashdata('error', lang('auth_login_fail_suspended'));
            redirect('/');
        }
    }
}
