<?php

/**
 * Array helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

use InvalidArgumentException;
use Nails\Common\Exception\NailsException;

/**
 * Class ArrayHelper
 *
 * @package Nails\Common\Helper
 */
class ArrayHelper
{
    /**
     * Retrieve a value from $sArray at $sKey, if it exists
     *
     * @param string|int|array $mKey     The key to get, if an array is passed the first valid key will be returned
     * @param array            $aArray   The array to look in
     * @param mixed            $mDefault What to return if $sKey doesn't exist in $aArray
     *
     * @return mixed
     */
    public static function get($mKey, array $aArray, $mDefault = null)
    {
        $aKeys = (array) $mKey;
        foreach ($aKeys as $sKey) {
            if (array_key_exists($sKey, $aArray)) {
                return $aArray[$sKey];
            }
        }

        return $mDefault;
    }

    // --------------------------------------------------------------------------

    /**
     * Removes duplicate items from a multi-dimensional array
     * Hat-tip: http://phpdevblog.niknovo.com/2009/01/using-array-unique-with-multidimensional-arrays.html
     *
     * @param array $aArray The array to filter
     *
     * @return array
     */
    public static function arrayUniqueMulti(array $aArray)
    {
        // Unique Array for return
        $aArrayRewrite = [];

        // Array with the md5 hashes
        $aArrayHashes = [];

        foreach ($aArray as $key => $item) {

            // Serialize the current element and create a md5 hash
            $hash = md5(serialize($item));

            /**
             * If the md5 didn't come up yet, add the element to to arrayRewrite,
             * otherwise drop it
             */

            if (!isset($aArrayHashes[$hash])) {

                // Save the current element hash
                $aArrayHashes[$hash] = $hash;

                // Add element to the unique Array
                $aArrayRewrite[$key] = $item;
            }
        }

        unset($aArrayHashes);
        unset($key);
        unset($item);
        unset($hash);

        return $aArrayRewrite;
    }

    // --------------------------------------------------------------------------

    /**
     * Sorts a multi dimensional array
     *
     * @param array  &$aArray The array to sort
     * @param string  $sField The key to sort on
     */
    public static function arraySortMulti(array &$aArray, $sField)
    {
        uasort($aArray, function ($a, $b) use ($sField) {

            $oA = (object) $a;
            $oB = (object) $b;

            if (strpos($sField, '.') !== false) {

                //  Check validity of variable name
                $aKey = array_map(
                    function ($sKey) {
                        if (preg_match('/[^a-zA-Z0-9_\-]/', $sKey)) {
                            throw new InvalidArgumentException(
                                sprintf(
                                    '"%s" contains invalid characters',
                                    $sKey
                                )
                            );
                        }
                        return $sKey;
                    },
                    explode('.', $sField)
                );

                $sKey = implode('->', $aKey);
                $mA   = strtolower(eval('return $oA->' . $sKey . ' ?? null;'));
                $mB   = strtolower(eval('return $oB->' . $sKey . ' ?? null;'));

            } else {
                $mA = property_exists($oA, $sField) ? strtolower($oA->$sField) : null;
                $mB = property_exists($oB, $sField) ? strtolower($oB->$sField) : null;
            }

            //  Equal?
            if ($mA == $mB) {
                return 0;
            }

            //  If $mA is a prefix of $mB then $mA comes first
            if (preg_match('/^' . preg_quote($mA, '/') . '/', $mB)) {
                return -1;
            }

            //  Not equal, work out which takes precedence
            $aSort = [$mA, $mB];
            sort($aSort);

            return $aSort[0] == $mA ? -1 : 1;
        });
    }

    // --------------------------------------------------------------------------

