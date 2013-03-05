<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Email Controller
 * 
 * Description:	Executes common email functionality
 * 
 **/

class NAILS_Email_Controller extends NAILS_Controller
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Load language file
		$this->lang->load( 'email', RENDER_LANG );
	}
}


/* End of file _email.php */
/* Location: ./application/modules/email/controllers/_email.php */