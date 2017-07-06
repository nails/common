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

namespace Nails\Common\Library;

use Nails\Common\Exception\ErrorHandlerException;
use Nails\Environment;
use Nails\Factory;

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

    // --------------------------------------------------------------------------

    /**
     * The fully qualified driver class name
     * @var string
     */
    protected $sDriverClass;

    // --------------------------------------------------------------------------

    /**
     * Sets up the appropriate error handling driver
     */
    public function __construct()
    {
        /**
         * Work out how we're handling errors. Production environments take into
         * consideration error reporting. Non-production environments use local
         * error reporting, that is CI Error reporting
         */

        $sErrorHandler = defined('DEPLOY_ERROR_REPORTING_HANDLER') ? DEPLOY_ERROR_REPORTING_HANDLER : 'Nails';
        $sDriverClass  = '\Nails\Common\Driver\ErrorHandler\\' . $sErrorHandler;

        if (!class_exists($sDriverClass)) {
            $sLoadError   = '"' . $sDriverClass . '" is not a valid ErrorHandler';
            $sDriverClass = '\Nails\Common\Driver\ErrorHandler\Nails';
        }

        $sDriverClass::init();

        set_error_handler($sDriverClass . '::error');
        set_exception_handler($sDriverClass . '::exception');
        register_shutdown_function($sDriverClass . '::fatal');

        if (!empty($sLoadError)) {
            throw new ErrorHandlerException($sLoadError, 1);
        }

        $this->sDriverClass = $sDriverClass;
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
        $sDriverClass = $this->sDriverClass;
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
        if (is_array($sMessage)) {
            $sMessage = implode("\n", $sMessage);
        }

        $bIsCli  = isCli();
        $bIsProd = Environment::is('PRODUCTION');

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
            'exception',
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
        //  Production only
        if (Environment::not('PRODUCTION')) {
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
        $sTo           = Environment::not('PRODUCTION') && EMAIL_OVERRIDE ? EMAIL_OVERRIDE : APP_DEVELOPER_EMAIL;

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
     * @param string $sPage     The URI which 404'd
     * @param bool   $bLogError Whether to log the error
     */
    public function show404Screen($sPage = '', $bLogError = true)
    {
        if (empty($sPage) && isset($_SERVER)) {
            $sPage = getFromArray('REQUEST_URI', $_SERVER);
        }

        /**
         * By default we log this, but allow a dev to skip it. Additionally, skip
         * if it's a HEAD request.
         *
         * Reasoning: I often use HEAD requests to check the existance of a file
         * in JS before fetching it. I feel that these shouldn't be logged. A
         * direct GET/POST/etc request to a non existant file is more  likely a
         * user following a deadlink so these _should_ be logged.
         *
         * If you disagree, open up an issue and we'll work something out.
         */

        $sRequestMethod = isset($_SERVER) ? strtoupper(getFromArray('REQUEST_METHOD', $_SERVER)) : '';

        if ($bLogError && $sRequestMethod != 'HEAD') {
            log_message('error', '404 Page Not Found --> ' . $sPage);
        }

        defineConst('NAILS_IS_404', true);
        set_status_header(404);
        $this->renderErrorView('404');
        exit(500);
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

        $sType      = is_cli() ? 'cli' : 'html';
        $aAppPath   = [
            rtrim(APPPATH, DIRECTORY_SEPARATOR),
            'views',
            'errors',
            $sType,
            $sView . '.php',
        ];
        $aNailsPath = [
            rtrim(NAILS_COMMON_PATH, DIRECTORY_SEPARATOR),
            'views',
            'errors',
            $sType,
            $sView . '.php',
        ];

        $sAppPath   = implode(DIRECTORY_SEPARATOR, $aAppPath);
        $sNailsPath = implode(DIRECTORY_SEPARATOR, $aNailsPath);


        extract($aData);

        if (file_exists($sAppPath)) {
            include $sAppPath;
        } elseif (file_exists($sNailsPath)) {
            include $sNailsPath;
        } else {
            _NAILS_ERROR($sMessage, $sSubject);
        }
    }
}
