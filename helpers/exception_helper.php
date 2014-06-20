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
		$_usr = get_userobject();

		//	Logged in users can't be redirected to log in, they
		//	simply get an unauthorised page

		if ( $_usr->is_logged_in() ) :

			show_error( 'The page you are trying to view is restricted. Sadly you don\'t have enough permissions to see it\'s content.', 401, 'Sorry, you are not authorised to view this page' );

		endif;

		$_ci  =& get_instance();

		$_ci->session->set_flashdata( 'message', $message );

		if ( $_ci->input->server( 'REQUEST_URI' ) ) :

			$_return = $_ci->input->server( 'REQUEST_URI' );

		elseif( uri_string() ) :

			$_return = uri_string();

		else :

			$_return = '';

		endif;

		$_return = $_return ? '?return_to=' . urlencode( $_return ) : '';

		redirect( 'auth/login' . $_return );
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


/**
 * Shows a fatal error
 *
 */
if ( ! function_exists( 'show_fatal_error' ) )
{
	function show_fatal_error( $subject = '', $message = '' )
	{
		if ( isset( get_instance()->fatal_error_handler ) && is_callable( array( get_instance()->fatal_error_handler, 'send_developer_mail' ) ) ) :

			if ( $subject && $message ) :

				get_instance()->fatal_error_handler->send_developer_mail( $subject, $message );

			endif;

			get_instance()->fatal_error_handler->show( $subject, $message );

		elseif( function_exists( '_NAILS_ERROR' ) ) :

			_NAILS_ERROR( $message, $subject );

		else :

			echo '<h1>ERROR: ' . $subject . '</h1>';
			echo '<h2>' . $message . '</h2>';
			exit(0);

		endif;
	}
}

/* End of file exception_helper.php */
/* Location: ./helpers/exception_helper.php */