<?php (defined('BASEPATH') OR defined('SYSPATH')) or die('No direct access allowed.');

class ProxyLib
{
	private $_url;


	// --------------------------------------------------------------------------


	public function __construct( $options = '' )
	{
		$this->url( $options );
	}

	// --------------------------------------------------------------------------


	public function url( $url )
	{
		$this->_url = $url;
	}


	// --------------------------------------------------------------------------


	public function go()
	{
		//	Fetch the remote page's headers
		$_headers = get_headers( $this->_url );

		if ( $_headers ) :

			foreach ($_headers AS $header ) :

				header( $header );

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch content
		get_instance()->load->library( 'curl' );
		echo get_instance()->curl->simple_get( $this->_url );
		die();
	}
}