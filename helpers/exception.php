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
     * @param  string $message A message to show users when redirected
     *
     * @return void
     */
    function show_401($message = '<strong>Sorry,</strong> you need to be logged in to see that page.')
    {
        /**
         * Logged in users can't be redirected to log in, they simply get
         * an unauthorised page
         */

        if (isLoggedIn()) {

            $title   = 'Sorry, you are not authorised to view this page';
            $message = 'The page you are trying to view is restricted. Sadly you don\'t have enough ';
            $message .= 'permissions to see it\'s content.';

            if (wasAdmin()) {

                $adminRecoveryData = getAdminRecoveryData();

                $message .= '<br /><br />';
                $message .= '<small>';
                $message .= 'However, it looks like you\'re logged in as someone else.';
                $message .= '<br />' . anchor($adminRecoveryData->loginUrl, 'Log back in as ' . $adminRecoveryData->name) . ' and try again.';
                $message .= '</small>';
            }

            show_error($message, 401, $title, false);
        }

        $oSession = Factory::service('Session', 'nailsapp/module-auth');
        $oInput   = Factory::service('Input');
        $oSession->set_flashdata('message', $message);

        if ($oInput->server('REQUEST_URI')) {
            $return = $oInput->server('REQUEST_URI');
        } elseif (uri_string()) {
            $return = uri_string();
        } else {
            $return = '';
        }

        $return = $return ? '?return_to=' . urlencode($return) : '';

        redirect('auth/login' . $return);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('unauthorised')) {

    /**
     * Alias of show_401
     *
     * @param  string $message A message to show users when redirected
     *
     * @return void
     */
    function unauthorised($message = '<strong>Sorry,</strong> you need to be logged in to see that page.')
    {
        show_401($message);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('showFatalError')) {
    /**
     * Renders the fatal error screen and alerts developers
     *
     * @param  string $subject The subject of the developer alert
     * @param  string $message The body of the developer alert
     *
     * @return void
     */
    function showFatalError($subject = '', $message = '')
    {
        if (is_callable('\Nails\Common\Library\ErrorHandler::showFatalErrorScreen')) {

            if (is_callable('\Nails\Common\Library\ErrorHandler::sendDeveloperMail') && (!empty($subject) || !empty($message))) {
                \Nails\Common\Library\ErrorHandler::sendDeveloperMail($subject, $message);
            }

            \Nails\Common\Library\ErrorHandler::showFatalErrorScreen($subject, $message);

        } elseif (function_exists('_NAILS_ERROR')) {

            _NAILS_ERROR($message, $subject);

        } else {

            echo '<h1>ERROR: ' . $subject . '</h1>';
            echo '<h2>' . $message . '</h2>';
            exit(0);
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('sendDeveloperMail')) {

    /**
     * Quickly send a high priority email via mail() to the APP_DEVELOPER
     *
     * @param  string $subject The email's subject
     * @param  string $message The email's body
     *
     * @return boolean
     */
    function sendDeveloperMail($subject, $message)
    {
        if (is_callable('\Nails\Common\Library\ErrorHandler::sendDeveloperMail')) {
            return \Nails\Common\Library\ErrorHandler::sendDeveloperMail($subject, $message);
        } else {
            return false;
        }
    }
}
