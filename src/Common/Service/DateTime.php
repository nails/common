<?php

/**
 * Gets datetime formats and provides a convenient mechanism for converting timestamps between datetime zones
 *
 * @package     Nails
 * @subpackage  common
 * @category    service
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Factory;

class DateTime
{
    /**
     * The default timezone for users (defaults to system timezone)
     *
     * @var string
     */
    const TIMEZONE_DEFAULT = null;

    /**
     * The default date format
     *
     * @var string
     */
    const FORMAT_DATE_DEFAULT = 'DD/MM/YYYY';

    /**
     * The various date formats
     *
     * @var array
     */
    const FORMAT_DATE = [
        [
            'slug'   => 'DD-MMM-YYYY',
            'label'  => 'DD MMM YYYY',
            'format' => 'jS M Y',
        ],
        [
            'slug'   => 'DD-MMMM-YYYY',
            'label'  => 'DD MMMM YYYY',
            'format' => 'jS F Y',
        ],
        [
            'slug'   => 'DD/MM/YYYY',
            'label'  => 'DD/MM/YYYY',
            'format' => 'd/m/Y',
        ],
        [
            'slug'   => 'DD-MM-YYYY',
            'label'  => 'DD-MM-YYYY',
            'format' => 'd-m-Y',
        ],
        [
            'slug'   => 'DD/MM/YY',
            'label'  => 'DD/MM/YY',
            'format' => 'd/m/y',
        ],
        [
            'slug'   => 'DD-MM-YY',
            'label'  => 'DD-MM-YY',
            'format' => 'd-m-y',
        ],
        [
            'slug'   => 'MM/DD/YYYY',
            'label'  => 'MM/DD/YYYY',
            'format' => 'm/d/Y',
        ],
        [
            'slug'   => 'MM-DD-YYYY',
            'label'  => 'MM-DD-YYYY',
            'format' => 'm-d-Y',
        ],
        [
            'slug'   => 'MM/DD/YY',
            'label'  => 'MM/DD/YY',
            'format' => 'm/d/y',
        ],
        [
            'slug'   => 'MM-DD-YY',
            'label'  => 'MM-DD-YY',
            'format' => 'm-d-y',
        ],
    ];

    /**
     * The default time format
     *
     * @var string
     */
    const FORMAT_TIME_DEFAULT = '24H';

    /**
     * The various time formats
     *
     * @var array
     */
    const FORMAT_TIME = [
        [
            'slug'   => '24H',
            'label'  => '24 Hour',
            'format' => 'H:i:s',
        ],
        [
            'slug'   => '12H',
            'label'  => '12 Hour',
            'format' => 'g:i:s A',
        ],
    ];

    // --------------------------------------------------------------------------

    protected $sTimezoneNails;
    protected $sTimezoneUser;
    protected $sUserFormatDate;
    protected $sUserFormatTime;

    // --------------------------------------------------------------------------

    /**
     * DateTime constructor.
     */
    public function __construct()
    {
        $this->setTimezones();
        $this->setTimeFormat();
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default date format object
     *
     * @return \stdClass|bool
     */
    public function getDateFormatDefault()
    {
        return $this->getDateFormatBySlug(static::FORMAT_DATE_DEFAULT) ?: false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default date format's slug
     *
     * @return string|bool
     */
    public function getDateFormatDefaultSlug()
    {
        $oFormat = $this->getDateFormatDefault();
        return !empty($oFormat) ? $oFormat->slug : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default date format's label
     *
     * @return string|bool
     */
    public function getDateFormatDefaultLabel()
    {
        $oFormat = $this->getDateFormatDefault();
        return !empty($oFormat) ? $oFormat->label : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default date format's format
     *
     * @return string|bool
     */
    public function getDateFormatDefaultFormat()
    {
        $oFormat = $this->getDateFormatDefault();
        return !empty($oFormat) ? $oFormat->format : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the defined date format objects
     *
     * @return array
     */
    public function getAllDateFormat()
    {
        $aFormats = static::FORMAT_DATE;
        $oNow     = Factory::factory('DateTime');
        foreach ($aFormats as &$aFormat) {
            $aFormat          = (object) $aFormat;
            $aFormat->example = $oNow->format($aFormat->format);
        }
        return $aFormats;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the date format objects as a flat array
     *
     * @return array
     */
    public function getAllDateFormatFlat()
    {
        $aOut     = [];
        $aFormats = $this->getAllDateFormat();

        foreach ($aFormats as $oFormat) {
            $aOut[$oFormat->slug] = $oFormat->example;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for a date format by it's slug
     *
     * @param  string $sSlug The slug to search for
     *
     * @return \stdClass|null
     */
    public function getDateFormatBySlug($sSlug)
    {
        $aFormats = $this->getAllDateFormat();
        foreach ($aFormats as $oFormat) {
            if ($oFormat->slug === $sSlug) {
                return $oFormat;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default time format object
     *
     * @return \stdClass
     */
    public function getTimeFormatDefault()
    {
        return $this->getTimeFormatBySlug(static::FORMAT_TIME_DEFAULT) ?: false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default time format's slug
     *
     * @return string|bool
     */
    public function getTimeFormatDefaultSlug()
    {
        $oFormat = $this->getTimeFormatDefault();
        return !empty($oFormat) ? $oFormat->slug : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default time format's label
     *
     * @return string|bool
     */
    public function getTimeFormatDefaultLabel()
    {
        $oFormat = $this->getTimeFormatDefault();
        return !empty($oFormat) ? $oFormat->label : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default time format's format
     *
     * @return string|bool
     */
    public function getTimeFormatDefaultFormat()
    {
        $oFormat = $this->getTimeFormatDefault();
        return !empty($oFormat) ? $oFormat->format : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the defined time format objects
     *
     * @return array
     */
    public function getAllTimeFormat()
    {
        $aFormats = static::FORMAT_TIME;

        foreach ($aFormats as &$aFormat) {
            $aFormat          = (object) $aFormat;
            $oDateTimeObject  = $this->convert(time(), $this->sTimezoneUser);
            $aFormat->example = $oDateTimeObject->format($aFormat->format);
        }

        return $aFormats;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the time format objects as a flat array
     *
     * @return array
     */
    public function getAllTimeFormatFlat()
    {
        $aOut     = [];
        $aFormats = $this->getAllTimeFormat();

        foreach ($aFormats as $oFormat) {
            $aOut[$oFormat->slug] = $oFormat->label;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for a time format by it's slug
     *
     * @param  string $sSlug The slug to search for
     *
     * @return \stdClass|null
     */
    public function getTimeFormatBySlug($sSlug)
    {
        $aFormats = $this->getAllTimeFormat();

        foreach ($aFormats as $oFormat) {
            if ($oFormat->slug === $sSlug) {
                return $oFormat;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Set both the date and the time format at the same time
     *
     * @param string $sDateSlug The date format's slug
     * @param string $sTimeSlug The time format's slug
     */
    public function setFormats($sDateSlug, $sTimeSlug)
    {
        $this->setDateFormat($sDateSlug);
        $this->setTimeFormat($sTimeSlug);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the date format to use, uses default if slug cannot be found
     *
     * @param string $sSlug The date format's slug
     */
    public function setDateFormat($sSlug)
    {
        $oDateFormat = $this->getDateFormatBySlug($sSlug);
        if (empty($oDateFormat)) {
            $oDateFormat = $this->getDateFormatDefault();
        }

        $this->sUserFormatDate = $oDateFormat->format;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the time format to use, uses default if slug cannot be found
     *
     * @param string $sSlug The time format's slug
     */
    public function setTimeFormat($sSlug = null)
    {
        $oTimeFormat = $this->getTimeFormatBySlug($sSlug);
        if (empty($oTimeFormat)) {
            $oTimeFormat = $this->getTimeFormatDefault();
        }

        $this->sUserFormatTime = $oTimeFormat->format;
    }

    // --------------------------------------------------------------------------

    /**
     * Convert a date timestamp to the User's timezone from the Nails timezone
     *
     * @param  mixed  $mTimestamp The timestamp to convert
     * @param  string $sFormat    The format of the timestamp to return, defaults to User's date preference
     *
     * @return string
     */
    public function toUserDate($mTimestamp = null, $sFormat = null)
    {
        $oConverted = $this->convert($mTimestamp, $this->sTimezoneUser, $this->sTimezoneNails);

        if (is_null($oConverted)) {
            return null;
        }

        if (is_null($sFormat)) {
            $sFormat = $this->sUserFormatDate;
        }

        return $oConverted->format($sFormat);
    }

    // --------------------------------------------------------------------------

    /**
     * Convert a date timestamp to the Nails timezone from the User's timezone, formatted as Y-m-d
     *
     * @param  mixed $mTimestamp The timestamp to convert
     *
     * @return string|null
     */
    public function toNailsDate($mTimestamp = null)
    {
        $oConverted = $this->convert($mTimestamp, $this->sTimezoneNails, $this->sTimezoneUser);

        if (is_null($oConverted)) {
            return null;
        }

        return $oConverted->format('Y-m-d');
    }

    // --------------------------------------------------------------------------

    /**
     * Convert a datetime timestamp to the user's timezone from the Nails timezone
     *
     * @param  mixed  $mTimestamp The timestamp to convert
     * @param  string $sFormat    The format of the timestamp to return, defaults to User's dateTime preference
     *
     * @return string|null
     */
    public function toUserDatetime($mTimestamp = null, $sFormat = null)
    {
        $oConverted = $this->convert($mTimestamp, $this->sTimezoneUser, $this->sTimezoneNails);

        if (is_null($oConverted)) {
            return null;
        }

        if (is_null($sFormat)) {
            $sFormat = $this->sUserFormatDate . ' ' . $this->sUserFormatTime;
        }

        return $oConverted->format($sFormat);
    }

    // --------------------------------------------------------------------------

    /**
     * Convert a datetime timestamp to the Nails timezone from the User's timezone
     *
     * @param  mixed $mTimestamp The timestamp to convert
     *
     * @return string|null
     */
    public function toNailsDatetime($mTimestamp = null)
    {
        $oConverted = $this->convert($mTimestamp, $this->sTimezoneNails, $this->sTimezoneUser);

        if (is_null($oConverted)) {
            return null;
        }

        return $oConverted->format('Y-m-d H:i:s');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default timezone
     *
     * @return string
     */
    public function getTimezoneDefault()
    {
        return static::TIMEZONE_DEFAULT ?: date_default_timezone_get();
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the Nails and User timezones simultaneously
     *
     * @param string $sTzNails The Nails timezone
     * @param string $sTzUser  The User's timezone
     */
    public function setTimezones($sTzNails = null, $sTzUser = null)
    {
        $this->setNailsTimezone($sTzNails);
        $this->setUserTimezone($sTzUser);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the Nails timezone
     *
     * @param string $sTimezone The timezone to set
     */
    public function setNailsTimezone($sTimezone = null)
    {
        $this->sTimezoneNails = $sTimezone ?: $this->getTimezoneDefault();
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the Nails timezone
     *
     * @return string
     */
    public function getNailsTimezone()
    {
        return $this->sTimezoneNails;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the User's timezone
     *
     * @param string $sTimezone The timezone to set
     */
    public function setUserTimezone($sTimezone = null)
    {
        $this->sTimezoneUser = $sTimezone ?: $this->getTimezoneDefault();
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the User's timezone
     *
     * @return string
     */
    public function getUserTimezone()
    {
        return $this->sTimezoneUser;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a multi-dimensional array of supported timezones
     *
     * @return array
     */
    public function getAllTimezone()
    {
        //  Hat-tip to: https://gist.github.com/serverdensity/82576
        $aZones     = \DateTimeZone::listIdentifiers();
        $aLocations = ['UTC' => 'Coordinated Universal Time (UTC/GMT)'];

        foreach ($aZones as $sZone) {

            // 0 => Continent, 1 => City
            $aZoneExploded   = explode('/', $sZone);
            $aZoneAcceptable = [
                'Africa',
                'America',
                'Antarctica',
                'Arctic',
                'Asia',
                'Atlantic',
                'Australia',
                'Europe',
                'Indian',
                'Pacific',
            ];

            // Only use "friendly" continent names
            if (in_array($aZoneExploded[0], $aZoneAcceptable)) {
                if (isset($aZoneExploded[1]) != '') {

                    $sArea = str_replace('_', ' ', $aZoneExploded[1]);

                    if (!empty($aZoneExploded[2])) {
                        $sArea = $sArea . ' (' . str_replace('_', ' ', $aZoneExploded[2]) . ')';
                    }

                    // Creates array(DateTimeZone => 'Friendly name')
                    $aLocations[$aZoneExploded[0]][$sZone] = $sArea;
                }
            }
        }

        return $aLocations;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the supported timezones as a flat array
     *
     * @return array
     */
    public function getAllTimezoneFlat()
    {
        $aTimezones = $this->getAllTimezone();
        $aOut       = [];

        foreach ($aTimezones as $sKey => $mValue) {
            if (is_array($mValue)) {
                foreach ($mValue as $subKey => $subValue) {
                    if (is_string($subValue)) {
                        $aOut[$subKey] = $sKey . ' - ' . $subValue;
                    }
                }
            } else {
                $aOut[$sKey] = $mValue;
            }
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Converts a datetime into a human friendly relative string
     *
     * @param  mixed   $mDate                  The timestamp to convert
     * @param  boolean $bIncludeTense          Whether or not to append the tense (e.g, X minutes _ago_)
     * @param  string  $sMessageBadDate        The message to show if a bad timestamp is supplied
     * @param  string  $sMessageGreaterOneWeek The message to show if the timestamp is greater than one week away
     * @param  string  $sMessageLessTenMinutes The message to show if the timestamp is less than ten minutes away
     *
     * @return string
     */
    public static function niceTime(
        $mDate = false,
        $bIncludeTense = true,
        $sMessageBadDate = null,
        $sMessageGreaterOneWeek = null,
        $sMessageLessTenMinutes = null
    ) {
        if (empty($mDate) || $mDate == '0000-00-00') {
            if ($sMessageBadDate) {
                return $sMessageBadDate;
            } else {
                return 'No date supplied';
            }
        }

        $aPeriods = ['second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade'];
        $aLengths = [60, 60, 24, 7, '4.35', 12, 10];
        $sNow     = Factory::factory('DateTime')->format('U');

        if (is_int($mDate)) {
            $iUnixTime = $mDate;
        } else {
            $iUnixTime = strtotime($mDate);
        }

        //  Check date supplied is valid
        if (empty($iUnixTime)) {
            if ($sMessageBadDate) {
                return $sMessageBadDate;
            } else {
                return 'Bad date supplied (' . $mDate . ')';
            }
        }

        //  If date is effectively null
        if ($mDate == '0000-00-00 00:00:00') {
            return 'Unknown';
        }

        //  Determine past or future date
        $sTense = '';
        if ($sNow >= $iUnixTime) {

            $iDifference = $sNow - $iUnixTime;
            if ($bIncludeTense) {
                $sTense = 'ago';
            }

        } else {

            $iDifference = $iUnixTime - $sNow;
            if ($bIncludeTense) {
                $sTense = 'from now';
            }
        }

        for ($i = 0; $iDifference >= $aLengths[$i] && $i < count($aLengths) - 1; $i++) {
            $iDifference /= $aLengths[$i];
        }

        $iDifference = round($iDifference);

        if ($iDifference != 1) {
            $aPeriods[$i] .= 's';
        }

        // If it's greater than 1 week and $sMessageGreaterOneWeek is defined, return that
        if (substr($aPeriods[$i], 0, 4) == 'week' && $sMessageGreaterOneWeek !== null) {
            return $sMessageGreaterOneWeek;
        }

        // If it's less than 20 seconds, return 'a moment ago'
        if (is_null($sMessageLessTenMinutes) && substr($aPeriods[$i], 0, 6) == 'second' && $iDifference <= 20) {
            return 'a moment ' . $sTense;
        }

        //  If $sMessageLessTenMinutes is set then return that if less than 10 minutes
        if (!is_null($sMessageLessTenMinutes)
            &&
            (
                (substr($aPeriods[$i], 0, 6) == 'minute' && $iDifference <= 10) ||
                (substr($aPeriods[$i], 0, 6) == 'second' && $iDifference <= 60)
            )
        ) {
            return $sMessageLessTenMinutes;
        }

        if ($iDifference . ' ' . $aPeriods[$i] . ' ' . $sTense == '1 day ago') {
            return 'yesterday';
        } elseif ($iDifference . ' ' . $aPeriods[$i] . ' ' . $sTense == '1 day from now') {
            return 'tomorrow';
        } else {
            return $iDifference . ' ' . $aPeriods[$i] . ' ' . $sTense;
        }
    }

    // --------------------------------------------------------------------------f

    /**
     * Get the timezone code from the timezone string
     *
     * @param  string $sTimezone The timezone, e.g. Europe/London
     *
     * @return string|false
     */
    public static function getCodeFromTimezone($sTimezone)
    {
        $aAbbreviations = \DateTimeZone::listAbbreviations();
        foreach ($aAbbreviations as $sCode => $aValues) {
            foreach ($aValues as $aValue) {
                if ($aValue['timezone_id'] == $sTimezone) {
                    return strtoupper($sCode);
                }
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the timezone string from the timezone code
     *
     * @param string $sCode The timezone code, e.g. GMT
     *
     * @return string|false
     */
    public static function getTimezoneFromCode($sCode)
    {
        $aAbbreviations = \DateTimeZone::listAbbreviations();
        foreach ($aAbbreviations as $sTzCode => $aValues) {
            if (strtolower($sCode) == $sTzCode) {
                $aTimeZone = reset($aValues);
                return getFromArray('timezone_id', $aTimeZone, false);
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Arbitrarily convert a timestamp between timezones
     *
     * @param  mixed  $mTimestamp The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @param  string $sToTz      The timezone to convert to
     * @param  string $sFromTz    The timezone to convert from
     *
     * @return \DateTime|null
     */
    public static function convert($mTimestamp, $sToTz, $sFromTz = 'UTC')
    {
        //  Has a specific timestamp been given?
        if (is_null($mTimestamp)) {

            $oDateTime = Factory::factory('DateTime');

        } elseif (is_numeric($mTimestamp)) {

            $oDateTime = Factory::factory('DateTime');
            $oDateTime->setTimestamp($mTimestamp);

        } elseif ($mTimestamp instanceof \DateTime) {

            $oDateTime = $mTimestamp;

        } elseif (!empty($mTimestamp) && $mTimestamp !== '0000-00-00' && $mTimestamp !== '0000-00-00 00:00:00') {

            $oDateTime = new \DateTime($mTimestamp);

        } else {
            return null;
        }

        // --------------------------------------------------------------------------

        //  Perform the conversion
        $oFromTz = new \DateTimeZone($sFromTz);
        $oToTz   = new \DateTimeZone($sToTz);
        $oOut    = new \DateTime($oDateTime->format('Y-m-d H:i:s'), $oFromTz);

        $oOut->setTimeZone($oToTz);

        return $oOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Calculates a person's age (or age at a certain date)
     *
     * @param string $birthYear  The year to calculate from
     * @param string $birthMonth The month to calculate from
     * @param string $birthDay   The day to calculate from
     * @param string $deathYear  The year to calculate to
     * @param string $deathMonth The month to calculate to
     * @param string $deathDay   The day to calculate to
     *
     * @return bool|float
     */
    public function calculateAge(
        $birthYear,
        $birthMonth,
        $birthDay,
        $deathYear = null,
        $deathMonth = null,
        $deathDay = null
    ) {
        //  Only calculate to a date which isn't today if all values are supplied
        if (is_null($deathYear) || is_null($deathMonth) || is_null($deathDay)) {
            $deathYear  = date('Y');
            $deathMonth = date('m');
            $deathDay   = date('d');
        }

        // --------------------------------------------------------------------------

        $_birth_time = mktime(0, 0, 0, $birthMonth, $birthDay, $birthYear);
        $_death_time = mktime(0, 0, 0, $deathMonth, $deathDay, $deathYear);

        // --------------------------------------------------------------------------

        //  If $_death_time is smaller than $_birth_time then something's wrong
        if ($_death_time < $_birth_time) {
            return false;
        }

        // --------------------------------------------------------------------------

        //  Calculate age
        $_age       = ($_birth_time < 0) ? ($_death_time + ($_birth_time * -1)) : $_death_time - $_birth_time;
        $_age_years = floor($_age / (31536000));    //  Divide by number of seconds in a year

        // --------------------------------------------------------------------------

        return $_age_years;
    }
}
