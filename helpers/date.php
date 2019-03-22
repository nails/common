<?php

/**
 * This file provides date related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

if (!function_exists('convertDateTime')) {

    /**
     * Arbitrarily convert a timestamp between timezones
     *
     * @param  mixed  $mTimestamp The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @param  string $sToTz      The timezone to convert to
     * @param  string $sFromTz    The timezone to convert from
     *
     * @return string
     */
    function convertDateTime($mTimestamp, $sToTz, $sFromTz = 'UTC')
    {
        $oDateTimeService = Factory::service('DateTime');
        return $oDateTimeService->convert($mTimestamp, $sToTz, $sFromTz);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toUserDate')) {

    /**
     * Convert a date timestamp to the User's timezone from the Nails timezone
     *
     * @param  mixed  $mTimestamp The timestamp to convert
     * @param  string $sFormat    The format of the timestamp to return, defaults to User's date preference
     *
     * @return string
     */
    function toUserDate($mTimestamp = null, $sFormat = null)
    {
        $oDateTimeService = Factory::service('DateTime');
        return $oDateTimeService->toUserDate($mTimestamp, $sFormat);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toNailsDate')) {

    /**
     * Convert a date timestamp to the Nails timezone from the User's timezone
     *
     * @param  mixed $mTimestamp The timestamp to convert
     *
     * @return string
     */
    function toNailsDate($mTimestamp = null)
    {
        $oDateTimeService = Factory::service('DateTime');
        return $oDateTimeService->toNailsDate($mTimestamp);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toUserDatetime')) {

    /**
     * Convert a datetime timestamp to the user's timezone from the Nails timezone
     *
     * @param  mixed  $mTimestamp The timestamp to convert
     * @param  string $sFormat    The format of the timestamp to return, defaults to User's dateTime preference
     *
     * @return string
     */
    function toUserDatetime($mTimestamp = null, $sFormat = null)
    {
        $oDateTimeService = Factory::service('DateTime');
        return $oDateTimeService->toUserDatetime($mTimestamp, $sFormat);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toNailsDatetime')) {

    /**
     * Convert a datetime timestamp to the Nails timezone from the User's timezone
     *
     * @param  mixed $mTimestamp The timestamp to convert
     *
     * @return string
     */
    function toNailsDatetime($mTimestamp = null)
    {
        $oDateTimeService = Factory::service('DateTime');
        return $oDateTimeService->toNailsDatetime($mTimestamp);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('niceTime')) {

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
    function niceTime(
        $mDate = false,
        $bIncludeTense = true,
        $sMessageBadDate = null,
        $sMessageGreaterOneWeek = null,
        $sMessageLessTenMinutes = null
    ) {
        $oDateTimeService = Factory::service('DateTime');
        return $oDateTimeService->niceTime(
            $mDate,
            $bIncludeTense,
            $sMessageBadDate,
            $sMessageGreaterOneWeek,
            $sMessageLessTenMinutes);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('calculateAge')) {

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
    function calculateAge($birthYear, $birthMonth, $birthDay, $deathYear = null, $deathMonth = null, $deathDay = null)
    {
        $oDateTimeService = Factory::service('DateTime');
        return $oDateTimeService->calculateAge($birthYear, $birthMonth, $birthDay, $deathYear, $deathMonth, $deathDay);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dropdownYears')) {

    /**
     * Generates a dropdown containing the values between $iStartYear and $iEndYear
     *
     * @param string  $sFieldName   The name to give the field
     * @param integer $iStartYear   The first year to list
     * @param integer $iEndYear     The last year to list
     * @param integer $iSelected    The year to select
     * @param string  $sPlaceholder The placeholder value
     *
     * @return string
     */
    function dropdownYears($sFieldName, $iStartYear = null, $iEndYear = null, $iSelected = null, $sPlaceholder = '')
    {
        /*** defaults ***/
        $iStartYear = is_null($iStartYear) ? date('Y') : $iStartYear;
        $iEndYear   = is_null($iEndYear) ? $iStartYear - 10 : $iEndYear;

        /*** the current year ***/
        $iSelected = is_null($iSelected) ? date('Y') : $iSelected;

        /*** range of years ***/
        $aRange = range($iStartYear, $iEndYear);

        /*** create the select ***/
        $sOut = '<select name="' . $sFieldName . '" id="' . $sFieldName . '">';
        $sOut .= "<option value=\"0000\"";
        $sOut .= !$iSelected ? ' selected="selected"' : '';
        $sOut .= ">" . ($sPlaceholder ? $sPlaceholder : '-') . "</option>\n";

        foreach ($aRange as $year) {
            $sOut .= "<option value=\"$year\"";
            $sOut .= $year == $iSelected ? ' selected="selected"' : '';
            $sOut .= ">$year</option>\n";
        }

        $sOut .= '</select>';

        return $sOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dropdownMonths')) {

    /**
     * Generates a dropdown containing the months of the year
     *
     * @param string  $sFieldName   The name to give the field
     * @param bool    $bShort       Wheteher to use short or long month names
     * @param integer $iSelected    The month to select
     * @param string  $sPlaceholder The placeholder value
     *
     * @return string
     */
    function dropdownMonths($sFieldName, $bShort = false, $iSelected = null, $sPlaceholder = '')
    {
        /*** array of months ***/
        $aMonthsShort = [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'May',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Aug',
            9  => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        $aMonthsLong = [
            1  => 'January',
            2  => 'February',
            3  => 'March',
            4  => 'April',
            5  => 'May',
            6  => 'June',
            7  => 'July',
            8  => 'August',
            9  => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $aMonths = $bShort === false ? $aMonthsLong : $aMonthsShort;

        /*** current month ***/
        $iSelected = is_null($iSelected) ? date('n') : $iSelected;

        $sOut = '<select name="' . $sFieldName . '" id="' . $sFieldName . '">' . "\n";
        $sOut .= "<option value=\"00\"";
        $sOut .= !$iSelected ? ' selected="selected"' : '';
        $sOut .= ">" . ($sPlaceholder ? $sPlaceholder : '-') . "</option>\n";

        foreach ($aMonths as $iKey => $sMonth) {
            $sOut .= "<option value=\"" . str_pad($iKey, 2, '0', STR_PAD_LEFT) . "\"";
            $sOut .= $iKey == $iSelected ? ' selected="selected"' : '';
            $sOut .= ">$sMonth</option>\n";
        }

        $sOut .= '</select>';

        return $sOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dropdownDays')) {

    /**
     * @param string  $sFieldName   The name to give the field
     * @param integer $iSelected    The day to select
     * @param string  $sPlaceholder The placeholder value
     *
     * @return string
     */
    function dropdownDays($sFieldName, $iSelected = null, $sPlaceholder = '')
    {
        /*** range of days ***/
        $aRange = range(1, 31);

        /*** current day ***/
        $iSelected = is_null($iSelected) ? date('j') : $iSelected;

        $sOut = '<select name="' . $sFieldName . '" id="' . $sFieldName . '">' . "\n";

        $sOut .= "<option value=\"00\"";
        $sOut .= !$iSelected ? ' selected="selected"' : '';
        $sOut .= ">" . ($sPlaceholder ? $sPlaceholder : '-') . "</option>\n";

        foreach ($aRange as $iDay) {
            $sOut .= "<option value=\"" . str_pad($iDay, 2, '0', STR_PAD_LEFT) . "\"";
            $sOut .= $iDay == $iSelected ? ' selected="selected"' : '';
            $sOut .= ">" . str_pad($iDay, 2, '0', STR_PAD_LEFT) . "</option>\n";
        }

        $sOut .= '</select>';

        return $sOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dropdownHours')) {

    /**
     * Generates a dropdown containing the numbers 0 - 23
     *
     * @param string  $sFieldName The name to give the field
     * @param integer $iSelected  The hour to select
     *
     * @return string
     */
    function dropdownHours($sFieldName, $iSelected = null)
    {
        /*** range of hours ***/
        $aRange = range(0, 23);

        /*** current hour ***/
        $iSelected = is_null($iSelected) ? date('G') : $iSelected;

        $sOut = '<select name="' . $sFieldName . '" id="' . $sFieldName . '">' . "\n";
        foreach ($aRange as $iHour) {
            $sOut .= "<option value=\"" . str_pad($iHour, 2, '0', STR_PAD_LEFT) . "\"";
            $sOut .= $iHour == $iSelected ? ' selected="selected"' : '';
            $sOut .= ">" . str_pad($iHour, 2, '0', STR_PAD_LEFT) . "</option>\n";
        }

        $sOut .= '</select>';

        return $sOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('dropdownMinutes')) {

    /**
     * Generates a dropdown containing the numbers 0 - 59
     *
     * @param string  $sFieldName The name to give the field
     * @param array   $aRange     The range of minutes to use
     * @param integer $iSelected  The minute to select
     *
     * @return string
     */
    function dropdownMinutes($sFieldName, $aRange = null, $iSelected = null)
    {
        /*** array of mins ***/
        $aMinutes  = is_null($aRange) ? range(0, 59) : $aRange;
        $iSelected = in_array($iSelected, $aMinutes) ? $iSelected : 0;

        $sOut = '<select name="' . $sFieldName . '" id="' . $sFieldName . '">' . "\n";
        foreach ($aMinutes as $iMinute) {
            $sOut .= "<option value=\"" . str_pad($iMinute, 2, '0', STR_PAD_LEFT) . "\"";
            $sOut .= ($iMinute == $iSelected) ? ' selected="selected"' : '';
            $sOut .= ">" . str_pad($iMinute, 2, '0', STR_PAD_LEFT) . "</option>\n";
        }

        $sOut .= '</select>';

        return $sOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('datepicker')) {

    /**
     * Generates the dropdowns to select a date using the defaults
     *
     * @param string $sFieldName The name to give the field
     *
     * @return string
     */
    function datepicker($sFieldName)
    {
        $sOut = dropdownYears($sFieldName . '_year');
        $sOut .= dropdownMonths($sFieldName . '_month');
        $sOut .= dropdownDays($sFieldName . '_day');
        $sOut .= ' &nbsp; ';
        $sOut .= dropdownHours($sFieldName . '_hour');
        $sOut .= dropdownMinutes($sFieldName . '_minute');

        return $sOut;
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/date_helper.php';
