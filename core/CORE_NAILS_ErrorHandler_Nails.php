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
        if (config_item('log_threshold') == 0)
        {
            return;
        }

        $code    = $exception->getCode();
        $msg     = $exception->getMessage();
        $file    = $exception->getFile();
        $line    = $exception->getLine();
        $errMsg  = 'Uncaught Exception with message "' . $msg . '" and code "' . $code . '" in ' . $file . ' on line ' . $line;

        log_message('error', $errMsg, true);
    }

    // --------------------------------------------------------------------------

    public static function fatal()
    {
        $error = error_get_last();

        if (!is_null($error) && $error['type'] === E_ERROR) {

            //  Finally, show the user something
            if (is_file(FCPATH . APPPATH . 'errors/error_fatal.php')) {

                include_once FCPATH . APPPATH . 'errors/error_fatal.php';

            } else {

                include_once NAILS_COMMON_PATH . 'errors/error_fatal.php';
            }
            exit(0);
        }
    }
}