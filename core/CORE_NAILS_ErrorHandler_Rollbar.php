<?php

class CORE_NAILS_ErrorHandler_Rollbar implements CORE_NAILS_ErrorHandler_Interface
{
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

    public static function exception($exception)
    {
        Rollbar::report_exception($exception);
        CORE_NAILS_ErrorHandler::exception($exception);
    }

    // --------------------------------------------------------------------------

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
