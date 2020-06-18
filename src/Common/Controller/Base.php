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
use Nails\Common\Service\Asset;
use Nails\Common\Service\DateTime;
use Nails\Common\Service\ErrorHandler;
use Nails\Common\Service\Event;
use Nails\Common\Service\Input;
use Nails\Common\Service\Language;
use Nails\Common\Service\Meta;
use Nails\Common\Service\MetaData;
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
        Factory::service('Event')
            ->trigger(Events::SYSTEM_STARTING);

        parent::__construct();

        // --------------------------------------------------------------------------

        //  Populate some standard fields
        $this->oLocale   = Factory::service('Locale');
        $this->oMetaData = Factory::service('MetaData');
        $this->oMetaData->setLocale($this->oLocale->get());

        //  @todo (Pablo - 2020-02-24) - Remove this/backwards compatibility
        $this->data              =& getControllerData();
        $this->data['oMetaData'] = $this->oMetaData;
        $this->data['page']      = $this->oMetaData;

        // --------------------------------------------------------------------------

        $this
            ->maintenanceMode()
            ->setErrorReporting()
            ->setContentType()
            ->definePackages()
            ->passwordProtected()
            ->initiateDatabase()
            ->instantiateLanguages()
            ->instantiateDateTime()
            ->generateRoutes()
            ->setGlobalJs()
            ->setGlobalCss();

        // --------------------------------------------------------------------------

        static::populateUserFeedback($this->data);

        // --------------------------------------------------------------------------

        /**
         * Forced maintenance mode?
         */
        if (appSetting('maintenance_mode_enabled', 'site')) {
            $this->maintenanceMode(true);
        }

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
     * @return $this
     */
    protected function setErrorReporting(): self
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

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the content type to use for the request to UTF-8
     *
     * @return $this
     * @throws FactoryException
     */
    protected function setContentType(): self
    {
        /** @var Output $oOutput */
        $oOutput = Factory::service('Output');
        $oOutput->set_content_type('text/html; charset=utf-8');

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Maintenance Mode is enabled, shows the holding page if so.
     *
     * @param bool   $bForce Force maintenance mode on
     * @param string $sTitle Override the page title
     * @param string $sBody  Override the page body
     *
     * @return $this
     * @throws FactoryException
     */
    protected function maintenanceMode(bool $bForce = false, string $sTitle = '', string $sBody = ''): self
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

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if credentials should be requested for staging environments
     *
     * @link https://docs.nailsapp.co.uk/key-concepts/environments#protecting-environments
     * @return $this
     * @throws FactoryException
     */
    protected function passwordProtected(): self
    {

        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        $aCredentials = $this->passwordProtectedIpCredentials();

        if (!$oInput::isCli() && !empty($aCredentials)) {

            $aWhitelistedIps = $this->passwordProtectedIpWhitelist();
            $bWhitelisted    = !empty($aWhitelistedIps) && isIpInRange($oInput->ipAddress(), $aWhitelistedIps);

            if (!$bWhitelisted) {

                $sAuthUser = $oInput->server('PHP_AUTH_USER') ?: $oInput->header('X-Auth-User');
                $sAuthPass = $oInput->server('PHP_AUTH_PW') ?: $oInput->header('X-Auth-Password');

                if (empty($sAuthUser)) {
                    $this->passwordProtectedRequest();
                }

                if (!empty($sAuthUser) && !empty($sAuthPass)) {

                    $bExists  = array_key_exists($sAuthUser, $aCredentials);
                    $bIsEqual = $bExists && $aCredentials[$sAuthUser] == hash('sha256', $sAuthPass);
                    if (!$bExists || !$bIsEqual) {
                        $this->passwordProtectedRequest();
                    }

                } else {
                    $this->passwordProtectedRequest();
                }
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of key/value pairs for password protection
     *
     * @return array
     */
    protected function passwordProtectedIpCredentials(): array
    {
        $mConfig = Config::get('APP_USER_PASS_' . Environment::get());
        $sFile   = 'protect.' . strtolower(Environment::get()) . '.users.json';
        if ($mConfig === null && file_exists($sFile)) {
            $mConfig = @json_decode(file_get_contents($sFile)) ?? [];
        }

        return (array) $mConfig;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of whitelisted IPs for password protection
     *
     * @return array
     */
    protected function passwordProtectedIpWhitelist(): array
    {
        $mConfig = Config::get('APP_USER_PASS_WHITELIST_' . Environment::get());
        $sFile   = 'protect.' . strtolower(Environment::get()) . '.whitelist.json';
        if ($mConfig === null && file_exists($sFile)) {
            $mConfig = @json_decode(file_get_contents($sFile)) ?? [];
        }

        return (array) $mConfig;
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
        /** @var ErrorHandler $oErrorHandler */
        $oErrorHandler = Factory::service('ErrorHandler');

        //  Send headers immediately
        header('WWW-Authenticate: Basic realm="' . Config::get('APP_NAME') . ' - Restricted Area"');
        header($oInput->server('SERVER_PROTOCOL') . ' 401 Unauthorized');

        $oErrorHandler->show401(null, null, true, true);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up date & time handling
     *
     * @return $this
     * @throws NailsException
     */
    protected function instantiateDateTime(): self
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

        Config::default('APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG', $oDefaultDateFormat->slug);
        Config::default('APP_DEFAULT_DATETIME_FORMAT_DATE_LABEL', $oDefaultDateFormat->label);
        Config::default('APP_DEFAULT_DATETIME_FORMAT_DATE_FORMAT', $oDefaultDateFormat->format);

        //  Define default time format
        $oDefaultTimeFormat = $oDateTimeService->getTimeFormatDefault();

        if (empty($oDefaultTimeFormat)) {
            throw new NailsException(
                'No default time format has been set, or it\'s been set incorrectly.'
            );
        }

        Config::default('APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG', $oDefaultTimeFormat->slug);
        Config::default('APP_DEFAULT_DATETIME_FORMAT_TIME_LABEL', $oDefaultTimeFormat->label);
        Config::default('APP_DEFAULT_DATETIME_FORMAT_TIME_FORMAT', $oDefaultTimeFormat->format);

        // --------------------------------------------------------------------------

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if routes need to be generated as part of the startup request
     *
     * @return $this
     * @throws NailsException
     */
    protected function generateRoutes(): self
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

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets up language handling
     *
     * @return $this
     * @throws FactoryException
     * @throws NailsException
     */
    protected function instantiateLanguages(): self
    {
        /** @var Language $oLanguageService */
        $oLanguageService = Factory::service('Language');
        $oDefault         = $oLanguageService->getDefault();

        if (empty($oDefault)) {
            throw new NailsException('No default language has been set, or it\'s been set incorrectly.');
        }

        Config::default('APP_DEFAULT_LANG_CODE', $oDefault->code);
        Config::default('APP_DEFAULT_LANG_LABEL', $oDefault->label);

        // --------------------------------------------------------------------------

        //  Set the language config item which CodeIgniter will use.
        //  @todo (Pablo - 2020-06-18) - Move away from this ASAP
        /** @var \Nails\Common\Service\Config $oConfig */
        $oConfig = Factory::service('Config');
        $oConfig->set_item(
            'language',
            function_exists('activeUser') && activeUser('language')
                ? activeUser('language')
                : Config::get('APP_DEFAULT_LANG_CODE')
        );

        //  Load the Nails. generic lang file
        get_instance()->lang->load('nails');

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Defines all the package paths
     *
     * @return $this
     */
    protected function definePackages(): self
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

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Initiate the database connection; needs done early so all services properly connect
     *
     * @return $this
     * @throws FactoryException
     */
    protected function initiateDatabase(): self
    {
        Factory::service('Database');
        return $this;
    }


    // --------------------------------------------------------------------------

    /**
     * Populates an array from the UserFeedback and session classes
     *
     * @param array $aData The array to populate
     *
     * @return void
     * @throws FactoryException
     */
    public static function populateUserFeedback(array &$aData): void
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
     * Sets global JS
     *
     * @return $this
     * @throws FactoryException
     * @throws NailsException
     * @throws AssetException
     */
    protected function setGlobalJs(): self
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
        $sGoogleAnalyticsProfile = appSetting('google_analytics_account', 'site');
        if (!empty($sGoogleAnalyticsProfile)) {
            $oAsset->load(
                'https://www.googletagmanager.com/gtag/js?id=' . $sGoogleAnalyticsProfile,
                null,
                'JS',
                true
            );
            $oAsset->inline('
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag(\'js\', new Date());
                gtag(\'config\', \'' . $sGoogleAnalyticsProfile . '\');
            ', 'JS');
        }

        // --------------------------------------------------------------------------

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets global CSS
     *
     * @return $this
     * @throws AssetException
     * @throws FactoryException
     * @throws NailsException
     */
    protected function setGlobalCss(): self
    {
        /** @var Asset $oAsset */
        $oAsset     = Factory::service('Asset');
        $sCustomCss = appSetting('site_custom_css', 'site');
        if (!empty($sCustomCss)) {
            $oAsset->inline($sCustomCss, 'CSS');
        }

        return $this;
    }
}
