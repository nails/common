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

use Nails\Common\Exception\FactoryException;
use Nails\Common\Service\AppSetting;
use Nails\Factory;

if (!function_exists('appSetting')) {

    /**
     * Helper for quickly accessing app settings
     *
     * @param null   $key
     * @param string $grouping
     * @param bool   $force_refresh
     *
     * @return mixed
     * @throws FactoryException
     */
    function appSetting($key = null, $grouping = 'app', $force_refresh = false)
    {
        /** @var AppSetting $oAppSettingService */
        $oAppSettingService = Factory::service('AppSetting');
        return $oAppSettingService->get($key, $grouping, $force_refresh);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('setAppSetting')) {

    /**
     * Helper for quickly setting app settings
     *
     * @param        $key
     * @param string $grouping
     * @param null   $value
     * @param bool   $encrypt
     *
     * @return bool
     * @throws FactoryException
     */
    function setAppSetting($key, $grouping = 'app', $value = null, $encrypt = false)
    {
        /** @var AppSetting $oAppSettingService */
        $oAppSettingService = Factory::service('AppSetting');
        return $oAppSettingService->set($key, $grouping, $value, $encrypt);
    }
}
