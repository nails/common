<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Controller extends MX_Controller {

	protected $data;
	private $_supported_lang;

	// --------------------------------------------------------------------------

	/**
	 * Build the main framework. All autoloaded items have been loaded and
	 * instantiated by this point and are safe to use.
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Set the level of error reporting
		$this->_set_error_reporting();

		// --------------------------------------------------------------------------

		//	Include the composer autoloader
		if ( ! file_exists( FCPATH . 'vendor/autoload.php' ) ) :

			echo '<style type="text/css">';
			echo 'p {font-family:monospace;margin:20px 10px;}';
			echo 'strong { color:red;}';
			echo 'code { padding:5px;border:1px solid #CCC;background:#EEE }';
			echo '</style>';
			echo '<p><strong>ERROR:</strong> Composer autoloader not found; run <code>composer install</code> to install dependencies.</p>';
			exit( 0 );

		endif;

		require_once( FCPATH . 'vendor/autoload.php' );

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

		//	Load, instantiate and apply the fatal error handler
		$this->_fatal_error_handler();

		// --------------------------------------------------------------------------

		//	Do we need to instantiate the database?
		$this->_instantiate_db();

		// --------------------------------------------------------------------------

		//	Instanciate the user model
		$this->_instantiate_user();

		// --------------------------------------------------------------------------

		//	Instanciate languages
		$this->_instantiate_languages();

		// --------------------------------------------------------------------------

		//	Is the suer suspended?
		//	Executed here so that both the user and language systems are initialised
		//	(so that any errors can be shown in the correct language).

		$this->_is_user_suspended();

		// --------------------------------------------------------------------------

		//	Instanciate DateTime
		$this->_instantiate_datetime();

		// --------------------------------------------------------------------------

		//	Profiling
		$this->_instantiate_profiler();

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


	protected function _set_error_reporting()
	{
	 	switch( ENVIRONMENT ) :

	 		case 'production' :

	 			//	Suppress all errors on production
	 			error_reporting( 0 );

	 		break;

	 		// --------------------------------------------------------------------------

	 		default :

	 			//	Show errors everywhere else
	 			error_reporting( E_ALL|E_STRICT );

	 		break;

	 	endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _define_constants()
	{
		//	Define the Nails version constant
		define( 'NAILS_VERSION',	'0.1.0' );

		// --------------------------------------------------------------------------

		//	These settings can be specified wherever it makes most sense (e.g if
		//	maintenance mode needs enabled app wide, then specify it in app.php, if
		//	only a single server needs to be put in maintenance mode then define in
		//	deploy.php

		if ( ! defined( 'MAINTENANCE') )					define( 'MAINTENANCE',					FALSE );
		if ( ! defined( 'MAINTENANCE_WHITELIST') )			define( 'MAINTENANCE_WHITELIST',		'127.0.0.1' );

		// --------------------------------------------------------------------------

		//	Default app constants (if not already defined)
		//	These should be specified in settings/app.php

		if ( ! defined( 'NAILS_DB_PREFIX' ) )				define( 'NAILS_DB_PREFIX',				'nails_' );
		if ( ! defined( 'APP_PRIVATE_KEY' ) )				define( 'APP_PRIVATE_KEY',				'' );
		if ( ! defined( 'APP_NAME' ) )						define( 'APP_NAME',						'Untitled' );
		if ( ! defined( 'APP_EMAIL_FROM_NAME' ) )			define( 'APP_EMAIL_FROM_NAME',			APP_NAME );
		if ( ! defined( 'APP_EMAIL_FROM_EMAIL' ) )			define( 'APP_EMAIL_FROM_EMAIL',			'' );
		if ( ! defined( 'APP_DEVELOPER_EMAIL' ) )			define( 'APP_DEVELOPER_EMAIL',			'' );
		if ( ! defined( 'APP_USER_ALLOW_REGISTRATION' ) )	define( 'APP_USER_ALLOW_REGISTRATION',	FALSE );
		if ( ! defined( 'APP_USER_DEFAULT_GROUP' ) )		define( 'APP_USER_DEFAULT_GROUP',		3 );
		if ( ! defined( 'APP_MULTI_LANG' ) )				define( 'APP_MULTI_LANG',				FALSE );
		if ( ! defined( 'APP_DEFAULT_LANG_SLUG' ) )			define( 'APP_DEFAULT_LANG_SLUG',		'english' );
		if ( ! defined( 'APP_NAILS_MODULES' ) )				define( 'APP_NAILS_MODULES',			'' );
		if ( ! defined( 'APP_STAGING_USERPASS' ) )			define( 'APP_STAGING_USERPASS',			serialize( array() ) );
		if ( ! defined( 'APP_SSL_ROUTING' ) )				define( 'APP_SSL_ROUTING',				FALSE );
		if ( ! defined( 'APP_DEFAULT_TIMEZONE' ) )			define( 'APP_DEFAULT_TIMEZONE',			'UTC' );
		if ( ! defined( 'APP_NATIVE_LOGIN_USING' ) )		define( 'APP_NATIVE_LOGIN_USING',		'USERNAME' );	//	[EMAIL|USERNAME|BOTH]

		// --------------------------------------------------------------------------

		//	Deployment specific constants (if not already defined)
		//	These should be specified in settings/deploy.php

		if ( ! defined( 'DEPLOY_SYSTEM_TIMEZONE') )			define( 'DEPLOY_SYSTEM_TIMEZONE',		'UTC' );

		// --------------------------------------------------------------------------

		//	Email
		if ( ! defined( 'SMTP_HOST' ) )						define( 'SMTP_HOST',					'' );
		if ( ! defined( 'SMTP_USERNAME' ) )					define( 'SMTP_USERNAME',				'' );
		if ( ! defined( 'SMTP_PASSWORD' ) )					define( 'SMTP_PASSWORD',				'' );
		if ( ! defined( 'SMTP_PORT' ) )						define( 'SMTP_PORT',					'' );
		if ( ! defined( 'EMAIL_DEBUG' ) )					define( 'EMAIL_DEBUG',					FALSE );

		// --------------------------------------------------------------------------

		//	CDN
		if ( ! defined( 'APP_CDN_DRIVER' ) )				define( 'APP_CDN_DRIVER',				'local' );
		if ( ! defined( 'DEPLOY_CDN_MAGIC') )				define( 'DEPLOY_CDN_MAGIC',				'' );
		if ( ! defined( 'DEPLOY_CDN_PATH') )				define( 'DEPLOY_CDN_PATH',				FCPATH . 'assets/uploads/' );

		//	Define how long CDN items should be cached for, this is a maximum age in seconds
		//	According to http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html this shouldn't be
		//	more than 1 year.

		if ( ! defined( 'APP_CDN_CACHE_MAX_AGE' ) )			define( 'APP_CDN_CACHE_MAX_AGE',		'31536000' ); // 1 year


		// --------------------------------------------------------------------------

		//	SSL
		//	If a SECURE_BASE_URL is not defined then assume the secure URL is simply
		//	https://BASE_URL

		if ( ! defined( 'SECURE_BASE_URL' ) ) :

			//	Not defined, play it safe and just copy the BASE_URL
			define( 'SECURE_BASE_URL', BASE_URL );

		endif;


		//	Set NAILS_URL here as it's dependent on knowing whether SSL is set or not
		//	and if the current page is secure.
		if ( ! defined( 'NAILS_URL') ) :

			if ( APP_SSL_ROUTING && $this->_page_is_secure() ) :

				define( 'NAILS_URL', SECURE_BASE_URL . 'vendor/shed/nails/' );

			else :

				define( 'NAILS_URL', BASE_URL . 'vendor/shed/nails/' );

			endif;

		endif;

		//	Set the NAILS_ASSETS_URL
		if ( ! defined( 'NAILS_ASSETS_URL') ) :

			if ( APP_SSL_ROUTING && $this->_page_is_secure() ) :

				define( 'NAILS_ASSETS_URL', NAILS_URL . 'assets/' );

			else :

				define( 'NAILS_ASSETS_URL', NAILS_URL . 'assets/' );

			endif;

		endif;


		// --------------------------------------------------------------------------

		//	Caching
		if ( ! defined( 'DEPLOY_CACHE_DIR') ) define( 'DEPLOY_CACHE_DIR',				FCPATH . 'application/cache/' );

		//	Update the system configs to use this cache dir
		$this->config->set_item( 'cache_path', DEPLOY_CACHE_DIR );

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
		if ( MAINTENANCE || file_exists( FCPATH . '.MAINTENANCE' ) ) :

			$whitelist_ip = explode(',', MAINTENANCE_WHITELIST );

			if ( ! $this->input->is_cli_request() && array_search( $this->input->ip_address(), $whitelist_ip ) === FALSE ) :

				header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
				header( 'Status: 503 Service Temporarily Unavailable' );
				header( 'Retry-After: 7200' );

				// --------------------------------------------------------------------------

				//	If the request is an AJAX request, or the URL is on the API then spit back JSON
				if ( $this->input->is_ajax_request() || $this->uri->segment( 1 ) == 'api' ) :

					header( 'Cache-Control: no-store, no-cache, must-revalidate' );
					header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
					header( 'Content-type: application/json' );
					header( 'Pragma: no-cache' );

					$_out = array( 'status' => 503, 'error' => 'Down for Maintenance' );

					echo json_encode( $_out );

				//	Otherwise, render some HTML
				else :

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

		 		endif;

				// --------------------------------------------------------------------------

		 		//	Halt script execution
	 			exit(0);

	 		elseif ( $this->input->is_cli_request() ) :

	 			echo 'Down for Maintenance' . "\n";

				// --------------------------------------------------------------------------

		 		//	Halt script execution
	 			exit(0);

		 	endif;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _staging()
	{
		$_users		= @unserialize( APP_STAGING_USERPASS );

		if ( ENVIRONMENT == 'staging' && $_users ) :

			if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) ) :

				$this->_staging_request_credentials();

			endif;

			if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) :

				//	Determine the users
				$_users			= array_filter( $_users );
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
				<link href="<?=NAILS_ASSETS_URL?>css/nails.default.css" rel="stylesheet">

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


	protected function _instantiate_db()
	{
		if ( defined( 'DB_USERNAME' ) && DB_USERNAME && defined( 'DB_DATABASE' ) && DB_DATABASE ) :

			define( 'NAILS_DB_ENABLED', TRUE );
			$this->load->database();

		else :

			define( 'NAILS_DB_ENABLED', FALSE );

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _instantiate_datetime()
	{
		//	Pass the user object to the datetime model
		$this->datetime->set_usr_obj( $this->user );

		// --------------------------------------------------------------------------

		//	Set the timezones.

		//	Choose the user's timezone - starting with their rpeference, followed by
		//	the app's default, followed by the system and if all that fails (how?!)
		//	then default to UTC

		if ( active_user( 'timezone' ) ) :

			$_timezone_user = active_user( 'timezone' );

		elseif( defined( 'APP_DEFAULT_TIMEZONE' ) && APP_DEFAULT_TIMEZONE ) :

			$_timezone_user = APP_DEFAULT_TIMEZONE;

		elseif( defined( 'DEPLOY_SYSTEM_TIMEZONE' ) && DEPLOY_SYSTEM_TIMEZONE ) :

			$_timezone_user = DEPLOY_SYSTEM_TIMEZONE;

		else :

			$_timezone_user = 'UTC';

		endif;

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


	protected function _instantiate_profiler()
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


	protected function _instantiate_languages()
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

		//	Test that $_SERVER is available, the session library needs this
		//	Generally not available when running on the command line. If it's
		//	not available then load up the faux session which has the same methods
		//	as the session library, but behave as if logged out - comprende?

		if ( $this->input->server( 'REMOTE_ADDR' ) ) :

			$_libraries[]	= 'session';

		else :

			$_libraries[]	= array( 'faux_session', 'session' );

		endif;

		$_libraries[]	= 'encrypt';
		$_libraries[]	= 'asset';
		$_libraries[]	= 'logger';

		foreach ( $_libraries AS $library ) :

			if ( is_array( $library ) ) :

				$this->load->library( $library[0], array(), $library[1] );

			else :

				$this->load->library( $library );

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Load the system & user helper
		$this->load->helper( 'system' );
		$this->load->helper( 'user' );

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
		$_helpers[]		= 'log';

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


	protected function _fatal_error_handler()
	{
		$this->load->library( 'Fatal_error_handler' );
	}


	// --------------------------------------------------------------------------


	protected function _instantiate_user()
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


	// --------------------------------------------------------------------------


	protected function _page_is_secure()
	{
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) :

			//	Page is being served through HTTPS
			return TRUE;

		elseif ( isset( $_SERVER['SERVER_NAME'] ) && isset( $_SERVER['REQUEST_URI'] ) && defined( 'SECURE_BASE_URL' ) ) :

			//	Not being served through HTTPS, but does the URL of the page begin
			//	with SECURE_BASE_URL

			$_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

			if (  preg_match( '#^' . SECURE_BASE_URL . '.*#', $_url ) ) :

				return TRUE;

			else :

				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Unknown, assume not
		return FALSE;
	}
}

/* End of file NAILS_Controller.php */
/* Location: ./application/core/NAILS_Controller.php */