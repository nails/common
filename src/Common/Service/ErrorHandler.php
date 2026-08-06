<?php

/**
 * This is the main error handler for Nails. It sets assigns the appropriate handler for different types of errors
 *
 * @package     Nails
 * @subpackage  common
 * @category    errors
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Controller\NailsMockController;
use Nails\Common\Events;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ViewNotFoundException;
use Nails\Common\Helper\ArrayHelper;
use Nails\Components;
use Nails\Config;
use Nails\Factory;
use ReflectionException;
use stdClass;

/**
 * Class ErrorHandler
 *
 * @package Nails\Common\Service
 */
class ErrorHandler
{
    /**
     * Human versions of the various PHP error levels
     */
    const LEVELS = [
        E_ERROR           => 'Error',
        E_WARNING         => 'Warning',
        E_PARSE           => 'Parsing Error',
        E_NOTICE          => 'Notice',
        E_CORE_ERROR      => 'Core Error',
        E_CORE_WARNING    => 'Core Warning',
        E_COMPILE_ERROR   => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR      => 'User Error',
        E_USER_WARNING    => 'User Warning',
        E_USER_NOTICE     => 'User Notice',
    ];

    /*
     * The name of the default error handler
     * @var string
     */
    const DEFAULT_ERROR_HANDLER = 'nails/driver-error-handler-default';

    /**
     * The name of the interface which drivers must implement
     */
    const INTERFACE_NAME = 'Nails\\Common\\Interfaces\\ErrorHandlerDriver';

    // --------------------------------------------------------------------------

    /**
     * Whether the handler has initiated itself
     *
     * @var bool
     */
    protected static $bIsReady = false;

    /**
     * The fully qualified driver class name
     *
     * @var string
     */
    protected static $sDriverClass;

    /**
     * The configuration for the default driver
     *
     * @var stdClass
     */
    protected static $oDefaultDriver;

    // --------------------------------------------------------------------------

