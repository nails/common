<?php

/**
 * This driver is the default error handler
 *
 * @package     Nails
 * @subpackage  common
 * @category    errors
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Driver\ErrorHandler;

class Nails implements \Nails\Common\Interfaces\ErrorHandlerDriver
{
    /**
     * Set up the driver
     * @return void
     */
    public static function init()
    {
        //  Nothing to do, but interface requires this method be defined.
    }

    // --------------------------------------------------------------------------

    /**
     * Called when a PHP error occurs
     * @param  int    $errno   The error number
     * @param  string $errstr  The error message
     * @param  string $errfile The file where the error occurred
     * @param  int    $errline The line number where the error occurred
     * @return boolean
     */
    public static function error($errno, $errstr, $errfile, $errline)
    {
        //  Don't clog the logs up with strict notices
        if ($errno === E_STRICT) {

            return;
        }

        //  Should we show this error?
        if ((bool) ini_get('display_errors') && error_reporting() !== 0) {

            if (!empty(\Nails\Common\Library\ErrorHandler::$levels[$errno])) {

                $severity = \Nails\Common\Library\ErrorHandler::$levels[$errno];

            } else {

                $severity = 'Unknown';
            }

            $message  = $errstr;
            $filepath = $errfile;
            $line     = $errline;

            include FCPATH . APPPATH . 'errors/error_php.php';
        }

        //  Show we log the item?
        if (function_exists('config_item') && config_item('log_threshold') != 0) {

            $errMsg = $errstr . ' (' . $errfile . ':' . $errline . ')';
            log_message('error', $errMsg, true);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Catches uncaught exceptions
     * @param  exception $exception The caught exception
     * @return void
     */
    public static function exception($exception)
    {
        $oDetails       = new \stdClass();
        $oDetails->type = get_class($exception);
        $oDetails->code = $exception->getCode();
        $oDetails->msg  = $exception->getMessage();
        $oDetails->file = $exception->getFile();
        $oDetails->line = $exception->getLine();

        $sSubject  = 'Uncaught Exception';
        $sMessage  = 'Uncaught Exception with message "' . $oDetails->msg . '" and code "';
        $sMessage .= $oDetails->code . '" in ' . $oDetails->file . ' on line ' . $oDetails->line;

        //  Show we log the item?
        if (function_exists('config_item') && config_item('log_threshold') != 0) {
            log_message('error', $sMessage, true);
        }

        \Nails\Common\Library\ErrorHandler::sendDeveloperMail($sSubject, $sMessage);
        \Nails\Common\Library\ErrorHandler::showFatalErrorScreen($sSubject, $sMessage, $oDetails);
    }

    // --------------------------------------------------------------------------

    /**
     * Catches fatal errors on shut down
     * @return void
     */
    public static function fatal()
    {
        $error = error_get_last();

        if (!is_null($error) && $error['type'] === E_ERROR) {

            $details       = new \stdClass();
            $details->type = 'Fatal Error';
            $details->code = $error['type'];
            $details->msg  = $error['message'];
            $details->file = $error['file'];
            $details->line = $error['line'];

            $subject = 'Fatal Error';
            $message = $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'];

            \Nails\Common\Library\ErrorHandler::sendDeveloperMail($subject, $message);
            \Nails\Common\Library\ErrorHandler::showFatalErrorScreen($subject, $message, $details);
        }
    }
}
