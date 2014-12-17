<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Calls the datetime_model's user_date() method.
 *
 * @access	public
 * @param	mixed $timestamp Either a UNIX timestamp or valid strtotime() string to format
 * @param	string $format_date A date format string recognised by the DateTime class
 * @return	string
 */
if ( ! function_exists( 'user_date' ) )
{
	function user_date( $timestamp = NULL, $format_date = NULL )
	{
		return get_instance()->datetime_model->user_date( $timestamp, $format_date );
	}
}


// --------------------------------------------------------------------------


/**
 * Quickly formats a date into a MySQL date, but in the user's timezone
 *
 * @access	public
 * @param	mixed $timestamp Either a UNIX timestamp or valid strtotime() string to format
 * @return	string
 */
if ( ! function_exists( 'user_mysql_date' ) )
{
	function user_mysql_date( $timestamp = NULL )
	{
		return get_instance()->datetime_model->user_date( $timestamp, 'Y-m-d' );
	}
}


// --------------------------------------------------------------------------


if ( ! function_exists( 'user_mysql_rdate' ) )
{
	function user_mysql_rdate( $timestamp = NULL )
	{
		return get_instance()->datetime_model->user_rdate( $timestamp, 'Y-m-d' );
	}
}


// --------------------------------------------------------------------------


/**
 * Calls the datetime_model's user_rdate() method, with the format as date
 *
 * @access	public
 * @param	mixed $timestamp Either a UNIX timestamp or valid strtotime() string
 * @return	string
 */
if ( ! function_exists( 'user_rdate' ) )
{
	function user_rdate( $timestamp = NULL )
	{
		return get_instance()->datetime_model->user_rdate( $timestamp, 'date' );
	}
}


// --------------------------------------------------------------------------


/**
 * Calls the datetime_model's user_datetime() method.
 *
 * @access	public
 * @param	mixed $timestamp Either a UNIX timestamp or valid strtotime() string to format
 * @param	string $format_date A date format string recognised by the DateTime class
 * @param	string $format_time A time format string recognised by the DateTime class
 * @return	string
 */
if ( ! function_exists( 'user_datetime' ) )
{
	function user_datetime( $timestamp = NULL, $format_date = NULL, $format_time = NULL )
	{
		return get_instance()->datetime_model->user_datetime( $timestamp, $format_date, $format_time );
	}
}


// --------------------------------------------------------------------------


/**
 * Quickly formats a date into a MySQL datetime, but in the user's timezone
 *
 * @access	public
 * @param	mixed $timestamp Either a UNIX timestamp or valid strtotime() string to format
 * @return	string
 */
if ( ! function_exists( 'user_mysql_datetime' ) )
{
	function user_mysql_datetime( $timestamp = NULL )
	{
		return get_instance()->datetime_model->user_datetime( $timestamp, 'Y-m-d', 'H:i:s' );
	}
}


// --------------------------------------------------------------------------


if ( ! function_exists( 'user_mysql_rdatetime' ) )
{
	function user_mysql_rdatetime( $timestamp = NULL )
	{
		return get_instance()->datetime_model->user_rdatetime( $timestamp, 'Y-m-d', 'H:i:s' );
	}
}


// --------------------------------------------------------------------------


/**
 * Calls the datetime_model's user_rdate() method, with the format as datetime
 *
 * @access	public
 * @param	mixed $timestamp Either a UNIX timestamp or valid strtotime() string
 * @return	string
 */
if ( ! function_exists( 'user_rdatetime' ) )
{
	function user_rdatetime( $timestamp = NULL )
	{
		return get_instance()->datetime_model->user_rdate( $timestamp, 'datetime' );
	}
}

/* End of file datetime_helper.php */
/* Location: ./helpers/datetime_helper.php */