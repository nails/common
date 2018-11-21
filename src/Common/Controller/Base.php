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

namespace Nails\Common\Controller;

use Nails\Common\Events;
use Nails\Common\Exception\NailsException;
use Nails\Components;
use Nails\Environment;
use Nails\Factory;
use Nails\Functions;

abstract class Base extends \MX_Controller
{
    protected $data;
    protected $user;
    protected $log;

    // --------------------------------------------------------------------------

    /**
     * Build the main framework. All auto-loaded items have been loaded and
     * instantiated by this point and are safe to use.
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        //  Setup Events
        $oEventService = Factory::service('Event');

        //  Call the SYSTEM:STARTUP event, the earliest event the app can bind to.
        $oEventService->trigger(Events::SYSTEM_STARTUP, 'nails/common');

        // --------------------------------------------------------------------------

        $this->maintenanceMode();
        $this->setErrorReporting();
        $this->setContentType();
        $this->definePackages();
        $this->passwordProtected();
        $this->testCache();
        $this->instantiateUser();
        $this->instantiateLanguages();
        $this->isUserSuspended();
        $this->instantiateDateTime();
        $this->generateRoutes();

        // --------------------------------------------------------------------------

        //  Populate some standard fields
        $this->data =& getControllerData();
        static::populateUserFeedback($this->data);
        static::populatePageData($this->data);

        // --------------------------------------------------------------------------

        /**
         * Forced maintenance mode?
         */
        if (appSetting('maintenance_mode_enabled', 'site')) {
            $this->maintenanceMode(true);
        }

        // --------------------------------------------------------------------------

        $this->setCommonMeta();
        $this->setNailsJs();
        $this->setGlobalJs();
        $this->setGlobalCss();

        // --------------------------------------------------------------------------

        //  @todo (Pablo - 2017-06-08) - Remove this
        self::backwardsCompatibility($this);

        // --------------------------------------------------------------------------

