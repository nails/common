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

use Nails\Common\Controller\Nails404Controller;
use Nails\Common\Events;
use Nails\Components;
use Nails\Environment;
use Nails\Factory;
use Nails\Functions;

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
        E_STRICT          => 'Runtime Notice',
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
     * @var \stdClass
     */
    protected static $oDefaultDriver;

    // --------------------------------------------------------------------------

    /**
     * Sets up the appropriate error handling driver
     */
    public function init()
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
            _NAILS_ERROR(implode(', ', $aNames), 'More than one error handler installed');
            return;
        } elseif (count($aCustomDrivers) === 1) {
            $oErrorHandler = reset($aCustomDrivers);
        } else {
            $oErrorHandler = $oDefaultDriver;
        }

        $sDriverNamespace = getFromArray('namespace', (array) $oErrorHandler->data);
        $sDriverClass     = getFromArray('class', (array) $oErrorHandler->data);
        $sClassName       = '\\' . $sDriverNamespace . $sDriverClass;

        if (!class_exists($sClassName)) {
            _NAILS_ERROR('Expected: ' . $sClassName, 'Driver class not available');
        } elseif (!in_array(static::INTERFACE_NAME, class_implements($sClassName))) {
            _NAILS_ERROR('Error Handler "' . $sClassName . '"  must implement "' . static::INTERFACE_NAME . '"');
        }

        $sClassName::init();

        set_error_handler($sClassName . '::error');
        set_exception_handler($sClassName . '::exception');
        register_shutdown_function($sClassName . '::fatal');

        static::$sDriverClass   = $sClassName;
        static::$oDefaultDriver = $oDefaultDriver;
        static::$bIsReady       = true;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default error driver config
     *
     * @return \stdClass
     */
    public function getDefaultDriver()
    {
        $this->init();
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
        $this->init();
        $oDriver          = $this->getDefaultDriver();
        $sDriverNamespace = getFromArray('namespace', (array) $oDriver->data);
        $sDriverClass     = getFromArray('class', (array) $oDriver->data);
        return '\\' . $sDriverNamespace . $sDriverClass;
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
        $this->init();
        //  PHP5.6 doesn't like $this->sDriverClass::error()
        $sDriverClass = static::$sDriverClass;
        $sDriverClass::error($iErrorNumber, $sErrorString, $sErrorFile, $iErrorLine);
    }

    // --------------------------------------------------------------------------

    /**
     * Shows the fatal error screen. A diagnostic screen is shown on non-production
     * environments
     *
     * @param  string    $sSubject The error subject
     * @param  string    $sMessage The error message
     * @param  \stdClass $oDetails Breakdown of the error which occurred
     *
     * @return void
     */
    public function showFatalErrorScreen($sSubject = '', $sMessage = '', $oDetails = null)
    {
        $this->init();

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

        //  Get the backtrace
        if (function_exists('debug_backtrace')) {
            $oDetails->backtrace = debug_backtrace();
        } else {
            $oDetails->backtrace = [];
        }

        set_status_header(500);
        $this->renderErrorView(
            '500',
            [
                'sSubject' => $sSubject,
                'sMessage' => $sMessage,
                'oDetails' => $oDetails,
            ]
        );
        exit(500);
    }

    // --------------------------------------------------------------------------

    /**
     * Sends a diagnostic email to the developers
     *
     * @param  string $sSubject The diagnostic subject
     * @param  string $sMessage The diagnostic message
     *
     * @return boolean
     */
    public function sendDeveloperMail($sSubject, $sMessage)
    {
        $this->init();

        //  Production only
        if (Environment::not(Environment::ENV_PROD)) {
            return true;
        }

        // --------------------------------------------------------------------------

        //  Do we know who we're sending to?
        if (!defined(APP_DEVELOPER_EMAIL) || empty(APP_DEVELOPER_EMAIL)) {
            //  Log the fact there's no email
            if (function_exists('log_message')) {
                log_message('error', 'Attempting to send developer email, but APP_DEVELOPER_EMAIL is not defined.');
            }
            return false;
        }

        // --------------------------------------------------------------------------

        try {

            $oEmailer   = Factory::service('Emailer');
            $sFromName  = $oEmailer->getFromName();
            $sFromEmail = $oEmailer->getFromEmail();

        } catch (\Exception $e) {
            $sFromName  = 'Log Error Reporter';
            $sFromEmail = 'root@' . gethostname();
        }

        // --------------------------------------------------------------------------

        $oCi  =& get_instance();
        $info = [
            'uri'     => isset($oCi->uri) ? $oCi->uri->uri_string() : '',
            'session' => isset($oCi->session) ? json_encode($oCi->session->userdata) : '',
            'post'    => isset($_POST) ? json_encode($_POST) : '',
            'get'     => isset($_GET) ? json_encode($_GET) : '',
            'server'  => isset($_SERVER) ? json_encode($_SERVER) : '',
            'globals' => isset($GLOBALS['error']) ? json_encode($GLOBALS['error']) : '',
        ];

        //  Closures cannot be serialized
        try {
            $info['debug_backtrace'] = json_encode(debug_backtrace());
        } catch (\Exception $e) {
            $info['debug_backtrace'] = 'Failed to json_encode Backtrace: ' . $e->getMessage();
        }

        $sExtended = 'URI: ' . $info['uri'] . "\n\n";
        $sExtended .= 'SESSION: ' . $info['session'] . "\n\n";
        $sExtended .= 'POST: ' . $info['post'] . "\n\n";
        $sExtended .= 'GET: ' . $info['get'] . "\n\n";
        $sExtended .= 'SERVER: ' . $info['server'] . "\n\n";
        $sExtended .= 'GLOBALS: ' . $info['globals'] . "\n\n";
        $sExtended .= 'BACKTRACE: ' . $info['debug_backtrace'] . "\n\n";

        if (isset($oCi->db)) {
            $sExtended .= 'LAST KNOWN QUERY: ' . $oCi->db->last_query() . "\n\n";
        }

        // --------------------------------------------------------------------------

        //  Prepare and send
        $sMimeBoundary = md5(uniqid(time()));
        $sTo           = Environment::not(Environment::ENV_PROD) && EMAIL_OVERRIDE ? EMAIL_OVERRIDE : APP_DEVELOPER_EMAIL;

        //  Headers
        $sHeaders = 'From: ' . $sFromName . ' <' . $sFromEmail . '>' . "\r\n";
        $sHeaders .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
        $sHeaders .= 'X-Priority: 1 (Highest)' . "\r\n";
        $sHeaders .= 'X-Mailer: X-MSMail-Priority: High/' . "\r\n";
        $sHeaders .= 'Importance: High' . "\r\n";
        $sHeaders .= 'MIME-Version:1.0' . "\r\n";
        $sHeaders .= 'Content-Type:multipart/mixed; boundary="' . $sMimeBoundary . '"' . "\r\n\r\n";

        //  Message
        $sHeaders .= '--' . $sMimeBoundary . "\r\n";
        $sHeaders .= 'Content-Type:text/html; charset="ISO-8859-1"' . "\r\n";
        $sHeaders .= 'Content-Transfer-Encoding:7bit' . "\r\n\r\n";

        $sHeaders .= '<html><head><style type="text/css">body { font:10pt Arial; }</style></head><body>';
        $sHeaders .= str_replace("\r", '', str_replace("\n", '<br />', $sMessage));
        $sHeaders .= '</body></html>' . "\r\n\r\n";

        //  Attachment
        $sHeaders .= '--' . $sMimeBoundary . "\r\n";
        $sHeaders .= 'Content-Type:application/octet-stream; name="debugging-data.txt"' . "\r\n";
        $sHeaders .= 'Content-Transfer-Encoding:base64' . "\r\n";
        $sHeaders .= 'Content-Disposition:attachment; filename="debugging-data.txt"' . "\r\n";
        $sHeaders .= base64_encode($sExtended) . "\r\n\r\n";

        // --------------------------------------------------------------------------

        //  Send!
        if (!empty($sTo)) {
            if (function_exists('mail')) {
                @mail($sTo, '!! ' . $sSubject . ' - ' . APP_NAME, '', $sHeaders);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 404 page and halts script execution
     *
     * @param bool $bLogError Whether to log the error
     */
    public function show404($bLogError = true)
    {
        $this->init();

        $sPage = getFromArray('REQUEST_URI', $_SERVER);

        /**
         * By default we log this, but allow a dev to skip it. Additionally, skip
         * if it's a HEAD request.
         *
         * Reasoning: I often use HEAD requests to check the existence of a file
         * in JS before fetching it. I feel that these shouldn't be logged. A
         * direct GET/POST/etc request to a non-existent file is more  likely a
         * user following a dead link so these _should_ be logged.
         *
         * If you disagree, open up an issue and we'll work something out.
         */

        $sRequestMethod = isset($_SERVER) ? strtoupper(getFromArray('REQUEST_METHOD', $_SERVER)) : '';

        if ($bLogError && $sRequestMethod != 'HEAD') {
            log_message('error', '404 Page Not Found --> ' . $sPage);
        }

        // --------------------------------------------------------------------------

        /**
         * Define a constant for easier identification of 404 pages
         */
        Functions::define('NAILS_IS_404', true);

        // --------------------------------------------------------------------------

        /**
         * If the SYSTEM_READY event hasn't been fired then we know that the app's controller hasn't been executed.
         * Instantiate this controller to allow the full application stack to execute, this allows the 404 views to
         * make use of variables defined by /App/Sontroller/Base. Also, load CI's core services as they might not
         * have been loaded.
         */

        $oEvent = Factory::service('Event');
        if (!$oEvent->hasBeenTriggered(Events::SYSTEM_READY)) {

            require_once BASEPATH . 'core/Controller.php';

            load_class('Output', 'core');
            load_class('Security', 'core');
            load_class('Input', 'core');
            load_class('Lang', 'core');

            new Nails404Controller();
        }

        // --------------------------------------------------------------------------

        set_status_header(404);
        $this->renderErrorView(
            '404',
            [
                'sSubject' => '404 Page Not Found',
                'sMessage' => '',
            ]
        );
        exit(404);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 401 page and halts script execution
     *
     * @param bool $bLogError Whether to log the error
     */
    public function show401($bLogError = true)
    {
        $this->init();

        if (function_exists('isLoggedIn') && isLoggedIn()) {

            if ($bLogError) {
                $sPage = getFromArray('REQUEST_URI', $_SERVER);
                log_message('error', '401 Unauthorised --> ' . $sPage);
            }

            $sMessage = 'The page you are trying to view is restricted. Sadly you do not have enough ';
            $sMessage .= 'permissions to see its content.';

            if (function_exists('wasAdmin') && wasAdmin()) {

                $oUserModel = Factory::model('User', 'nails/module-auth');
                $oRecovery  = $oUserModel->getAdminRecoveryData();

                $sMessage .= '<br /><br />';
                $sMessage .= '<small>';
                $sMessage .= 'However, it looks like you are logged in as someone else.';
                $sMessage .= '<br />' . anchor($oRecovery->loginUrl, 'Log back in as ' . $oRecovery->name);
                $sMessage .= ' and try again.';
                $sMessage .= '</small>';
            }

            set_status_header(401);
            $this->renderErrorView(
                '401',
                [
                    'sSubject' => '404 Unauthorized',
                    'sMessage' => $sMessage,
                ]
            );
            exit(404);

        } else {

            $oSession = Factory::service('Session', 'nails/module-auth');
            $oInput   = Factory::service('Input');
            $sMessage = 'Sorry, you need to be logged in to see that page.';

            $oSession->setFlashData('message', $sMessage);

            if ($oInput->server('REQUEST_URI')) {
                $sReturn = $oInput->server('REQUEST_URI');
            } elseif (uri_string()) {
                $sReturn = uri_string();
            } else {
                $sReturn = '';
            }

            $sReturn = $sReturn ? '?return_to=' . urlencode($sReturn) : '';

            redirect('auth/login' . $sReturn);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the error view appropriate for the environment
     *
     * @param string  $sView        The view to load
     * @param array   $aData        Data to make available to the view
     * @param boolean $bFlushBuffer Whether to flush the output buffer or not
     */
    public static function renderErrorView(
        $sView,
        $aData = [],
        $bFlushBuffer = true
    ) {

        //  Flush the output buffer
        if ($bFlushBuffer) {
            $sObContents = ob_get_contents();
            if (!empty($sObContents)) {
                ob_clean();
            }
        }

        $oRouter = Factory::service('Router');
        $oInput  = Factory::service('Input');
        $sType   = $oInput::isCli() ? 'cli' : 'html';

        $aPaths      = [];
        $sController = ucfirst($oRouter->fetch_class());

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
            rtrim(APPPATH, DIRECTORY_SEPARATOR),
            'views',
            'errors',
            $sType,
            $sView . '.php',
        ]);

        //  Nails
        $aPaths[] = implode(DIRECTORY_SEPARATOR, [
            rtrim(NAILS_COMMON_PATH, DIRECTORY_SEPARATOR),
            'views',
            'errors',
            $sType,
            $sView . '.php',
        ]);

        $sValidPath = null;
        foreach ($aPaths as $sPath) {
            if (file_exists($sPath)) {
                $sValidPath = $sPath;
                break;
            }
        }

        if ($sValidPath) {

            $oView = Factory::service('View');
            $oView->setData($aData);
            echo $oView->load($sValidPath, [], true);

        } else {
            _NAILS_ERROR('404 Page Not Found');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * A very low-level error function, used before the main error handling stack kicks in
     *
     * @param string $sError   The error to show
     * @param string $sSubject An optional subject line
     */
    public static function halt($sError, $sSubject = '')
    {
        $oInput = Factory::service('Input');
        if ($oInput::isCli()) {

            echo "\n";
            echo $sSubject ? 'ERROR: ' . $sSubject . ":\n" : '';
            echo $sSubject ? $sError : 'ERROR: ' . $sError;
            echo "\n\n";

        } else {
            ?>
            <style type="text/css">
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
            </style>
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
