<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			Admin_Controller
 *
 * Description:	This controller executes various bits of common admin functionality
 * 
 **/


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
		
		//	Check this module is enabled in settings
		if ( ! $this->_module_is_enabled( 'auth' ) ) :
		
			//	Cancel execution, module isn't enabled
			show_404();
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Admins only please
		if ( ! $this->user->is_admin() ) :
		
			unauthorised();
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load admin helper and config
		$this->load->model( 'admin_model' );
		
		if ( file_exists( FCPATH . 'application/config/admin.php' ) ) :
		
			$this->config->load( 'admin' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load up the modules which have been enabled for this installation and the
		//	user has permission to see.
		
		$this->_loaded_modules			= array();
		$this->data['loaded_modules']	=& $this->_loaded_modules;
		$this->_load_active_modules();
		
		// --------------------------------------------------------------------------
		
		//	Check the user has permission to view this module (skip the dashboard
		//	we need to show them _something_)
		
		$_active_module	= $this->uri->segment( 2 );
		$_active_method	= $this->uri->segment( 3, 'index' );
		$_acl			= active_user( 'acl' );
		
		if ( ! $this->user->is_superuser() && ! isset( $this->_loaded_modules[$_active_module] ) ) :
		
			//	If this is the dashboard, we should see if the user has permission to
			//	access any other modules before we 404 their ass.
			
			if ( $_active_module == 'dashboard' ) :
			
				//	Look at the user's ACL
				if ( isset( $_acl['admin'] )  ) :
				
					//	If they have other modules defined, loop them until one is found
					//	which appears in the loaded modules list. If this doesn't happen
					//	then they'll fall back to the 'no loaded modules' page.
					
					foreach( $_acl['admin'] AS $module => $methods ) :
					
						if ( isset( $this->_loaded_modules[$module] ) ) :
						
							redirect( 'admin/' . $module );
							break;
						
						endif;
					
					endforeach;
				
				endif;
			
			else :
			
				// Oh well, it's not, 404 bitches!	
				show_404();
				
			endif;
		
		elseif ( ! $this->user->is_superuser() ) :
		
			//	Module is OK, check to make sure they can access this method
			if ( ! isset( $_acl['admin'][$_active_module][$_active_method] ) ) :
			
				unauthorised();
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load admin helper
		$this->load->helper( 'admin' );
		
		// --------------------------------------------------------------------------
		
		//	Add the current module to the $page variable (for convenience)
		$this->data['page'] = new stdClass();
		
		if ( isset( $this->_loaded_modules[ $this->uri->segment( 2 ) ] ) ) :
		
			$this->data['page']->module = $this->_loaded_modules[ $this->uri->segment( 2 ) ];
		
		else :
		
			$this->data['page']->moduled = FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Unload any previously loaded assets, admin handles it's own assets
		$this->asset->clear_all();
		
		//	Load admin styles and JS
		$this->asset->load( 'nails.admin.css', TRUE );
		$this->asset->load( 'jquery.tipsy.min.js', TRUE );
		$this->asset->load( 'jquery.chosen.min.js', TRUE );
		$this->asset->load( 'jquery.fancybox.min.js', TRUE );
		$this->asset->load( 'nails.default.min.js', TRUE );
		$this->asset->load( 'nails.admin.min.js', TRUE );
		
		//	Look for any Admin styles provided by the app
		if ( file_exists( FCPATH . 'assets/css/admin.css' ) ) :
		
			$this->asset->load( 'admin.css' );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	static function _can_access( $module, $file )
	{
		$_acl		= active_user( 'acl' );
		$_module	= basename( $file, '.php' );
		
		// --------------------------------------------------------------------------
		
		//	Super users can see what they like
		if ( get_userobject()->is_superuser() ) :
		
			return $module;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Everyone else needs to have the correct ACL
		if ( isset( $_acl['admin'][$_module] ) ) :
		
			return $module;
		
		else :
		
			return NULL;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _get_current_module()
	{
		here();
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