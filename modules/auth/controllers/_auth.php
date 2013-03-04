<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NAILS_Auth_Controller
 *
 * Description:	This controller executes various bits of common admin Auth functionality
 * 
 **/


class NAILS_Auth_Controller extends NAILS_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Check this module is enabled in settings
		if ( ! module_is_enabled( 'auth' ) ) :
		
			//	Cancel execution, module isn't enabled
			show_404();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load model
		$this->load->model( 'auth_model' );
		
		// --------------------------------------------------------------------------
		
		//	Load language file
		//	TODO: Load lang using RENDER_LANG constant
		$this->lang->load( 'auth', 'english' );
	}
}

/* End of file _auth.php */
/* Location: ./application/modules/api/controllers/_auth.php */