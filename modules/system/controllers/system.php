<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		System 
 *
 * Description:	Used for various misc. functionality
 * 
 **/

class System extends NAILS_Controller
{
	/**
	 * Handle 404 errors
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function render_404()
	{
		show_404();
	}
}

/* End of file system.php */
/* Location: ./application/modules/system/controllers/system.php */