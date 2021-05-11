<?php

/**
 * This file provides array related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Common\Helper\ArrayHelper;

if (!function_exists('arrayUniqueMulti')) {
    function arrayUniqueMulti(array $aArray)
    {
        return ArrayHelper::arrayUniqueMulti($aArray);
    }
}

if (!function_exists('array_unique_multi')) {
    function array_unique_multi(array &$aArray)
    {
        deprecatedError(__METHOD__, 'arrayUniqueMulti');
        return ArrayHelper::arrayUniqueMulti($aArray);
    }
}

if (!function_exists('arraySortMulti')) {
    function arraySortMulti(array &$aArray, $sField)
    {
        ArrayHelper::arraySortMulti($aArray, $sField);
    }
}

if (!function_exists('array_sort_multi')) {
    function array_sort_multi(array &$aArray, $sField)
    {
        deprecatedError(__METHOD__, 'arraySortMulti');
        ArrayHelper::arraySortMulti($aArray, $sField);
    }
}

if (!function_exists('arraySearchMulti')) {
    function arraySearchMulti($sValue, $sKey, array $aArray)
    {
        return ArrayHelper::arraySearchMulti($sValue, $sKey, $aArray);
    }
}

if (!function_exists('array_search_multi')) {
    function array_search_multi($sValue, $sKey, array $aArray)
    {
        deprecatedError(__METHOD__, 'arraySearchMulti');
        return ArrayHelper::arraySearchMulti($sValue, $sKey, $aArray);
    }
}

if (!function_exists('arrayFilterMulti')) {
    function arrayFilterMulti($sKey, array $aArray, callable $cFilter = null)
    {
        return ArrayHelper::arrayFilterMulti($sKey, $aArray, $cFilter);
    }
}

if (!function_exists('inArray')) {
    function inArray($aValues, array $aArray): bool
    {
        return ArrayHelper::inArray($aValues, $aArray);
    }
}

if (!function_exists('inArrayMulti')) {
    function inArrayMulti($sValue, $sKey, array $aArray): bool
    {
        return ArrayHelper::inArrayMulti($sValue, $sKey, $aArray);
    }
}

if (!function_exists('in_array_multi')) {
    function in_array_multi($sValue, $sKey, array $aArray): bool
    {
        deprecatedError(__METHOD__, 'inArrayMulti');
        return ArrayHelper::inArrayMulti($sValue, $sKey, $aArray);
    }
}

if (!function_exists('arrayExtractProperty')) {
    function arrayExtractProperty(array $aInput, $sProperty)
    {
        return ArrayHelper::extract($aInput, $sProperty);
    }
}

if (!function_exists('arrayFlattenWithDotNotation')) {
    function arrayFlattenWithDotNotation($mInput)
    {
        return ArrayHelper::arrayFlattenWithDotNotation($mInput);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/array_helper.php';
