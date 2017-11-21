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

if (!function_exists('array_unique_multi')) {

    /**
     * Removes duplicate items from a multi-dimensional array
     * Hat-tip: http://phpdevblog.niknovo.com/2009/01/using-array-unique-with-multidimensional-arrays.html
     *
     * @param  array  $array The array to filter
     * @return array
     */
    function array_unique_multi(array $array)
    {
        // Unique Array for return
        $arrayRewrite = array();

        // Array with the md5 hashes
        $arrayHashes = array();

        foreach ($array as $key => $item) {

            // Serialize the current element and create a md5 hash
            $hash = md5(serialize($item));

            /**
             * If the md5 didn't come up yet, add the element to to arrayRewrite,
             * otherwise drop it
             */

            if (!isset($arrayHashes[$hash])) {

                // Save the current element hash
                $arrayHashes[$hash] = $hash;

                // Add element to the unique Array
                $arrayRewrite[$key] = $item;
            }
        }

        unset($arrayHashes);
        unset($key);
        unset($item);
        unset($hash);

        return $arrayRewrite;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('array_sort_multi')) {

    /**
     * Sorts a multi dimensional array
     * @param  array  &$array The array to sort
     * @param  string $field  The key to sort on
     * @return void
     */
    function array_sort_multi(array &$array, $field)
    {
        uasort($array, function ($a, $b) use ($field) {

            $a = (object) $a;
            $b = (object) $b;

            //  Equal?
            if ($a->$field == $b->$field) {
                return 0;
            }

            //  If $a is a prefix of $b then $a comes first
            $sPattern = '/^' . preg_quote($a->$field, '/') . '/';
            if (preg_match($sPattern, $b->$field)) {
                return -1;
            }

            //  Not equal, work out which takes precedence
            $_sort = array($a->$field, $b->$field);
            sort($_sort);

            return $_sort[0] == $a->$field ? -1 : 1;
        });
    }
}

// --------------------------------------------------------------------------

if (!function_exists('array_search_multi')) {

    /**
     * Searches a multi-dimensional array
     * @param  string $value Search value
     * @param  string $key   Key to search
     * @param  array  $array The array to search
     * @return mixed         The array key on success, false on failure
     */
    function array_search_multi($value, $key, array $array)
    {
        foreach ($array as $k => $val) {

            if (is_array($val)) {

                if ($val[$key] == $value) {
                    return $k;
                }

            } elseif (is_object($val)) {

                if ($val->$key == $value) {
                    return $k;
                }
            }
        }
        return false;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('in_array_multi')) {

    /**
     * Reports whether a value exists in a multi dimensional array
     * @param  string $value The value to search for
     * @param  string $key   The key to search on
     * @param  array  $array The array to search
     * @return boolean
     */
    function in_array_multi($value, $key, array $array)
    {
        return array_search_multi($value, $key, $array) !== false;
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
        $aOutput = [];
        foreach ($aInput as $mItem) {
            $aItem = (array) $mItem;
            if (array_key_exists($sProperty, $aItem)) {
                $aOutput[] = $aItem[$sProperty];
            }
        }
        return $aOutput;
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include FCPATH . 'vendor/rogeriopradoj/codeigniter/system/helpers/array_helper.php';
