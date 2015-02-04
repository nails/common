<?php

/**
 * This class is the main execution point for all page requests. It
 * checks and configures the Nails environment.
 *
 * @package     Nails
 * @subpackage  common
 * @category    controller
 * @author      Nails Dev Team
 * @link
 */

class CORE_NAILS_Controller extends MX_Controller {

    protected $data;
    private $_supported_lang;
    protected $nailsErrorHandler;

    // --------------------------------------------------------------------------

    /**
     * Build the main framework. All autoloaded items have been loaded and
     * instantiated by this point and are safe to use.
     *
     * @access  public
     * @return  void
     *
     **/
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        //  Include the composer autoloader
        if (!file_exists(FCPATH . 'vendor/autoload.php')) {

            $_ERROR  = 'Composer autoloader not found; run <code>composer install</code> ';
            $_ERROR .= 'to install dependencies.';

            include NAILS_COMMON_PATH . 'errors/startup_error.php';
        }

        require_once FCPATH . 'vendor/autoload.php';

        // --------------------------------------------------------------------------

        //  Is Nails in maintenance mode?
        $this->maintenanceMode();

        // --------------------------------------------------------------------------

        //  Nails PHP Version Check
        $this->checkPhpVersion();

        // --------------------------------------------------------------------------

        //  Include global Nails files
        require_once NAILS_COMMON_PATH . 'core/CORE_NAILS_Traits.php';

        // --------------------------------------------------------------------------

        //  Configure error reporting
        $this->setErrorReporting();

        // --------------------------------------------------------------------------

        //  Set the default content-type
        $this->output->set_content_type('text/html; charset=utf-8');

        // --------------------------------------------------------------------------

        //  Define data array (used extensively in views)
        $this->data =& getControllerData();

        // --------------------------------------------------------------------------

        //  Instantiate the database?
        $this->instantiateDb();

        // --------------------------------------------------------------------------

        //  Define all the packages
        $this->autoloadItems();

        // --------------------------------------------------------------------------

        //  If we're on a staging environment then prompt for a password;
        //  but only if a password has been defined in app.php

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
                $messgae .= $this->routes_model->last_error();

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

        //  Set alerts

        //  These are hooks for code to add feedback messages to the user.
        $this->data['notice']  = $this->session->flashdata('notice');
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['error']   = $this->session->flashdata('error');
        $this->data['success'] = $this->session->flashdata('success');

        // --------------------------------------------------------------------------

