<?php

/**
 * This file provides miscallaeneous related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

if (!function_exists('map')) {

    /**
     * Re-maps a number from one range to another
     * See http://www.arduino.cc/en/Reference/Map
     *
     * @param   float   Number to map
     * @param   int     Current low
     * @param   int     Current high
     * @param   int     New low
     * @param   int     New high
     *
     * @return  float
     */
    function map($x, $in_min, $in_max, $out_min, $out_max)
    {
        return ($x - $in_min) * ($out_max - $out_min) / ($in_max - $in_min) + $out_min;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('special_chars')) {

    /**
     * Replaces special chars with their HTML counterpart
     *
     * @param   string  String to parse
     *
     * @return  float
     */
    function special_chars($string)
    {
        /* Only do the slow convert if there are 8-bit characters */
        /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
        if (!preg_match("/[\200-\237]/", $string) and !preg_match("/[\241-\377]/", $string)) {

            return $string;
        }

        // decode three byte unicode characters
        $string = preg_replace(
            "/([\340-\357])([\200-\277])([\200-\277])/e",
            "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",
            $string
        );

        // decode two byte unicode characters
        $string = preg_replace(
            "/([\300-\337])([\200-\277])/e",
            "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
            $string
        );

        return $string;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('formatBytes')) {

    /**
     * Formats a filesize given in bytes into a human-friendly string
     *
     * @param  integer $iBytes     The filesize, in bytes
     * @param  integer $iPrecision The precision to use
     *
     * @return string
     */
    function formatBytes($iBytes, $iPrecision = 2)
    {
        $oCdn = Factory::service('Cdn', 'nailsapp/module-cdn');
        return $oCdn->formatBytes($iBytes, $iPrecision);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('returnBytes')) {

    /**
     * Formats a filesize as bytes (e.g max_upload_size)
     * hat-tip: http://php.net/manual/en/function.ini-get.php#96996
     *
     * @param  string $sSize The string to convert to bytes
     *
     * @return integer
     */
    function returnBytes($sSize)
    {
        $oCdn = Factory::service('Cdn', 'nailsapp/module-cdn');
        return $oCdn->returnBytes($sSize);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('maxUploadSize')) {

    /**
     * Returns the configured maximum upload size for this system by inspecting
     * upload_max_filesize and post_max_size, if available.
     *
     * @param  boolean $bFormat Whether to format the string using formatBytes
     *
     * @return integer|string
     */
    function maxUploadSize($bFormat = true)
    {
        if (function_exists('ini_get')) {

            $aMaxSizes = [
                returnBytes(ini_get('upload_max_filesize')),
                returnBytes(ini_get('post_max_size')),
            ];

            $iMaxSize = min($aMaxSizes);

            return $bFormat ? formatBytes($iMaxSize) : $iMaxSize;

        } else {

            return null;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('stringToBoolean')) {

    /**
     * Converts a string to a boolean
     *
     * @param   string
     *
     * @return  float
     */
    function stringToBoolean($string)
    {
        if ($string && strtolower($string) !== 'false') {

            return true;

        } else {

            return false;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isIpInRange')) {

    /**
     * Determines whether an IP Address falls within a CIDR range
     *
     * @param $sIp    string The IP Address to test
     * @param $mRange mixed  The CIDR range, either as a string, or an array of ranges
     *
     * @return bool
     */
    function isIpInRange($sIp, $mRange)
    {
        if (!is_array($mRange)) {

            //  Prepare the range
            $mRangeRaw = $mRange;
            $mRangeRaw = str_replace("\n\r", "\n", $mRangeRaw);
            $aRangeRaw = explode("\n", $mRangeRaw);
            $aRange    = [];

            foreach ($aRangeRaw as $line) {
                $aRange = array_merge(explode(',', $line), $aRange);
            }

            $aRange = array_unique($aRange);
            $aRange = array_filter($aRange);
            $aRange = array_map('trim', $aRange);
            $aRange = array_values($aRange);

        } else {

            $aRange = $mRange;
        }

        foreach ($aRange as $sCIDRMask) {

            if (strpos($sCIDRMask, '/') !== false) {

                //  Hat tip: http://stackoverflow.com/a/594134/789224
                list ($sSubnet, $sBits) = explode('/', $sCIDRMask);

                $iBits   = (int) $sBits;
                $iIp     = ip2long($sIp);
                $sSubnet = ip2long($sSubnet);
                $iMask   = -1 << (32 - $iBits);
                $sSubnet &= $iMask; # nb: in case the supplied subnet wasn't correctly aligned

                if (($iIp & $iMask) == $sSubnet) {
                    return true;
                }

            } else {

                if ($sIp == $sCIDRMask) {
                    return true;
                }
            }
        }

        return false;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('nullIfEmpty')) {

    /**
     * Returns null if the input is empty, or the input if not
     *
     * @param   mixed $mVal The input to check
     *
     * @return  mixed
     */
    function nullIfEmpty($mVal)
    {
        return empty($mVal) ? null : $mVal;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('classImplements')) {

    /**
     * Checks if a class implements a particular interface
     *
     * @param object|string $mClass     The class to test, either as an object or a string
     * @param string        $sInterface The interface to look for
     *
     * @return bool
     */
    function classImplements($mClass, $sInterface)
    {
        return in_array(
            ltrim($sInterface, '\\'),
            class_implements($mClass)
        );
    }
}

// --------------------------------------------------------------------------

if (!function_exists('classUses')) {

    /**
     * Checks if a class uses a particular trait
     *
     * @param object|string $mClass The class to test, either as an object or a string
     * @param string        $sTrait The trait to look for
     *
     * @return bool
     */
    function classUses($mClass, $sTrait)
    {
        return in_array(
            ltrim($sTrait, '\\'),
            class_uses($mClass)
        );
    }
}
