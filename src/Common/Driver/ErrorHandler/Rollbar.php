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

namespace Nails\Common\Driver\ErrorHandler;

use Nails\Common\Interfaces\ErrorHandlerDriver;
use Nails\Factory;
use Nails\Environment;
use Rollbar\Payload\Level;

class Rollbar implements ErrorHandlerDriver
{
    /**
     * Sets up the driver
     * @return void
     */
    public static function init()
    {
        $oErrorHandler = Factory::service('ErrorHandler');

        if (!defined('DEPLOY_ROLLBAR_ACCESS_TOKEN')) {

            $sSubject = 'Rollbar is not configured correctly';
            $sMessage = 'Rollbar is enabled but DEPLOY_ROLLBAR_ACCESS_TOKEN is not defined.';

            $oErrorHandler->sendDeveloperMail($sSubject, $sMessage);
            $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
        }

        if (!class_exists('\Rollbar\Rollbar')) {

            $sSubject = 'Rollbar is not configured properly.';
            $sMessage = 'Rollbar is set as the error handler, but the Rollbar class ';
            $sMessage .= 'could not be found. Ensure that it is in composer.json.';

            $oErrorHandler->sendDeveloperMail($sSubject, $sMessage);
            $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
        }

        $aConfig = [
            'access_token' => DEPLOY_ROLLBAR_ACCESS_TOKEN,
            'environment'  => Environment::get(),
            'person_fn'    => '\Nails\Common\Driver\ErrorHandler\Rollbar\Rollbar::getPerson',
        ];

        \Rollbar\Rollbar::init($aConfig, false, false, false);
    }

    // --------------------------------------------------------------------------

    /**
     * Called when a PHP error occurs
     *
     * @param  int    $iNumber  The error number
     * @param  string $sMessage The error message
     * @param  string $sFile    The file where the error occurred
     * @param  int    $iLine    The line number where the error occurred
     *
     * @return void
     */
    public static function error($iNumber, $sMessage, $sFile, $iLine)
    {
        //  Ignore strict errors
        if ($iNumber == E_STRICT) {
            return;
        }

        //  Send report to Rollbar
        \Rollbar\Rollbar::log(
            Level::WARNING,
            $sMessage,
            [
                'error_number' => $iNumber,
                'file'         => $sFile,
                'line'         => $iLine,
            ]
        );

        //  Let this bubble to the normal Nails error handler
        Nails::error($iNumber, $sMessage, $sFile, $iLine);
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
        \Rollbar\Rollbar::log(Level::ERROR, $oException);

        $oDetails       = new \stdClass();
        $oDetails->type = get_class($oException);
        $oDetails->code = $oException->getCode();
        $oDetails->msg  = $oException->getMessage();
        $oDetails->file = $oException->getFile();
        $oDetails->line = $oException->getLine();

        $sMessage = 'Uncaught Exception with message "' . $oDetails->msg . '" and code "';
        $sMessage .= $oDetails->code . '" in ' . $oDetails->file . ' on line ' . $oDetails->line;

        //  Show we log the item?
        if (config_item('log_threshold') != 0) {
            log_message('error', $sMessage, true);
        }

        //  Show something to the user
        if (Environment::not('PRODUCTION')) {
            $sSubject = 'Uncaught Exception';
        } else {
            $sSubject = '';
            $sMessage = '';
        }

        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage, $oDetails);
    }

    // --------------------------------------------------------------------------

    /**
     * Catches fatal errors on shut down
     * @return void
     */
    public static function fatal()
    {
        \Rollbar\Rollbar::fatalHandler();

        $aError = error_get_last();

        if (!is_null($aError) && $aError['type'] === E_ERROR) {

            //  Show something to the user
            if (Environment::not('PRODUCTION')) {
                $sSubject = 'Fatal Error';
                $sMessage = $aError['message'] . ' in ' . $aError['file'] . ' on line ' . $aError['line'];
            } else {
                $sSubject = '';
                $sMessage = '';
            }

            $oErrorHandler = Factory::service('ErrorHandler');
            $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Get's the active user, if any
     * @return array
     */
    public static function getPerson()
    {
        return [
            'id'       => activeUser('id'),
            'username' => activeUser('username'),
            'email'    => activeUser('email'),
        ];
    }
}
