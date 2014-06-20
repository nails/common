<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Sitemap
*
* Description:	Generates a sitemap in various formats
*
*/

/**
 * OVERLOADING NAILS' SITEMAP MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Sitemap extends NAILS_Controller
{
	protected $_filename_json;
	protected $_filename_xml;

	// --------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Enabled?
		if ( ! module_is_enabled( 'sitemap' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->load->model( 'sitemap/sitemap_model' );

		$this->_filename_json	= $this->sitemap_model->get_filename_json();
		$this->_filename_xml	= $this->sitemap_model->get_filename_xml();
	}

	// --------------------------------------------------------------------------


	/**
	 * Map all requests to _sitemap
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function _remap()
	{
		switch( uri_string() ) :

			case 'sitemap' :				$this->_output_html();	break;
			case $this->_filename_xml :		$this->_output_xml();	break;
			case $this->_filename_json :	$this->_output_json();	break;
			default :						show_404();				break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _output_html()
	{
		//	Check cache for $this->_filename_json
		if ( ! $this->_check_cache( $this->_filename_json ) ) :

			return;

		endif;

		// --------------------------------------------------------------------------

		$this->data['sitemap'] = json_decode( file_get_contents( DEPLOY_CACHE_DIR . $this->_filename_json ) );

		if ( empty( $this->data['sitemap'] ) ) :

			//	Something fishy goin' on.
			//	Send a temporarily unavailable header, we don't want search engines
			//	unlisting us because of this.

			$this->output->set_header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 503 Service Temporarily Unavailable' );
			$this->output->set_header( 'Status: 503 Service Temporarily Unavailable' );
			$this->output->set_header( 'Retry-After: 7200' );

			//	Inform devs
			send_developer_mail( $this->_filename_json . ' contained no data' , 'The cache file for the site map was found but did not contain any data.' );

			$this->load->view( 'sitemap/error' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Page data
		$this->data['page']->title = 'Site Map';

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'sitemap/html',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _output_xml()
	{
		//	Check cache for $this->_filename_xml
		if ( ! $this->_check_cache( $this->_filename_xml ) ) :

			return;

		endif;

		// --------------------------------------------------------------------------

		//	Set XML headers
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Content-type: text/xml' );
		header( 'Pragma: no-cache' );

		readfile( DEPLOY_CACHE_DIR . $this->_filename_xml );

		// --------------------------------------------------------------------------

		//	Kill script, th, th, that's all folks.
		//	Stop the output class from hijacking our headers and
		//	setting an incorrect Content-Type

		exit(0);
	}


	// --------------------------------------------------------------------------


	protected function _output_json()
	{
		//	Check cache for $this->_filename_json
		if ( ! $this->_check_cache( $this->_filename_json ) ) :

			return;

		endif;

		// --------------------------------------------------------------------------

		//	Set JSON headers
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Content-type: application/json' );
		header( 'Pragma: no-cache' );

		//	Stream
		readfile( DEPLOY_CACHE_DIR . $this->_filename_json );

		// --------------------------------------------------------------------------

		//	Kill script, th, th, that's all folks.
		//	Stop the output class from hijacking our headers and
		//	setting an incorrect Content-Type

		exit(0);
	}


	// --------------------------------------------------------------------------


	protected function _check_cache( $file )
	{
		//	Check cache for $file
		if ( ! is_file( DEPLOY_CACHE_DIR . $file ) ) :

			//	If not found, generate
			$this->load->model( 'sitemap/sitemap_model' );

			if ( ! $this->sitemap_model->generate() ) :

				//	Failed to generate sitemap
				_LOG( 'Failed to generate sitemap: ' . $this->sitemap_model->last_error() );

				//	Let the dev's know too, this could be serious
				send_developer_mail( 'Failed to generate sitemap', 'There was no ' . $file . ' data in the cache and I failed to recreate it.' );

				//	Send a temporarily unavailable header, we don't want search engines unlisting us because of this.
				$this->output->set_header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 503 Service Temporarily Unavailable' );
				$this->output->set_header( 'Status: 503 Service Temporarily Unavailable' );
				$this->output->set_header( 'Retry-After: 7200' );

				$this->load->view( 'sitemap/error' );
				return FALSE;

			endif;

		endif;

		return TRUE;
	}

}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' EMAIL MODULES
 *
 * The following block of code makes it simple to extend one of the core Nails
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SITEMAP' ) ) :

	class Sitemap extends NAILS_Sitemap
	{
	}

endif;


/* End of file sitemap.php */
/* Location: ./application/modules/sitemap/controllers/sitemap.php */