<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Render
 *
 * Description:	Renders a CMS controlled page
 *
 **/

/**
 * OVERLOADING NAILS' AUTH MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

//	Include _cdn.php; executes common functionality
require_once '_cms.php';

class NAILS_Cms extends NAILS_CMS_Controller
{
	private $_slug;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->load->model( 'cms/cms_page_model' );

		// --------------------------------------------------------------------------

		$this->_slug = uri_string();
	}


	// --------------------------------------------------------------------------


	public function index()
	{
		$_page = $this->cms_page_model->get_by_slug( $this->_slug, TRUE );

		if ( ! $_page ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Get the page HTML
		$this->data['page']->title			= $_page->title;
		$this->data['page']->slug			= $_page->slug;
		$this->data['page']->layout			= $_page->layout;
		$this->data['page']->sidebar_width	= $_page->sidebar_width;
		$this->data['rendered_page']		= $this->cms_page_model->render( $_page );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'cms/page/render',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' CMS MODULE
 *
 * The following block of code makes it simple to extend one of the core auth
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CMS' ) ) :

	class Cms extends NAILS_Cms
	{
	}

endif;