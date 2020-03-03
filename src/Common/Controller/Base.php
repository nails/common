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

use Nails\Auth;
use Nails\Common\Events;
use Nails\Common\Exception\AssetException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Factory\Locale;
use Nails\Common\Resource\MetaData;
use Nails\Common\Service\Asset;
use Nails\Common\Service\DateTime;
use Nails\Common\Service\ErrorHandler;
use Nails\Common\Service\Event;
use Nails\Common\Service\Input;
use Nails\Common\Service\Language;
use Nails\Common\Service\Meta;
use Nails\Common\Service\Output;
use Nails\Common\Service\Profiler;
use Nails\Common\Service\Session;
use Nails\Common\Service\Uri;
use Nails\Common\Service\UserFeedback;
use Nails\Components;
use Nails\Config;
use Nails\Environment;
use Nails\Factory;
use Nails\Functions;

/**
 * Class Base
 *
 * @package Nails\Common\Controller
 */
abstract class Base extends \MX_Controller
{
    /**
     * The page's locale
     *
     * @var Locale
     */
    protected $oLocale;

    /**
     * The page's meta data
     *
     * @var MetaData
     */
    protected $oMetaData;

    /**
     * Items passed to here will be automatically passed to
     * the View when rendered. Deprecated: pass explicitly using the View service's
     * setData method.
     *
     * @var array
     * @deprecated
     */
    protected $data;

    // --------------------------------------------------------------------------

