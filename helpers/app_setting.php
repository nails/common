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
     * @param string|null $sKey          The key to retrieve
     * @param string      $sGrouping     The group the key belongs to
     * @param mixed       $mDefault      The default value to return if no setting is found
     * @param bool        $bForceRefresh Force a refresh of settings
     *
     * @return mixed
     * @throws FactoryException
     */
    function appSetting(string $sKey = null, string $sGrouping = 'app', $mDefault = null, $bForceRefresh = false)
    {
        /** @var AppSetting $oAppSettingService */
        $oAppSettingService = Factory::service('AppSetting');
        $oSetting           = $oAppSettingService->get($sKey, $sGrouping, $bForceRefresh);
        if (is_array($oSetting)) {
            return array_combine(
                array_map(function ($oSetting) {
                    return $oSetting->key;
                }, $oSetting),
                array_map(function ($oSetting) {
                    return $oSetting->getValue();
                }, $oSetting)
            );
        } else {
            return $oSetting ? $oSetting->getValue() : $mDefault;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('setAppSetting')) {

    /**
     * Helper for quickly setting app settings
     *
     * @param string|string[] $mKey      The key to set, or an array of key => value pairs
     * @param string          $sGrouping The grouping to store the keys under
     * @param mixed           $mValue    The data to store, only used if $mKey is a string
     * @param bool            $bEncrypt  Whether to encrypt the data or not
     *
     * @return bool
     * @throws FactoryException
     */
    function setAppSetting(string $sKey, string $sGrouping = 'app', $mValue = null, $bEncrypt = false): bool
    {
        /** @var AppSetting $oAppSettingService */
        $oAppSettingService = Factory::service('AppSetting');
        return $oAppSettingService->set($sKey, $sGrouping, $mValue, $bEncrypt);
    }
}
