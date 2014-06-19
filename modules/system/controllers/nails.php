<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		System
 *
 * Description:	Used for nails info reporting
 *
 **/

//	Include _system.php; executes common functionality
require_once '_system.php';

/**
 * OVERLOADING NAILS' SYSTEM MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Nails extends NAILS_System_Controller
{
	/**
	 * Return info about the Nails installation
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function info()
	{
		//	Command line or super user only
		if ( ! $this->input->is_cli_request() && ! $this->user_model->is_superuser() ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$_out						= new stdClass();
		$_out->nails				= new stdClass();
		$_out->app					= new stdClass();

		$_version = explode( '.', NAILS_VERSION );

		$_out->nails->version		= NAILS_VERSION;
		$_out->nails->major			= isset( $_version[0] ) ? (int) $_version[0] : 0;
		$_out->nails->minor			= isset( $_version[1] ) ? (int) $_version[1] : 0;
		$_out->nails->patch			= isset( $_version[2] ) ? (int) $_version[2] : 0;

		$_out->app->name			= APP_NAME;
		$_out->app->path			= FCPATH;
		$_out->app->url				= site_url();

		$this->output->set_output( json_encode( $_out ) );
	}


	// --------------------------------------------------------------------------


	public function configure()
	{
		if ( ! $this->user_model->is_superuser() && ! $this->input->is_cli_request() && ! $this->input->get( 'token' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Define page data
		$this->data['page']->title = 'Nails. Configuration Manager';

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->library( 'jqueryui' );
		$this->asset->load( 'nails.configure.css',		TRUE );
		$this->asset->load( 'nails.configure.min.js',	TRUE );

		// --------------------------------------------------------------------------

		//	Load views
		$this->data['header_override'] = 'structure/header/blank';
		$this->data['footer_override'] = 'structure/footer/blank';

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'system/nails/configure/index',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' SYSTEM MODULES
 *
 * The following block of code makes it simple to extend one of the core admin
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_NAILS' ) ) :

	class Nails extends NAILS_Nails
	{
	}

endif;


/* End of file system.php */
/* Location: ./application/modules/system/controllers/system.php */