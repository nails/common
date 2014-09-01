<?php

/**
 * _NAILS_GET_POTENTIAL_MODULES()
 *
 * Fetch all the potentially available modules for this app
 *
 * @access	public
 * @param	none
 * @return	object
 */
if ( ! function_exists( '_NAILS_GET_POTENTIAL_MODULES' ) )
{
	function _NAILS_GET_POTENTIAL_MODULES()
	{
		//	If we already know which modules are available then return that, save
		//	the [small] overhead of working out the modules again and again.

		if ( isset( $GLOBALS['NAILS_POTENTIAL_MODULES'] ) ) :

			return $GLOBALS['NAILS_POTENTIAL_MODULES'];

		endif;

		// --------------------------------------------------------------------------


		$_composer = @file_get_contents(NAILS_PATH . 'nails/composer.json');

		if ( empty( $_composer ) ) :

			_NAILS_ERROR('Failed to discover potential modules; could not load composer.json' );

		endif;

		$_composer = json_decode( $_composer );

		if ( empty( $_composer->extra->modules ) ) :

			_NAILS_ERROR('Failed to discover potential modules; could not decode composer.json' );

		endif;

		$_modules = array();

		foreach ( $_composer->extra->modules AS $vendor => $modules ) :

			foreach ( $modules AS $module ) :

				$_modules[] = $vendor . '/' . $module;

			endforeach;

		endforeach;

		//	Save as a $GLOBAL for next time
		$GLOBALS['NAILS_POTENTIAL_MODULES'] = $_modules;

		// --------------------------------------------------------------------------

		return $_modules;
	}
}


// --------------------------------------------------------------------------


/**
 * _NAILS_GET_AVAILABLE_MODULES()
 *
 * Fetch the avalable modules for this app
 *
 * @access	public
 * @param	none
 * @return	object
 */
