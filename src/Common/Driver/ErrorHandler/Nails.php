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

use Nails\Common\Interfaces\ErrorHandlerDriver;
use Nails\Common\Library\ErrorHandler;

class Nails implements ErrorHandlerDriver
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
     *
     * @param  int    $iErrorNumber The error number
     * @param  string $sErrorString The error message
     * @param  string $sErrorFile   The file where the error occurred
     * @param  int    $iErrorLine   The line number where the error occurred
     *
     * @return void
     */
    public static function error($iErrorNumber, $sErrorString, $sErrorFile, $iErrorLine)
    {
        //  Don't clog the logs up with strict notices
        if ($iErrorNumber === E_STRICT) {
            return;
        }

        //  Should we show this error?
        if ((bool) ini_get('display_errors') && error_reporting() !== 0) {

            if (!empty(ErrorHandler::$levels[$iErrorNumber])) {
                $severity = ErrorHandler::$levels[$iErrorNumber];
            } else {
                $severity = 'Unknown';
            }

            $message  = $sErrorString;
            $filepath = $sErrorFile;
            $line     = $iErrorLine;

            include FCPATH . APPPATH . 'errors/error_php.php';
        }

        //  Show we log the item?
        if (function_exists('config_item') && config_item('log_threshold') != 0) {
            $errMsg = $sErrorString . ' (' . $sErrorFile . ':' . $iErrorLine . ')';
            log_message('error', $errMsg, true);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Catches uncaught exceptions
     *
     * @param  \Exception $oException The caught exception
     *
     * @return void
     */
    public static function exception($oException)
    {
        $oDetails       = new \stdClass();
        $oDetails->type = get_class($oException);
        $oDetails->code = $oException->getCode();
        $oDetails->msg  = $oException->getMessage();
        $oDetails->file = $oException->getFile();
        $oDetails->line = $oException->getLine();

        $sSubject = $oDetails->msg;
        $sMessage = 'Uncaught Exception with code: ' . $oDetails->code;

        //  Show we log the item?
        if (function_exists('config_item') && config_item('log_threshold') != 0) {
            log_message('error', $sMessage, true);
        }

        ErrorHandler::sendDeveloperMail($sSubject, $sMessage);
        ErrorHandler::showFatalErrorScreen($sSubject, $sMessage, $oDetails);
    }

    // --------------------------------------------------------------------------

    /**
     * Catches fatal errors on shut down
     * @return void
     */
    public static function fatal()
    {
        $aError = error_get_last();

        if (!is_null($aError) && $aError['type'] === E_ERROR) {

            $oDetails = (object) [
                'type' => 'Fatal Error',
                'code' => $aError['type'],
                'msg'  => $aError['message'],
                'file' => $aError['file'],
                'line' => $aError['line'],
            ];

            $sSubject = 'Fatal Error';
            $sMessage = $aError['message'] . ' in ' . $aError['file'] . ' on line ' . $aError['line'];

            ErrorHandler::sendDeveloperMail($sSubject, $sMessage);
            ErrorHandler::showFatalErrorScreen($sSubject, $sMessage, $oDetails);
        }
    }
}
