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

use Nails\Factory;
use Nails\Environment;

class ErrorHandler
{
    public static $levels = array(
                        E_ERROR           =>  'Error',
                        E_WARNING         =>  'Warning',
                        E_PARSE           =>  'Parsing Error',
                        E_NOTICE          =>  'Notice',
                        E_CORE_ERROR      =>  'Core Error',
                        E_CORE_WARNING    =>  'Core Warning',
                        E_COMPILE_ERROR   =>  'Compile Error',
                        E_COMPILE_WARNING =>  'Compile Warning',
                        E_USER_ERROR      =>  'User Error',
                        E_USER_WARNING    =>  'User Warning',
                        E_USER_NOTICE     =>  'User Notice',
                        E_STRICT          =>  'Runtime Notice'
                    );

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

            throw new \Nails\Common\Exception\ErrorHandlerException($sLoadError, 1);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Shows the fatal error screen. A diagnostic screen is shown on non-production
     * environments
     * @param  string   $subject The error subject
     * @param  string   $message The error message
     * @param  stdClass $details Breakdown of the error which occurred
     * @return void
     */
    public static function showFatalErrorScreen($subject = '', $message = '', $details = null)
    {
        $bIsCli = isCli();

        if (is_null($details)) {

            $details       = new \stdClass();
            $details->code = '';
            $details->msg  = '';
            $details->file = '';
            $details->line = '';
        }

        //  Get the backtrace
        if (function_exists('debug_backtrace')) {

            $details->backtrace = debug_backtrace();

        } else {

            $details->backtrace = array();
        }

        //  Set a 500 error
        $serverProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '';
        $headerString   = '500 Internal Server Error';

        header($serverProtocol . ' ' . $headerString);

        //  Flush the output buffer
        $obContents = ob_get_contents();
        if (!empty($obContents)) {
            ob_clean();
        }

        //  Non-CLI and Non-production and have an app-specific dev error file?
        if (!$bIsCli && Environment::not('PRODUCTION') && is_file(APPPATH . 'views/errors/html/error_fatal_dev.php')) {

            include_once APPPATH . 'views/errors/html/error_fatal_dev.php';

        //  Non-CLI and Production and have an app-specific error file?
        } elseif (!$bIsCli && Environment::is('PRODUCTION') && is_file(APPPATH . 'views/errors/html/error_fatal.php')) {

            include_once APPPATH . 'views/errors/html/error_fatal.php';

        //  Non-CLI and Non-production?
        } elseif (!$bIsCli && Environment::not('PRODUCTION')) {

            include_once NAILS_COMMON_PATH . 'views/errors/html/error_fatal_dev.php';

        //  CLI and Non-production and have an app-specific dev error file?
        } elseif ($bIsCli && Environment::not('PRODUCTION') && is_file(APPPATH . 'views/errors/html/error_fatal_dev_cli.php')) {

            include_once APPPATH . 'views/errors/html/error_fatal_dev_cli.php';

        //  CLI and Production and have an app-specific error file?
        } elseif ($bIsCli && Environment::is('PRODUCTION') && is_file(APPPATH . 'views/errors/html/error_fatal_cli.php')) {

            include_once APPPATH . 'views/errors/html/error_fatal_cli.php';

        //  CLI and Non-production?
        } elseif ($bIsCli && Environment::not('PRODUCTION')) {

            include_once NAILS_COMMON_PATH . 'views/errors/html/error_fatal_dev_cli.php';

        //  CLI Production
        } elseif ($bIsCli) {

            include_once NAILS_COMMON_PATH . 'views/errors/html/error_fatal_cli.php';

        //  Non-CLI Production
        } else {

            include_once NAILS_COMMON_PATH . 'views/errors/html/error_fatal.php';
        }

        exit(500);
    }

    // --------------------------------------------------------------------------

    /**
     * Sends a diagnostic email to the developers
     * @param  string $subject The diagnostic subject
     * @param  string $message The diagnostic message
     * @return boolean
     */
    public static function sendDeveloperMail($subject, $message)
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

