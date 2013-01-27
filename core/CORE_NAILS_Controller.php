<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Controller extends MX_Controller {

	protected $data;
	protected $user;
	protected $nails;
	protected $nails_modules;
	
	// --------------------------------------------------------------------------
	
	/**
	 * Build the main framework. All autoloaded items have been loaded and
	 * instanciated by this point and are safe to use.
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
		
		//	Define empty values
		$this->data	= array();
		
		// --------------------------------------------------------------------------
		
		//	Define the Nails version constant
		define( 'NAILS_VERSION',	'2.4.1' );
		define( 'NAILS_RELEASED',	'16th March 2012 @ 18:12' );
		
		// --------------------------------------------------------------------------
		
		//	Determine which modules are to be loaded; include default modules
		//	Duplicate in CORE_NAILS_Model.php
		
		$_app_modules			= explode( ',', 'auth,admin,dashboard,' . APP_NAILS_MODULES );
		$this->nails_modules	= array_unique( $_app_modules );
		$this->nails_modules	= array_filter( $this->nails_modules );
		
		// --------------------------------------------------------------------------
		
		//	Profiling
		if ( defined( 'PROFILING' ) && PROFILING ) :
			
			/**
			 * Enable profiler if not AJAX or CI request and there's no user_token. user_token
			 * is used by uploadify to validate the upload due to uploadify not passing
			 * the session during upload.
			 * 
			 **/
			
			if ( ! $this->input->is_cli_request() && ! $this->input->is_ajax_request() && ! $this->input->post( 'user_token' ) ) :
			
				$this->output->enable_profiler( TRUE );
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load the generic user model
		$this->load->model( 'CORE_NAILS_user_model' );
		
		// --------------------------------------------------------------------------
		
		//	Check to see if the app is extending the user object; if it is load it up
		//	and set up all our handy shortcut references.
		
		if ( file_exists( FCPATH . APPPATH . 'models/user_model.php' ) ) :
		
			//	Extension detected, load 'er up!
			$this->load->model( 'user_model' );
			
			//	Set references
			$this->user			=& $this->user_model;
			$this->data['user'] =& $this->user_model;
			
			// --------------------------------------------------------------------------
			
			//	Define the NAILS_USR_OBJ constant; this is used in get_userobject() to
			//	reference the user model
			
			define( 'NAILS_USR_OBJ', 'user_model' );
			
		else :
		
			//	Not being extended, reference as normal
			$this->user			=& $this->CORE_NAILS_user_model;
			$this->data['user'] =& $this->CORE_NAILS_user_model;
			
			// --------------------------------------------------------------------------
			
			//	Define the NAILS_USR_OBJ constant; this is used in get_userobject() to
			//	reference the user model
			
			define( 'NAILS_USR_OBJ', 'CORE_NAILS_user_model' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Find a remembered user and initialise the user model; this routine checks
		//	the user's cookies and set's up the session for an existing or new user.
		
		$this->user->find_remembered_user();
		$this->user->init();
		
		// --------------------------------------------------------------------------
		
		//	Load the Nails Helper Model
		$this->load->model( 'CORE_NAILS_nails_model' );
		
		// --------------------------------------------------------------------------
		
		//	Reference to the nails model (mainly for simplicity)
		$this->nails =& $this->CORE_NAILS_nails_model;
		
		// --------------------------------------------------------------------------
		
		//	Set alerts
		
		//	These are hooks for code to add feedback messages to the user.
		$this->data['notice']	= $this->session->flashdata( 'notice' );
		$this->data['message']	= $this->session->flashdata( 'message' );
		$this->data['error']	= $this->session->flashdata( 'error' );
		$this->data['success']	= $this->session->flashdata( 'success' );
		
		// --------------------------------------------------------------------------
		
		//	Meta Defaults - these appear in the header files
		$this->data['title']		= $this->config->item( 'title' );
		$this->data['description']	= $this->config->item( 'description' );
		$this->data['keywords']		= $this->config->item( 'keywords' );
		
		
		// --------------------------------------------------------------------------
		
		//	Other defaults
		$this->data['page']	= new stdClass();
		
		
		// --------------------------------------------------------------------------
		
		/**
		 * Load any models which may require the use of $this->user here; using
		 * autoload will mean they are loaded before the user_model and may cause
		 * object referencing heartache.
		 *
		 **/
		
		// $this->load->model( 'some_model' );
		
		
		// --------------------------------------------------------------------------
		
		
		/**
		 * SANDBOX
		 * 
		 * This is where you should test your code; all user data has been loaded
		 * and the module constructor is about to be called.
		 * 
		 * DO NOT COMMIT CODE HERE TO THE REPO ONLY USE THIS LOCALLY!
		 * 
		 * - Pablo
		 * 
		 **/
		
		
		
		
		
		// END SANDBOX
	}
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether a module is defined in APP_NAILS_MODULE
	 *
	 * @access	protected
	 * @param	string	$module	The module to look for
	 * @return	bool
	 * @author	Pablo
	 **/
	protected function _module_is_enabled( $module )
	{
		return array_search( strtolower( $module ), $this->nails_modules ) !== FALSE;
	}
}

/* End of file NAILS_Controller.php */
/* Location: ./application/core/NAILS_Controller.php */