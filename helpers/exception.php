<?php

/**
 * This file provides exception related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

if (!function_exists('showFatalError')) {
    /**
     * Renders the fatal error screen and alerts developers
     *
     * @param  string $sSubject The subject of the developer alert
     * @param  string $sMessage The body of the developer alert
     *
     * @return void
     */
    function showFatalError($sSubject = '', $sMessage = '')
    {
        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('sendDeveloperMail')) {

    /**
     * Quickly send a high priority email via mail() to the APP_DEVELOPER
     *
     * @param  string $sSubject The email's subject
     * @param  string $sMessage The email's body
     *
     * @return boolean
     */
    function sendDeveloperMail($sSubject, $sMessage)
    {
        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->sendDeveloperMail($sSubject, $sMessage);
    }
}
