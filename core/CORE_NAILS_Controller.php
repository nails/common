<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Controller extends MX_Controller {

	protected $data;
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

		//	Error styles
		$_styles = <<<EOT

			<style type="text/css">

				p {font-family:monospace;margin:20px 10px;}
				strong { color:red;}
				code { padding:5px;border:1px solid #CCC;background:#EEE }

			</style>

EOT;

		//	Include the environment Nails config
		if ( ! file_exists( NAILS_PATH . '/config/_nails.php' ) ) :

			echo $_styles;
			echo '<p><strong>ERROR:</strong> Nails. environment not correctly configured; config file not found.</p>';
			exit( 0 );

		endif;

		require_once( NAILS_PATH . '/config/_nails.php' );

		// --------------------------------------------------------------------------

		//	Include the composer autoloader
		if ( ! file_exists( NAILS_PATH . '/vendor/autoload.php' ) ) :

			echo $_styles;
			echo '<p><strong>ERROR:</strong> Composer autoloader not found; run <code>composer install</code> to install dependencies.</p>';
			exit( 0 );

		endif;

		require_once( NAILS_PATH . '/vendor/autoload.php' );

		// --------------------------------------------------------------------------

		//	Define data array (used extensively in views)
		$this->data	= array();

		// --------------------------------------------------------------------------

		//	Define constants (set defaults if not already set)
		$this->_define_constants();

		// --------------------------------------------------------------------------

		//	Is Nails in maintenance mode?
		$this->_maintenance_mode();

		// --------------------------------------------------------------------------

		//	If we're on a staging environment then prompt for a password;
		//	but only if a password has been defined in app.php

		$this->_staging();

		// --------------------------------------------------------------------------

		//	Load these items, everytime.
		$this->_autoload_items();

		// --------------------------------------------------------------------------

		//	Do we need to instanciate the database?
		$this->_instanciate_db();

		// --------------------------------------------------------------------------

		//	Instanciate the user model
		$this->_instanciate_user();

		// --------------------------------------------------------------------------

		//	Instanciate languages
		$this->_instanciate_languages();

		// --------------------------------------------------------------------------

		//	Is the suer suspended?
		//	Executed here so that both the user and language systems are initialised
		//	(so that any errors can be shown in the correct language).

		$this->_is_user_suspended();

		// --------------------------------------------------------------------------

		//	Instanciate DateTime
		$this->_instanciate_datetime();

		// --------------------------------------------------------------------------

		//	Profiling
		$this->_instanciate_profiler();

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
	}


	// --------------------------------------------------------------------------


	protected function _define_constants()
	{
		//	Define the Nails version constant
		define( 'NAILS_VERSION',	'0.1.0' );

		// --------------------------------------------------------------------------

		//	Default Nails. constants
		//	These should be defined in config/_nails.php

		if ( ! defined( 'NAILS_ENVIRONMENT') )				define( 'NAILS_ENVIRONMENT',			'development' );
		if ( ! defined( 'NAILS_MAINTENANCE') )				define( 'NAILS_MAINTENANCE',			FALSE );
		if ( ! defined( 'NAILS_MAINTENANCE_WHITELIST') )	define( 'NAILS_MAINTENANCE_WHITELIST',	'127.0.0.1' );
		if ( ! defined( 'NAILS_DEFAULT_TIMEZONE') )			define( 'NAILS_DEFAULT_TIMEZONE',		'UTC' );
		if ( ! defined( 'NAILS_URL') )						define( 'NAILS_URL',					site_url( 'vendor/shed/nails/assets/' ) );
		if ( ! defined( 'NAILS_STAGING_USERPASS') )			define( 'NAILS_STAGING_USERPASS',		serialize( array() ) );
		if ( ! defined( 'NAILS_EMAIL_DEVELOPER') )			define( 'NAILS_EMAIL_DEVELOPER',		'' );

		// --------------------------------------------------------------------------

		//	Default app constants (if not already defined)
		if ( ! defined( 'APP_PRIVATE_KEY' ) )				define( 'APP_PRIVATE_KEY',				'' );
		if ( ! defined( 'APP_NAME' ) )						define( 'APP_NAME',						'Untitled' );
		if ( ! defined( 'APP_EMAIL_FROM_NAME' ) )			define( 'APP_EMAIL_FROM_NAME',			APP_NAME );
		if ( ! defined( 'APP_EMAIL_FROM_EMAIL' ) )			define( 'APP_EMAIL_FROM_EMAIL',			'' );
		if ( ! defined( 'APP_EMAIL_DEVELOPER' ) )			define( 'APP_EMAIL_DEVELOPER',			'' );
		if ( ! defined( 'APP_USER_ALLOW_REGISTRATION' ) )	define( 'APP_USER_ALLOW_REGISTRATION',	FALSE );
		if ( ! defined( 'APP_USER_DEFAULT_GROUP' ) )		define( 'APP_USER_DEFAULT_GROUP',		3 );
		if ( ! defined( 'APP_MULTI_LANG' ) )				define( 'APP_MULTI_LANG',				FALSE );
		if ( ! defined( 'APP_DEFAULT_LANG_SLUG' ) )			define( 'APP_DEFAULT_LANG_SLUG',		'english' );
		if ( ! defined( 'APP_NAILS_MODULES' ) )				define( 'APP_NAILS_MODULES',			'' );
		if ( ! defined( 'SSL_ROUTING' ) )					define( 'SSL_ROUTING',					FALSE );
		if ( ! defined( 'APP_STAGING_USERPASS' ) )			define( 'APP_STAGING_USERPASS',			serialize( array() ) );

		// --------------------------------------------------------------------------

		//	Email
		if ( ! defined( 'SMTP_HOST' ) )						define( 'SMTP_HOST',					'' );
		if ( ! defined( 'SMTP_USERNAME' ) )					define( 'SMTP_USERNAME',				'' );
		if ( ! defined( 'SMTP_PASSWORD' ) )					define( 'SMTP_PASSWORD',				'' );
		if ( ! defined( 'SMTP_PORT' ) )						define( 'SMTP_PORT',					'' );


		// --------------------------------------------------------------------------

		//	Database Debug
		if ( ! defined( 'DB_DEBUG' ) ) :

			if ( ENVIRONMENT == 'production' ) :

				define( 'DB_DEBUG', FALSE );

			else :

				define( 'DB_DEBUG', TRUE );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Default common API credentials
		if ( ! defined( 'NAILS_SHOP_OPENEXCHANGERATES_APP_ID') )	define( 'NAILS_SHOP_OPENEXCHANGERATES_APP_ID',	'' );

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


	protected function _staging()
	{
		$_users_nails	= @unserialize( NAILS_STAGING_USERPASS );
		$_users_app		= @unserialize( APP_STAGING_USERPASS );

		if ( ENVIRONMENT == 'staging' && ( $_users_nails || $_users_app ) ) :

			if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) ) :

				$this->_staging_request_credentials();

			endif;

			if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) :

				//	Determine the users
				$_users			= array_filter( array_merge( (array) $_users_nails, (array) $_users_app ) );
				$_user_check	= array();

				foreach ( $_users AS $user ) :

					$_user_check[$user[0]] = $user[1];

				endforeach;

				if ( ! isset( $_user_check[$_SERVER['PHP_AUTH_USER']] ) || $_user_check[$_SERVER['PHP_AUTH_USER']] != $_SERVER['PHP_AUTH_PW'] ) :

					$this->_staging_request_credentials();

				endif;

			else :

				$this->_staging_request_credentials();

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _staging_request_credentials()
	{
		header( 'WWW-Authenticate: Basic realm="' . APP_NAME . ' Staging Area"' );
		header( 'HTTP/1.0 401 Unauthorized' );
		?>
		<!DOCTYPE html>
		<html>
			<head>
				<title><?=APP_NAME?> - Unauthorised</title>
				<meta charset="utf-8">

				<!--	STYLES	-->
				<link href="<?=NAILS_URL?>css/nails.default.css" rel="stylesheet">

				<style type="text/css">

					#main-col
					{
						text-align:center;
						margin-top:100px;
					}

				</style>

			</head>
			<body>
				<div class="container row">
					<div class="six columns first last offset-by-five" id="main-col">
						<h1>unauthorised</h1>
						<hr />
						<p>This staging environment restrticted to authorised users only.</p>
					</div>
				</div>
			</body>
		</html>
		<?php
		exit( 0 );
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


	protected function _instanciate_datetime()
	{
		//	Pass the user object to the datetime model
		$this->datetime->set_usr_obj( $this->user );

		// --------------------------------------------------------------------------

		//	Set the timezones
		$_timezone_user = active_user( 'timezone' ) ? active_user( 'timezone' ) : NAILS_DEFAULT_TIMEZONE;
		$this->datetime->set_timezones( 'UTC', $_timezone_user );

		// --------------------------------------------------------------------------

		//	Set the default date/time formats
		$_format_date	= active_user( 'pref_date_format' ) ? active_user( 'pref_date_format' ) : 'Y-m-d';
		$_format_time	= active_user( 'pref_time_format' ) ? active_user( 'pref_time_format' ) : 'H:i:s';
		$this->datetime->set_formats( $_format_date, $_format_time );

		// --------------------------------------------------------------------------

		//	Make sure the system is running on UTC
		date_default_timezone_set( 'UTC' );

		// --------------------------------------------------------------------------

		//	Make sure the DB is thinking along the same lines
		if ( NAILS_DB_ENABLED ) :

			$this->db->query( 'SET time_zone = \'+0:00\'' );

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
		//	Pass the user object to the language model
		$this->language->set_usr_obj( $this->user );

		//	Check default lang is supported by nails
		$this->_supported	= array();
		$this->_supported[]	= 'english';

		if ( array_search( APP_DEFAULT_LANG_SLUG, $this->_supported ) === FALSE ) :

	 		header( 'HTTP/1.1 500 Bad Request' );
			die( 'ERROR: Default language "' . APP_DEFAULT_LANG_SLUG . '" is not a supported language.' );

		endif;

		define( 'APP_DEFAULT_LANG_ID',		$this->language->get_default_id() );
		define( 'APP_DEFAULT_LANG_NAME',	$this->language->get_default_name() );

		// --------------------------------------------------------------------------

		//	Load the Nails. generic lang file
		$this->lang->load( 'nails' );

		// --------------------------------------------------------------------------

		//	Set any global preferences for this user, e.g languages, fall back to
		//	the app's default language (defined in config.php).

		$_user_pref = active_user( 'language_setting' );

		if ( isset( $_user_pref->slug ) && $_user_pref->slug ) :

			define( 'RENDER_LANG_SLUG',	$_user_pref->slug );
			define( 'RENDER_LANG_ID',	$_user_pref->id );

		else :

			define( 'RENDER_LANG_SLUG',	APP_DEFAULT_LANG_SLUG );
			define( 'RENDER_LANG_ID',	APP_DEFAULT_LANG_ID );

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _autoload_items()
	{
		$_packages		= array();
		$_packages[]	= NAILS_PATH;

		foreach ( $_packages AS $package ) :

			$this->load->add_package_path( $package );

		endforeach;

		// --------------------------------------------------------------------------

		$_libraries		= array();
		$_libraries[]	= 'session';
		$_libraries[]	= 'encrypt';
		$_libraries[]	= 'asset';

		foreach ( $_libraries AS $library ) :

			if ( is_array( $library ) ) :

				$this->load->library( $library[0], $library[1] );

			else :

				$this->load->library( $library );

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Load the system helper
		$this->load->helper( 'system' );

		// --------------------------------------------------------------------------

		$_helpers		= array();
		$_helpers[]		= 'site';
		$_helpers[]		= 'datetime';
		$_helpers[]		= 'url';
		$_helpers[]		= 'form';
		$_helpers[]		= 'html';
		$_helpers[]		= 'tools';
		$_helpers[]		= 'debug';
		$_helpers[]		= 'language';
		$_helpers[]		= 'text';
		$_helpers[]		= 'exception';
		$_helpers[]		= 'typography';
		$_helpers[]		= 'event';

		//	Module specific helpers
		//	CDN
		if ( module_is_enabled( 'cdn' ) ) :

			$_helpers[]	= 'cdn';

		endif;

		//	Shop
		if ( module_is_enabled( 'shop' ) ) :

			$_helpers[]	= 'shop';

		endif;

		//	Blog
		if ( module_is_enabled( 'blog' ) ) :

			$_helpers[]	= 'blog';

		endif;

		//	CMS
		if ( module_is_enabled( 'cms' ) ) :

			$_helpers[]	= 'cms';

		endif;

		//	Load...
		foreach ( $_helpers AS $helper ) :

			$this->load->helper( $helper );

		endforeach;


		// --------------------------------------------------------------------------

		$_models	= array();
		$_models[]	= array( 'system/site_model', 'site' );
		$_models[]	= array( 'system/user_model', 'user' );
		$_models[]	= array( 'system/datetime_model', 'datetime' );
		$_models[]	= array( 'system/language_model', 'language' );

		foreach ( $_models AS $model ) :

			if ( is_array( $model ) ) :

				$this->load->model( $model[0], $model[1] );

			else :

				$this->load->model( $model );

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		$_nails_assets		= array();
		$_nails_assets[]	= 'nails.default.css';
		$_nails_assets[]	= 'nails.default.min.js';

		foreach ( $_nails_assets AS $asset ) :

			$this->asset->load( $asset, TRUE );

		endforeach;

		//	App assets
		if ( file_exists( FCPATH . 'assets/css/styles.css' ) ) :

			$this->asset->load( 'styles.css' );

		endif;

		// --------------------------------------------------------------------------

		//	Inline assets
		$_js  = 'var _nails;';
		$_js .= '$(function(){';

		$_js .= 'if ( typeof( NAILS_JS ) === \'function\' ){';
		$_js .= '_nails = new NAILS_JS();';
		$_js .= '_nails.init();}';

		$_js .= '});';

		$this->asset->inline( '<script>' . $_js . '</script>' );
	}

	// --------------------------------------------------------------------------


	protected function _instanciate_user()
	{
		//	Set a $user variable (for the views)
		$this->data['user'] =& $this->user;

		//	Define the NAILS_USR_OBJ constant; this is used in get_userobject() to
		//	reference the user model

		define( 'NAILS_USR_OBJ', 'user' );

		// --------------------------------------------------------------------------

		//	Find a remembered user and initialise the user model; this routine checks
		//	the user's cookies and set's up the session for an existing or new user.

		$this->user->find_remembered_user();
		$this->user->init();
	}


	// --------------------------------------------------------------------------


	protected function _is_user_suspended()
	{
		//	Check if this user is suspended
		if ( $this->user->is_logged_in() && active_user( 'is_suspended' ) ) :

			//	Load models and langs
			$this->load->model( 'auth/auth_model' );
			$this->lang->load( 'auth/auth', RENDER_LANG_SLUG );

			//	Log the user out
			$this->auth_model->logout();

			//	Create a new session
			$this->session->sess_create();

			//	Give them feedback
			$this->session->set_flashdata( 'error', lang( 'auth_login_fail_suspended' ) );
			redirect( '/' );

		endif;
	}
}

/* End of file NAILS_Controller.php */
/* Location: ./application/core/NAILS_Controller.php */