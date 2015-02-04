<?php

if (!function_exists('userDate'))
{
    /**
     * Calls the datetime_model's userDate() method.
     * @param   mixed  $timestamp  Either a UNIX timestamp or valid strtotime() string to format
     * @param   string $formatDate A date format string recognised by the DateTime class
     * @return  string
     */
    function userDate($timestamp = null, $formatDate = null)
    {
        return get_instance()->datetime_model->userDate($timestamp, $formatDate);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('userMysqlDate'))
{
    /**
     * Quickly formats a date into a MySQL date, but in the user's timezone
     * @param  mixed  $timestamp Either a UNIX timestamp or valid strtotime() string to format
     * @return string
     */
    function userMysqlDate($timestamp = null)
    {
        return get_instance()->datetime_model->userDate($timestamp, 'Y-m-d');
    }
}

// --------------------------------------------------------------------------

if (!function_exists('userMysqlReverseDate'))
{
    /**
     * Converts a timestamp in the User's timezone to the Nails timezone and format's a Y-m-d
     * @param  mixed  $timestamp  The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @return string
     */
    function userMysqlReverseDate($timestamp = null)
    {
        return get_instance()->datetime_model->userReverseDate($timestamp, 'Y-m-d');
    }
}

// --------------------------------------------------------------------------

if (!function_exists('userReverseDate'))
{
    /**
     * Converts a timestamp in the User's timezone to the Nails timezone and format's a Y-m-d
     * @param  mixed  $timestamp  The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @return string
     */
    function userReverseDate($timestamp = null)
    {
        return get_instance()->datetime_model->userReverseDate($timestamp, 'date');
    }
}

// --------------------------------------------------------------------------

if (!function_exists('userDatetime'))
{
    /**
     * Converts a timestamp in the Nails timezone to the User's timezone and format's as per their date & time preferences.
     * @param  mixed  $timestamp  The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @return string
     */
    function userDatetime($timestamp = null, $formatDate = null, $formatTime = null)
    {
        return get_instance()->datetime_model->userDatetime($timestamp, $formatDate, $formatTime);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('userMysqlDatetime'))
{
    /**
     * Converts a timestamp in the Nails timezone to the User's timezone and format's as Y-m-d H:i:s
     * @param  mixed  $timestamp  The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @return string
     */
    function userMysqlDatetime($timestamp = null)
    {
        return get_instance()->datetime_model->userDatetime($timestamp, 'Y-m-d', 'H:i:s');
    }
}

// --------------------------------------------------------------------------

if (!function_exists('userMysqlReverseDatetime'))
{
    /**
     * Converts a timestamp in the User's timezone to the Nails timezone and format's as Y-m-d H:i:s
     * @param  mixed  $timestamp  The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @return string
     */
    function userMysqlReverseDatetime($timestamp = null)
    {
        return get_instance()->datetime_model->userReverseDatetime($timestamp, 'Y-m-d', 'H:i:s');
    }
}

// --------------------------------------------------------------------------

if (!function_exists('userReverseDatetime'))
{
    /**
     * Converts a timestamp in the User's timezone to the Nails timezone and format's a Y-m-d H:i:s
     * @param  mixed  $timestamp  The timestamp to convert. If null current time is used, if numeric treated as timestamp, else passed to strtotime()
     * @return string
     */
    function userReverseDatetime($timestamp = null)
    {
        return get_instance()->datetime_model->userReverseDate($timestamp, 'datetime');
    }
}

// --------------------------------------------------------------------------

if (!function_exists('nice_time'))
{
    /**
     * Converts a datetime into a human friendly relative string
     * @param  mixed   $date           The timestamp to convert
     * @param  boolean $tense          Whether or not to append the tense (e.g, X minutes _ago_)
     * @param  string  $optBadMsg      The message to show if a bad timestamp is supplied
     * @param  string  $greaterOneWeek The message to show if the timestanmp is greater than one week away
     * @param  string  $lessTenMins    The message to show if the timestamp is less than ten minutes away
     * @return string
     */
    function niceTime($date = false, $tense = true, $optBadMsg = null, $greaterOneWeek = null, $LessTenMins = null)
    {
        return get_instance()->datetime_model->niceTime($date, $tense, $optBadMsg, $greaterOneWeek, $LessTenMins);
    }
}
