<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Controller extends MX_Controller {

	protected $data;
	protected $user;

	private $_supported_lang;
	
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
		
		//	Include the environment Nails config
		if ( ! file_exists( NAILS_PATH . '/config/_nails.php' ) ) :
		
			die( lang( 'nails_not_configured' ) );
		
		endif;
		
		require_once( NAILS_PATH . '/config/_nails.php' );

		// --------------------------------------------------------------------------
		
		//	Define data array (used extensively in views)
		$this->data	= array();

		// --------------------------------------------------------------------------

		//	Define constants (set defaults if not already set)
		$this->_default_constants();

		// --------------------------------------------------------------------------
		
		//	Is Nails in maintenance mode?
		$this->_maintenance_mode();
		
		// --------------------------------------------------------------------------
		
		//	Do we need to instanciate the database?
		$this->_instanciate_db();
		
		// --------------------------------------------------------------------------
		
		//	Instanciate languages
		$this->_instanciate_languages();
		
		// --------------------------------------------------------------------------
		
		//	Profiling
		$this->_instanciate_profiler();
		
		// --------------------------------------------------------------------------
		
		//	Instanciate the user model
		$this->_instanciate_user();

		// --------------------------------------------------------------------------
		
		//	Set alerts
		
		//	These are hooks for code to add feedback messages to the user.
		$this->data['notice']	= $this->session->flashdata( 'notice' );
		$this->data['message']	= $this->session->flashdata( 'message' );
		$this->data['error']	= $this->session->flashdata( 'error' );
		$this->data['success']	= $this->session->flashdata( 'success' );
		
		// --------------------------------------------------------------------------
		
		//	Other defaults
		$this->data['page']					= new stdClass();
		$this->data['page']->title			= '';
		$this->data['page']->description	= '';
		$this->data['page']->keywords		= '';
		
		// --------------------------------------------------------------------------

		//	Default assets
		$this->asset->load( 'nails.default.css', TRUE );
		$this->asset->load( 'jquery.min.js', TRUE );
		$this->asset->load( 'jquery.fancybox.min.js', TRUE );
		$this->asset->load( 'jquery.tipsy.min.js', TRUE );
		$this->asset->load( 'nails.default.min.js', TRUE );

		//	App assets
		if ( file_exists( FCPATH . 'assets/css/styles.css' ) ) :

			$this->asset->load( 'styles.css' );

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _default_constants()
	{
		//	Define the Nails version constant
		define( 'NAILS_VERSION',	'0.0.0' );
		define( 'NAILS_RELEASED',	'Never Released' );

		// --------------------------------------------------------------------------

		//	Default app constants (if not already defined)
		if ( ! defined( 'APP_PRIVATE_KEY' ) )				define( 'APP_PRIVATE_KEY',				'' );
		if ( ! defined( 'APP_NAME' ) )						define( 'APP_NAME',						'Untitled' );
		if ( ! defined( 'APP_EMAL_FROM_NAME' ) )			define( 'APP_EMAL_FROM_NAME',			APP_NAME );
		if ( ! defined( 'APP_EMAIL_FROM_EMAIL' ) )			define( 'APP_EMAIL_FROM_EMAIL',			'' );
		if ( ! defined( 'APP_EMAIL_DEVELOPER' ) )			define( 'APP_EMAIL_DEVELOPER',			'' );
		if ( ! defined( 'APP_USER_ALLOW_REGISTRATION' ) )	define( 'APP_USER_ALLOW_REGISTRATION',	FALSE );
		if ( ! defined( 'APP_USER_DEFAULT_GROUP' ) )		define( 'APP_USER_DEFAULT_GROUP',		3 );
		if ( ! defined( 'APP_MULTI_LANG' ) )				define( 'APP_MULTI_LANG',				FALSE );
		if ( ! defined( 'APP_DEFAULT_LANG_SAFE' ) )			define( 'APP_DEFAULT_LANG_SAFE',		'english' );
		if ( ! defined( 'APP_NAILS_MODULES' ) )				define( 'APP_NAILS_MODULES',			'' );
		if ( ! defined( 'SSL_ROUTING' ) )					define( 'SSL_ROUTING',					FALSE );

	}


	// --------------------------------------------------------------------------


	protected function _maintenance_mode()
	{
		if ( NAILS_MAINTENANCE ) :

			$whitelist_ip = explode(',', NAILS_MAINTENANCE_WHITELIST );
			
			if ( array_search( $this->input->ip_address(), $whitelist_ip ) === FALSE ) :
			
				header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
				header( 'Status: 503 Service Temporarily Unavailable' );
				header( 'Retry-After: 7200' );
				
				// --------------------------------------------------------------------------
				
		 		//	Look for an app override
		 		if ( file_exists( FCPATH . APPPATH . 'views/maintenance/maintenance.php' ) ) :
		 		
		 			require FCPATH . APPPATH . 'views/maintenance/maintenance.php';
		 		
		 		//	Fall back to the Nails maintenance page
		 		elseif ( file_exists( NAILS_PATH . 'views/maintenance/maintenance.php' ) ):
		 		
		 			require NAILS_PATH . 'views/maintenance/maintenance.php';
		 		
		 		//	Fall back, back to plain text
		 		else :
		 		
		 			echo '<h1>Down for maintenance</h1>';
		 		
		 		endif;
		 		
		 		// --------------------------------------------------------------------------
		 		
		 		//	Halt script execution
	 			exit(0);
		 		
		 	endif;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _instanciate_db()
	{
		if ( defined( 'DB_USERNAME' ) && DB_USERNAME && defined( 'DB_DATABASE' ) && DB_DATABASE ) :
		
			define( 'NAILS_DB_ENABLED', TRUE );
			$this->load->database();
		
		else :
		
			define( 'NAILS_DB_ENABLED', FALSE );
		
		endif;
	}


	// --------------------------------------------------------------------------


	protected function _instanciate_profiler()
	{
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
	}


	// --------------------------------------------------------------------------


	protected function _instanciate_languages()
	{
		//	Load the language model and set the defaults
		$this->load->model( 'core_nails_language_model', 'language_model' );
		
		//	Check default lang is supported by nails
		$this->_supported		= array();
		$this->_supported[]	= 'english';
		
		if ( array_search( APP_DEFAULT_LANG_SAFE, $this->_supported ) === FALSE ) :
		
	 		header( 'HTTP/1.1 500 Bad Request' );
			die( 'ERROR: Default language "' . APP_DEFAULT_LANG_SAFE . '" is not a supported language.' );
		
		endif;
		
		define( 'APP_DEFAULT_LANG_ID',		$this->language_model->get_default_id() );
		define( 'APP_DEFAULT_LANG_NAME',	$this->language_model->get_default_name() );

		// --------------------------------------------------------------------------

		//	Load the Nails. generic lang file
		$this->lang->load( 'nails' );

		// --------------------------------------------------------------------------
		
		//	Set any global preferences for this user, e.g languages, fall back to
		//	the app's default language (defined in config.php).
		
		$_user_pref = active_user( 'language_setting' );
		
		if ( isset( $_user_pref->safe_name ) && $_user_pref->safe_name ) :
		
			define( 'RENDER_LANG',		$_user_pref->safe_name );
			define( 'RENDER_LANG_ID',	$_user_pref->id );
		
		else :
		
			define( 'RENDER_LANG',		APP_DEFAULT_LANG_SAFE );
			define( 'RENDER_LANG_ID',	APP_DEFAULT_LANG_ID );
		
		endif;
	}


	// --------------------------------------------------------------------------


	protected function _instanciate_user()
	{
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
	}
}

/* End of file NAILS_Controller.php */
/* Location: ./application/core/NAILS_Controller.php */