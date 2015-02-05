<?php

if (!function_exists('toUserDate'))
{
    /**
     * Convert a date timestamp to the User's timezone from the Nails timezone
     * @param  mixed  $timestamp The timestamp to convert
     * @param  string $format    The format of the timestamp to return, defaults to User's date preference
     * @return string
     */
    function toUserDate($timestamp = null, $format = null)
    {
        return get_instance()->datetime_model->toUserDate($timestamp, $format);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toNailsDate'))
{
    /**
     * Convert a date timestamp to the Nails timezone from the User's timezone
     * @param  mixed  $timestamp The timestamp to convert
     * @return string
     */
    function toNailsDate($timestamp = null)
    {
        return get_instance()->datetime_model->toNailsDate($timestamp);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toUserDatetime'))
{
    /**
     * Convert a datetime timestamp to the user's timezone from the Nails timezone
     * @param  mixed  $timestamp The timestamp to convert
     * @param  string $format    The format of the timestamp to return, defaults to User's dateTime preference
     * @return string
     */
    function toUserDatetime($timestamp = null, $format = null)
    {
        return get_instance()->datetime_model->toUserDatetime($timestamp, $format);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('toNailsDatetime'))
{
    /**
     * Convert a datetime timestamp to the Nails timezone from the User's timezone
     * @param  mixed  $timestamp The timestamp to convert
     * @return string
     */
    function toNailsDatetime($timestamp = null)
    {
        return get_instance()->datetime_model->toNailsDatetime($timestamp);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('niceTime'))
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
