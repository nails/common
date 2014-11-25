<?php

/**
 * The following functions are used internally by Nails
 */

// --------------------------------------------------------------------------

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


		$_composer = @file_get_contents( NAILS_PATH . 'nails/composer.json' );

		if ( empty( $_composer ) ) :

			_NAILS_ERROR('Failed to discover potential modules; could not load composer.json' );

		endif;

		$_composer = json_decode( $_composer );

		if ( empty( $_composer->extra->nails->modules ) ) :

			_NAILS_ERROR('Failed to discover potential modules; could not decode composer.json' );

		endif;

		$_modules = array();

		foreach ( $_composer->extra->nails->modules AS $vendor => $modules ) :

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

			if ( is_dir( 'vendor/' . $module ) ) :

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

			if ( ! is_dir( 'vendor/' . $module ) ) :

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
 * PHP Version Check
 * =================
 *
 * We need to loop through all available modules and have a look at what version
 * of PHP they require, we'll then take the highest version and set that as our
 * minimum supported value.
 *
 * To set a requirement, within the module's nails object in composer.json,
 * specify the minPhpVersion value. You should also specify the appropriate
 * constraint for composer in the "require" section of composer.json.
 *
 * e.g:
 *
 * 	"extra":
 * 	{
 *		"nails" :
 *		{
 *			"minPhpVersion": "5.4.0"
 *		}
 * 	}
 */

if ( ! function_exists( '_NAILS_MIN_PHP_VERSION' ) )
{
	function _NAILS_MIN_PHP_VERSION()
	{
		$_modules		= array( 'nailsapp/common' ) + _NAILS_GET_AVAILABLE_MODULES();
		$_min_version	= 0;

		foreach ( $_modules AS $m ) :

			$_composer = @file_get_contents( 'vendor/' . $m . '/composer.json' );

			if ( ! empty( $_composer ) ) :

				$_composer = json_decode( $_composer );

				if ( ! empty( $_composer->extra->nails->minPhpVersion ) ) :

					if ( version_compare( $_composer->extra->nails->minPhpVersion, $_min_version, '>' ) ) :

						$_min_version = $_composer->extra->nails->minPhpVersion;

					endif;

				endif;

			endif;

		endforeach;

		return $_min_version;
	}
}

define( 'NAILS_MIN_PHP_VERSION', _NAILS_MIN_PHP_VERSION() );

if ( version_compare( PHP_VERSION, NAILS_MIN_PHP_VERSION, '<' ) ) :

	$subject	= 'PHP Version ' . PHP_VERSION . ' is not supported by Nails';
	$message	= 'The version of PHP you are running is not supported. Nails requires at least PHP version ' . NAILS_MIN_PHP_VERSION;

	if ( function_exists( '_NAILS_ERROR' ) ) :

		_NAILS_ERROR( $message, $subject );

	else :

		echo '<h1>ERROR: ' . $subject . '</h1>';
		echo '<h2>' . $message . '</h2>';
		exit(0);

	endif;

endif;


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


// --------------------------------------------------------------------------


/**
 *
 * The following class traits are used throughout Nails
 *
 */


// --------------------------------------------------------------------------


/**
 * Implements a common API for error handling in classes
 */
trait NAILS_COMMON_TRAIT_ERROR_HANDLING
{
	protected $_errors = array();

	// --------------------------------------------------------------------------

	/**
	 * Set a generic error
	 * @param string $error The error message
	 */
	protected function _set_error( $error )
	{
		$this->_errors[] = $error;
	}


	// --------------------------------------------------------------------------