        //  Call the SYSTEM:READY event, the system is all geared up and ready to go
        $oEventService->trigger(Events::SYSTEM_READY, 'nails/common');
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the appropriate error reporting values and handlers
     *
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
            switch (Environment::get()) {
                case Environment::ENV_PROD:
                    //  Suppress all errors on production
                    ini_set('display_errors', false);
                    break;
                default:
                    //  Show errors everywhere else
                    ini_set('display_errors', true);
                    break;
            }
        }

        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->init();
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the content type to use for the request to UTF-8
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function setContentType()
    {
        $oOutput = Factory::service('Output');
        $oOutput->set_content_type('text/html; charset=utf-8');
    }

    // --------------------------------------------------------------------------

    /**
     * Tests that the cache is writable
     *
     * @return bool
     * @throws NailsException
     */
    protected function testCache()
    {
        if (is_writable(CACHE_PATH)) {

            return true;

        } elseif (is_dir(CACHE_PATH)) {

            //  Attempt to chmod the dir
            if (@chmod(CACHE_PATH, FILE_WRITE_MODE)) {

                return true;

            } else {
                throw new NailsException(
                    'The app\'s cache dir "' . CACHE_PATH . '" exists but is not writable.',
                    1
                );
            }

        } elseif (@mkdir(CACHE_PATH)) {

            return true;

        } else {
            throw new NailsException(
                'The app\'s cache dir "' . CACHE_PATH . '" does not exist and could not be created.',
                1
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Maintenance Mode is enabled, shows the holding page if so.
     *
     * @param  boolean $force  Force maintenance mode on
     * @param  string  $sTitle Override the page title
     * @param  string  $sBody  Override the page body
     *
     * @return void
     */
    protected function maintenanceMode($force = false, $sTitle = '', $sBody = '')
    {
        if ($force || file_exists(NAILS_APP_PATH . '.MAINTENANCE')) {

            /**
             * We're in maintenance mode. This can happen very early so we need to
             * make sure that we've loaded everything we need to load as we're
             * exiting whether we like it or not
             */

            $oInput = Factory::service('Input');
            $oUri   = Factory::service('Uri');

            try {

                //  Load the encryption service. Set the package path so it is loaded correctly
                //  (this runs early, before the paths are added)
                $this->load->add_package_path(NAILS_COMMON_PATH);
                Factory::service('encrypt');

                $whitelistIp   = (array) appSetting('maintenance_mode_whitelist', 'site');
                $isWhiteListed = isIpInRange($oInput->ipAddress(), $whitelistIp);

                //  Customisations
                $sMaintenanceTitle = $sTitle ? $sTitle : appSetting('maintenance_mode_title', 'site');
                $sMaintenanceBody  = $sBody ? $sBody : appSetting('maintenance_mode_body', 'site');

            } catch (\Exception $e) {

                //  No database, or it failed, defaults
                $isWhiteListed     = false;
                $sMaintenanceTitle = $sTitle;
                $sMaintenanceBody  = $sBody;
            }

            // --------------------------------------------------------------------------

            if (!$isWhiteListed) {

                if (!$oInput::isCli()) {

                    header($oInput->server('SERVER_PROTOCOL') . ' 503 Service Temporarily Unavailable');
                    header('Status: 503 Service Temporarily Unavailable');
                    header('Retry-After: 7200');

                    // --------------------------------------------------------------------------

                    //  If the request is an AJAX request, or the URL is on the API then spit back JSON
                    if ($oInput::isAjax() || $oUri->segment(1) == 'api') {

                        header('Cache-Control: no-store, no-cache, must-revalidate');
                        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                        header('Content-Type: application/json');
                        header('Pragma: no-cache');

                        $_out = ['status' => 503, 'error' => 'Down for Maintenance'];

                        echo json_encode($_out);

                    } else {
                        //  Otherwise, render some HTML
                        if (file_exists(APPPATH . 'views/errors/html/maintenance.php')) {
                            //  Look for an app override
                            require APPPATH . 'views/errors/html/maintenance.php';
                        } else {
                            //  Fall back to the Nails maintenance page
                            require NAILS_COMMON_PATH . 'views/errors/html/maintenance.php';
                        }
                    }

                } else {
                    if (file_exists(APPPATH . 'views/errors/cli/maintenance.php')) {
                        //  Look for an app override
                        require APPPATH . 'views/errors/cli/maintenance.php';
                    } else {
                        //  Fall back to the Nails maintenance page
                        require NAILS_COMMON_PATH . 'views/errors/cli/maintenance.php';
                    }
                }
                exit(0);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if credentials should be requested for staging environments
     *
     * @return void
     */
    protected function passwordProtected()
    {
        /**
         * To password protect an environment you must create a constant which is
         * a JSON string of key/value pairs (where the key is the username and the
         * value is a sha1 hash of the password):
         *
         *     {
         *         'john': '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8',
         *         'amy': '3fc9b689459d738f8c88a3a48aa9e33542016b7a4052e001aaa536fca74813cb'
         *     }
         *
         * You may also whitelist IP/IP Ranges by providing an array of IP Ranges
         *
         *      [
         *          '123.456.789.123',
         *          '123.456/789'
         *      ]
         */

        $oInput                 = Factory::service('Input');
        $sConstantName          = 'APP_USER_PASS_' . Environment::get();
        $sConstantNameWhitelist = 'APP_USER_PASS_WHITELIST_' . Environment::get();

        if (!$oInput::isCli() && defined($sConstantName)) {

            //  On the whitelist?
            if (defined($sConstantNameWhitelist)) {

                $oInput          = Factory::service('Input');
                $aWhitelsitedIps = @json_decode(constant($sConstantNameWhitelist));
                $bWhitelisted    = isIpInRange($oInput->ipAddress(), $aWhitelsitedIps);

            } else {

                $bWhitelisted = false;
            }

            if (!$bWhitelisted) {

                $oCredentials = @json_decode(constant($sConstantName));

                if (empty($_SERVER['PHP_AUTH_USER'])) {
                    $this->passwordProtectedRequest();
                }

                if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

                    //  Determine the users
                    $bIsSet   = isset($oCredentials->{$_SERVER['PHP_AUTH_USER']});
                    $bIsEqual = $bIsSet && $oCredentials->{$_SERVER['PHP_AUTH_USER']} == hash('sha256', $_SERVER['PHP_AUTH_PW']);
                    if (!$bIsSet || !$bIsEqual) {
                        $this->passwordProtectedRequest();
                    }

                } else {

                    $this->passwordProtectedRequest();
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Requests staging credentials
     *
     * @return void
     */
    protected function passwordProtectedRequest()
    {
        $oInput = Factory::service('Input');
        header('WWW-Authenticate: Basic realm="' . APP_NAME . ' - Restricted Area"');
        header($oInput->server('SERVER_PROTOCOL') . ' 401 Unauthorized');
        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->renderErrorView(401);
        exit(401);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up date & time handling
     *
     * @throws NailsException
     */
    protected function instantiateDateTime()
    {
        $oDateTimeModel = Factory::model('DateTime');

        //  Define default date format
        $oDefaultDateFormat = $oDateTimeModel->getDateFormatDefault();

        if (empty($oDefaultDateFormat)) {
            throw new NailsException(
                'No default date format has been set, or it\'s been set incorrectly.',
                1
            );
        }

        Functions::define('APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG', $oDefaultDateFormat->slug);
        Functions::define('APP_DEFAULT_DATETIME_FORMAT_DATE_LABEL', $oDefaultDateFormat->label);
        Functions::define('APP_DEFAULT_DATETIME_FORMAT_DATE_FORMAT', $oDefaultDateFormat->format);

        //  Define default time format
        $oDefaultTimeFormat = $oDateTimeModel->getTimeFormatDefault();

        if (empty($oDefaultTimeFormat)) {
            throw new NailsException(
                'No default time format has been set, or it\'s been set incorrectly.',
                1
            );
        }

        Functions::define('APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG', $oDefaultTimeFormat->slug);
        Functions::define('APP_DEFAULT_DATETIME_FORMAT_TIME_LABEL', $oDefaultTimeFormat->label);
        Functions::define('APP_DEFAULT_DATETIME_FORMAT_TIME_FORMAT', $oDefaultTimeFormat->format);

        // --------------------------------------------------------------------------

        /**
         * Set the timezones.
         * Choose the user's timezone - starting with their preference, followed by
         * the app's default.
         */

        if (activeUser('timezone')) {
            $sTimezoneUser = activeUser('timezone');
        } else {
            $sTimezoneUser = $oDateTimeModel->getTimezoneDefault();
        }

        $oDateTimeModel->setTimezones('UTC', $sTimezoneUser);

        // --------------------------------------------------------------------------

        //  Set the user date/time formats
        $sFormatDate = activeUser('datetime_format_date');
        $sFormatDate = $sFormatDate ? $sFormatDate : APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG;

        $sFormatTime = activeUser('datetime_format_time');
        $sFormatTime = $sFormatTime ? $sFormatTime : APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG;

        $oDateTimeModel->setFormats($sFormatDate, $sFormatTime);

        // --------------------------------------------------------------------------

        //  Make sure the database is running on UTC
        $oDb = Factory::service('Database');
        $oDb->query('SET time_zone = \'+0:00\'');
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if routes need to be generated as part of the startup request
     *
     * @throws NailsException
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function generateRoutes()
    {
        if (defined('NAILS_STARTUP_GENERATE_APP_ROUTES') && NAILS_STARTUP_GENERATE_APP_ROUTES) {
            $oRoutesService = Factory::service('Routes');
            if (!$oRoutesService->update()) {
                throw new NailsException('Failed to generate routes_app.php. ' . $oRoutesService->lastError(), 500);
            } else {
                $oInput = Factory::service('Input');
                redirect($oInput->server('REQUEST_URI'), 'auto', 307);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up language handling
     *
     * @return void
     */
    protected function instantiateLanguages()
    {
        //  Define default language
        $oLanguageModel = Factory::model('Language');
        $oDefault       = $oLanguageModel->getDefault();

        if (empty($oDefault)) {
            showFatalError('No default language has been set, or it\'s been set incorrectly.');
        }

        Functions::define('APP_DEFAULT_LANG_CODE', $oDefault->code);
        Functions::define('APP_DEFAULT_LANG_LABEL', $oDefault->label);

        // --------------------------------------------------------------------------

        /**
         * Set any global preferences for this user, e.g languages, fall back to the
         * app's default language (defined in config.php).
         */

        $sUserLangCode = activeUser('language');

        if (!empty($sUserLangCode)) {
            Functions::define('RENDER_LANG_CODE', $sUserLangCode);
        } else {
            Functions::define('RENDER_LANG_CODE', APP_DEFAULT_LANG_CODE);
        }

        //  Set the language config item which CodeIgniter will use.
        $oConfig = Factory::service('Config');
        $oConfig->set_item('language', RENDER_LANG_CODE);

        //  Load the Nails. generic lang file
        $this->lang->load('nails');
    }

    // --------------------------------------------------------------------------

    /**
     * Defines all the package paths
     */
    protected function definePackages()
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
        $oConfig                = Factory::service('Config');
        $oConfig->_config_paths = [];

        $aPaths = [];

        //  Nails Common
        $aPaths[] = NAILS_COMMON_PATH;

        //  Available Modules
        $aAvailableModules = Components::modules();

        foreach ($aAvailableModules as $oModule) {
            $aPaths[] = $oModule->path;
        }

        //  The Application
        $aPaths[] = APPPATH;

        foreach ($aPaths as $sPath) {
            $this->load->add_package_path($sPath);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set up the active user
     *
     * @return void
     */
    protected function instantiateUser()
    {
        /**
         * Find a remembered user and initialise the user model; this routine checks
         * the user's cookies and set's up the session for an existing or new user.
         */

        $oUserModel = Factory::model('User', 'nails/module-auth');
        $oUserModel->init();
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether the active user is suspended and, if so, logs them out.
     *
     * @return void
     */
    protected function isUserSuspended()
    {
        //  Check if this user is suspended
        if (isLoggedIn() && activeUser('is_suspended')) {

            //  Load models and langs
            $oAuthModel = Factory::model('Auth', 'nails/module-auth');
            $this->lang->load('auth/auth');

            //  Log the user out
            $oAuthModel->logout();

            //  Create a new session
            $oSession = Factory::service('Session', 'nails/module-auth');
            $oSession->sess_create();

            //  Give them feedback
            $oSession->setFlashData('error', lang('auth_login_fail_suspended'));
            redirect('/');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Provides some backwards compatability
     *
     * @param \stdClass $oBindTo The class to bind to
     */
    public static function backwardsCompatibility(&$oBindTo)
    {
        /**
         * Backwards compatibility
         * Various older modules expect to be able to access a few services/models
         * via magic methods. These will be deprecated soon.
         */

        //  @todo (Pablo - 2017-06-07) - Remove these
        $oBindTo->db                  = Factory::service('Database');
        $oBindTo->input               = Factory::service('Input');
        $oBindTo->output              = Factory::service('Output');
        $oBindTo->meta                = Factory::service('Meta');
        $oBindTo->asset               = Factory::service('Asset');
        $oBindTo->encrypt             = Factory::service('Encrypt');
        $oBindTo->logger              = Factory::service('Logger');
        $oBindTo->uri                 = Factory::service('Uri');
        $oBindTo->security            = Factory::service('Security');
        $oBindTo->emailer             = Factory::service('Emailer', 'nails/module-email');
        $oBindTo->event               = Factory::service('Event', 'nails/module-event');
        $oBindTo->session             = Factory::service('Session', 'nails/module-auth');
        $oBindTo->user                = Factory::model('User', 'nails/module-auth');
        $oBindTo->user_group_model    = Factory::model('UserGroup', 'nails/module-auth');
        $oBindTo->user_password_model = Factory::model('UserPassword', 'nails/module-auth');

        //  Set variables for the views, too
        $oBindTo->data['user']          = $oBindTo->user;
        $oBindTo->data['user_group']    = $oBindTo->user_group_model;
        $oBindTo->data['user_password'] = $oBindTo->user_password_model;
    }

    // --------------------------------------------------------------------------

    /**
     * Populates an array from the UserFeedback and session classes
     *
     * @param array $aData The array to populate
     */
    public static function populateUserFeedback(array &$aData)
    {
        //  Set User Feedback alerts for the views
        $oSession          = Factory::service('Session', 'nails/module-auth');
        $oUserFeedback     = Factory::service('UserFeedback');
        $aData['error']    = $oUserFeedback->get('error') ?: $oSession->getFlashData('error');
        $aData['negative'] = $oUserFeedback->get('negative') ?: $oSession->getFlashData('negative');
        $aData['success']  = $oUserFeedback->get('success') ?: $oSession->getFlashData('success');
        $aData['positive'] = $oUserFeedback->get('positive') ?: $oSession->getFlashData('positive');
        $aData['info']     = $oUserFeedback->get('info') ?: $oSession->getFlashData('info');
        $aData['warning']  = $oUserFeedback->get('message') ?: $oSession->getFlashData('warning');

        //  @deprecated
        $aData['message'] = $oUserFeedback->get('message') ?: $oSession->getFlashData('message');
        $aData['notice']  = $oUserFeedback->get('notice') ?: $oSession->getFlashData('notice');
    }

    // --------------------------------------------------------------------------

    /**
     * Populates an array with a default object for page and SEO
     *
     * @param array $aData The array to populate
     */
    public static function populatePageData(array &$aData)
    {
        $aData['page'] = (object) [
            'title' => '',
            'seo'   => (object) [
                'title'       => '',
                'description' => '',
                'keywords'    => '',
            ],
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Sets some common meta for every page load
     *
     * @param array $aMeta Any additional meta items to set
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function setCommonMeta(array $aMeta = [])
    {
        $oMeta = Factory::service('Meta');
        $oMeta
            ->addRaw([
                'charset' => 'utf-8',
            ])
            ->addRaw([
                'name'    => 'viewport',
                'content' => 'width=device-width, initial-scale=1',
            ]);

        foreach ($aMeta as $aItem) {
            $oMeta->addRaw($aItem);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a global Nails JS object
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    public static function setNailsJs()
    {
        $oAsset     = Factory::service('Asset');
        $aVariables = [
            'ENVIRONMENT' => Environment::get(),
            'SITE_URL'    => site_url('', Functions::isPageSecure()),
            'NAILS'       => (object) [
                'URL'  => NAILS_ASSETS_URL,
                'LANG' => (object) [],
                'USER' => (object) [
                    'ID'    => activeUser('id') ? activeUser('id') : null,
                    'FNAME' => activeUser('first_name'),
                    'LNAME' => activeUser('last_name'),
                    'EMAIL' => activeUser('email'),
                ],
            ],
        ];
        foreach ($aVariables as $sKey => $mValue) {
            $oAsset->inline('window.' . $sKey . ' = ' . json_encode($mValue) . ';', 'JS', 'HEADER');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets global JS
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function setGlobalJs()
    {
        $oAsset = Factory::service('Asset');

        // --------------------------------------------------------------------------

        $sCustomJs = appSetting('site_custom_js', 'site');
        if (!empty($sCustomJs)) {
            $oAsset->inline($sCustomJs, 'JS');
        }

        // --------------------------------------------------------------------------

        /**
         * If a Google Analytics profile has been specified then include that too
         */
        $sGoogleAnalyticsProfile = appSetting('google_analytics_account');
        if (!empty($sGoogleAnalyticsProfile)) {
            $oAsset->inline(
                "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
                ga('create', '" . appSetting('google_analytics_account') . "', 'auto');
                ga('send', 'pageview');",
                'JS'
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets global CSS
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function setGlobalCss()
    {
        $sCustomCss = appSetting('site_custom_css', 'site');
        if (!empty($sCustomCss)) {
            $oAsset->inline($sCustomCss, 'CSS');
        }
    }
}
