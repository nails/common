<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			Admin
 *
 * Docs:			http://nails.shedcollective.org/docs/admin/
 *
 * Created:			09/01/2012
 * Modified:		09/01/2012
 *
 * Description:	Exists purely to redirect users to the dashboard
 * 
 **/

class Admin extends NAILS_Controller {

	public function index()
	{
		//	Keep flashdata
		$this->session->keep_flashdata();
		
		// --------------------------------------------------------------------------
		
		//	Cheerio!
		redirect( 'admin/dashboard' );
	}

}

/* End of file admin.php */
/* Location: ./application/modules/admin/controllers/admin.php */