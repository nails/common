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
    /**
     * Removes duplicate items from a multi-dimensional array
     * Hat-tip: http://phpdevblog.niknovo.com/2009/01/using-array-unique-with-multidimensional-arrays.html
     *
     * @param  array $aArray The array to filter
     *
     * @return array
     */
    function arrayUniqueMulti(array $aArray)
    {
        return ArrayHelper::arrayUniqueMulti($aArray);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('array_unique_multi')) {
    /**
     * Alias of arrayUniqueMulti()
     *
     * @param  array $aArray The array to filter
     *
     * @deprecated
     * @see ArrayHelper::arrayUniqueMulti()
     *
     * @return array
     */
    function array_unique_multi(array &$aArray)
    {
        deprecatedError(__METHOD__, 'arrayUniqueMulti');
        return ArrayHelper::arrayUniqueMulti($aArray);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('arraySortMulti')) {

    /**
     * Sorts a multi dimensional array
     *
     * @param  array  &$aArray The array to sort
     * @param  string  $sField The key to sort on
     */
    function arraySortMulti(array &$aArray, $sField)
    {
        ArrayHelper::arraySortMulti($aArray, $sField);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('array_sort_multi')) {

    /**
     * Alias of arraySortMulti()
     *
     * @param  array  &$aArray The array to sort
     * @param  string  $sField The key to sort on
     *
     * @deprecated
     * @see ArrayHelper::arraySortMulti()
     */
    function array_sort_multi(array &$aArray, $sField)
    {
        deprecatedError(__METHOD__, 'arraySortMulti');
        ArrayHelper::arraySortMulti($aArray, $sField);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('arraySearchMulti')) {

    /**
     * Searches a multi-dimensional array
     *
     * @param  string $sValue Search value
     * @param  string $sKey   Key to search
     * @param  array  $aArray The array to search
     *
     * @return mixed         The array key on success, false on failure
     */
    function arraySearchMulti($sValue, $sKey, array $aArray)
    {
        return ArrayHelper::arraySearchMulti($sValue, $sKey, $aArray);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('array_search_multi')) {

    /**
     * Searches a multi-dimensional array
     *
     * @param  string $sValue Search value
     * @param  string $sKey   Key to search
     * @param  array  $aArray The array to search
     *
     * @deprecated
     * @see ArrayHelper::arraySearchMulti()
     *
     * @return mixed The array key on success, false on failure
     */
    function array_search_multi($sValue, $sKey, array $aArray)
    {
        deprecatedError(__METHOD__, 'arraySearchMulti');
        return ArrayHelper::arraySearchMulti($sValue, $sKey, $aArray);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('inArrayMulti')) {

    /**
     * Reports whether a value exists in a multi dimensional array
     *
     * @param  string $sValue The value to search for
     * @param  string $sKey   The key to search on
     * @param  array  $aArray The array to search
     *
     * @return boolean
     */
    function inArrayMulti($sValue, $sKey, array $aArray)
    {
        return ArrayHelper::inArrayMulti($sValue, $sKey, $aArray);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('in_array_multi')) {

    /**
     * Alias of inArrayMulti()
     *
     * @param  string $sValue The value to search for
     * @param  string $sKey   The key to search on
     * @param  array  $aArray The array to search
     *
     * @deprecated
     * @see ArrayHelper::inArrayMulti()
     *
     * @return boolean
     */
    function in_array_multi($sValue, $sKey, array $aArray)
    {
        deprecatedError(__METHOD__, 'inArrayMulti');
        return ArrayHelper::inArrayMulti($sValue, $sKey, $aArray);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('arrayExtractProperty')) {

    /**
     * Extracts the value of properties from a multi-dimensional array into an array of those values
     *
     * @param array  $aInput    The array to iterate over
     * @param string $sProperty The property to extract
     *
     * @return array
     */
    function arrayExtractProperty(array $aInput, $sProperty)
    {
        return ArrayHelper::arrayExtractProperty($aInput, $sProperty);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/array_helper.php';
