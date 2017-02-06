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

if (!function_exists('convertDateTime')) {

    /**
     * Arbitrarily convert a timestamp between timezones
     * @param  mixed  $mTimestamp The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @param  string $sToTz      The timezone to convert to
     * @param  string $sFromTz    The timezone to convert from
     * @return string
     */
    function convertDateTime($mTimestamp, $sToTz, $sFromTz = 'UTC')
    {
        $oDateTimeModel = nailsFactory('model', 'DateTime');
        return $oDateTimeModel->convert($mTimestamp, $sToTz, $sFromTz);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toUserDate')) {

    /**
     * Convert a date timestamp to the User's timezone from the Nails timezone
     * @param  mixed  $mTimestamp The timestamp to convert
     * @param  string $sFormat    The format of the timestamp to return, defaults to User's date preference
     * @return string
     */
    function toUserDate($mTimestamp = null, $sFormat = null)
    {
        $oDateTimeModel = nailsFactory('model', 'DateTime');
        return $oDateTimeModel->toUserDate($mTimestamp, $sFormat);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toNailsDate')) {

    /**
     * Convert a date timestamp to the Nails timezone from the User's timezone
     * @param  mixed  $mTimestamp The timestamp to convert
     * @return string
     */
    function toNailsDate($mTimestamp = null)
    {
        $oDateTimeModel = nailsFactory('model', 'DateTime');
        return $oDateTimeModel->toNailsDate($mTimestamp);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toUserDatetime')) {

    /**
     * Convert a datetime timestamp to the user's timezone from the Nails timezone
     * @param  mixed  $mTimestamp The timestamp to convert
     * @param  string $sFormat    The format of the timestamp to return, defaults to User's dateTime preference
     * @return string
     */
    function toUserDatetime($mTimestamp = null, $sFormat = null)
    {
        $oDateTimeModel = nailsFactory('model', 'DateTime');
        return $oDateTimeModel->toUserDatetime($mTimestamp, $sFormat);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toNailsDatetime')) {

    /**
     * Convert a datetime timestamp to the Nails timezone from the User's timezone
     * @param  mixed  $mTimestamp The timestamp to convert
     * @return string
     */
    function toNailsDatetime($mTimestamp = null)
    {
        $oDateTimeModel = nailsFactory('model', 'DateTime');
        return $oDateTimeModel->toNailsDatetime($mTimestamp);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('niceTime')) {

    /**
     * Converts a datetime into a human friendly relative string
     * @param  mixed   $mDate           The timestamp to convert
     * @param  boolean $bTense          Whether or not to append the tense (e.g, X minutes _ago_)
     * @param  string  $sOptBadMsg      The message to show if a bad timestamp is supplied
     * @param  string  $sGreaterOneWeek The message to show if the timestanmp is greater than one week away
     * @param  string  $sLessTenMins    The message to show if the timestamp is less than ten minutes away
     * @return string
     */
    function niceTime($mDate = false, $bTense = true, $sOptBadMsg = null, $sGreaterOneWeek = null, $sLessTenMins = null)
    {
        $oDateTimeModel = nailsFactory('model', 'DateTime');
        return $oDateTimeModel->niceTime($mDate, $bTense, $sOptBadMsg, $sGreaterOneWeek, $sLessTenMins);
    }
}

// --------------------------------------------------------------------------

/**
 * calculate_age()
 *
 * Calculates a person's age (or age at a certain date
 *
 * @access  public
 * @param   string
 * @return  string
 */
if (!function_exists('calculate_age')) {

    function calculate_age($y, $m, $d, $death_y = null, $death_m = null, $death_d = null)
    {
        //  Only calculate to a date which isn't today if all values are supplied
        if (is_null($death_y) || is_null($death_m) || is_null($death_d)) {

            $death_y = date('Y');
            $death_m = date('m');
            $death_d = date('d');
        }

        // --------------------------------------------------------------------------

        $_birth_time = mktime(0, 0, 0, $m, $d, $y);
        $_death_time = mktime(0, 0, 0, $death_m, $death_d, $death_y);

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

// --------------------------------------------------------------------------

/**
 * Dropdown - Years
 *
 * Generates a dropdown containing the values between $start_year and $end_year
 *
 * @access  public
 * @param   mixed
 * @return  string
 */
if (!function_exists('dropdown_years')) {

    function dropdown_years($field_name, $start_year = null, $end_year = null, $selected = null, $placeholder = null)
    {
        /*** defaults ***/
        $start_year = is_null($start_year)  ? date('Y')     : $start_year;
        $end_year   = is_null($end_year)    ? $start_year - 10  : $end_year;

        /*** the current year ***/
        $selected = is_null($selected) ? date('Y') : $selected;

        /*** range of years ***/
        $r = range($start_year, $end_year);

        /*** create the select ***/
        $select = '<select name="'.$field_name.'" id="'.$field_name.'">';

        $select .= "<option value=\"0000\"";
        $select .= (!$selected) ? ' selected="selected"' : '';
        $select .= ">" . ($placeholder ? $placeholder : '-') . "</option>\n";

        foreach ($r as $year) {

            $select .= "<option value=\"$year\"";
            $select .= ($year == $selected) ? ' selected="selected"' : '';
            $select .= ">$year</option>\n";
        }

        $select .= '</select>';

        return $select;
    }
}

// --------------------------------------------------------------------------

/**
 * Dropdown - Months
 *
 * Generates a dropdown containing the months of the year
 *
 * @access  public
 * @param   mixed
 * @return  string
 */
if (!function_exists('dropdown_months')) {

    function dropdown_months($field_name, $short = false, $selected = null, $placeholder = null)
    {
        /*** array of months ***/
        $months_short = array(  1   =>  'Jan',
                                2   =>  'Feb',
                                3   =>  'Mar',
                                4   =>  'Apr',
                                5   =>  'May',
                                6   =>  'Jun',
                                7   =>  'Jul',
                                8   =>  'Aug',
                                9   =>  'Sep',
                                10  =>  'Oct',
                                11  =>  'Nov',
                                12  =>  'Dec');

        $months_long = array(   1   =>  'January',
                                2   =>  'February',
                                3   =>  'March',
                                4   =>  'April',
                                5   =>  'May',
                                6   =>  'June',
                                7   =>  'July',
                                8   =>  'August',
                                9   =>  'September',
                                10  =>  'October',
                                11  =>  'November',
                                12  =>  'December');

        $months = $short === false ? $months_long : $months_short;

        /*** current month ***/
        $selected = is_null($selected) ? date('n') : $selected;

        $select = '<select name="'.$field_name.'" id="'.$field_name.'">'."\n";

        $select .= "<option value=\"00\"";
        $select .= (!$selected) ? ' selected="selected"' : '';
        $select .= ">" . ($placeholder ? $placeholder : '-') . "</option>\n";

        foreach ($months as $key => $mon) {

            $select .= "<option value=\"".str_pad($key, 2, '0', STR_PAD_LEFT)."\"";
            $select .= ($key == $selected) ? ' selected="selected"' : '';
            $select .= ">$mon</option>\n";
        }

        $select .= '</select>';

        return $select;
    }
}

// --------------------------------------------------------------------------

/**
 * Dropdown - Days
 *
 * Generates a dropdown containing the days of the month
 *
 * @access  public
 * @param   mixed
 * @return  string
 */
if (!function_exists('dropdown_days')) {

    function dropdown_days($field_name, $selected = null, $placeholder = null)
    {
        /*** range of days ***/
        $r = range(1, 31);

        /*** current day ***/
        $selected = is_null($selected) ? date('j') : $selected;

        $select = '<select name="'.$field_name.'" id="'.$field_name.'">'."\n";

        $select .= "<option value=\"00\"";
        $select .= (!$selected) ? ' selected="selected"' : '';
        $select .= ">" . ($placeholder ? $placeholder : '-') . "</option>\n";

        foreach ($r as $day) {

            $select .= "<option value=\"".str_pad($day, 2, '0', STR_PAD_LEFT)."\"";
            $select .= ($day == $selected) ? ' selected="selected"' : '';
            $select .= ">".str_pad($day, 2, '0', STR_PAD_LEFT)."</option>\n";
        }

        $select .= '</select>';

        return $select;
    }
}

// --------------------------------------------------------------------------

/**
 * Dropdown - Hours
 *
 * Generates a dropdown containing the numbers 0 - 23
 *
 * @access  public
 * @param   mixed
 * @return  string
 */
if (!function_exists('dropdown_hours')) {

    function dropdown_hours($field_name, $selected = null)
    {
        /*** range of hours ***/
        $r = range(0, 23);

        /*** current hour ***/
        $selected = is_null($selected) ? date('G') : $selected;

        $select = '<select name="'.$field_name.'" id="'.$field_name.'">'."\n";
        foreach ($r as $hour) {

            $select .= "<option value=\"".str_pad($hour, 2, '0', STR_PAD_LEFT)."\"";
            $select .= ($hour == $selected) ? ' selected="selected"' : '';
            $select .= ">" . str_pad($hour, 2, '0', STR_PAD_LEFT) . "</option>\n";
        }

        $select .= '</select>';

        return $select;
    }
}

// --------------------------------------------------------------------------

/**
 * Dropdown - Minutes
 *
 * Generates a dropdown containing the numbers 0 - 59
 *
 * @access  public
 * @param   mixed
 * @return  string
 */
if (!function_exists('dropdown_minutes')) {

    function dropdown_minutes($field_name, $range = null, $selected = null)
    {
        /*** array of mins ***/
        $minutes = is_null($range) ? range(0, 59) : $range ;

        $selected = in_array($selected, $minutes) ? $selected : 0;

        $select = '<select name="'.$field_name.'" id="'.$field_name.'">'."\n";
        foreach ($minutes as $min) {

            $select .= "<option value=\"".str_pad($min, 2, '0', STR_PAD_LEFT)."\"";
            $select .= ($min == $selected) ? ' selected="selected"' : '';
            $select .= ">".str_pad($min, 2, '0', STR_PAD_LEFT)."</option>\n";
        }

        $select .= '</select>';

        return $select;
    }
}

// --------------------------------------------------------------------------

/**
 * dropdown_datepicker
 *
 * Generates the dropdowns to select a date using the defaults
 *
 * @access  public
 * @param   mixed
 * @return  string
 */
if (!function_exists('datepicker')) {

    function datepicker($field_name)
    {
        $out = "";
        $out .= dropdown_years($field_name.'_year');
        $out .= dropdown_months($field_name.'_month');
        $out .= dropdown_days($field_name.'_day');
        $out .= ' &nbsp; ';
        $out .= dropdown_hours($field_name.'_hour');
        $out .= dropdown_minutes($field_name.'_minute');

        return $out;
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include FCPATH . 'vendor/codeigniter/framework/system/helpers/date_helper.php';
