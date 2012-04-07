<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin - Content
*
* Docs:			-
*
* Created:		01/06/2011
* Modified:		11/01/2012
*
* Description:	-
* 
*/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

class Content extends Admin_Controller {
	
	
	/**
	 * Announces this module's details to anyone who asks.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function announce()
	{
		//	Configurations
		$d->priority			= 15;				//	Module's order in nav (unique).
		$d->name				= 'Content';		//	Display name.
		$d->funcs['blog']		= 'Blog';			//	Sub-nav function.
		$d->funcs['faq']		= 'FAQ';			//	Sub-nav function.
		$d->announce_to			= array();			//	Which groups can access this module.
		$d->searchable			= FALSE;			//	Is module searchable?
		
		//	Dynamic
		$d->base_url		= basename( __FILE__, '.php' );	//	For link generation.
		
		return $d;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Handle requests for the blog; redirect to custom controller
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function blog()
	{
		redirect( 'admin/blog' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Handle requests for the FAQ's; redirect to custom controller
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function faq()
	{
		redirect( 'admin/faq' );
	}
}

/* End of file content.php */
/* Location: ./application/modules/admin/controllers/content.php */