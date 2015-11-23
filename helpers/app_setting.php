<?php

use Nails\Factory;

if (!function_exists('appSetting')) {

    /**
     * Helper for quickly accessing app settings
     * @return  mixed
     */
    function appSetting($key = null, $grouping = 'app', $force_refresh = false)
    {
        $oAppSettingModel = Factory::model('AppSetting');
        return $oAppSettingModel->get($key, $grouping, $force_refresh);
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
        $oAppSettingModel = Factory::model('AppSetting');
        return $oAppSettingModel->set($key, $grouping, $value, $encrypt);
    }
}

