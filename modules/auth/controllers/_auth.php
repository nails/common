<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NAILS_Auth_Controller
 *
 * Created:		18/11/2012
 * Modified:	18/11/2012
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
		if ( ! $this->_module_is_enabled( 'auth' ) ) :
		
			//	Cancel execution, module isn't enabled
			show_404();
		
		endif;
	}
}

/* End of file _auth.php */
/* Location: ./application/modules/api/controllers/_auth.php */