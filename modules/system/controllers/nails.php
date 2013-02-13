<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		System 
 *
 * Description:	Used for nails info reporting
 * 
 **/

class Nails extends NAILS_Controller
{
	/**
	 * Return info about the Nails installation
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function info()
	{
		//	Command line or super user only
		if ( ! $this->input->is_cli_request() && ! $this->user->is_superuser() ) :
		
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
		$_out->nails->revision		= NAILS_REVISION;
		$_out->nails->environment	= NAILS_ENVIRONMENT;
		
		$_out->app->name			= APP_NAME;
		$_out->app->path			= FCPATH;
		$_out->app->url				= site_url();
				
		$this->output->set_output( json_encode( $_out ) );
	}
}

/* End of file system.php */
/* Location: ./application/modules/system/controllers/system.php */