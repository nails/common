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

if (!function_exists('show_401')) {

    /**
     * Renders the 401 unauthorised page
     *
     * @param  string $sMessage A message to show users when redirected
     *
     * @return void
     */
    function show_401($sMessage = '')
    {
        /**
         * Logged in users can't be redirected to log in, they simply get
         * an unauthorised page
         */

        if (isLoggedIn()) {

            $sSubject = 'Sorry, you are not authorised to view this page';
            $sMessage = 'The page you are trying to view is restricted. Sadly you don\'t have enough ';
            $sMessage .= 'permissions to see its content.';

            if (wasAdmin()) {

                $oAdminRecoveryData = getAdminRecoveryData();
                $sUrl               = $oAdminRecoveryData->loginUrl;
                $sName              = $oAdminRecoveryData->name;

                $sMessage .= '<br /><br />';
                $sMessage .= '<small>';
                $sMessage .= 'However, it looks like you\'re logged in as someone else. <br>';
                $sMessage .= anchor($sUrl, 'Log back in as ' . $sName) . ' and try again.';
                $sMessage .= '</small>';
            }

            $oErrorHandler = Factory::service('ErrorHandler');
            $oErrorHandler->renderErrorView(
                '401',
                [
                    'sSubject' => $sSubject,
                    'sMessage' => $sMessage,
                ]
            );
            exit();
        }

        $sMessage = $sMessage ?: 'Sorry, you need to be logged in to see that page.';
        $oSession = Factory::service('Session', 'nailsapp/module-auth');
        $oInput   = Factory::service('Input');
        $oSession->set_flashdata('message', $sMessage);

        if ($oInput->server('REQUEST_URI')) {
            $sReturn = $oInput->server('REQUEST_URI');
        } elseif (uri_string()) {
            $sReturn = uri_string();
        } else {
            $sReturn = '';
        }

        $sReturn = $sReturn ? '?return_to=' . urlencode($sReturn) : '';

        redirect('auth/login' . $sReturn);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('unauthorised')) {

    /**
     * Alias of show_401
     *
     * @param  string $sMessage A message to show users when redirected
     *
     * @return void
     */
    function unauthorised($sMessage = '')
    {
        show_401($sMessage);
    }
}

// --------------------------------------------------------------------------

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
