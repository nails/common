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

		//	Load config
		$this->config->load( 'auth' );

		// --------------------------------------------------------------------------

		//	Load language file
		$this->lang->load( 'auth', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------

		//	Load model
		$this->load->model( 'auth_model' );
	}
}

/* End of file _auth.php */
/* Location: ./application/modules/auth/controllers/_auth.php */