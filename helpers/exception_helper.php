<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Renders the 401 unauthorised page.
 * 
 * Sends the user off to the login page
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'show_401' ) )
{
	function show_401( $message = '<strong>Sorry,</strong> you need to be logged in to see that page.' )
	{
		$_usr =& get_userobject();
		
		//	Logged in users can't be redirected to log in, they
		//	simply get an unauthorised page
		
		if ( $_usr->is_logged_in() ) :
		
			show_error( 'The page you are trying to view is restricted. Sadly you don\'t have enough permissions to see it\'s content.', 401, 'Sorry, you are not authorised to view this page' );
		
		endif;
		
		$_ci  =& get_instance();
		
		$_ci->session->set_flashdata( 'message', $message );
		
		$_return_to = ( $_ci->uri->uri_string() ) ? '?return_to=' . urlencode( $_ci->uri->uri_string() ) : NULL;
		
		redirect( 'auth/login' . $_return_to );
		
		exit( 0 );
	}
}


/**
 * Alias of show_401()
 *
 */
if ( ! function_exists( 'unauthorised' ) )
{
	function unauthorised( $message = '<strong>Sorry,</strong> you need to be logged in to see that page.' )
	{
		show_401( $message );
	}
}

/* End of file exception_helper.php */
/* Location: ./helpers/exception_helper.php */