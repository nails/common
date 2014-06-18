<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		System Controller
 *
 * Description:	Executes common system functionality
 *
 **/

class NAILS_System_Controller extends NAILS_Controller
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Load language file
		$this->lang->load( 'system' );
	}
}


/* End of file _system.php */
/* Location: ./application/modules/system/controllers/_system.php */