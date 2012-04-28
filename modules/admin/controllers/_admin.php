<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin_Controller
*
* Docs:			http://nails.shedcollective.org/docs/admin/
*
* Created:		09/01/2012
* Modified:		09/01/2012
*
* Description:	This controller executes various bits of common admin functionality
* 
*/


class Admin_Controller extends NAILS_Controller
{
	protected $_loaded_modules;
	
	
	// --------------------------------------------------------------------------
	
	
	
	/**
	* Common constructor for all admin pages
	* 
	* @access	public
	* @return	void
	* @author	Pablo
	* 
	**/
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Admins only please
		if ( ! $this->user->is_admin() )
			unauthorised();
		
		// --------------------------------------------------------------------------
		
		//	Load admin helper
		$this->load->model( 'admin_model' );
		
		// --------------------------------------------------------------------------
		
		//	Load up the modules which have been enabled for this installation and the
		//	user has permission to see.
		
		$this->_loaded_modules			= array();
		$this->data['loaded_modules']	=& $this->_loaded_modules;
		$this->_load_active_modules();
		
		// --------------------------------------------------------------------------
		
		//	Check the user has permission to view this module (skip the dashboard
		//	we need to show them _something_)
		
		$_active_module = $this->uri->segment( 2 );
		
		if ( ! isset( $this->_loaded_modules[$_active_module] ) ) :
		
			//	If this is the dashboard, we should see if the user has permission to
			//	access any other modules before we 404 their ass.
			
			if ( $_active_module == 'dashboard' ) :
			
				//	Look at the user's ACL
				$_acl = active_user( 'acl' );
				
				if ( isset( $_acl['admin'] )  ) :
				
					//	If they have other modules defined, loop them until one is found
					//	which appears in the loaded modules list. If this doesn't happen
					//	then they'll fall back to the 'no loaded modules' page.
					
					foreach( $_acl['admin'] AS $option ) :
					
						if ( isset( $this->_loaded_modules[$option] ) ) :
						
							redirect( 'admin/' . $option );
							break;
						
						endif;
					
					endforeach;
				
				endif;
			
			else :
			
				// Oh well, it's not, 404 bitches!	
				show_404();
				
			endif;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load admin helper
		$this->load->helper( 'admin' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	* Loop through the enabled modules and see if a controller exists for it; if
	* it does load it up and execute the annouce static method to see if we can
	* display it to the active user.
	* 
	* @access	public
	* @return	void
	* @author	Pablo
	* 
	**/
	private function _load_active_modules()
	{
		foreach( $this->nails_modules AS $module ) :
		
			$_module = $this->admin_model->find_module( $module );
			if ( $_module )
				$this->_loaded_modules[$module] = $_module;
		
		endforeach;
	}
}