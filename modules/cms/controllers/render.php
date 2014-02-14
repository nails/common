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

class NAILS_Render extends NAILS_CMS_Controller
{
	protected $_page_id;
	protected $_is_preview;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->load->model( 'cms/cms_page_model', 'cms_page' );

		// --------------------------------------------------------------------------

		$this->_page_id		= $this->uri->rsegment( 3 );
		$this->_is_preview	= FALSE;
	}


	// --------------------------------------------------------------------------


	public function page( $preview = FALSE )
	{
		$_page = $this->cms_page->get_by_id( $this->_page_id, TRUE );

		if ( ! $_page || $_page->is_deleted ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	If a page is not published, show_404()
		if ( ! $_page->is_published ) :

			if ( ! $this->_is_preview ) :

				show_404();

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Determine which data to use
		if ( $this->_is_preview ) :

			$_data = $_page->draft;

		else :

			$_data = $_page->published;

		endif;

		// --------------------------------------------------------------------------

		//	Are we using the render caches or not?
		if ( DEPLOY_CMS_PAGES_USE_CACHE ) :

			$this->output->set_output( $_data->rendered_html );

		else :

			//	Set some page level data
			$this->data['page']->title			= $_data->title;
			$this->data['page']->description	= $_data->seo_description;
			$this->data['page']->keywords		= $_data->seo_keywords;

			//	Render the template
			$_tpl_data	= isset( $_data->template_data->widget_areas->{$_data->template} ) ? $_data->template_data->widget_areas->{$_data->template} : array();
			$_html		= $this->cms_page->render_template( $_data->template, $_tpl_data, $this->data );

			$this->output->set_output( $_html );

		endif;
	}


	// --------------------------------------------------------------------------


	public function preview()
	{
		if ( $this->user->is_admin() && user_has_permission( 'admin.cms.can_edit_page' ) ) :

			$this->_is_preview = TRUE;
			return $this->page();

		else :

			show_404();

		endif;
	}


	// --------------------------------------------------------------------------


	public function legacy_slug()
	{
		//	Get the page and attempt to 301 redirect
		$_id = $this->uri->rsegment( 3 );

		if ( $_id ) :

			$_page = $this->cms_page->get_by_id( $_id );

			if ( $_page && $_page->is_published ) :

				redirect( $_page->published->slug, 'location', 301 );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	We don't know what to do, *falls over*
		show_404();
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CMS_RENDER' ) ) :

	class Render extends NAILS_Render
	{
	}

endif;