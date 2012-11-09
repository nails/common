<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Generate a login as URL for a user
 *
 * @access	public
 * @param	int		$uid		The ID of the user we're logging in as
 * @param	string	$upassword	The encoded password of the user to log in as
 * @return	string
 */
if ( ! function_exists( 'login_as_url' ) )
{
	function login_as_url( $uid, $upassword )
	{
		//	Generate the return string
		$_return_string = '?return_to=' . urlencode( uri_string() . '?' . $_SERVER['QUERY_STRING'] );
		
		// --------------------------------------------------------------------------
		
		return site_url( 'auth/override/login_as/' . md5( $uid ) . '/' . md5( $upassword ) . $_return_string );
	}
}


// --------------------------------------------------------------------------


/**
 * Generate a login as button
 *
 * @access	public
 * @param	int		$uid		The ID of the user we're logging in as
 * @param	string	$upassword	The encoded password of the user to log in as
 * @return	string
 */
if ( ! function_exists( 'login_as_button' ) )
{
	function login_as_button( $uid, $upassword, $text = 'Login As', $attr = 'class="awesome small"' )
	{
		return anchor( login_as_url( $uid, $upassword ), $text, $attr );
	}
}

/* End of file admin_helper.php */
/* Location: ./application/modules/admin/helpers/admin_helper.php */