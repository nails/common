<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Controller extends MX_Controller {

	protected $data;
	protected $user;
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
		
		//	Do we need to instanciate the database?
		if ( defined( 'DB_USERNAME' ) && DB_USERNAME && defined( 'DB_DATABASE' ) && DB_DATABASE ) :
		
			$this->load->database();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Define empty values
		$this->data	= array();
		
		// --------------------------------------------------------------------------
		
		//	Define the Nails version constant
		define( 'NAILS_VERSION',	'0.0.0' );
		define( 'NAILS_RELEASED',	'Never Released' );
		
		//	Include the environment Nails config
		if ( ! file_exists( NAILS_PATH . '/config/_nails.php' ) ) :
		
			die( 'Nails environment not correctly configured.' );
		
		endif;
		
		require_once( NAILS_PATH . '/config/_nails.php' );
		
		// --------------------------------------------------------------------------
		
		//	Determine which modules are to be loaded
		//	Duplicate in CORE_NAILS_Model.php
		
		$_app_modules	= explode( ',', APP_NAILS_MODULES );
		$_app_modules	= array_unique( $_app_modules );
		$_app_modules	= array_filter( $_app_modules );
		$_app_modules	= array_combine( $_app_modules, $_app_modules );
		
		$this->nails_modules = array();
		foreach ( $_app_modules AS $module ) :
		
			preg_match( '/^(.*?)(\[(.*?)\])?$/', $module, $_matches );
			
			if ( isset( $_matches[1] ) && isset( $_matches[3] ) ) :
			
				$this->nails_modules[$_matches[1]] = explode( '|', $_matches[3] );
			
			elseif ( isset( $_matches[1] ) ) :
			
				$this->nails_modules[$_matches[1]] = array();
			
			endif;
		
		endforeach;
		
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
		preg_match( '/^(.*?)(\[(.*?)\])?$/', $module, $_matches );
		
		$_module	= isset( $_matches[1] ) ? $_matches[1] : '';
		$_submodule	= isset( $_matches[3] ) ? $_matches[3] : '';
		
		if ( isset( $this->nails_modules[$_module] ) ) :
		
			//	Are we testing for a submodule in particular?
			if ( $_submodule ) :
			
				return array_search( $_submodule, $this->nails_modules[$_module] ) !== FALSE;
			
			else :
			
				return TRUE;
			
			endif;
		
		else :
		
			return FALSE;
		
		endif;
	}
}

/* End of file NAILS_Controller.php */
/* Location: ./application/core/NAILS_Controller.php */