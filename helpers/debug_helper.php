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
		if ( ! is_null( $dump ) ) :

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
	function lastquery( $return = FALSE )
	{
		if ( defined( NAILS_DB_ENABLED ) && NAILS_DB_ENABLED ) :

			$_last_query = get_instance()->db->last_query();

		else :

			$_last_query = NULL;

		endif;

		if ( $return ) :

			return $_last_query;

		else :

			dump( $_last_query );

		endif;
	}
}


/* End of file debug_helper.php */
/* Location: ./helpers/debug_helper.php */