        //  Other defaults
        $this->data['page']                   = new stdClass();
        $this->data['page']->title            = '';
        $this->data['page']->seo              = new stdClass();
        $this->data['page']->seo->title       = '';
        $this->data['page']->seo->description = '';
        $this->data['page']->seo->keywords    = '';
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
     * Sets the appropriate error reporting values and handlers
     * @return void
     */
    protected function setErrorReporting()
    {
        /**
         * Configure how verbose PHP is; Everything except E_STRICT and E_ERROR;
         * we'll let the errorHandler pickup fatal erros
         */

        error_reporting(E_ALL ^ E_STRICT ^ E_ERROR);

        //  Configure whether errors are shown or no
        if (function_exists('ini_set')) {

            switch (strtoupper(ENVIRONMENT)) {

                case 'PRODUCTION' :

                    //  Suppress all errors on production
                    ini_set('display_errors', '0');
                    break;

                default :

                    //  Show errors everywhere else
                    ini_set('display_errors', '1');
                    break;
            }
        }

        require_once NAILS_COMMON_PATH . 'core/CORE_NAILS_ErrorHandler.php';
        $this->nailsErrorHandler = new CORE_NAILS_ErrorHandler();
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

        } elseif(@mkdir(DEPLOY_CACHE_DIR)) {

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
     * @return void
     */
    protected function maintenanceMode()
    {
        if (file_exists(FCPATH . '.MAINTENANCE')) {

            /**
             * We're in maintenance mode. This happens very early so we need to
             * make sure that we've loaded everything we need to load as we're
             * exiting whether we like it or not
             */

            //  Whitelist
            if ($this->instantiateDb(true)) {

                //  Load the traits
                require_once NAILS_COMMON_PATH . 'core/CORE_NAILS_Traits.php';

                //  Set the package path (so helpers and libraries are loaded correctly)
                $this->load->add_package_path(NAILS_COMMON_PATH);

                //  Load the helpers
                $this->load->library('encrypt');
                $this->load->helper('app_setting');
                $this->load->helper('tools');

                $whitelistIp   = (array) app_setting('maintenance_mode_whitelist');
                $isWhitelisted = isIpInRange($this->input->ip_address(), $whitelistIp);

            } else {

                //  No database, or it failed, defaults
                $isWhiteListed = false;
            }

            // --------------------------------------------------------------------------

            if (!$isWhitelisted) {

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
            }

            exit(0);
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

                if (
                    !isset($users[$_SERVER['PHP_AUTH_USER']])
                    || $users[$_SERVER['PHP_AUTH_USER']] != md5(trim($_SERVER['PHP_AUTH_PW']))
                ) {

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
     * Loads the database
     * @param  boolean $failGracefully Whether or not to fail gracefully
     * @return boolean
     */
    protected function instantiateDb($failGracefully = false)
    {
        if (DEPLOY_DB_USERNAME && DEPLOY_DB_DATABASE) {

            $this->load->database();

            /**
             * Don't run transactions in strict mode. In my opinion it's odd behaviour:
             * When a transaction is committed it should be the end of the story. If it's
             * not then a failure elsewhere can cause a rollback unexpectedly. Silly CI.
             */

            $this->db->trans_strict(false);

            return true;

        } elseif (!$failGracefully) {

            show_error('No database is configured.');
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up date & time handling
     * @return void
     */
    protected function instantiateDateTime()
    {
        //  Define default date format
        $_default = $this->datetime_model->getDateFormatDefault();

        if (empty($_default)) {

            showFatalError('No default date format has been set, or it\'s been set incorrectly.');
        }

        define('APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG', $_default->slug);
        define('APP_DEFAULT_DATETIME_FORMAT_DATE_LABEL', $_default->label);
        define('APP_DEFAULT_DATETIME_FORMAT_DATE_FORMAT', $_default->format);

        //  Define default time format
        $_default = $this->datetime_model->getTimeFormatDefault();

        if (empty($_default)) {

            showFatalError('No default time format has been set, or it\'s been set incorrectly.');
        }

        define('APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG', $_default->slug);
        define('APP_DEFAULT_DATETIME_FORMAT_TIME_LABEL', $_default->label);
        define('APP_DEFAULT_DATETIME_FORMAT_TIME_FORMAT', $_default->format);

        // --------------------------------------------------------------------------

        /**
         * Set the timezones.
         * Choose the user's timezone - starting with their preference, followed by
         * the app's default.
         */

        if (active_user('timezone')) {

            $_timezone_user = active_user('timezone');

        } else {

            $_timezone_user = $this->datetime_model->getTimezoneDefault();
        }

        $this->datetime_model->setTimezones('UTC', $_timezone_user);

        // --------------------------------------------------------------------------

        //  Set the user date/time formats
        $_format_date = active_user('datetime_format_date') ? active_user('datetime_format_date') : APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG;
        $_format_time = active_user('datetime_format_time') ? active_user('datetime_format_time') : APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG;

        $this->datetime_model->setFormats($_format_date, $_format_time);

        // --------------------------------------------------------------------------

        //  Make sure the system is running on UTC
        date_default_timezone_set('UTC');

        // --------------------------------------------------------------------------

        //  Make sure the DB is thinking along the same lines
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
        $_default = $this->language_model->get_default();

        if (empty($_default)) {

            showFatalError('No default language has been set, or it\'s been set incorrectly.');
        }

        define('APP_DEFAULT_LANG_CODE', $_default->code);
        define('APP_DEFAULT_LANG_LABEL', $_default->label);

        // --------------------------------------------------------------------------

        //  Set any global preferences for this user, e.g languages, fall back to
        //  the app's default language (defined in config.php).

        $_user_lang = active_user('language');

        if (!empty($_user_lang)) {

            define('RENDER_LANG_CODE', $_user_lang);

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

        $_packages = array();

        //  Nails Common
        $_packages[] = NAILS_COMMON_PATH;

        //  Available Modules
        $_available_modules = _NAILS_GET_MODULES();

        foreach ($_available_modules as $module) {

            $_packages[] = $module->path;
        }

        //  The Application
        $_packages[] = FCPATH . APPPATH;

        foreach ($_packages as $package) {

            $this->load->add_package_path($package);
        }

        // --------------------------------------------------------------------------

        //  Load the user helper
        $_helpers   = array();
        $_helpers[] = 'user';
        $_helpers[] = 'app_setting';
        $_helpers[] = 'app_notification';
        $_helpers[] = 'datetime';
        $_helpers[] = 'url';
        $_helpers[] = 'cookie';
        $_helpers[] = 'form';
        $_helpers[] = 'html';
        $_helpers[] = 'tools';
        $_helpers[] = 'debug';
        $_helpers[] = 'language';
        $_helpers[] = 'text';
        $_helpers[] = 'exception';
        $_helpers[] = 'typography';
        $_helpers[] = 'event';
        $_helpers[] = 'log';

        //  Module specific helpers
        //  CDN
        if (isModuleEnabled('nailsapp/module-cdn')) {

            $_helpers[] = 'cdn';
        }

        //  Shop
        if (isModuleEnabled('nailsapp/module-shop')) {

            $_helpers[] = 'shop';
        }

        //  Blog
        if (isModuleEnabled('nailsapp/module-blog')) {

            $_helpers[] = 'blog';
        }

        //  CMS
        if (isModuleEnabled('nailsapp/module-cms')) {

            $_helpers[] = 'cms';
        }

        //  Load...
        foreach ($_helpers as $helper) {

            $this->load->helper($helper);
        }

        // --------------------------------------------------------------------------

        //  Fairly sure load order is important here.
        $_models   = array();
        $_models[] = 'app_setting_model';
        $_models[] = 'auth/user_model';
        $_models[] = 'auth/user_group_model';
        $_models[] = 'auth/user_password_model';
        $_models[] = 'datetime_model';
        $_models[] = 'language_model';

        foreach ($_models as $model) {

            $this->load->model($model);
        }

        // --------------------------------------------------------------------------

        $_libraries = array();

        /**
         * Test that $_SERVER is available, the session library needs this
         * Generally not available when running on the command line. If it's
         * not available then load up the faux session which has the same methods
         * as the session library, but behaves as if logged out - comprende?
         */

        if ($this->input->server('REMOTE_ADDR')) {

            $_libraries[] = 'session';

        } else {

            $_libraries[] = array('auth/faux_session', 'session');
        }

        // --------------------------------------------------------------------------

        /**
         * STOP!Before we load the session library, we need to check if we're using
         * the database. If we are then check if `sess_table_name` is "nails_session".
         * If it is, and NAILS_DB_PREFIX != nails_ then replace 'nails_' with NAILS_DB_PREFIX
         */

        $_sess_table_name = $this->config->item('sess_table_name');

        if ($_sess_table_name === 'nails_session' && NAILS_DB_PREFIX !== 'nails_') {

            $_sess_table_name = str_replace('nails_', NAILS_DB_PREFIX, $_sess_table_name);
            $this->config->set_item('sess_table_name', $_sess_table_name);
        }

        // --------------------------------------------------------------------------

        $_libraries[] = 'encrypt';
        $_libraries[] = 'asset/asset';
        $_libraries[] = 'email/emailer';
        $_libraries[] = 'event/event';
        $_libraries[] = 'logger';

        foreach ($_libraries as $library) {

            if (is_array($library)) {

                $this->load->library($library[0], array(), $library[1]);

            } else {

                $this->load->library($library);
            }
        }
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
        if ($this->user_model->is_logged_in() && active_user('is_suspended')) {

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