    /**
     * Searches a multi-dimensional array
     *
     * @param string $sValue Search value
     * @param string $sKey   Key to search
     * @param array  $aArray The array to search
     *
     * @return mixed         The array key on success, false on failure
     */
    public static function arraySearchMulti($sValue, $sKey, array $aArray)
    {
        foreach ($aArray as $k => $val) {

            if (is_array($val)) {

                if ($val[$sKey] == $sValue) {
                    return $k;
                }

            } elseif (is_object($val)) {
                if ($val->$sKey == $sValue) {
                    return $k;
                }
            }
        }
        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Filters a multi-dimensional array based on a specific key/property
     *
     * @param string|int    $mKey    The key/property to analyse
     * @param array         $aArray  The array to filter
     * @param callable|null $cFilter The callable to test
     *
     * @return array
     * @throws NailsException
     */
    public static function arrayFilterMulti($mKey, array $aArray, callable $cFilter = null): array
    {
        if ($cFilter === null) {
            // Filter out empty values by default
            $cFilter = function ($mVal) {
                return !empty($mVal);
            };
        }

        foreach ($aArray as $k => &$val) {

            if (is_array($val)) {

                if (!array_key_exists($mKey, $val)) {
                    throw new NailsException('"' . $mKey . '" does not exist on item at index "' . $k . '"');
                }

                if (!$cFilter($val[$mKey])) {
                    $val = null;
                }

            } elseif (is_object($val)) {

                if (!property_exists($val, $mKey)) {
                    throw new NailsException('"' . $mKey . '" does not exist on item at index "' . $k . '"');
                }

                if (!$cFilter($val->{$mKey})) {
                    $val = null;
                }
            }
        }

        return array_filter($aArray);
    }

    // --------------------------------------------------------------------------

    /**
     * Test if an array contains value(s)
     *
     * @param string|array $aValues The values to check for
     * @param array        $aArray  The array to search
     *
     * @return bool
     */
    public static function inArray($aValues, array $aArray): bool
    {
        if (is_string($aValues)) {
            $aValues = (array) $aValues;
        }

        foreach ($aValues as $sValue) {
            if (in_array($sValue, $aArray)) {
                return true;
            }
        }
        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Reports whether a value exists in a multi dimensional array
     *
     * @param string $sValue The value to search for
     * @param string $sKey   The key to search on
     * @param array  $aArray The array to search
     *
     * @return boolean
     */
    public static function inArrayMulti($sValue, $sKey, array $aArray): bool
    {
        return static::arraySearchMulti($sValue, $sKey, $aArray) !== false;
    }

    // --------------------------------------------------------------------------

    /**
     * Extracts the value of properties from a multi-dimensional array into an array of those values
     *
     * @param array  $aInput    The array to iterate over
     * @param string $sProperty The property to extract
     *
     * @return array
     */
    public static function extract(array $aInput, $sProperty)
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

    // --------------------------------------------------------------------------

    /**
     * Flattens a multi-deimensional object into a flat array with dot notation for keys
     *
     * @param mixed $mInput The object to iterate over
     *
     * @return array
     */
    public static function arrayFlattenWithDotNotation($mInput): array
    {
        $oIterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($mInput));
        $aResult   = [];

        foreach ($oIterator as $mLeafValue) {
            $aKeys = [];
            foreach (range(0, $oIterator->getDepth()) as $iDepth) {
                $aKeys[] = $oIterator->getSubIterator($iDepth)->key();
            }
            $aResult[join('.', $aKeys)] = $mLeafValue;
        }

        return $aResult;
    }

    // --------------------------------------------------------------------------

    /**
     * Read an array or object using dot notation
     *
     * @param array|object $mIterable An item to traverse
     * @param string       $sPath     The item's path
     *
     * @return mixed
     */
    public static function dot($mIterable, string $sPath)
    {
        if (empty($sPath)) {
            return $mIterable;
        }

        if (!is_array($mIterable)) {
            $mIterable = (array) $mIterable;
        }

        if (is_array($mIterable) && array_key_exists($sPath, $mIterable)) {
            return $mIterable[$sPath];
        }

        $aSegments = explode('.', $sPath);
        $sSegment  = array_shift($aSegments);

        if (array_key_exists($sSegment, $mIterable)) {
            if (count($aSegments) === 0) {
                return $mIterable[$sSegment];
            } else {
                return static::dot($mIterable[$sSegment], implode('.', $aSegments));
            }
        }

        return null;
    }
}
