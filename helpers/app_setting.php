<?php

/**
 * This file provides app setting related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

if (!function_exists('appSetting')) {

    /**
     * Helper for quickly accessing app settings
     * @return  mixed
     */
    function appSetting($key = null, $grouping = 'app', $force_refresh = false)
    {
        $oAppSettingService = Factory::service('AppSetting');
        return $oAppSettingService->get($key, $grouping, $force_refresh);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('setAppSetting')) {

    /**
     * Helper for quickly setting app settings
     * @return  void
     */
    function setAppSetting($key, $grouping = 'app', $value = null, $encrypt = false)
    {
        $oAppSettingService = Factory::service('AppSetting');
        return $oAppSettingService->set($key, $grouping, $value, $encrypt);
    }
}
