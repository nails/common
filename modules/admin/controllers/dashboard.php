<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin
*
* Docs:			-
*
* Created:		14/10/2010
* Modified:		20/06/2011
*
* Description:	-
* 
*/

//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

class Dashboard extends Admin_Controller {

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function announce()
	{
		//	Configurations
		$d->name				= 'Dashboard';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['index']		= 'Dashboard';					//	Sub-nav function.
		$d->funcs['help']		= 'Help';						//	Sub-nav function.
		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		$_acl = active_user( 'acl' );
		if ( ! isset( $_acl['admin'] ) || array_search( basename( __FILE__, '.php' ), $_acl['admin'] ) === FALSE )
			return NULL;
		
		// --------------------------------------------------------------------------
		
		//	Hey user! Pick me! Pick me!
		return $d;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Administration homepage / dashboard
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		
		//	If no modules have been discovered it means that while this user is an admin
		//	no modules have either a) been enabled for the site or b) for the user. Either
		//	way we should show a friendly error.
		
		if ( ! $this->_loaded_modules ) :
		
			$this->load->view( 'dashboard/no_modules',		$this->data );
			
		else :
		
			$this->load->view( 'dashboard/dashboard',		$this->data );
		
		endif;
		
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Administration help
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function help()
	{
		//	Load model
		$this->load->model( 'admin_help_model' );
		
		// --------------------------------------------------------------------------
		
		//	Get data
		$this->data['help'] = $this->admin_help_model->get_all();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'dashboard/help',		$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


}

/* End of file admin.php */
/* Location: ./application/modules/admin/controllers/admin.php */