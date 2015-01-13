<?php

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Datetime_model extends NAILS_Model
{
    public $timezone_nails;
    public $timezone_user;
    protected $formatDate;
    protected $formatTime;

    // --------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->config->load('datetime');
    }

    /**
     * DATE FORMAT
     * The Following methods deal with formatting dates
     */

    public function get_date_format_default()
    {
        $default    = $this->config->item('datetime_format_date_default');
        $dateFormat = $this->get_date_format_by_slug($default);

        return !empty($dateFormat) ? $dateFormat : false;
    }

    // --------------------------------------------------------------------------

    public function get_date_format_default_slug()
    {
        $default = $this->get_date_format_default();
        return empty($default->slug) ? false : $default->slug;
    }

    // --------------------------------------------------------------------------

    public function get_date_format_default_label()
    {
        $default = $this->get_date_format_default();
        return empty($default->label) ? false : $default->label;
    }

    // --------------------------------------------------------------------------

    public function get_date_format_default_format()
    {
        $default = $this->get_date_format_default();
        return empty($default->format) ? false : $default->format;
    }

    // --------------------------------------------------------------------------

    public function get_all_date_format()
    {
        $formats = $this->config->item('datetime_format_date');

        foreach ($formats as $format) {

            $format->example = date($format->format);
        }

        return $formats;
    }

    // --------------------------------------------------------------------------

    public function get_all_date_format_flat()
    {
        $out     = array();
        $formats = $this->get_all_date_format();

        foreach ($formats as $format) {

            $out[$format->slug] = $format->label;
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    public function get_date_format_by_slug($slug)
    {
        $formats = $this->get_all_date_format();

        return !empty($formats[$slug]) ? $formats[$slug] : false;
    }

    /**
     * TIME FORMAT
     * The Following methods deal with formatting times
     */

    public function get_time_format_default()
    {
        $default    = $this->config->item('datetime_format_time_default');
        $timeFormat = $this->get_time_format_by_slug($default);

        return !empty($timeFormat) ? $timeFormat : false;
    }

    // --------------------------------------------------------------------------

    public function get_time_format_default_slug()
    {
        $default = $this->get_time_format_default();
        return empty($default->slug) ? false : $default->slug;
    }

    // --------------------------------------------------------------------------

    public function get_time_format_default_label()
    {
        $default = $this->get_time_format_default();
        return empty($default->label) ? false : $default->label;
    }

    // --------------------------------------------------------------------------

    public function get_time_format_default_format()
    {
        $default = $this->get_time_format_default();
        return empty($default->format) ? false : $default->format;
    }

    // --------------------------------------------------------------------------

    public function get_all_time_format()
    {
        $formats = $this->config->item('datetime_format_time');

        if ($this->timezone_user) {

            foreach ($formats as $format) {

                $time = strtotime($this->convert_datetime(time(), $this->timezone_user));
                $format->example = date($format->format, $time);
            }
        }

        return $formats;
    }

    // --------------------------------------------------------------------------

    public function get_all_time_format_flat()
    {
        $out     = array();
        $formats = $this->get_all_time_format();

        foreach ($formats as $format) {

            $out[$format->slug] = $format->label;
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    public function get_time_format_by_slug($slug)
    {
        $formats = $this->get_all_time_format();
        return !empty($formats[$slug]) ? $formats[$slug] : false;
    }

    /**
     * GENERIC FORMAT METHODS
     */

    public function set_formats($dateSlug, $timeSlug)
    {
        $this->set_date_format($dateSlug);
        $this->set_time_format($timeSlug);
    }

    // --------------------------------------------------------------------------

    public function set_date_format($slug)
    {
        $dateFormat = $this->get_date_format_by_slug($slug);

        if (empty($dateFormat)) {

            $dateFormat = $this->get_date_format_default();
        }

        $this->_format_date = $dateFormat->format;
    }

    // --------------------------------------------------------------------------

    public function set_time_format($slug)
    {
        $timeFormat = $this->get_time_format_by_slug($slug);

        if (empty($timeFormat)) {

            $timeFormat = $this->get_time_format_default();
        }

        $this->_format_time = $timeFormat->format;
    }

    /**
     * USER METHODS
     */

    public function user_date($timestamp = null, $formatDate = null)
    {
        //  Has a specific timestamp been given?
        if (is_null($timestamp)) {

            $timestamp = date('Y-m-d');

        } else {

            //  Are we dealing with a UNIX timestamp or a datetime?
            if (!is_numeric($timestamp)) {

                if (!$timestamp || $timestamp == '0000-00-00') {

                    return '';
                }

                $timestamp = date('Y-m-d', strtotime($timestamp));

            } else {

                if (!$timestamp) {

                    return '';
                }

                $timestamp = date('Y-m-d', $timestamp);
            }
        }

        // --------------------------------------------------------------------------

        //  Has a date/time format been supplied? If so overwrite the defaults
        $formatDate = is_null($formatDate) ? $this->_format_date : $formatDate;

        // --------------------------------------------------------------------------

        //  Create the new DateTime object
        $datetime = new DateTime($timestamp, new DateTimeZone($this->timezone_nails));

        // --------------------------------------------------------------------------

        //  If the user's timezone is different from the Nails. timezone then set it so.
        if ($this->timezone_nails != $this->timezone_user) {

            $datetime->setTimeZone(new DateTimeZone($this->timezone_user));
        }

        // --------------------------------------------------------------------------

        //  Return the formatted date
        return $datetime->format($formatDate);
    }

    // --------------------------------------------------------------------------

    public function user_rdate($timestamp = null, $format = 'date')
    {
        //  Has a specific timestamp been given?
        if (is_null($timestamp)) {

            $timestamp = date('Y-m-d H:i:s');

        } else {

            $format = $format == 'date' ? 'Y-m-d' : 'Y-m-d H:i:s';

            //  Are we dealing with a UNIX timestamp or a datetime?
            if (!is_numeric($timestamp)) {

                $timestamp = date($format, strtotime($timestamp));

            } else {

                $timestamp = date($format, $timestamp);
            }
        }

        // --------------------------------------------------------------------------

        //  Create the new DateTime object
        $datetime = new DateTime($timestamp, new DateTimeZone($this->timezone_user));

        // --------------------------------------------------------------------------

        //  If the user's timezone is different from the Nails. timezone then set it so.
        if ($this->timezone_nails != $this->timezone_user) {

            $datetime->setTimeZone(new DateTimeZone($this->timezone_nails));
        }

        // --------------------------------------------------------------------------

        //  Return the formatted date
        return $format == 'date' ? $datetime->format('Y-m-d') : $datetime->format('Y-m-d H:i:s');
    }

    // --------------------------------------------------------------------------

    public function user_datetime($timestamp = null, $formatDate = null, $formatTime = null)
    {
        //  Has a specific timestamp been given?
        if (is_null($timestamp)) {

            $timestamp = date('Y-m-d H:i:s');

        } else {

            //  Are we dealing with a UNIX timestamp or a datetime?
            if ($timestamp && !is_numeric($timestamp)) {

                if (!$timestamp || $timestamp == '0000-00-00 00:00:00') {

                    return '';
                }

                $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));

            } else {

                if (!$timestamp) {

                    return '';
                }

                $timestamp = date('Y-m-d H:i:s', $timestamp);
            }
        }

        // --------------------------------------------------------------------------

        //  Has a date/time format been supplied? If so overwrite the defaults
        $formatDate = is_null($formatDate) ? $this->_format_date : $formatDate;
        $formatTime = is_null($formatTime) ? $this->_format_time : $formatTime;

        // --------------------------------------------------------------------------

        //  Create the new DateTime object
        $datetime = new DateTime($timestamp, new DateTimeZone($this->timezone_nails));

        // --------------------------------------------------------------------------

        //  If the user's timezone is different from the Nails. timezone then set it so.
        if ($this->timezone_nails != $this->timezone_user) {

            $datetime->setTimeZone(new DateTimeZone($this->timezone_user));
        }

        // --------------------------------------------------------------------------

        //  Return the formatted date
        return $datetime->format($formatDate . ' ' . $formatTime);
    }

    // --------------------------------------------------------------------------

    public function user_rdatetime($timestamp = null, $formatDate = null, $formatTime = null)
    {
        //  Has a specific timestamp been given?
        if (is_null($timestamp)) {

            $timestamp = date('Y-m-d H:i:s');

        } else {

            //  Are we dealing with a UNIX timestamp or a datetime?
            if ($timestamp && !is_numeric($timestamp)) {

                if (!$timestamp || $timestamp == '0000-00-00 00:00:00') {

                    return '';
                }

                $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));

            } else {

                if (!$timestamp) {

                    return '';
                }

                $timestamp = date('Y-m-d H:i:s', $timestamp);
            }
        }

        // --------------------------------------------------------------------------

        //  Has a date/time format been supplied? If so overwrite the defaults
        $formatDate = is_null($formatDate) ? $this->_format_date : $formatDate;
        $formatTime = is_null($formatTime) ? $this->_format_time : $formatTime;

        // --------------------------------------------------------------------------

        //  Create the new DateTime object
        $datetime = new DateTime($timestamp, new DateTimeZone($this->timezone_user));

        // --------------------------------------------------------------------------

        //  If the user's timezone is different from the Nails. timezone then set it so.
        if ($this->timezone_nails != $this->timezone_user) {

            $datetime->setTimeZone(new DateTimeZone($this->timezone_nails));
        }

        // --------------------------------------------------------------------------

        //  Return the formatted date
        return $datetime->format($formatDate . ' ' . $formatTime);
    }

    /**
     * TIMEZONE METHODS
     */

    public function get_timezone_default()
    {
        $default = $this->config->item('datetime_timezone_default');

        if ($default) {

            return $default;

        } else {

            return date_default_timezone_get();
        }
    }

    // --------------------------------------------------------------------------

    public function set_timezones($tzNails, $tzUser)
    {
        $this->set_nails_timezone($tzNails);
        $this->set_user_timezone($tzUser);
    }

    // --------------------------------------------------------------------------

    public function set_nails_timezone($tz)
    {
        $this->timezone_nails = $tz;
    }

    // --------------------------------------------------------------------------

    public function set_user_timezone($tz)
    {
        $this->timezone_user = $tz;
    }

    // --------------------------------------------------------------------------

    public function get_all_timezone()
    {
        //  Hat-tip to: https://gist.github.com/serverdensity/82576
        $zones     = DateTimeZone::listIdentifiers();
        $locations = array('UTC' => 'Coordinated Universal Time (UTC/GMT)');

        foreach ($zones as $zone) {

            // 0 => Continent, 1 => City
            $zoneExploded = explode('/', $zone);

            $zoneAcceptable   = array();
            $zoneAcceptable[] = 'Africa';
            $zoneAcceptable[] = 'America';
            $zoneAcceptable[] = 'Antarctica';
            $zoneAcceptable[] = 'Arctic';
            $zoneAcceptable[] = 'Asia';
            $zoneAcceptable[] = 'Atlantic';
            $zoneAcceptable[] = 'Australia';
            $zoneAcceptable[] = 'Europe';
            $zoneAcceptable[] = 'Indian';
            $zoneAcceptable[] = 'Pacific';

            // Only use "friendly" continent names
            if (in_array($zoneExploded[0], $zoneAcceptable)) {

                if (isset($zoneExploded[1]) != '') {

                    $area = str_replace('_', ' ', $zoneExploded[1]);

                    if (!empty($zoneExploded[2])) {

                        $area = $area . ' (' . str_replace('_', ' ', $zoneExploded[2]) . ')';
                    }

                    // Creates array(DateTimeZone => 'Friendly name')
                    $locations[$zoneExploded[0]][$zone] = $area;
                }
            }
        }

        return $locations;
    }

    // --------------------------------------------------------------------------

    public function get_all_timezone_flat()
    {
        $locations = $this->get_all_timezone();
        $out       = array();

        foreach ($locations as $key => $value) {

            if (is_array($value)) {

                foreach ($value as $subKey => $subValue) {

                    if (is_string($subValue)) {

                        $out[$subKey] = $subValue;
                    }
                }

            } else {

                $out[$key] = $value;
            }

        }

        return $out;
    }

    /**
     * OTHER METHODS
     */

    public static function nice_time($date = false, $tense = true, $optBadMsg = null, $greaterOneWeek = null, $lessTenMins = null)
    {
        if (empty($date) || $date == '0000-00-00') {

            if ($optBadMsg) {

                return $optBadMsg;

            } else {

                return 'No date supplied';
            }
        }

        $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
        $lengths = array(60,60,24,7,'4.35', 12, 10);
        $now     = time();

        if (is_int($date)) {

            $unix_date = $date;

        } else {

            $unix_date = strtotime($date);
        }

        //  Check date supplied is valid
        if (empty($unix_date)) {

            if ($optBadMsg) {

                return $optBadMsg;

            } else {

                return 'Bad date supplied ('.$date.')';
            }
        }

        //  If date is effectively null
        if ($date == '0000-00-00 00:00:00') {

            return 'Unknown';
        }

        //  Determine past or future date
        if ($now >= $unix_date) {

            $difference = $now - $unix_date;

            if ($tense === true) {

                $tense = 'ago';
            }

        } else {

            $difference = $unix_date - $now;

            if ($tense === true) {

                $tense = 'from now';
            }
        }

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {

            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if ($difference != 1) {

            $periods[$j] .= 's';
        }

        // If it's greater than 1 week and $greaterOneWeek is defined, return that
        if (substr($periods[$j], 0, 4) == 'week' && $greaterOneWeek !== null) {

            return $greaterOneWeek;
        }

        // If it's less than 20 seconds, return 'Just now'
        if (is_null($lessTenMins) && substr($periods[$j], 0, 6) == 'second' && $difference <=20) {

            return 'a moment ago';
        }

        //  If $lessTenMins is set then return that if less than 10 minutes
        if (!is_null($lessTenMins)
                &&
                (
                    (substr($periods[$j], 0, 6) == 'minute' && $difference <= 10) ||
                    (substr($periods[$j], 0, 6) == 'second' && $difference <= 60)
                )
            ) {

            return $lessTenMins;
        }

        if ($difference . ' ' . $periods[$j] . ' ' . $tense == '1 day ago') {

            return 'yesterday';

        } elseif ($difference . ' ' . $periods[$j] . ' ' . $tense == '1 day from now') {

            return 'tomorrow';

        } else {

            return $difference . ' ' . $periods[$j] . ' ' . $tense;
        }
    }

    // --------------------------------------------------------------------------

    public static function get_code_from_timezone($timezone)
    {
        $abbreviations = DateTimeZone::listAbbreviations();

        foreach ($abbreviations as $code => $values) {

            foreach ($values as $v) {

                if ($v['timezone_id'] == $timezone) {

                    return strtoupper($code);
                }
            }
        }
    }

    /**
     * CONVERSION METHODS
     */

    public static function convert_datetime($timestamp, $toTz, $fromTz = 'UTC')
    {
        //  Has a specific timestamp been given?
        if (is_null($timestamp)) {

            $timestamp = date('Y-m-d H:i:s');

        } else {

            //  Are we dealing with a UNIX timestamp or a datetime?
            if (!is_numeric($timestamp)) {

                if (!$timestamp || $timestamp == '0000-00-00') {

                    return '';
                }

                $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));

            } else {

                if (!$timestamp) {

                    return '';
                }

                $timestamp = date('Y-m-d H:i:s', $timestamp);
            }
        }

        // --------------------------------------------------------------------------

        //  Perform the conversion
        $fromTz = new DateTimeZone($fromTz);
        $out    = new Datetime($timestamp, $fromTz);

        //  Set the output timezone
        $toTz = new DateTimeZone($toTz);
        $out->setTimeZone($toTz);

        return $out->format('Y-m-d H:i:s');
    }
}

// --------------------------------------------------------------------------

/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core
 * models. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if (!defined('NAILS_ALLOW_EXTENSION_DATETIME_MODEL')) {

    class Datetime_model extends NAILS_Datetime_model
    {
    }
}
