<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

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

            $_ERROR = 'Composer autoloader not found; run <code>composer install</code> to install dependencies.';
            include NAILS_COMMON_PATH . 'errors/startup_error.php';
        }

        require_once(FCPATH . 'vendor/autoload.php');

        // --------------------------------------------------------------------------

        //  Configure error reporting
        $this->setErrorReporting();

        // --------------------------------------------------------------------------

        //  Set the default content-type
        $this->output->set_content_type('text/html; charset=utf-8');

        // --------------------------------------------------------------------------

        //  Define data array (used extensively in views)
        $this->data =& get_controller_data();

        // --------------------------------------------------------------------------

        //  Instantiate the database?
        $this->instantiateDb();

        // --------------------------------------------------------------------------

        //  Define all the packages
        $this->autoloadItems();

        // --------------------------------------------------------------------------

        //  Is Nails in maintenance mode?
        $this->maintenanceMode();

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

            $this->load->model('system/routes_model');

            if (!$this->routes_model->update()) {

                //  Fall over, routes_app.php *must* be there
                showFatalError('Failed To generate routes_app.php', 'routes_app.php was not found and could not be generated. ' . $this->routes_model->last_error());

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

    protected function setErrorReporting()
    {
        /**
         * Configure how verbose PHP is; Everything except E_STRICT and E_ERROR;
         * we'll let the errorHandler pickup fatal erros
         */

        error_reporting(E_ALL ^ E_STRICT ^ E_ERROR);

        //  Configure whether errors are shown or no
        if (function_exists('ini_set')) {

            switch(strtoupper(ENVIRONMENT)) {

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

                showFatalError('Cache Dir is not writeable', 'The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" exists but is not writeable.');
            }

        } elseif(@mkdir(DEPLOY_CACHE_DIR)) {

            return true;

        } elseif (strtoupper(ENVIRONMENT) !== 'PRODUCTION') {

            show_error('The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" does not exist and could not be created.');

        } else {

            showFatalError('Cache Dir is not writeable', 'The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" does not exist and could not be created.');
        }
    }

    // --------------------------------------------------------------------------


    protected function maintenanceMode()
    {
        if (app_setting('maintenance_mode_enabled') || file_exists(FCPATH . '.MAINTENANCE')) {

            $whitelist_ip = (array) app_setting('maintenance_mode_whitelist');

            if (!$this->input->is_cli_request() && ip_in_range($this->input->ip_address(), $whitelist_ip) === false) {

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

                exit(0);

            } elseif ($this->input->is_cli_request()) {

                echo 'Down for Maintenance' . "\n";
                exit(0);
            }
        }
    }


    // --------------------------------------------------------------------------


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

                if (!isset($users[$_SERVER['PHP_AUTH_USER']]) || $users[$_SERVER['PHP_AUTH_USER']] != md5(trim($_SERVER['PHP_AUTH_PW']))) {

                    $this->stagingRequestCredentials();
                }

            } else {

                $this->stagingRequestCredentials();
            }
        }
    }


    // --------------------------------------------------------------------------


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


    protected function instantiateDb()
    {
        if (DEPLOY_DB_USERNAME && DEPLOY_DB_DATABASE) {

            $this->load->database();

            /**
             * Don't run transactions in strict mode. In my opinion it's odd behaviour:
             * When a transaction is committed it should be the end of the story. If it's
             * not then a failure elsewhere can cause a rollback unexpectedly. Silly CI.
             */

            $this->db->trans_strict(false);

        } else {

            show_error('No database is configured.');
        }
    }


    // --------------------------------------------------------------------------


    protected function instantiateDateTime()
    {
        //  Define default date format
        $_default = $this->datetime_model->get_date_format_default();

        if (empty($_default)) {

            showFatalError('No default date format has been set, or it\'s been set incorrectly.');
        }

        define('APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG', $_default->slug);
        define('APP_DEFAULT_DATETIME_FORMAT_DATE_LABEL', $_default->label);
        define('APP_DEFAULT_DATETIME_FORMAT_DATE_FORMAT', $_default->format);

        //  Define default time format
        $_default = $this->datetime_model->get_time_format_default();

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

            $_timezone_user = $this->datetime_model->get_timezone_default();
        }

        $this->datetime_model->set_timezones('UTC', $_timezone_user);

        // --------------------------------------------------------------------------

        //  Set the user date/time formats
        $_format_date = active_user('datetime_format_date') ? active_user('datetime_format_date') : APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG;
        $_format_time = active_user('datetime_format_time') ? active_user('datetime_format_time') : APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG;

        $this->datetime_model->set_formats($_format_date, $_format_time);

        // --------------------------------------------------------------------------

        //  Make sure the system is running on UTC
        date_default_timezone_set('UTC');

        // --------------------------------------------------------------------------

        //  Make sure the DB is thinking along the same lines
        $this->db->query('SET time_zone = \'+0:00\'');
    }


    // --------------------------------------------------------------------------


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


    protected function autoloadItems()
    {
        $_packages          = array();
        $_available_modules = _NAILS_GET_AVAILABLE_MODULES();

        foreach ($_available_modules as $module) {

            $_packages[] = FCPATH . 'vendor/' . $module . '/';
        }

        $_packages[] = NAILS_COMMON_PATH . '';

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
        if (module_is_enabled('cdn')) {

            $_helpers[] = 'cdn';
        }

        //  Shop
        if (module_is_enabled('shop')) {

            $_helpers[] = 'shop';
        }

        //  Blog
        if (module_is_enabled('blog')) {

            $_helpers[] = 'blog';
        }

        //  CMS
        if (module_is_enabled('cms')) {

            $_helpers[] = 'cms';
        }

        //  Load...
        foreach ($_helpers as $helper) {

            $this->load->helper($helper);
        }

        // --------------------------------------------------------------------------

        $_models   = array();
        $_models[] = 'system/app_setting_model';
        $_models[] = 'system/user_model';
        $_models[] = 'system/user_group_model';
        $_models[] = 'system/user_password_model';
        $_models[] = 'system/datetime_model';
        $_models[] = 'system/language_model';

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


    protected function instantiateUser()
    {
        /**
         * Find a remembered user and initialise the user model; this routine checks
         * the user's cookies and set's up the session for an existing or new user.
         */

        $this->user_model->init();

        // --------------------------------------------------------------------------

        //  Inject the user object into the user_group, user_password & datetime models
        $this->user_group_model->_set_user_object($this->user_model);
        $this->user_password_model->_set_user_object($this->user_model);
        $this->datetime_model->_set_user_object($this->user_model);

        // --------------------------------------------------------------------------

        //  Shortcut/backwards compatibility
        $this->user = $this->user_model;

        //  Set a $user variable (for the views)
        $this->data['user'] = $this->user_model;
        $this->data['user_group'] = $this->user_group_model;
        $this->data['user_password'] = $this->user_password_model;
    }


    // --------------------------------------------------------------------------


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

/* End of file NAILS_Controller.php */
/* Location: ./application/core/NAILS_Controller.php */