<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Validate email address
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('valid_email'))
{
	function valid_email($address)
	{
		if ( function_exists( 'filter_var' ) ) :

			return (bool) filter_var( $address, FILTER_VALIDATE_EMAIL );

		else :

			return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;

		endif;
	}
}