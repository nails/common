<?php

interface CORE_NAILS_ErrorHandler_Interface
{
    //  Object methods
    public static function init();
    public static function error($errno, $errstr, $errfile, $errline);
    public static function exception($exception);
    public static function fatal();
}

class CORE_NAILS_ErrorHandler
{
    public function __construct()
    {
        /**
         * Work out how we're handling errors. Production environments take into
         * consideration error reporting. Non-production environments use local
         * error reporting, that is CI Error reporting
         */

        if (ENVIRONMENT === 'DEVELOPMENT')
        {
            switch (APP_ERROR_REPORTING_HANDLER) {

                case 'ROLLBAR':

                    $className = 'Rollbar';
                    break;

                case 'NAILS':
                default:

                    $className = 'Nails';
                    break;
            }

            require_once NAILS_COMMON_PATH . 'core/CORE_NAILS_ErrorHandler_' . $className . '.php';

            //  Init the handler
            $errorHandler = 'CORE_NAILS_ErrorHandler_' . $className;
            $errorHandler::init();

            //  Set the handlers
            set_error_handler($errorHandler . '::error');
            set_exception_handler($errorHandler . '::exception');
            register_shutdown_function($errorHandler . '::fatal');
        }
    }
}