<?php

/**
 * This driver brings support for Rollbar error handling
 *
 * @package     Nails
 * @subpackage  common
 * @category    errors
 * @author      Nails Dev Team
 * @link
 */

class CORE_NAILS_ErrorHandler_Rollbar implements CORE_NAILS_ErrorHandler_Interface
{
    /**
     * Sets up the driver
     * @return void
     */
    public static function init()
    {
        if (!defined('DEPLOY_ROLLBAR_ACCESS_TOKEN')) {

            $subject = 'Rollbar is not configured correctly';
            $message = 'Rollbar is enabled but DEPLOY_ROLLBAR_ACCESS_TOKEN is not defined.';

            CORE_NAILS_ErrorHandler::sendDeveloperMail($subject, $message);
            CORE_NAILS_ErrorHandler::showFatalErrorScreen($subject, $message);
        }

        $config = array(
            'access_token' => DEPLOY_ROLLBAR_ACCESS_TOKEN,
            'environment' => ENVIRONMENT,
            'person_fn' => 'CORE_NAILS_ErrorHandler_Rollbar::getPerson'
        );

        Rollbar::init($config, false, false, false);
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
        //  Ignore strict errors
        if ($errno == E_STRICT)
        {
            return false;
        }

        //  Send report to Rollbar
        Rollbar::report_php_error($errno, $errstr, $errfile, $errline);

        //  Let this bubble to the normal Nails error handler
        CORE_NAILS_ErrorHandler::error($errno, $errstr, $errfile, $errline);
    }

    // --------------------------------------------------------------------------

    /**
     * Catches uncaught exceptions
     * @param  exception $exception The caught exception
     * @return void
     */
    public static function exception($exception)
    {
        Rollbar::report_exception($exception);

        $code    = $exception->getCode();
        $msg     = $exception->getMessage();
        $file    = $exception->getFile();
        $line    = $exception->getLine();
        $errMsg  = 'Uncaught Exception with message "' . $msg . '" and code "';
        $errMsg .= $code . '" in ' . $file . ' on line ' . $line;

        //  Show we log the item?
        if (config_item('log_threshold') != 0)
        {
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

        CORE_NAILS_ErrorHandler::showFatalErrorScreen($subject, $message);
    }

    // --------------------------------------------------------------------------

    /**
     * Catches fatal errors on shut down
     * @return void
     */
    public static function fatal()
    {
        Rollbar::report_fatal_error();
        Rollbar::flush();

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

            CORE_NAILS_ErrorHandler::showFatalErrorScreen($subject, $message);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Get's the active user, if any
     * @return array
     */
    public static function getPerson()
    {
        $person = array(
            'id' => active_user('id'),
            'username' => active_user('username'),
            'email' => active_user('email')
        );

        return $person;
    }
}
