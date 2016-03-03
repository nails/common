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

if (!function_exists('map')) {

    /**
     * Re-maps a number from one range to another
     * See http://www.arduino.cc/en/Reference/Map
     * @param   float   Number to map
     * @param   int     Current low
     * @param   int     Current high
     * @param   int     New low
     * @param   int     New high
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
     * @param   string  String to parse
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
     * @param  integer $iBytes     The filesize, in bytes
     * @param  integer $iPrecision The precision to use
     * @return string
     */
    function formatBytes($iBytes, $iPrecision = 2)
    {
        $oCdn = nailsFactory('service', 'Cdn', 'nailsapp/module-cdn');
        return $oCdn->formatBytes($iBytes, $iPrecision);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('returnBytes')) {

    /**
     * Formats a filesize as bytes (e.g max_upload_size)
     * hat-tip: http://php.net/manual/en/function.ini-get.php#96996
     * @param  string $sSize The string to convert to bytes
     * @return integer
     */
    function returnBytes($sSize)
    {
        $oCdn = nailsFactory('service', 'Cdn', 'nailsapp/module-cdn');
        return $oCdn->returnBytes($sSize);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('maxUploadSize')) {

    /**
     * Returns the configured maximum upload size for this system by inspecting
     * upload_max_filesize and post_max_size, if available.
     * @param  boolean $bFormat Whether to format the string using formatBytes
     * @return integer|string
     */
    function maxUploadSize($bFormat = true)
    {
        if (function_exists('ini_get')) {

            $aMaxSizes = array(
                returnBytes(ini_get('upload_max_filesize')),
                returnBytes(ini_get('post_max_size'))
            );

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
     * @param   string
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
     * Match an IP to a given CIDR range
     * @param   string
     * @return  boolean
     */
    function isIpInRange($ip, $range)
    {
        if (!is_array($range)) {

            //  Prepare the range
            $range_raw = $range;
            $range_raw = str_replace("\n\r", "\n", $range_raw);
            $range_raw = explode("\n", $range_raw);
            $range     = array();

            foreach ($range_raw as $line) {

                $range = array_merge(explode(',', $line), $range);
            }

            $range = array_unique($range);
            $range = array_filter($range);
            $range = array_map('trim', $range);
            $range = array_values($range);

        } else {

            $range = $range;
        }

        foreach ($range as $cidr_mask) {

            if (strpos($cidr_mask, '/') !== false) {

                //  Hat tip: http://stackoverflow.com/a/594134/789224
                list ($subnet, $bits) = explode('/', $cidr_mask);
                $ip     = ip2long($ip);
                $subnet = ip2long($subnet);
                $mask   = -1 << (32 - $bits);
                $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned

                if (($ip & $mask) == $subnet) {
                    return true;
                }

            } else {

                if ($ip == $cidr_mask) {
                    return true;
                }
            }
        }

        return false;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('readFileChunked')) {

    /**
     * Outputs a file in bytesized chunks.
     * http://teddy.fr/2007/11/28/how-serve-big-files-through-php/
     * @param  string  $filename  The file to output
     * @param  integer $chunkSize The chunk size, in bytes
     * @return mixed              Ineger on success, false on failure
     */
    function readFileChunked($filename, $chunkSize = 1048576)
    {
        $bytesRead = 0;

        // $handle = fopen($filename, "rb");
        $handle = fopen($filename, 'rb');
        if ($handle === false) {

            return false;
        }

        while (!feof($handle)) {

            $buffer = fread($handle, $chunkSize);
            echo $buffer;

            $bytesRead += strlen($buffer);
        }

        $status = fclose($handle);

        if ($status) {

            return $bytesRead;

        } else {

            return false;
        }
    }
}
