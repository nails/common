<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf
{

	private $_ci;
	private $_dompdf;
	private $_default_filename;


	// --------------------------------------------------------------------------


	/**
	 * Construct the PDF library
	 */
	public function __construct()
	{
		$this->_ci =& get_instance();

		// --------------------------------------------------------------------------

		$this->_default_filename = 'document.pdf';

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


	/**
	 * Loads CI views and passes it as HTML to DOMPDF
	 * @param  mixed $views An array of views, or a single view as a string
	 * @param  array  $data  View variables to pass to the view
	 * @return void
	 */
	public function load_view( $views, $data = array() )
	{
		$_html	= '';
		$views	= (array) $views;
		$views	= array_filter( $_views );

		foreach( $views AS $view ) :

			$_html .= $this->_ci->load->view( $view, $data, TRUE );

		endforeach;

		$this->_dompdf->load_html( $_html );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the PDF and sends it to the browser as a download.
	 * @param  string $filename The filename to give the PDF
	 * @param  array $options  An array of options to pass to DOMPDF's stream() method
	 * @return void
	 */
	public function download( $filename = '', $options = NULL )
	{
		$filename = $filename ? $filename : $this->_default_filename;

		//	Set the content attachment, by default send to the browser
		if ( is_null( $options ) ) :

			$options = array();

		endif;

		$options['Attachment'] = 1;

		$this->_dompdf->stream( $filename, $options );
		exit();
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the PDF and sends it to the browser as an inline PDF.
	 * @param  string $filename The filename to give the PDF
	 * @param  array $options  An array of options to pass to DOMPDF's stream() method
	 * @return void
	 */
	public function stream( $filename = '', $options = NULL )
	{
		$filename = $filename ? $filename : $this->_default_filename;

		//	Set the content attachment, by default send to the browser
		if ( is_null( $options ) ) :

			$options = array();

		endif;

		$options['Attachment'] = 0;

		$this->_dompdf->stream( $filename, $options );
		exit();
	}


	// --------------------------------------------------------------------------


	/**
	 * MagicMethod routes any method calls to this class to DOMPDF if it exists
	 * @param  string $method    The method called
	 * @param  array  $arguments Any arguments passed
	 * @return mixed
	 */
	public function __call( $method, $arguments = array() )
	{
		if ( method_exists( $this->_dompdf, $method ) ) :

			return call_user_func_array( array( $this->_dompdf, $method ), $arguments );

		else :

			throw new exception( 'Call to undefined method Pdf::' . $method . '()' );

		endif;
	}
}