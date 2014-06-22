<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Geo_ip
*
* Description:	Gateway to the FreeAgent API wrapper provided by HostLikeToast
*
*/

class Geo_ip
{
	private $_ip;
	private $_driver;


	// --------------------------------------------------------------------------


	public function __construct( $config = array() )
	{
		$_driver		= ! empty( $config['driver'] ) ? $config['driver'] : 'Nails_ip_services';
		$_driver_path	= ! empty( $config['driver_path'] ) ? $config['driver_path'] : NAILS_COMMON_PATH . 'libraries/_resources/geoip_drivers/';
		$_driver_path	.= substr( $_driver_path, -1 ) != '/' ? '/' : '';
		$_driver_config	= ! empty( $config['driver_config'] ) ? (array) $config['driver_config'] : array();

		if ( file_exists( $_driver_path . ucfirst( strtolower( $_driver ) ) . '.php' ) ) :

			require_once $_driver_path . ucfirst( strtolower( $_driver ) ) . '.php';

			$_class = 'Geo_ip_driver_' . $_driver;
			$this->_driver = new $_class( $_driver_config );

		else :

			show_error( $_driver_path . ucfirst( strtolower( $_driver ) ) . '.php is not a valid Geo_ip driver' );

		endif;
	}


	// --------------------------------------------------------------------------


	public function __call( $method, $arguments )
	{
		if ( method_exists( $this->_driver, $method ) ) :

			return call_user_func_array( array( $this->_driver, $method ), $arguments );

		else :

			throw new Exception( '<strong>Fatal error</strong>: Call to undefined method Geo_ip::' . $method . '()' );

		endif;
	}
}