    /**
     * Sets up the appropriate error handling driver
     */
    public static function init()
    {
        if (static::$bIsReady) {
            return;
        }

        $aErrorHandlers = Components::drivers('nails/common', 'ErrorHandler');
        $oDefaultDriver = null;
        $aCustomDrivers = [];
        foreach ($aErrorHandlers as $oErrorHandler) {
            if ($oErrorHandler->slug == static::DEFAULT_ERROR_HANDLER) {
                $oDefaultDriver = $oErrorHandler;
            } else {
                $aCustomDrivers[] = $oErrorHandler;
            }
        }

        if (count($aCustomDrivers) > 1) {
            $aNames = [];
            foreach ($aCustomDrivers as $oErrorHandler) {
                $aNames[] = $oErrorHandler->slug;
            }
            static::halt(implode(', ', $aNames), 'More than one error handler installed');
            return;
        } elseif (count($aCustomDrivers) === 1) {
            $oErrorHandler = reset($aCustomDrivers);
        } else {
            $oErrorHandler = $oDefaultDriver;
        }

        $sDriverNamespace = ArrayHelper::get('namespace', (array) $oErrorHandler->data);
        $sDriverClass     = ArrayHelper::get('class', (array) $oErrorHandler->data);
        $sClassName       = $sDriverNamespace . $sDriverClass;

        if (!class_exists($sClassName)) {
            static::halt('Expected: ' . $sClassName, 'Driver class not available');
        } elseif (!in_array(static::INTERFACE_NAME, class_implements($sClassName))) {
            static::halt('Error Handler "' . $sClassName . '"  must implement "' . static::INTERFACE_NAME . '"');
        }

        $sClassName::init();

        static::$sDriverClass   = $sClassName;
        static::$oDefaultDriver = $oDefaultDriver;

        static::setHandlers();

        static::$bIsReady = true;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the error handlers
     */
    public static function setHandlers(): void
    {
        set_error_handler(static::class . '::handleError');
        set_exception_handler(static::class . '::handleException');
        register_shutdown_function(static::class . '::handleFatal');
    }

    // --------------------------------------------------------------------------

    public static function handleError($iErrorNumber, $sErrorString, $sErrorFile, $iErrorLine): void
    {
        if (!Factory::isReady()) {
            static::halt(sprintf(
                '%s: %s in file: %s on line: %d',
                $iErrorNumber,
                $sErrorString,
                $sErrorFile,
                $iErrorLine
            ), 'Error');
        } else {
            static::getDriverClass()::error($iErrorNumber, $sErrorString, $sErrorFile, $iErrorLine);
        }
    }

    public static function handleException($oException, $bHaltExecution = true): void
    {
        if (!Factory::isReady()) {
            static::halt(sprintf(
                'Uncaught %s Exception: code %s in file: %s on line: %s',
                get_class($oException),
                $oException->getCode(),
                $oException->getFile(),
                $oException->getLine()
            ), 'Uncaught Exception');
        } else {
            static::getDriverClass()::exception($oException, $bHaltExecution);
        }
    }

    public static function handleFatal(): void
    {
        if (!Factory::isReady()) {
            $aError = error_get_last();
            if (!is_null($aError) && $aError['type'] === E_ERROR) {
                static::halt(sprintf(
                    '%s in file: %s on line: %d',
                    $aError['message'],
                    $aError['file'],
                    $aError['line']
                ), 'Fatal Error');
            }
        } else {
            static::getDriverClass()::fatal();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the configured driver
     *
     * @return string
     */
    public static function getDriverClass(): string
    {
        return static::$sDriverClass;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default error driver config
     *
     * @return stdClass
     */
    public function getDefaultDriver()
    {
        return static::$oDefaultDriver;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default error driver class name
     *
     * @return string
     */
    public function getDefaultDriverClass()
    {
        $oDriver          = $this->getDefaultDriver();
        $sDriverNamespace = ArrayHelper::get('namespace', (array) $oDriver->data);
        $sDriverClass     = ArrayHelper::get('class', (array) $oDriver->data);
        return $sDriverNamespace . $sDriverClass;
    }

    // --------------------------------------------------------------------------

    /**
     * Manually trigger an error
     *
     * @param int    $iErrorNumber
     * @param string $sErrorString
     * @param string $sErrorFile
     * @param int    $iErrorLine
     */
    public function triggerError($iErrorNumber = 0, $sErrorString = '', $sErrorFile = '', $iErrorLine = 0)
    {
        //  PHP5.6 doesn't like $this->sDriverClass::error()
        $sDriverClass = static::$sDriverClass;
        $sDriverClass::error($iErrorNumber, $sErrorString, $sErrorFile, $iErrorLine);
    }

    // --------------------------------------------------------------------------

    /**
     * Shows the fatal error screen. A diagnostic screen is shown on non-production
     * environments
     *
     * @param string   $sSubject The error subject
     * @param string   $sMessage The error message
     * @param stdClass $oDetails Breakdown of the error which occurred
     *
     * @return void
     * @throws FactoryException
     * @throws ReflectionException
     * @throws ViewNotFoundException
     */
    public function showFatalErrorScreen($sSubject = '', $sMessage = '', $oDetails = null, ?array $aTrace = null)
    {
        if (is_array($sMessage)) {
            $sMessage = implode("\n", $sMessage);
        }

        if (is_null($oDetails)) {
            $oDetails = (object) [
                'type'      => null,
                'code'      => null,
                'msg'       => null,
                'file'      => null,
                'line'      => null,
                'backtrace' => null,
            ];
        }

        $aTrace              = $aTrace ?? (function_exists('debug_backtrace') ? debug_backtrace() : null);
        $oDetails->backtrace = $aTrace;

        set_status_header(HttpCodes::STATUS_INTERNAL_SERVER_ERROR);
        $this->renderErrorView(
            '500',
            [
                'sSubject' => $sSubject,
                'sMessage' => $sMessage,
                'oDetails' => $oDetails,
            ],
            true,
            $aTrace
        );
        exit(HttpCodes::STATUS_INTERNAL_SERVER_ERROR);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 404 page and halts script execution
     *
     * @param bool $bLogError Whether to log the error
     *
     * @throws FactoryException
     * @throws ReflectionException
     * @throws ViewNotFoundException
     */
    public function show404(bool $bLogError = false)
    {
        $sPage = ArrayHelper::get('REQUEST_URI', $_SERVER);

        /**
         * By default we log this, but allow a dev to skip it. Additionally, skip
         * if it's a HEAD request.
         * Reasoning: I often use HEAD requests to check the existence of a file
         * in JS before fetching it. I feel that these shouldn't be logged. A
         * direct GET/POST/etc request to a non-existent file is more likely a
         * user following a dead link so these _should_ be logged.
         * If you disagree, open up an issue and we'll work something out.
         */

        $sRequestMethod = isset($_SERVER) ? strtoupper(ArrayHelper::get('REQUEST_METHOD', $_SERVER)) : '';

        if ($bLogError && $sRequestMethod != 'HEAD') {
            log_message('info', '404 Page Not Found --> ' . $sPage);
        }

        // --------------------------------------------------------------------------

        /**
         * Define a constant for easier identification of 404 pages
         */
        Config::set('NAILS_IS_404', true);

        // --------------------------------------------------------------------------

        set_status_header(HttpCodes::STATUS_NOT_FOUND);
        $this->renderErrorView(
            '404',
            [
                'sSubject' => '404 Page Not Found',
                'sMessage' => '',
            ]
        );
        exit(HttpCodes::STATUS_NOT_FOUND);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 401 page and halts script execution
     *
     * @param string $sFlashMessage The flash message to display to the user
     * @param string $sReturnUrl    The URL to return to after logging in
     * @param bool   $bLogError     Whether to log the error or not
     * @param bool   $bForceView    Force the view to render (ratehr than redirect to login)
     *
     * @throws FactoryException
     * @throws ReflectionException
     * @throws ViewNotFoundException
     */
    public function show401(
        ?string $sFlashMessage = null,
        ?string $sReturnUrl = null,
        bool $bLogError = true,
        bool $bForceView = false
    ): void {

        /** @var UserFeedback $oSession */
        $oUserFeedback = Factory::service('UserFeedback');

        // --------------------------------------------------------------------------

        if ($bForceView || (function_exists('isLoggedIn') && isLoggedIn())) {

            if ($bLogError) {
                $sPage = ArrayHelper::get('REQUEST_URI', $_SERVER);
                log_message('error', '401 Unauthorised --> ' . $sPage);
            }

            if (!is_null($sFlashMessage)) {
                $sMessage = $sFlashMessage;

            } elseif ($oUserFeedback->issetError()) {
                $sMessage = $oUserFeedback->getError();

            } else {
                $sMessage = 'The page you are trying to view is restricted. Sadly you do not have enough permissions to see its content.';
            }

            // --------------------------------------------------------------------------

            /**
             * Define a constant for easier identification of 401 pages
             */
            Config::set('NAILS_IS_401', true);

            // --------------------------------------------------------------------------

            set_status_header(HttpCodes::STATUS_UNAUTHORIZED);
            $this->renderErrorView(
                '401',
                [
                    'sSubject' => '401 Unauthorized',
                    'sMessage' => $sMessage,
                ]
            );
            exit(401);

        } else {

            if (!is_null($sFlashMessage)) {
                $oUserFeedback->error($sFlashMessage);
            } elseif (!$oUserFeedback->issetError()) {
                $oUserFeedback->error('Sorry, you need to be logged in to see that page.');
            }

            if (is_null($sReturnUrl)) {
                //  Allow helper to determine what he returnTo should be
                $sReturnUrl = '';
            }

            redirect(loginUrl($sReturnUrl));
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the error view appropriate for the environment
     *
     * @param string  $sView        The view to load
     * @param array   $aData        Data to make available to the view
     * @param boolean $bFlushBuffer Whether to flush the output buffer or not
     *
     * @throws FactoryException
     * @throws ViewNotFoundException
     * @throws ReflectionException
     */
    public static function renderErrorView(
        $sView,
        $aData = [],
        $bFlushBuffer = true,
        ?array $aTrace = null
    ) {

        $iCode = is_numeric($sView)
            ? (int) $sView
            : HttpCodes::STATUS_INTERNAL_SERVER_ERROR;

        //  Stops cascading errors if CI isn't available
        if (!class_exists('CI_Lang') || !class_exists('MX_Lang')) {

            $sSubject = ArrayHelper::get('sSubject', $aData);
            $sMessage = ArrayHelper::get('sMessage', $aData);
            $sFile    = ArrayHelper::get('sFile', $aData);
            $iLine    = ArrayHelper::get('iLine', $aData);

            if (!empty($sFile)) {
                $sMessage .= '; File: ' . $sFile;
            }

            if (!empty($iLine)) {
                $sMessage .= '; Line: ' . $iLine;
            }

            static::halt(
                $sMessage,
                $sSubject,
                $iCode,
                $aTrace
            );
        }

        static::instantiateMockController();

        //  Flush the output buffer
        if ($bFlushBuffer) {
            $sObContents = ob_get_contents();
            if (!empty($sObContents)) {
                ob_clean();
            }
        }

        /** @var Input $oInput */
        $oInput     = Factory::service('Input');
        $sType      = $oInput::isCli() ? 'cli' : 'html';
        $sValidPath = static::getValidPath($sView, $sType);

        if ($sValidPath) {

            /** @var View $oView */
            $oView = Factory::service('View');
            /** @var Event $oEvent */
            $oEvent = Factory::service('Event');

            static::triggerErrorEvent(Events::VIEW_ERROR_PRE, $iCode, $sType, $aData);
            static::setErrorPageTitle($iCode);

            $oView->setData($aData);
            echo $oView->load($sValidPath, [], true);

            static::triggerErrorEvent(Events::VIEW_ERROR_POST, $iCode, $sType, $aData);

        } else {
            static::halt(
                ArrayHelper::get('sMessage', $aData),
                ArrayHelper::get('sSubject', $aData),
                $iCode
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Gets a valid apth for the view, if possible
     *
     * @param string $sView The view to load
     * @param string $sType the type
     *
     * @return string|null
     * @throws \Nails\Common\Exception\FactoryException
     * @throws \ReflectionException
     */
    protected static function getValidPath(string $sView, string $sType): ?string
    {
        $aPaths = [];

        /** @var \CI_Router $oRouter */
        $oRouter     = Factory::service('Router');
        $sController = ucfirst($oRouter->fetch_class());

        //  @todo (Pablo - 2019-07-26) - Add path for app module-specific view

        if (class_exists($sController)) {
            $oReflection     = new \ReflectionClass($sController);
            $sModuleViewPath = preg_replace('/controllers$/', '', dirname($oReflection->getFileName()));
            $aPaths[]        = implode(DIRECTORY_SEPARATOR, [
                rtrim($sModuleViewPath, DIRECTORY_SEPARATOR),
                'views',
                'errors',
                $sType,
                $sView . '.php',
            ]);
        }

        //  App generic
        $aPaths[] = implode(DIRECTORY_SEPARATOR, [
            Config::get('NAILS_APP_PATH') . 'application',
            'views',
            'errors',
            $sType,
            $sView . '.php',
        ]);

        //  Nails
        $aPaths[] = implode(DIRECTORY_SEPARATOR, [
            rtrim(Config::get('NAILS_COMMON_PATH'), DIRECTORY_SEPARATOR),
            'views',
            'errors',
            $sType,
            $sView . '.php',
        ]);

        foreach ($aPaths as $sPath) {
            if (file_exists($sPath)) {
                return $sPath;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Triggers error related events
     *
     * @param string $sEvent The event to trigger
     * @param int    $iCode  The error code
     * @param string $sType  The error type
     * @param array  $aData  The error data
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected static function triggerErrorEvent(string $sEvent, int $iCode, string $sType, array &$aData): void
    {
        /** @var Event $oEvent */
        $oEvent = Factory::service('Event');
        $oEvent->trigger(
            $sEvent,
            Events::getEventNamespace(),
            [
                $iCode,
                $sType,
                &$aData,
            ]
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the title for the error page
     *
     * @param int $iCode The error code being rendered
     *
     * @return string
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected static function setErrorPageTitle(int $iCode): void
    {
        /** @var \Nails\Common\Service\MetaData $oMetaData */
        $oMetaData = \Nails\Factory::service('MetaData');
        $oMetaData
            ->setTitles(array_filter([
                Config::get(
                    'ERROR_TITLE_' . $iCode,
                    sprintf(
                        '%d %s',
                        $iCode,
                        HttpCodes::getByCode($iCode)
                    )
                ),
            ]));
    }

    // --------------------------------------------------------------------------

    /**
     * If the SYSTEM_READY event hasn't been fired then we know that the app's controller hasn't been executed.
     * Instantiate this controller to allow the full application stack to execute, this allows the 404 views to
     * make use of variables defined by /App/Controller/Base. Also, load CI's core services as they might not
     * have been loaded.
     *
     * @throws FactoryException
     */
    protected static function instantiateMockController()
    {
        /** @var Event $oEvent */
        $oEvent = Factory::service('Event');

        if (!$oEvent->hasBeenTriggered(Events::SYSTEM_STARTING)) {

            require_once BASEPATH . 'core/Controller.php';

            load_class('Output', 'core');
            load_class('Security', 'core');
            load_class('Input', 'core');
            load_class('Lang', 'core');

            new NailsMockController();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * A very low-level error function, used before the main error handling stack kicks in
     *
     * @param string $sError   The error to show
     * @param string $sSubject An optional subject line
     * @param int    $iCode    The status code to send
     */
    public static function halt($sError, $sSubject = '', int $iCode = HttpCodes::STATUS_INTERNAL_SERVER_ERROR, ?array $aTrace = null)
    {
        if (php_sapi_name() === 'cli' || defined('STDIN')) {

            $sSubject = trim(strip_tags($sSubject ?? ''));
            $sError   = trim(strip_tags($sError ?? ''));

            echo "\n";
            echo $sSubject ? 'ERROR: ' . $sSubject . ":\n" : '';
            echo $sSubject ? $sError : 'ERROR: ' . $sError;
            echo "\n\n";

            if (is_array($aTrace)) {
                echo "Backtrace:\n\n";
                foreach ($aTrace as $index => $trace) {
                    echo "  " . ($trace['class'] ?? '<unknown class>') . ($trace['type'] ?? '::') . $trace['function'] . "()\n";
                    echo "  " . ($trace['file'] ?? '<unknown file>') . '::' . ($trace['line'] ?? '<unknown line>') . "\n";
                    echo "\n";
                }
            }

        } else {
            set_status_header($iCode);
            echo styleOpen();
            ?>
            p {
            font-family: monospace;
            margin: 20px 10px;
            }

            strong {
            color: red;
            }

            code {
            padding: 5px;
            border: 1px solid #CCC;
            background: #EEE
            }
            <?=styleClose()?>
            <p>
                <strong>ERROR:</strong>
                <?=$sSubject ? '<em>' . $sSubject . '</em> - ' : ''?>
                <?=$sError?>
            </p>
            <?php
        }
        exit(1);
    }
}
