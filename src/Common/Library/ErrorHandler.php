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
    public static $levels = [
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
        $sLoadError    = '';

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
    public static function showFatalErrorScreen($sSubject = '', $sMessage = '', $oDetails = null)
    {
        $bIsCli = Input::isCli();

        if (is_null($oDetails)) {

            $oDetails       = new \stdClass();
            $oDetails->type = null;
            $oDetails->code = null;
            $oDetails->msg  = null;
            $oDetails->file = null;
            $oDetails->line = null;
        }

        //  Get the backtrace
        if (function_exists('debug_backtrace')) {
            $oDetails->backtrace = debug_backtrace();
        } else {
            $oDetails->backtrace = [];
        }

        //  Set a 500 error
        $sServerProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '';
        $sHeaderString   = '500 Internal Server Error';

        header($sServerProtocol . ' ' . $sHeaderString);

        //  Flush the output buffer
        $sObContents = ob_get_contents();
        if (!empty($sObContents)) {
            ob_clean();
        }

        $bIsProd = Environment::is('PRODUCTION');

        if (!$bIsCli && !$bIsProd && is_file(FCPATH . APPPATH . 'errors/error_fatal_dev.php')) {

            //  Non-CLI and Non-production and have an app-specific dev error file?
            include_once FCPATH . APPPATH . 'errors/error_fatal_dev.php';

        } elseif (!$bIsCli && $bIsProd && is_file(FCPATH . APPPATH . 'errors/error_fatal.php')) {

            //  Non-CLI and Production and have an app-specific error file?
            include_once FCPATH . APPPATH . 'errors/error_fatal.php';

        } elseif (!$bIsCli && !$bIsProd) {

            //  Non-CLI and Non-production?
            include_once NAILS_COMMON_PATH . 'errors/error_fatal_dev.php';

        } elseif ($bIsCli && !$bIsProd && is_file(FCPATH . APPPATH . 'errors/error_fatal_dev_cli.php')) {

            //  CLI and Non-production and have an app-specific dev error file?
            include_once FCPATH . APPPATH . 'errors/error_fatal_dev_cli.php';

        } elseif ($bIsCli && $bIsProd && is_file(FCPATH . APPPATH . 'errors/error_fatal_cli.php')) {

            //  CLI and Production and have an app-specific error file?
            include_once FCPATH . APPPATH . 'errors/error_fatal_cli.php';

        } elseif ($bIsCli && !$bIsProd) {

            //  CLI and Non-production?
            include_once NAILS_COMMON_PATH . 'errors/error_fatal_dev_cli.php';

        } elseif ($bIsCli) {

            //  CLI Production
            include_once NAILS_COMMON_PATH . 'errors/error_fatal_cli.php';

        } else {

            //  Non-CLI Production
            include_once NAILS_COMMON_PATH . 'errors/error_fatal.php';
        }

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
    public static function sendDeveloperMail($sSubject, $sMessage)
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
}
