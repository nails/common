<?php

if (!function_exists('map'))
{
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

if (!function_exists('special_chars'))
{
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
        $string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",
        "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",
        $string);

        // decode two byte unicode characters
        $string = preg_replace("/([\300-\337])([\200-\277])/e",
        "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
        $string);

        return $string;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('format_bytes'))
{
    /**
     * Format a filesize in bytes, kilobytes, megabytes, etc...
     * @param   string
     * @return  float
     */
    function format_bytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);

        //  Uncomment one of the following alternatives
        //$bytes /= pow(1024, $pow);
        $bytes /= (1 << (10 * $pow));

        $var = round($bytes, $precision) . ' ' . $units[$pow];
        $pattern = '/(.+?)\.(.*?)/';

        return preg_replace_callback($pattern, function($matches) {

            return number_format($matches[1]) . '.' . $matches[2];
        }, $var);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('return_bytes'))
{
    /**
     * Formats a filesize as bytes (e.g max_upload_size)
     * hat-tip: http://php.net/manual/en/function.ini-get.php#96996
     * @param   string
     * @return  float
     */
    function return_bytes($sizeStr)
    {
        switch (strtoupper(substr($sizeStr, -1))) {

            case 'M':

                $return = (int) $sizeStr * 1048576;
                break;

            case 'K':

                $return = (int) $sizeStr * 1024;
                break;

            case 'G':

                $return = (int) $sizeStr * 1073741824;
                break;

            default:

                $return = $sizeStr;
                break;
        }

        return $return;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('stringToBoolean'))
{
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

if (!function_exists('isIpInRange'))
{
    /**
     * Match an IP to a given CIDR range
     * @param   string
     * @return  boolean
     */
    function isIpInRange($ip, $range)
    {
        if (!array($range)) {

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


if (!function_exists('_db_flush_caches'))
{
    /**
     * Flushes DB caches
     * @return void
     */
    function _db_flush_caches()
    {
        $ci =& get_instance();

        if (isset($ci->db)) {

            $ci->db->queries     = array();
            $ci->db->query_times = array();
            $ci->db->data_cache  = array();
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_db_reset_active_record'))
{
    /**
     * Resets Active Record values
     * @return void
     */
    function _db_reset_active_record()
    {
        $ci = get_instance();

        if (isset($ci->db)) {

            $ci->db->ar_select         = array();
            $ci->db->ar_from           = array();
            $ci->db->ar_join           = array();
            $ci->db->ar_where          = array();
            $ci->db->ar_like           = array();
            $ci->db->ar_groupby        = array();
            $ci->db->ar_having         = array();
            $ci->db->ar_orderby        = array();
            $ci->db->ar_wherein        = array();
            $ci->db->ar_aliased_tables = array();
            $ci->db->ar_no_escape      = array();
            $ci->db->ar_distinct       = false;
            $ci->db->ar_limit          = false;
            $ci->db->ar_offset         = false;
            $ci->db->ar_order          = false;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('readFileChunked'))
{
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