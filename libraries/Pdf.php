<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf
{

	private $_ci;
	private $_dompdf;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		$this->_ci =& get_instance();

		// --------------------------------------------------------------------------

		//	Load and configure DOMPDF
		define( 'DOMPDF_ENABLE_AUTOLOAD', FALSE );
		define( 'DOMPDF_FONT_DIR', DEPLOY_CACHE_DIR . '/dompdf/font/dir/' );
		define( 'DOMPDF_FONT_CACHE', DEPLOY_CACHE_DIR . '/dompdf/font/cache/' );

		// --------------------------------------------------------------------------

		//	Test the cache dirs exists and are writable.
		//	============================================
		//	If the caches aren't there or writable log this as an error,
		//	no need to halt execution as the cache *might* not be used. If
		//	on a production environment errors will be muted and shouldn't affect
		//	anything; but make a not in the logs regardless


		if ( ! is_dir( DOMPDF_FONT_DIR ) ) :

			//	Not a directory, attempt to create
			if ( ! @mkdir( DOMPDF_FONT_DIR, 0777, TRUE ) ) :

				//	Couldn't create. Sad Panda
				log_message( 'error', 'DOMPDF\'s cache folder doesn\'t exist, and I couldn\'t create it: ' . DOMPDF_FONT_DIR );

			endif;

		elseif ( ! is_really_writable( DOMPDF_FONT_DIR ) ) :

			//	Couldn't write. Sadder Panda
			log_message( 'error', 'DOMPDF\'s cache folder exists, but I couldn\'t write to it: ' . DOMPDF_FONT_DIR );

		endif;

		if ( ! is_dir( DOMPDF_FONT_CACHE ) ) :

			//	Not a directory, attempt to create
			if ( ! @mkdir( DOMPDF_FONT_CACHE, 0777, TRUE ) ) :

				//	Couldn't create. Sad Panda
				log_message( 'error', 'DOMPDF\'s cache folder doesn\'t exist, and I couldn\'t create it: ' . DOMPDF_FONT_CACHE );

			endif;

		elseif ( ! is_really_writable( DOMPDF_FONT_CACHE ) ) :

			//	Couldn't write. Sadder Panda
			log_message( 'error', 'DOMPDF\'s cache folder exists, but I couldn\'t write to it: ' . DOMPDF_FONT_CACHE );

		endif;

		// --------------------------------------------------------------------------

		require_once FCPATH . '/vendor/dompdf/dompdf/dompdf_config.inc.php';

		$this->_dompdf = new DOMPDF();
	}


	// --------------------------------------------------------------------------


	public function load_view( $view, $data = array() )
	{
		$_html = $this->_ci->load->view( $view, $data, TRUE );
		$this->_dompdf->load_html( $_html );
	}


	// --------------------------------------------------------------------------


	public function __call( $method, $arguments = array() )
	{
		if ( method_exists( $this->_dompdf, $method ) ) :

			return call_user_func_array( array( $this->_dompdf, $method ), $arguments );

		else :

			throw new exception( 'Call to undefined method Pdf::' . $method . '()' );

		endif;
	}
}