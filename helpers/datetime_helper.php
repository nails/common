<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Calls the datetime_mdoel's user_date() method.
 *
 * @access	public
 * @param	mixed $timestamp Either a UNIX timestamp or valid strtotime() string to format
 * @param	string $format_date A date format string recognised by the DateTime class
 * @param	string $format_time A time format string recognised by the DateTime class
 * @return	string
 */
if ( ! function_exists( 'user_date' ) )
{
	function user_date( $timestamp = NULL, $format_date = NULL, $format_time = NULL )
	{
		return get_instance()->datetime->user_date( $timestamp, $format_date, $format_time );
	}
}

/* End of file datetime_helper.php */
/* Location: ./helpers/datetime_helper.php */