<?php

class CORE_NAILS_ErrorHandler_Nails implements CORE_NAILS_ErrorHandler_Interface
{
    public static function init()
    {
    }

    // --------------------------------------------------------------------------

    public static function error($errno, $errstr, $errfile, $errline)
    {
        //  Let this bubble to the normal Codeigniter session handler
        return _exception_handler($errno, $errstr, $errfile, $errline);
    }

    // --------------------------------------------------------------------------

    public static function exception($exception)
    {
        //  Show we log the item?
        if (config_item('log_threshold') != 0)
        {
            $code    = $exception->getCode();
            $msg     = $exception->getMessage();
            $file    = $exception->getFile();
            $line    = $exception->getLine();
            $errMsg  = 'Uncaught Exception with message "' . $msg . '" and code "';
            $errMsg .= $code . '" in ' . $file . ' on line ' . $line;

            log_message('error', $errMsg, true);
        }

        //  Show something to the user
        if (ENVIRONMENT != 'PRODUCTION') {

            $subject = 'Uncaught Exception';
            $message = $errMsg;

        } else {

            $subject = '';
            $message = '';
        }

        CORE_NAILS_ErrorHandler::sendDeveloperMail($subject, $message);
        CORE_NAILS_ErrorHandler::showFatalErrorScreen($subject, $message);
    }

    // --------------------------------------------------------------------------

    public static function fatal()
    {
        $error = error_get_last();

        if (!is_null($error) && $error['type'] === E_ERROR) {

            //  Show something to the user
            if (ENVIRONMENT != 'PRODUCTION') {

                $subject = 'Fatal Error';
                $message = $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'];

            } else {

                $subject = '';
                $message = '';
            }

            CORE_NAILS_ErrorHandler::sendDeveloperMail($subject, $message);
            CORE_NAILS_ErrorHandler::showFatalErrorScreen($subject, $message);
        }
    }
}