	/**
	 * Return the error array
	 * @return array
	 */
	public function get_errors()
	{
		return $this->_errors;
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the last error
	 * @return string
	 */
	public function last_error()
	{
		return end( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Clears the last error
	 * @return mixed
	 */
	public function clear_last_error()
	{
		return array_pop( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Clears all errors
	 * @return void
	 */
	public function clear_errors()
	{
		$this->_errors = array();
	}
}


// --------------------------------------------------------------------------


/**
 * Implements a common API for caching in classes
 */
trait NAILS_COMMON_TRAIT_CACHING
{
	protected $_cache_values	= array();
	protected $_cache_keys		= array();
	protected $_cache_method	= 'LOCAL';


	// --------------------------------------------------------------------------


	/**
	 * Saves an item to the cache
	 * @param string $key   The cache key
	 * @param mixed  $value The data to be cached
	 */
	protected function _set_cache( $key, $value )
	{
		if ( empty( $key ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Prep the key, the key should have a prefix unique to this model
		$_prefix = $this->_cache_prefix();

		// --------------------------------------------------------------------------

		switch ( $this->_cache_method ) :

			case 'MEMCACHED' :

				//	TODO

			break;

			case 'LOCAL' :
			default :

				$this->_cache_values[md5( $_prefix . $key )] = serialize( $value );
				$this->_cache_keys[]	= $key;

			break;

		endswitch;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches an item from the cache
	 * @param  string $key The cache key
	 * @return mixed
	 */
	protected function _get_cache( $key )
	{
		if ( empty( $key ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Prep the key, the key should have a prefix unique to this model
		$_prefix = $this->_cache_prefix();

		// --------------------------------------------------------------------------

		switch ( $this->_cache_method ) :

			case 'MEMCACHED' :

				//	TODO

			break;

			case 'LOCAL' :
			default :

				if ( isset( $this->_cache_values[md5( $_prefix . $key )] ) ) :

					return unserialize( $this->_cache_values[md5( $_prefix . $key )] );

				else :

					return FALSE;

				endif;

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Deletes an item from the cache
	 * @param  string $key The cache key
	 * @return boolean
	 */
	protected function _unset_cache( $key )
	{
		if ( empty( $key ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Prep the key, the key should have a prefix unique to this model
		$_prefix = $this->_cache_prefix();

		// --------------------------------------------------------------------------

		switch ( $this->_cache_method ) :

			case 'MEMCACHED' :

				//	TODO

			break;

			case 'LOCAL' :
			default :

				unset( $this->_cache_values[md5( $_prefix . $key )] );

				$_key = array_search( $key, $this->_cache_keys );

				if ( $_key !== FALSE ) :

					unset( $this->_cache_keys[$_key] );

				endif;

			break;

		endswitch;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * In order to avoid collission between classes a prefix is used; this method
	 * defines the cache key prefix using the calling class' name.
	 * @return string
	 */
	protected function _cache_prefix()
	{
		return get_called_class();
	}
}

/**
 * Implements the common getcount_common() and _getcount_common_parse_sort() methods
 */
trait NAILS_COMMON_TRAIT_GETCOUNT_COMMON
{
	/**
	 * Applies common conditionals
	 *
	 * This method applies the conditionals which are common across the get_*()
	 * methods and the count() method.
	 * @param string $data Data passed from the calling method
	 * @param string $_caller The name of the calling method
	 * @return void
	 **/
	protected function _getcount_common( $data = array(), $_caller = NULL )
	{
		//	Handle wheres
		$_wheres = array( 'where', 'where_in', 'or_where_in', 'where_not_in', 'or_where_not_in' );

		foreach ( $_wheres AS $where_type ) :

			if ( ! empty( $data[$where_type] ) ) :

				if ( is_array( $data[$where_type] ) ) :

					/**
					 * If it's a single dimensional array then just bung that into
					 * the db->where(). If not, loop it and parse.
					 */

					$_first = reset( $data[$where_type] );

					if ( is_string( $_first ) ) :

						$this->db->$where_type( $data[$where_type] );

					else :

						foreach( $data[$where_type] AS $where ) :

							//	Work out column
							$_column = ! empty( $where['column'] ) ? $where['column'] : NULL;

							if ( $_column === NULL ) :

								$_column = ! empty( $where[0] ) && is_string( $where[0] ) ? $where[0] : NULL;

							endif;

							//	Work out value
							$_value = isset( $where['value'] ) ? $where['value'] : NULL;

							if ( $_value === NULL ) :

								$_value = ! empty( $where[1] ) ? $where[1] : NULL;

							endif;

							//	Escaped?
							$_escape = isset( $where['escape'] ) ? (bool) $where['escape'] : TRUE;

							if ( $_column ) :

								$this->db->$where_type( $_column, $_value, $_escape );

							endif;

						endforeach;

					endif;

				elseif ( is_string( $data[$where_type] ) ) :

					$this->db->$where_type( $data[$where_type] );

				endif;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Handle Likes
		//	TODO

		// --------------------------------------------------------------------------

		//	Handle sorting
		if ( ! empty( $data['sort'] ) ) :

			/**
			 * How we handle sorting
			 * =====================
			 *
			 * - If $data['sort'] is a string assume it's the field to sort on, use the default order
			 * - If $data['sort'] is a single dimension array then assume the first element (or the element
			 *   named 'column') is the column; and the second element (or the element named 'order') is the
			 *   direction to sort in
			 * - If $data['sort'] is a multidimensional array then loop each element and test as above.
			 *
			 **/


			if ( is_string( $data['sort'] ) ) :

				//	String
				$this->db->order_by( $data['sort'] );

			elseif( is_array( $data['sort'] ) ) :

				$_first = reset( $data['sort'] );

				if ( is_string( $_first ) ) :

					//	Single dimension array
					$_sort = $this->_getcount_common_parse_sort( $data['sort'] );

					if ( ! empty( $_sort['column'] ) ) :

						$this->db->order_by( $_sort['column'], $_sort['order'] );

					endif;

				else :

					//	Multi dimension array
					foreach( $data['sort'] AS $sort ) :

						$_sort = $this->_getcount_common_parse_sort( $sort );

						if ( ! empty( $_sort['column'] ) ) :

							$this->db->order_by( $_sort['column'], $_sort['order'] );

						endif;

					endforeach;

				endif;

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _getcount_common_parse_sort( $sort )
	{
		$_out = array( 'column' => NULL, 'order' => NULL );

		// --------------------------------------------------------------------------

		if ( is_string( $sort ) ) :

			$_out['column'] = $sort;
			return $_out;

		elseif ( isset( $sort['column'] ) ) :

			$_out['column'] = $sort['column'];

		else :

			//	Take the first element
			$_out['column'] = reset( $sort );
			$_out['column'] = is_string( $_out['column'] ) ? $_out['column'] : NULL;

		endif;

		if ( $_out['column'] ) :

			//	Determine order
			if ( isset( $sort['order'] ) ) :

				$_out['order'] = $sort['order'];

			elseif( count( $sort ) > 1 ) :

				//	Take the last element
				$_out['order'] = end( $sort );
				$_out['order'] = is_string( $_out['order'] ) ? $_out['order'] : NULL;

			endif;

		endif;

		// --------------------------------------------------------------------------

		return $_out;
	}
}
/* End of file CORE_NAILS_Common.php */
/* Location: ./common/CORE_NAILS_Common.php */