            $oEmailer = Factory::service('Emailer');

            $fromName = $oEmailer->getFromName();
            $fromEmail = $oEmailer->getFromEmail();


        } catch (\Exception $e) {

            $fromName  = 'Log Error Reporter';
            $fromEmail = 'root@' . gethostname();
        }

        // --------------------------------------------------------------------------

        $_ci =& get_instance();

        $info = array(
            'uri'     => isset($_ci->uri)         ? $_ci->uri->uri_string()              : '',
            'session' => isset($_ci->session)     ? json_encode($_ci->session->userdata) : '',
            'post'    => isset($_POST)            ? json_encode($_POST)                  : '',
            'get'     => isset($_GET)             ? json_encode($_GET)                   : '',
            'server'  => isset($_SERVER)          ? json_encode($_SERVER)                : '',
            'globals' => isset($GLOBALS['error']) ? json_encode($GLOBALS['error'])       : ''
        );

        //  Closures cannot be serialized
        try {

            $info['debug_backtrace'] = json_encode(debug_backtrace());

        } catch (Exception $e) {

            $info['debug_backtrace'] = 'Failed to json_encode get Backtrace: ' .  $e->getMessage();
        }

        $extended   = 'URI: ' . $info['uri'] . "\n\n";
        $extended  .= 'SESSION: ' . $info['session'] . "\n\n";
        $extended  .= 'POST: ' . $info['post'] . "\n\n";
        $extended  .= 'GET: ' . $info['get'] . "\n\n";
        $extended  .= 'SERVER: ' . $info['server'] . "\n\n";
        $extended  .= 'GLOBALS: ' . $info['globals'] . "\n\n";
        $extended  .= 'BACKTRACE: ' . $info['debug_backtrace'] . "\n\n";

        if (isset($_ci->db)) {

            $extended  .= 'LAST KNOWN QUERY: ' .   $_ci->db->last_query() . "\n\n";
        }

        // --------------------------------------------------------------------------

        //  Prepare and send
        $mimeBoundary = md5(uniqid(time()));
        $to           = Environment::not('PRODUCTION') && EMAIL_OVERRIDE ? EMAIL_OVERRIDE : APP_DEVELOPER_EMAIL;

        //  Headers
        $headers  = 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion()  . "\r\n";
        $headers .= 'X-Priority: 1 (Highest)' . "\r\n";
        $headers .= 'X-Mailer: X-MSMail-Priority: High/' . "\r\n";
        $headers .= 'Importance: High' . "\r\n";
        $headers .= 'MIME-Version:1.0' . "\r\n";
        $headers .= 'Content-Type:multipart/mixed; boundary="' . $mimeBoundary . '"' . "\r\n\r\n";

        //  Message
        $headers .= '--' . $mimeBoundary . "\r\n";
        $headers .= 'Content-Type:text/html; charset="ISO-8859-1"' . "\r\n";
        $headers .= 'Content-Transfer-Encoding:7bit' . "\r\n\r\n";

        $headers .= '<html><head><style type="text/css">body { font:10pt Arial; }</style></head><body>';
        $headers .= str_replace("\r", '', str_replace("\n", '<br />', $message));
        $headers .= '</body></html>' . "\r\n\r\n";

        //  Attachment
        $headers .= '--' . $mimeBoundary . "\r\n";
        $headers .= 'Content-Type:application/octet-stream; name="debugging-data.txt"' . "\r\n";
        $headers .= 'Content-Transfer-Encoding:base64' . "\r\n";
        $headers .= 'Content-Disposition:attachment; filename="debugging-data.txt"' . "\r\n";
        $headers .= base64_encode($extended) . "\r\n\r\n";

        // --------------------------------------------------------------------------

        //  Send!
        if (!empty($to)) {

            if (function_exists('mail')) {

                @mail($to, '!! ' . $subject . ' - ' . APP_NAME, '', $headers);
                return true;

            } else {

                return false;
            }

        } else {

            return false;
        }
    }
}
