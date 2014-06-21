<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * get_potential_modules()
 *
 * Fetch all the potentially available modules for this app
 *
 * @access	public
 * @param	none
 * @return	object
 */
if ( ! function_exists( 'get_potential_modules' ) )
{
	function get_potential_modules()
	{
		//	If we already know which modules are available then return that, save
		//	the [small] overhead of working out the modules again and again.

		if ( isset( $GLOBALS['NAILS_POTENTIAL_MODULES'] ) ) :

			return $GLOBALS['NAILS_POTENTIAL_MODULES'];

		endif;

		// --------------------------------------------------------------------------

		$_nails_data	= get_nails_data();
		$_modules		= array();

		if ( ! empty( $_nails_data ) && is_array( $_nails_data->modules ) ) :

			foreach ( $_nails_data->modules AS $module ) :

				$_modules[] = $module;

			endforeach;

		endif;

		//	Save as a $GLOBAL for next time
		$GLOBALS['NAILS_POTENTIAL_MODULES'] = $_modules;

		// --------------------------------------------------------------------------

		return $_modules;
	}
}


// --------------------------------------------------------------------------


/**
 * get_available_modules()
 *
 * Fetch the avalable modules for this app
 *
 * @access	public
 * @param	none
 * @return	object
 */
if ( ! function_exists( 'get_available_modules' ) )
{
	function get_available_modules()
	{
		//	If we already know which modules are available then return that, save
		//	the [small] overhead of working out the modules again and again.

		if ( isset( $GLOBALS['NAILS_AVAILABLE_MODULES'] ) ) :

			return $GLOBALS['NAILS_AVAILABLE_MODULES'];

		endif;

		// --------------------------------------------------------------------------

		$_potential	= get_potential_modules();
		$_modules	= array();

		foreach ( $_potential AS $module ) :

			if ( is_dir( FCPATH . 'vendor/nailsapp/' . $module ) ) :

				$_modules[] = $module;

			endif;

		endforeach;

		//	Save as a $GLOBAL for next time
		$GLOBALS['NAILS_AVAILABLE_MODULES'] = $_modules;

		// --------------------------------------------------------------------------

		return $_modules;
	}
}


// --------------------------------------------------------------------------


/**
 * get_unavailable_modules()
 *
 * Fetch the unavalable modules for this app
 *
 * @access	public
 * @param	none
 * @return	object
 */
if ( ! function_exists( 'get_unavailable_modules' ) )
{
	function get_unavailable_modules()
	{
		//	If we already know which modules are unavailable then return that, save
		//	the [small] overhead of working out the modules again and again.

		if ( isset( $GLOBALS['NAILS_UNAVAILABLE_MODULES'] ) ) :

			return $GLOBALS['NAILS_UNAVAILABLE_MODULES'];

		endif;

		// --------------------------------------------------------------------------

		$_potential	= get_potential_modules();
		$_modules	= array();

		foreach ( $_potential AS $module ) :

			if ( ! is_dir( FCPATH . 'vendor/nailsapp/' . $module ) ) :

				$_modules[] = $module;

			endif;

		endforeach;

		//	Save as a $GLOBAL for next time
		$GLOBALS['NAILS_UNAVAILABLE_MODULES'] = $_modules;

		// --------------------------------------------------------------------------

		return $_modules;
	}
}


// --------------------------------------------------------------------------


/**
 * module_is_enabled()
 *
 * Handy way of determining whether a module is enabled or not in the app's config
 *
 * @access	public
 * @param	string	$key	The key(s) to fetch
 * @return	object
 */
if ( ! function_exists( 'module_is_enabled' ) )
{
	function module_is_enabled( $module )
	{
		$_potential	= get_potential_modules();

		if ( array_search( 'module-' . $module, $_potential ) !== FALSE ) :

			return TRUE;

		endif;

		return FALSE;
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


/* End of file system_helper.php */
/* Location: ./helpers/system_helper.php */