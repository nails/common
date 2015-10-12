<?php

/**
 * Gets datetime formats and provides a convinient mechanism for converting timestamps between datetime zones
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

class DateTime extends Base
{
    public $timezoneNails;
    public $timezoneUser;
    protected $userFormatDate;
    protected $userFormatTime;

    // --------------------------------------------------------------------------

    /**
     * Constructs the model and laods the date helper
     */
    public function __construct()
    {
        parent::__construct();
        $this->config->load('datetime');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default date format object
     * @return stdClass
     */
    public function getDateFormatDefault()
    {
        $default    = $this->config->item('datetime_format_date_default');
        $dateFormat = $this->getDateFormatBySlug($default);

        return !empty($dateFormat) ? $dateFormat : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default date format's slug
     * @return string
     */
    public function getDateFormatDefaultSlug()
    {
        $default = $this->getDateFormatDefault();
        return empty($default->slug) ? false : $default->slug;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default date format's label
     * @return string
     */
    public function getDateFormatDefaultLabel()
    {
        $default = $this->getDateFormatDefault();
        return empty($default->label) ? false : $default->label;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default date format's format
     * @return string
     */
    public function getDateFormatDefaultFormat()
    {
        $default = $this->getDateFormatDefault();
        return empty($default->format) ? false : $default->format;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the defined date format objects
     * @return array
     */
    public function getAllDateFormat()
    {
        $formats = $this->config->item('datetime_format_date');

        foreach ($formats as $format) {

            $format->example = date($format->format);
        }

        return $formats;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the date format objects as a flat array
     * @return array
     */
    public function getAllDateFormatFlat()
    {
        $out     = array();
        $formats = $this->getAllDateFormat();

        foreach ($formats as $format) {

            $out[$format->slug] = $format->label;
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for a date format by it's slug
     * @param  string $slug The slug to search for
     * @return mixed        stdClass on success, false on failure
     */
    public function getDateFormatBySlug($slug)
    {
        $formats = $this->getAllDateFormat();

        return !empty($formats[$slug]) ? $formats[$slug] : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default time format object
     * @return stdClass
     */
    public function getTimeFormatDefault()
    {
        $default    = $this->config->item('datetime_format_time_default');
        $timeFormat = $this->getTimeFormatBySlug($default);

        return !empty($timeFormat) ? $timeFormat : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default time format's slug
     * @return string
     */
    public function getTimeFormatDefaultSlug()
    {
        $default = $this->getTimeFormatDefault();
        return empty($default->slug) ? false : $default->slug;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default time format's label
     * @return string
     */
    public function getTimeFormatDefaultLabel()
    {
        $default = $this->getTimeFormatDefault();
        return empty($default->label) ? false : $default->label;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default time format's format
     * @return string
     */
    public function getTimeFormatDefaultFormat()
    {
        $default = $this->getTimeFormatDefault();
        return empty($default->format) ? false : $default->format;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the defined time format objects
     * @return array
     */
    public function getAllTimeFormat()
    {
        $formats = $this->config->item('datetime_format_time');

        if ($this->timezoneUser) {

            foreach ($formats as $format) {

                $dateTimeObject  = $this->convertDatetime(time(), $this->timezoneUser);
                $format->example = $dateTimeObject->format($format->format);
            }
        }

        return $formats;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the time format objects as a flat array
     * @return array
     */
    public function getAllTimeFormatFlat()
    {
        $out     = array();
        $formats = $this->getAllTimeFormat();

        foreach ($formats as $format) {

            $out[$format->slug] = $format->label;
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for a time format by it's slug
     * @param  string $slug The slug to search for
     * @return mixed        stdClass on success, false on failure
     */
    public function getTimeFormatBySlug($slug)
    {
        $formats = $this->getAllTimeFormat();
        return !empty($formats[$slug]) ? $formats[$slug] : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Set both the date and the time format at the same time
     * @param string $dateSlug The date format's slug
     * @param string $timeSlug The time format's slug
     */
    public function setFormats($dateSlug, $timeSlug)
    {
        $this->setDateFormat($dateSlug);
        $this->setTimeFormat($timeSlug);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the date format to use, uses default if slug cannot be found
     * @param string $slug The date format's slug
     */
    public function setDateFormat($slug)
    {
        $dateFormat = $this->getDateFormatBySlug($slug);

        if (empty($dateFormat)) {

            $dateFormat = $this->getDateFormatDefault();
        }

        $this->userFormatDate = $dateFormat->format;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the time format to use, uses default if slug cannot be found
     * @param string $slug The time format's slug
     */
    public function setTimeFormat($slug)
    {
        $timeFormat = $this->getTimeFormatBySlug($slug);

        if (empty($timeFormat)) {

            $timeFormat = $this->getTimeFormatDefault();
        }

        $this->userFormatTime = $timeFormat->format;
    }

    // --------------------------------------------------------------------------

    /**
     * Convert a date timestamp to the User's timezone from the Nails timezone
     * @param  mixed  $timestamp The timestamp to convert
     * @param  string $format    The format of the timestamp to return, defaults to User's date preference
     * @return string
     */
    public function toUserDate($timestamp = null, $format = null)
    {
        $converted = $this->convertDatetime($timestamp, $this->timezoneUser, $this->timezoneNails);

        if (is_null($format)) {

            $format = $this->userFormatDate;
        }

        return $converted->format($format);
    }

    // --------------------------------------------------------------------------

    /**
     * Convert a date timestamp to the Nails timezone from the User's timezone, formatted as Y-m-d
     * @param  mixed  $timestamp The timestamp to convert
     * @return string
     */
    public function toNailsDate($timestamp = null)
    {
        $converted = $this->convertDatetime($timestamp, $this->timezoneNails, $this->timezoneUser);
        return $converted->format('Y-m-d');
    }

    // --------------------------------------------------------------------------

    /**
     * Convert a datetime timestamp to the user's timezone from the Nails timezone
     * @param  mixed  $timestamp The timestamp to convert
     * @param  string $format    The format of the timestamp to return, defaults to User's dateTime preference
     * @return string
     */
    public function toUserDatetime($timestamp = null, $format = null)
    {
        $converted = $this->convertDatetime($timestamp, $this->timezoneUser, $this->timezoneNails);

        if (is_null($format)) {

            $format = $this->userFormatDate . ' ' . $this->userFormatTime;
        }

        return $converted->format($format);
    }

    // --------------------------------------------------------------------------

    /**
     * Convert a datetime timestamp to the Nails timezone from the User's timezone
     * @param  mixed  $timestamp The timestamp to convert
     * @return string
     */
    public function toNailsDatetime($timestamp = null)
    {
        $converted = $this->convertDatetime($timestamp, $this->timezoneNails, $this->timezoneUser);
        return $converted->format('Y-m-d H:i:s');
    }

    // --------------------------------------------------------------------------

    /**
     * Return's the default timezone
     * @return string
     */
    public function getTimezoneDefault()
    {
        $default = $this->config->item('datetime_timezone_default');

        if ($default) {

            return $default;

        } else {

            return date_default_timezone_get();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the Nails and User timezones simultaneously
     * @param string $tzNails The Nails timezone
     * @param string $tzUser  The User's timezone
     */
    public function setTimezones($tzNails, $tzUser)
    {
        $this->setNailsTimezone($tzNails);
        $this->setUserTimezone($tzUser);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the Nails timezone
     * @param string $tz The timezone to set
     */
    public function setNailsTimezone($tz)
    {
        $this->timezoneNails = $tz;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the User's timezone
     * @param string $tz The timezone to set
     */
    public function setUserTimezone($tz)
    {
        $this->timezoneUser = $tz;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a multi-dimensional array of supported timezones
     * @return array
     */
    public function getAllTimezone()
    {
        //  Hat-tip to: https://gist.github.com/serverdensity/82576
        $zones     = \DateTimeZone::listIdentifiers();
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

    /**
     * Returns all the supported timezones as a flat array
     * @return array
     */
    public function getAllTimezoneFlat()
    {
        $locations = $this->getAllTimezone();
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

    // --------------------------------------------------------------------------

    /**
     * Converts a datetime into a human friendly relative string
     * @param  mixed   $date           The timestamp to convert
     * @param  boolean $tense          Whether or not to append the tense (e.g, X minutes _ago_)
     * @param  string  $optBadMsg      The message to show if a bad timestamp is supplied
     * @param  string  $greaterOneWeek The message to show if the timestanmp is greater than one week away
     * @param  string  $lessTenMins    The message to show if the timestamp is less than ten minutes away
     * @return string
     */
    public static function niceTime($date = false, $tense = true, $optBadMsg = null, $greaterOneWeek = null, $lessTenMins = null)
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

        // If it's less than 20 seconds, return 'a moment ago'
        if (is_null($lessTenMins) && substr($periods[$j], 0, 6) == 'second' && $difference <=20) {

            return 'a moment ' . $tense;
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

    /**
     * Get the timezone code from the timezone string
     * @param  string $timezone The timezone, e.g. Europe/London
     * @return mixed            String on success, false on failure
     */
    public static function getCodeFromTimezone($timezone)
    {
        $abbreviations = DateTimeZone::listAbbreviations();

        foreach ($abbreviations as $code => $values) {

            foreach ($values as $v) {

                if ($v['timezone_id'] == $timezone) {

                    return strtoupper($code);
                }
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Arbitarially convert a timestamp between timezones
     * @param  mixed  $timestamp The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @param  string $toTz      The timezone to convert to
     * @param  string $fromTz    The timezone to convert from
     * @return string
     */
    public static function convertDatetime($timestamp, $toTz, $fromTz = 'UTC')
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
        $fromTz = new \DateTimeZone($fromTz);

        $out = \Nails\Factory::factory('DateTime');
        $out->modify($timestamp);

        //  Set the output timezone
        $toTz = new \DateTimeZone($toTz);
        $out->setTimeZone($toTz);

        return $out;
    }
}
