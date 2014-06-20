<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Geo_ip
*
* Description:	Geo IP Driver for the IP services provided by nailsapp.co.uk
*
*/

requirE_once NAILS_PATH . 'libraries/_resources/geoip_drivers/_Geo_ip_driver.php';

class Geo_ip_driver_Nails_ip_services extends NAILS_Geo_ip_driver
{
	private $_ci;
	private $_ip;
	private $_endpoint;
	private $_enable_cache;
	private $_cache;
	private $_key;


	// --------------------------------------------------------------------------


	public function __construct( $config = array() )
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_ci				=& get_instance();
		$this->_ip				= ! empty( $config['ip'] )				? $config['ip'] : $this->_ci->input->ip_address();
		$this->_endpoint		= ! empty( $config['endpoint'] )		? $config['endpoint'] : 'http://nailsapp.co.uk/api/ip';
		$this->_enable_cache	= ! empty( $config['enable_cache'] )	? (bool) $config['enable_cache'] : TRUE;
		$this->_key				= ! empty( $config['key'] )				? $config['key'] : '';

		// --------------------------------------------------------------------------

		//	Load cURL library
		$this->_ci->load->library( 'curl' );

	}


	// --------------------------------------------------------------------------


	public function set_ip( $ip )
	{
		$this->_ip = $ip;
		return $this;
	}


	// --------------------------------------------------------------------------


	public function all( $params = array() )
	{
		return $this->_call( $params );
	}


	// --------------------------------------------------------------------------


	public function city( $params = array() )
	{
		return $this->_call( 'city', $params );
	}

	// --------------------------------------------------------------------------


	public function country( $params = array() )
	{
		return $this->_call( 'country', $params );
	}


	// --------------------------------------------------------------------------


	public function location( $params = array() )
	{
		return $this->_call( 'location', $params );
	}


	// --------------------------------------------------------------------------


	private function _call( $method = '', $params = array() )
	{
		//	Check cache (if enabled)
		if ( $this->_enable_cache ) :

			$_key = md5( $this->_ip . '-all-' . serialize( $params ) );
			$_cache = $this->_get_cache( $_key );

			if ( $_cache ):

				return $_cache;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	URL
		$_url	= array();
		$_url[]	= $this->_endpoint;
		$_url[]	= $method;
		$_url	= implode( '/', array_filter( $_url ) );

		//	Params
		$_params		= array();
		$_params['key']	= $this->_key;
		$_params['ip']	= $this->_ip;
		$_params		= array_filter( $_params );

		$_result = $this->_ci->curl->simple_get( $_url, $_params );

		if ( $_result ) :

			$_result = json_decode( $_result );

		endif;

		// --------------------------------------------------------------------------

		//	Caching?
		if ( $_result && $this->_enable_cache ) :

			$_cache = $this->_set_cache( $_key, $_result );

		endif;

		return $_result;
	}
}