    /**
     * Build the main framework. All auto-loaded items have been loaded and
     * instantiated by this point and are safe to use.
     *
     * @throws FactoryException
     * @throws NailsException
     */
    public function __construct()
    {
        Profiler::mark('CONTROLLER:PRE');
        parent::__construct();

        // --------------------------------------------------------------------------

        $this->maintenanceMode();
        $this->setErrorReporting();
        $this->setContentType();
        $this->definePackages();
        $this->passwordProtected();
        $this->initiateDatabase();
        $this->instantiateUser();
        $this->instantiateLanguages();
        $this->isUserSuspended();
        $this->instantiateDateTime();
        $this->generateRoutes();

        // --------------------------------------------------------------------------

        //  Populate some standard fields
        $this->oLocale   = Factory::service('Locale');
        $this->oMetaData = Factory::resource('MetaData');
        $this->oMetaData->setLocale($this->oLocale->get());

        //  @todo (Pablo - 2020-02-24) - Remove this/backwards compatibility
        $this->data              =& getControllerData();
        $this->data['oMetaData'] = $this->oMetaData;
        $this->data['page']      = $this->oMetaData;

        static::populateUserFeedback($this->data);

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

        //  Call the SYSTEM:READY event, the system is all geared up and ready to go
        Profiler::mark(Events::SYSTEM_READY);
        Factory::service('Event')
            ->trigger(Events::SYSTEM_READY);
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
         * we'll let the errorHandler pick up fatal errors
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
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the content type to use for the request to UTF-8
     *
     * @throws FactoryException
     */
    protected function setContentType()
    {
        /** @var Output $oOutput */
        $oOutput = Factory::service('Output');
        $oOutput->set_content_type('text/html; charset=utf-8');
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Maintenance Mode is enabled, shows the holding page if so.
     *
     * @param bool   $bForce Force maintenance mode on
     * @param string $sTitle Override the page title
     * @param string $sBody  Override the page body
     *
     * @return void
     * @throws FactoryException
     */
    protected function maintenanceMode(bool $bForce = false, string $sTitle = '', string $sBody = '')
    {
        if ($bForce || file_exists(Config::get('NAILS_APP_PATH') . '.MAINTENANCE')) {

            Config::set('NAILS_MAINTENANCE', true);

            /**
             * We're in maintenance mode. This can happen very early so we need to
             * make sure that we've loaded everything we need to load as we're
             * exiting whether we like it or not
             */

            /** @var Input $oInput */
            $oInput = Factory::service('Input');
            /** @var Uri $oUri */
            $oUri = Factory::service('Uri');
            /** @var Output $oOutput */
            $oOutput = Factory::service('Output');

            try {

                //  Load the encryption service. Set the package path so it is loaded correctly
                //  (this runs early, before the paths are added)
                get_instance()->load->add_package_path(Config::get('NAILS_COMMON_PATH'));
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

                    $oOutput->set_header($oInput->server('SERVER_PROTOCOL') . ' 503 Service Temporarily Unavailable');
                    $oOutput->set_header('Status: 503 Service Temporarily Unavailable');
                    $oOutput->set_header('Retry-After: 7200');

                    // --------------------------------------------------------------------------

                    //  If the request is an AJAX request, or the URL is on the API then spit back JSON
                    if ($oInput::isAjax() || $oUri->segment(1) == 'api') {

                        $oOutput->set_header('Cache-Control: no-store, no-cache, must-revalidate');
                        $oOutput->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                        $oOutput->set_header('Content-Type: application/json');
                        $oOutput->set_header('Pragma: no-cache');

                        echo $oOutput->_display(json_encode([
                            'status' => 503,
                            'error'  => $sMaintenanceTitle,
                        ]));

                    } else {
                        //  Otherwise, render some HTML
                        if (file_exists(Config::get('NAILS_APP_PATH') . 'application/views/errors/html/maintenance.php')) {
                            //  Look for an app override
                            require Config::get('NAILS_APP_PATH') . 'application/views/errors/html/maintenance.php';
                        } else {
                            //  Fall back to the Nails maintenance page
                            require Config::get('NAILS_COMMON_PATH') . 'views/errors/html/maintenance.php';
                        }
                    }

                } else {
                    if (file_exists(Config::get('NAILS_APP_PATH') . 'application/views/errors/cli/maintenance.php')) {
                        //  Look for an app override
                        require Config::get('NAILS_APP_PATH') . 'application/views/errors/cli/maintenance.php';
                    } else {
                        //  Fall back to the Nails maintenance page
                        require Config::get('NAILS_COMMON_PATH') . 'views/errors/cli/maintenance.php';
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
     * @link https://docs.nailsapp.co.uk/key-concepts/environments#protecting-environments
     * @return void
     * @throws FactoryException
     */
    protected function passwordProtected(): void
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        $sConfigKey          = 'APP_USER_PASS_' . Environment::get();
        $sConfigKeyWhitelist = 'APP_USER_PASS_WHITELIST_' . Environment::get();

        if (!$oInput::isCli() && Config::get($sConfigKey)) {

            //  On the whitelist?
            if (Config::get($sConfigKeyWhitelist)) {

                $aWhitelistedIps = @json_decode(constant($sConfigKeyWhitelist));
                $bWhitelisted    = isIpInRange($oInput->ipAddress(), $aWhitelistedIps);

            } else {
                $bWhitelisted = false;
            }

            if (!$bWhitelisted) {

                $oCredentials = @json_decode(constant($sConfigKey));

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
     * @throws FactoryException
     */
    protected function passwordProtectedRequest(): void
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Output $oOutput */
        $oOutput = Factory::service('Output');
        /** @var ErrorHandler $oErrorHandler */
        $oErrorHandler = Factory::service('ErrorHandler');

        $oOutput->set_header('WWW-Authenticate: Basic realm="' . Config::get('APP_NAME') . ' - Restricted Area"');
        $oOutput->set_header($oInput->server('SERVER_PROTOCOL') . ' 401 Unauthorized');

        $oErrorHandler->show401(null, null, true, true);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up date & time handling
     *
     * @throws NailsException
     */
    protected function instantiateDateTime()
    {
        /** @var DateTime $oDateTimeService */
        $oDateTimeService = Factory::service('DateTime');

        //  Define default date format
        $oDefaultDateFormat = $oDateTimeService->getDateFormatDefault();

        if (empty($oDefaultDateFormat)) {
            throw new NailsException(
                'No default date format has been set, or it\'s been set incorrectly.'
            );
        }

        Config::set('APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG', $oDefaultDateFormat->slug);
        Config::set('APP_DEFAULT_DATETIME_FORMAT_DATE_LABEL', $oDefaultDateFormat->label);
        Config::set('APP_DEFAULT_DATETIME_FORMAT_DATE_FORMAT', $oDefaultDateFormat->format);

        //  Define default time format
        $oDefaultTimeFormat = $oDateTimeService->getTimeFormatDefault();

        if (empty($oDefaultTimeFormat)) {
            throw new NailsException(
                'No default time format has been set, or it\'s been set incorrectly.'
            );
        }

        Config::set('APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG', $oDefaultTimeFormat->slug);
        Config::set('APP_DEFAULT_DATETIME_FORMAT_TIME_LABEL', $oDefaultTimeFormat->label);
        Config::set('APP_DEFAULT_DATETIME_FORMAT_TIME_FORMAT', $oDefaultTimeFormat->format);

        // --------------------------------------------------------------------------

        /**
         * Set the timezones.
         * Choose the user's timezone - starting with their preference, followed by
         * the app's default.
         */

        if (activeUser('timezone')) {
            $sTimezoneUser = activeUser('timezone');
        } else {
            $sTimezoneUser = $oDateTimeService->getTimezoneDefault();
        }

        $oDateTimeService->setTimezones('UTC', $sTimezoneUser);

        // --------------------------------------------------------------------------

        //  Set the user date/time formats
        $sFormatDate = activeUser('datetime_format_date');
        $sFormatDate = $sFormatDate ? $sFormatDate : Config::get('APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG');

        $sFormatTime = activeUser('datetime_format_time');
        $sFormatTime = $sFormatTime ? $sFormatTime : Config::get('APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG');

        $oDateTimeService->setUserFormats($sFormatDate, $sFormatTime);
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if routes need to be generated as part of the startup request
     *
     * @throws NailsException
     */
    protected function generateRoutes()
    {
        if (Config::get('NAILS_STARTUP_GENERATE_APP_ROUTES')) {
            try {

                /** @var Event $oEventService */
                $oEventService = Factory::service('Event');
                $oEventService->trigger(Events::ROUTES_UPDATE);

                /** @var Input $oInput */
                $oInput = Factory::service('Input');
                redirect($oInput->server('REQUEST_URI'), 'auto', 307);

            } catch (NailsException $e) {
                throw new NailsException(
                    'Failed to generate routes file. ' . $e->getMessage(),
                    500
                );
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up language handling
     *
     * @return void
     * @throws FactoryException
     * @throws NailsException
     */
    protected function instantiateLanguages()
    {
        /** @var Language $oLanguageService */
        $oLanguageService = Factory::service('Language');
        $oDefault         = $oLanguageService->getDefault();

        if (empty($oDefault)) {
            throw new NailsException('No default language has been set, or it\'s been set incorrectly.');
        }

        Config::set('APP_DEFAULT_LANG_CODE', $oDefault->code);
        Config::set('APP_DEFAULT_LANG_LABEL', $oDefault->label);

        // --------------------------------------------------------------------------

        /**
         * Set any global preferences for this user, e.g languages, fall back to the
         * app's default language (defined in config.php).
         */

        $sUserLangCode = activeUser('language');

        if (!empty($sUserLangCode)) {
            Config::set('RENDER_LANG_CODE', $sUserLangCode);
        } else {
            Config::set('RENDER_LANG_CODE', Config::get('APP_DEFAULT_LANG_CODE'));
        }

        //  Set the language config item which CodeIgniter will use.
        /** @var \Nails\Common\Service\Config $oConfig */
        $oConfig = Factory::service('Config');
        $oConfig->set_item('language', Config::get('RENDER_LANG_CODE'));

        //  Load the Nails. generic lang file
        get_instance()->lang->load('nails');
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
        /** @var \Nails\Common\Service\Config $oConfig */
        $oConfig = Factory::service('Config');

        $oConfig->_config_paths = [];

        $aPaths = [];

        //  Nails Common
        $aPaths[] = Config::get('NAILS_COMMON_PATH');

        //  Available Modules
        $aAvailableModules = Components::modules();

        foreach ($aAvailableModules as $oModule) {
            $aPaths[] = $oModule->path;
        }

        //  The Application
        $aPaths[] = Config::get('NAILS_APP_PATH') . 'application';

        foreach ($aPaths as $sPath) {
            get_instance()->load->add_package_path($sPath);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Initiate the database connection; needs done early so all services properly connect
     *
     * @throws FactoryException
     */
    protected function initiateDatabase()
    {
        Factory::service('Database');
    }

    // --------------------------------------------------------------------------

    /**
     * Set up the active user
     *
     * @return void
     * @throws FactoryException
     * @throws ModelException
     */
    protected function instantiateUser()
    {
        /**
         * Find a remembered user and initialise the user model; this routine checks
         * the user's cookies and set's up the session for an existing or new user.
         */

        /** @var Auth\Model\User $oUserModel */
        $oUserModel = Factory::model('User', Auth\Constants::MODULE_SLUG);
        $oUserModel->init();
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether the active user is suspended and, if so, logs them out.
     *
     * @return void
     * @throws FactoryException
     */
    protected function isUserSuspended()
    {
        //  Check if this user is suspended
        if (isLoggedIn() && activeUser('is_suspended')) {

            /** @var Auth\Service\Authentication $oAuthService */
            $oAuthService = Factory::service('Authentication', Auth\Constants::MODULE_SLUG);
            get_instance()->lang->load('auth/auth');

            $oAuthService->logout();

            /** @var Session $oSession */
            $oSession = Factory::service('Session');
            $oSession->setFlashData('error', lang('auth_login_fail_suspended'));
            redirect('/');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Populates an array from the UserFeedback and session classes
     *
     * @param array $aData The array to populate
     *
     * @throws FactoryException
     */
    public static function populateUserFeedback(array &$aData)
    {
        //  Set User Feedback alerts for the views
        /** @var Session $oSession */
        $oSession = Factory::service('Session');
        /** @var UserFeedback $oUserFeedback */
        $oUserFeedback = Factory::service('UserFeedback');

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
     * Sets some common meta for every page load
     *
     * @param array $aMeta Any additional meta items to set
     *
     * @throws FactoryException
     */
    protected function setCommonMeta(array $aMeta = [])
    {
        /** @var Meta $oMeta */
        $oMeta = Factory::service('Meta');

        $sAppName     = $this->oMetaData->getTitles()->implode();
        $sDescription = $this->oMetaData->getDescription();

        // --------------------------------------------------------------------------

        //  Generic meta
        $oMeta
            ->addRaw([
                'charset' => 'utf-8',
            ])
            ->addRaw([
                'name'    => 'viewport',
                'content' => 'width=device-width, initial-scale=1',
            ]);

        // --------------------------------------------------------------------------

        // Open graph meta
        $oMeta
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:title',
                'content'  => $sAppName,
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:type',
                'content'  => 'website',
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:locale',
                'content'  => (string) $this->oMetaData->getLocale(),
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:site_name',
                'content'  => $sAppName,
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:url',
                'content'  => siteUrl(),
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:description',
                'content'  => $sDescription,
            ]);

        // --------------------------------------------------------------------------

        // Twitter card config
        $oMeta
            ->addRaw([
                'name'    => 'twitter:title',
                'content' => $sAppName,
            ])
            ->addRaw([
                'name'    => 'twitter:description',
                'content' => $sDescription,
            ])
            ->addRaw([
                'name'    => 'twitter:card',
                'content' => 'summary_large_image',
            ]);

        // --------------------------------------------------------------------------

        //  Other meta tags
        $oMeta
            ->add('apple-mobile-web-app-title', $sAppName)
            ->add('application-name', $sAppName)
            ->add('description', $sDescription);

        // --------------------------------------------------------------------------

        //  Any passed meta
        foreach ($aMeta as $aItem) {
            $oMeta->addRaw($aItem);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a global Nails JS object
     *
     * @throws FactoryException
     */
    public static function setNailsJs()
    {
        $oAsset     = Factory::service('Asset');
        $aVariables = [
            'ENVIRONMENT' => Environment::get(),
            'SITE_URL'    => siteUrl('', Functions::isPageSecure()),
            'NAILS'       => (object) [
                'URL'  => Config::get('NAILS_ASSETS_URL'),
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
     * @throws FactoryException
     * @throws NailsException
     * @throws AssetException
     */
    protected function setGlobalJs()
    {
        /** @var Asset $oAsset */
        $oAsset    = Factory::service('Asset');
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
     * @throws AssetException
     * @throws FactoryException
     * @throws NailsException
     */
    protected function setGlobalCss()
    {
        /** @var Asset $oAsset */
        $oAsset     = Factory::service('Asset');
        $sCustomCss = appSetting('site_custom_css', 'site');
        if (!empty($sCustomCss)) {
            $oAsset->inline($sCustomCss, 'CSS');
        }
    }
}