if ( ! function_exists( '_NAILS_GET_AVAILABLE_MODULES' ) )
{
	function _NAILS_GET_AVAILABLE_MODULES()
	{
		//	If we already know which modules are available then return that, save
		//	the [small] overhead of working out the modules again and again.

		if ( isset( $GLOBALS['NAILS_AVAILABLE_MODULES'] ) ) :

			return $GLOBALS['NAILS_AVAILABLE_MODULES'];

		endif;

		// --------------------------------------------------------------------------

		$_potential	= _NAILS_GET_POTENTIAL_MODULES();
		$_modules	= array();

		foreach ( $_potential AS $module ) :

			if ( is_dir( FCPATH . 'vendor/' . $module ) ) :

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
 * _NAILS_GET_UNAVAILABLE_MODULES()
 *
 * Fetch the unavalable modules for this app
 *
 * @access	public
 * @param	none
 * @return	object
 */
if ( ! function_exists( '_NAILS_GET_UNAVAILABLE_MODULES' ) )
{
	function _NAILS_GET_UNAVAILABLE_MODULES()
	{
		//	If we already know which modules are unavailable then return that, save
		//	the [small] overhead of working out the modules again and again.

		if ( isset( $GLOBALS['NAILS_UNAVAILABLE_MODULES'] ) ) :

			return $GLOBALS['NAILS_UNAVAILABLE_MODULES'];

		endif;

		// --------------------------------------------------------------------------

		$_potential	= _NAILS_GET_POTENTIAL_MODULES();
		$_modules	= array();

		foreach ( $_potential AS $module ) :

			if ( ! is_dir( FCPATH . 'vendor/' . $module ) ) :

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
		$_potential	= _NAILS_GET_AVAILABLE_MODULES();

		if ( array_search( 'nailsapp/module-' . $module, $_potential ) !== FALSE ) :

			return TRUE;

		endif;

		return FALSE;
	}
}

// --------------------------------------------------------------------------

/**
 * $NAILS_CONTROLLER_DATA is an array populated by $this->data in controllers,
 * this function provides an easy interface to this array when it's not in scope.
 *
 * @access	public
 * @return	array	A reference to $NAILS_CONTROLLER_DATA
 **/
function &get_controller_data()
{
	global $NAILS_CONTROLLER_DATA;
	return $NAILS_CONTROLLER_DATA;
}


// --------------------------------------------------------------------------


/**
 * $NAILS_CONTROLLER_DATA is an array populated by $this->data in controllers,
 * this function provides an easy interface to populate this array when it's not
 * in scope.
 *
 * @access	public
 * @param string $key The key to populate
 * @param mixed $value The value to assign
 * @return	void
 **/
function set_controller_data( $key, $value )
{
	global $NAILS_CONTROLLER_DATA;
	$NAILS_CONTROLLER_DATA[$key] = $value;
}


// --------------------------------------------------------------------------


/**
 * Attempts to fetch the real domain from a URL
 *
 * Attempts to get the top level part of a URL (i.e example.tld from sub.domains.example.tld).
 *
 * Hat tip: http://uk1.php.net/parse_url#104874
 *
 * BUG: 2 character TLD's break this
 * TODO: Try and fix this bug
 *
 * @access	public
 * @param	string
 * @return	string	The real domain, or FALSE on error
 **/
if ( ! function_exists('get_domain_from_url')) :

	function get_domain_from_url( $url )
	{
		$_bits = explode( '/', $url );

		if ( $_bits[0] == 'http:' || $_bits[0] == 'https:' ) :

			$_domain = $_bits[2];

		else :

			$_domain = $_bits[0];

		endif;

		unset( $_bits );

		$_bits	= explode( '.', $_domain );
		$_idz	= count( $_bits );
		$_idz	-=3;

		if ( ! isset( $_bits[($_idz+2)] ) ) :

			$_url = FALSE;

		elseif ( strlen( $_bits[($_idz+2)] ) == 2 && isset( $_bits[($_idz+2)] ) ) :

			$_url	= array();
			$_url[] = ! empty( $_bits[$_idz] )		? $_bits[$_idz]		: FALSE;
			$_url[] = ! empty( $_bits[$_idz+1] )	? $_bits[$_idz+1]	: FALSE;
			$_url[] = ! empty( $_bits[$_idz+2] )	? $_bits[$_idz+2]	: FALSE;

			$_url = implode( '.', array_filter( $_url ) );

		elseif ( strlen( $_bits[($_idz+2)] ) == 0 ) :

			$_url	= array();
			$_url[] = ! empty( $_bits[$_idz] )		? $_bits[$_idz]		: FALSE;
			$_url[] = ! empty( $_bits[$_idz+1] )	? $_bits[$_idz+1]	: FALSE;

			$_url = implode( '.', array_filter( $_url ) );

		elseif ( isset( $_bits[($_idz+1)] ) ) :

			$_url	= array();
			$_url[] = ! empty( $_bits[$_idz+1] )	? $_bits[$_idz+1]	: FALSE;
			$_url[] = ! empty( $_bits[$_idz+2] )	? $_bits[$_idz+2]	: FALSE;

			$_url = implode( '.', array_filter( $_url ) );

		else :

			$_url = FALSE;

		endif;

		return $_url;
	}

endif;


// --------------------------------------------------------------------------


/**
 * Fetches the relative path between two directories
 *
 * Hat tip: Thanks to Gordon for this one; http://stackoverflow.com/a/2638272/789224
 *
 * @access	public
 * @param	string
 * @param	string
 * @return	string	The relative path between the two directories
 **/
if ( ! function_exists( 'get_relative_path' ) ) :

	function get_relative_path( $from, $to )
	{
		$from     = explode( '/', $from );
		$to       = explode( '/', $to );
		$relPath  = $to;

		foreach( $from AS $depth => $dir ) :

			//	Find first non-matching dir
			if( $dir === $to[$depth] ) :

				//	Ignore this directory
				array_shift( $relPath );

			else :

			//	Get number of remaining dirs to $from
			$remaining = count( $from ) - $depth;

				if ( $remaining > 1 ) :

					// add traversals up to first matching dir
					$padLength = ( count( $relPath ) + $remaining - 1 ) * -1;
					$relPath = array_pad( $relPath, $padLength, '..' );
					break;

				else :

					$relPath[0] = './' . $relPath[0];

				endif;

			endif;

		endforeach;

		return implode( '/', $relPath );
	}

endif;


// --------------------------------------------------------------------------


/**
 * Adds a trailing slash to the input string if there isn't already one there
 *
 * @access	public
 * @param	string The string to add a trailing shash to.
 * @return	string The input string with a trailing slash
 **/
function add_trailing_slash( $str )
{
	return rtrim( $str, '/' ) . '/';
}


// --------------------------------------------------------------------------


/**
 * Detects whether the current page is secure or not
 *
 * @access	public
 * @param	string
 * @return	bool
 */
function page_is_secure()
{
	if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) :

		//	Page is being served through HTTPS
		return TRUE;

	elseif ( isset( $_SERVER['SERVER_NAME'] ) && isset( $_SERVER['REQUEST_URI'] ) && SECURE_BASE_URL != BASE_URL ) :

		//	Not being served through HTTPS, but does the URL of the page begin
		//	with SECURE_BASE_URL (when BASE_URL is different)

		$_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		if (  preg_match( '#^' . SECURE_BASE_URL . '.*#', $_url ) ) :

			return TRUE;

		else :

			return FALSE;

		endif;

	endif;

	// --------------------------------------------------------------------------

	//	Unknown, assume not
	return FALSE;
}

/* End of file CORE_NAILS_Common.php */
/* Location: ./core/CORE_NAILS_Common.php */