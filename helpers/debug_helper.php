<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Alias to dump( $var, TRUE )
 *
 * @access	public
 * @param	mixed
 * @return	void
 */
if ( ! function_exists( 'dumpanddie' ) )
{
	function dumpanddie( $var = NULL )
	{
		dump( $var, TRUE );
	}
}


// --------------------------------------------------------------------------


/**
 * Dumps data, similar to var_dump()
 *
 * @access	public
 * @param	mixed
 * @return	void
 */
if ( ! function_exists( 'dump' ) )
{
	function dump( $var = NULL, $die = FALSE )
	{
		if ( is_string( $var ) ) :

			$output = "<pre>(string) {$var}</pre>";

		elseif ( is_int( $var ) ) :

			$output = "<pre>(int) {$var}</pre>";

		elseif ( is_bool( $var ) ) :

			$var = ( $var === TRUE )?"TRUE":"FALSE";

			$output = "<pre>(bool) {$var}</pre>";

		elseif ( is_float( $var ) ) :

			$output = "<pre>(float) {$var}</pre>";

		elseif ( is_null( $var ) ) :

			$output = "<pre>(NULL) NULL</pre>";

		else:

			$output = "<pre>".print_r( $var, TRUE )."</pre>";

		endif;

		//	Check the global ENVIRONMENT setting.
		switch( strtoupper( ENVIRONMENT ) ) :

			case 'PRODUCTION':

				//	Mute output regardless of setting
				return;

			break;

			case 'STAGING':
			case 'DEVELOPMENT':

				//	Continue execution unless instructed otherwise
				if ( $die !== FALSE ) :

					die( "\n\n" . $output . "\n\n" );

				endif;

				echo "\n\n" . $output . "\n\n";

			break;

		endswitch;
	}
}


// --------------------------------------------------------------------------


/**
 * Outputs a 'here at date()' string using dumpanddie(); useful for debugging.
 *
 * @access	public
 * @param	mixed
 * @return	void
 */
if ( ! function_exists( 'here' ) )
{
	function here( $dump = NULL )
	{
		$_now = gmdate( 'H:i:s' );

		//	Dump payload if there
		if ( NULL !== $dump ) :

			dump( $dump );

		endif;

		dumpanddie( 'Here @ ' . $_now );
	}
}


// --------------------------------------------------------------------------


/**
 * Dumps the last known query
 *
 * @access	public
 * @param	mixed
 * @return	void
 */
if ( ! function_exists( 'lastquery' ) )
{
	function lastquery( $die = TRUE )
	{
		$_last_query = get_instance()->db->last_query();

		// --------------------------------------------------------------------------

		if ( $die ) :

			dumpanddie( $_last_query );

		else :

			dump( $_last_query );

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * alias of lastquery()
 *
 * @access	public
 * @param	mixed
 * @return	void
 */
if ( ! function_exists( 'last_query' ) )
{
	function last_query( $die = TRUE )
	{
		return lastquery( $die );
	}
}


// --------------------------------------------------------------------------


/**
 * send_developer_mail()
 *
 * Quickly send a high priority email via mail() to the APP_DEVELOPER
 *
 *
 * @access	public
 * @param	string $subject The subject of the email
 * @param	string $message The message of the email
 * @return	object
 */
if ( ! function_exists( 'send_developer_mail' ) )
{
	function send_developer_mail( $subject, $message )
	{
		return get_instance()->fatal_error_handler->send_developer_mail( $subject, $message );
	}
}



/* End of file debug_helper.php */
/* Location: ./helpers/debug_helper.php */