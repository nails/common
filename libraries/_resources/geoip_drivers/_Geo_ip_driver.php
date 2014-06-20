<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			NAILS_Geo_ip_driver
*
* Description:	Base class for Geo_ip drivers
*
*/

class NAILS_Geo_ip_driver
{
	protected $_errors;
	protected $_cache_values;
	protected $_cache_keys;
	protected $_cache_method;


	// --------------------------------------------------------------------------


	/**
	 * Construct the model
	 *
	 * @access	public
	 * @return	void
	 **/
	public function __construct( )
	{
		//	Set the cache method
		//	TODO: check for availability of things like memcached

		$this->_cache_values	= array();
		$this->_cache_keys		= array();
		$this->_cache_method	= 'LOCAL';

		// --------------------------------------------------------------------------

		//	Define defaults
		$this->_errors = array();
	}


	// --------------------------------------------------------------------------


	/**
	 * Destruct the model
	 *
	 * @access	public
	 * @return	void
	 **/
	public function __destruct()
	{
		//	Clear cache's
		if ( isset( $this->_cache_keys ) && $this->_cache_keys ) :

			foreach ( $this->_cache_keys AS $key ) :

				$this->_unset_cache( $key );

			endforeach;

		endif;
	}

	// --------------------------------------------------------------------------


	/**
	 * Set a generic error
	 *
	 * @access	protected
	 * @param	string	$error	The error message
	 * @return	void
	 **/
	protected function _set_error( $error )
	{
		$this->_errors[] = $error;
	}


	// --------------------------------------------------------------------------


	/**
	 * Get any errors
	 *
	 * @access	public
	 * @return	array
	 **/
	public function get_errors()
	{
		return $this->_errors;
	}


	// --------------------------------------------------------------------------


	/**
	 * Get last error
	 *
	 * @access	public
	 * @return	mixed
	 **/
	public function last_error()
	{
		return end( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Provides models with the an easy interface for saving data to a cache.
	 *
	 * @access	protected
	 * @param string $key The key for the cached item
	 * @param mixed $value The data to be cached
	 * @return	array
	 **/
	protected function _set_cache( $key, $value )
	{
		if ( ! $key )
			return FALSE;

		// --------------------------------------------------------------------------

		//	Prep the key, the key should have a prefix unique to this model
		$_prefix = $this->_cache_prefix();

		// --------------------------------------------------------------------------

		switch ( $this->_cache_method ) :

			case 'LOCAL' :

				$this->_cache_values[md5( $_prefix . $key )] = serialize( $value );
				$this->_cache_keys[]	= $key;

			break;

			// --------------------------------------------------------------------------

			case 'MEMCACHED' :

				//	TODO

			break;

		endswitch;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Lookup a cache item
	 *
	 * @access	protected
	 * @param	string	$key	The key to fetch
	 * @return	mixed
	 **/
	protected function _get_cache( $key )
	{
		if ( ! $key )
			return FALSE;

		// --------------------------------------------------------------------------

		//	Prep the key, the key should have a prefix unique to this model
		$_prefix = $this->_cache_prefix();

		// --------------------------------------------------------------------------

		switch ( $this->_cache_method ) :

			case 'LOCAL' :

				if ( isset( $this->_cache_values[md5( $_prefix . $key )] ) ) :

					return unserialize( $this->_cache_values[md5( $_prefix . $key )] );

				else :

					return FALSE;

				endif;

			break;

			// --------------------------------------------------------------------------

			case 'MEMCACHED' :

				//	TODO

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Unset a cache item
	 *
	 * @access	protected
	 * @param	string	$key	The key to fetch
	 * @return	boolean
	 **/
	protected function _unset_cache( $key )
	{
		if ( ! $key )
			return FALSE;

		// --------------------------------------------------------------------------

		//	Prep the key, the key should have a prefix unique to this model
		$_prefix = $this->_cache_prefix();

		// --------------------------------------------------------------------------

		switch ( $this->_cache_method ) :

			case 'LOCAL' :

				unset( $this->_cache_values[md5( $_prefix . $key )] );

				$_key = array_search( $key, $this->_cache_keys );

				if ( $_key !== FALSE ) :

					unset( $this->_cache_keys[$_key] );

				endif;

			break;

			// --------------------------------------------------------------------------

			case 'MEMCACHED' :

				//	TODO

			break;

		endswitch;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Define the cache key prefix
	 *
	 * @access	private
	 * @return	string
	 **/
	private function _cache_prefix()
	{
		return get_called_class();
	}
}