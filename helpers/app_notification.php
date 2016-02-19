<?php

/**
 * This file provides app notification related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

if (!function_exists('appNotification')) {

    /**
     * Get's emails associated with a particular group/key
     * @param  string  $key          The key to retrieve
     * @param  string  $grouping     The group the key belongs to
     * @param  boolean $forceRefresh Whether to force a group refresh
     * @return array
     */
    function appNotification($key = null, $grouping = 'app', $forceRefresh = false)
    {
        $oAppNotificationModel = Factory::model('AppNotification');
        return $oAppNotificationModel->get($key, $grouping, $forceRefresh);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('appNotificationNotify')) {

    /**
     * Sends a notification to the email addresses associated with a particular key/grouping
     * @param  string $key      The key to send to
     * @param  string $grouping The key's grouping
     * @param  array  $data     An array of values to pass to the email template
     * @param  array  $override Override any of the definition values (this time only). Useful for defining custom email templates etc.
     * @return boolean
     */
    function appNotificationNotify($key = null, $grouping = 'app', $data = array(), $override = array())
    {
        $oAppNotificationModel = Factory::model('AppNotification');
        return $oAppNotificationModel->notify($key, $grouping, $data, $override);
    }
}

