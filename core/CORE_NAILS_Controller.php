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

		//	Set the default content-type
		$this->output->set_content_type( 'text/html; charset=utf-8' );

		// --------------------------------------------------------------------------

		//	Include the composer autoloader
		if ( ! file_exists( FCPATH . 'vendor/autoload.php' ) ) :

			$_ERROR = 'Composer autoloader not found; run <code>composer install</code> to install dependencies.';
			include NAILS_COMMON_PATH . 'errors/startup_error.php';

		endif;

		require_once( FCPATH . 'vendor/autoload.php' );

		// --------------------------------------------------------------------------

		//	Define data array (used extensively in views)
		$this->data	=& get_controller_data();

		// --------------------------------------------------------------------------

		//	Instantiate the database?
		$this->_instantiate_db();

		// --------------------------------------------------------------------------

		//	Define all the packages
		$this->_autoload_items();

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

		//	Test that the cache is writeable
		$this->_test_cache();

		// --------------------------------------------------------------------------

		//	Instanciate the user model
		$this->_instantiate_user();

		// --------------------------------------------------------------------------

		//	Instanciate languages
		$this->_instantiate_languages();

		// --------------------------------------------------------------------------

		//	Is the user suspended?
		//	Executed here so that both the user and language systems are initialised
		//	(so that any errors can be shown in the correct language).

		$this->_is_user_suspended();

		// --------------------------------------------------------------------------

		//	Instanciate DateTime
		$this->_instantiate_datetime();

		// --------------------------------------------------------------------------

		//	Need to generate the routes_app.php file?
		if ( defined( 'NAILS_STARTUP_GENERATE_APP_ROUTES' ) && NAILS_STARTUP_GENERATE_APP_ROUTES ) :

			$this->load->model( 'system/routes_model' );

			if ( ! $this->routes_model->update() ) :

				//	Fall over, routes_app.php *must* be there
				show_fatal_error( 'Failed To generate routes_app.php', 'routes_app.php was not found and could not be generated. ' . $this->routes_model->last_error() );

			else :

				//	Routes exist now, instruct the browser to try again
				if ( $this->input->post() ) :

					redirect( $this->input->server( 'REQUEST_URI' ), 'Location', 307 );

				else :

					redirect( $this->input->server( 'REQUEST_URI' ) );

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Set alerts

		//	These are hooks for code to add feedback messages to the user.
		$this->data['notice']	= $this->session->flashdata( 'notice' );
		$this->data['message']	= $this->session->flashdata( 'message' );
		$this->data['error']	= $this->session->flashdata( 'error' );
		$this->data['success']	= $this->session->flashdata( 'success' );

		// --------------------------------------------------------------------------

		//	Other defaults
		$this->data['page']						= new stdClass();
		$this->data['page']->title				= '';
		$this->data['page']->seo				= new stdClass();
		$this->data['page']->seo->title			= '';
		$this->data['page']->seo->description	= '';
		$this->data['page']->seo->keywords		= '';
	}


	// --------------------------------------------------------------------------


	protected function _set_error_reporting()
	{
	 	switch( strtoupper( ENVIRONMENT ) ) :

	 		case 'PRODUCTION' :

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


	protected function _test_cache()
	{
		if ( is_writable( DEPLOY_CACHE_DIR ) ) :

			return TRUE;

		elseif ( is_dir( DEPLOY_CACHE_DIR ) ) :

			//	Attempt to chmod the dir
			if ( @chmod( DEPLOY_CACHE_DIR, FILE_WRITE_MODE ) ) :

				return TRUE;

			elseif ( strtoupper( ENVIRONMENT ) !== 'PRODUCTION' ) :

				show_error( 'The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" exists but is not writeable.' );

			else :

				show_fatal_error( 'Cache Dir is not writeable', 'The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" exists but is not writeable.' );

			endif;

		elseif( @mkdir( DEPLOY_CACHE_DIR ) ) :

			return TRUE;

		elseif ( strtoupper( ENVIRONMENT ) !== 'PRODUCTION' ) :

			show_error( 'The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" does not exist and could not be created.' );

		else :

			show_fatal_error( 'Cache Dir is not writeable', 'The app\'s cache dir "' . DEPLOY_CACHE_DIR . '" does not exist and could not be created.' );

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _maintenance_mode()
	{
		if ( app_setting( 'maintenance_mode_enabled' ) || file_exists( FCPATH . '.MAINTENANCE' ) ) :

			$whitelist_ip = (array) app_setting( 'maintenance_mode_whitelist' );

			if ( ! $this->input->is_cli_request() && ip_in_range( $this->input->ip_address(), $whitelist_ip ) === FALSE ) :

				header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 503 Service Temporarily Unavailable' );
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
			 		elseif ( file_exists( NAILS_COMMON_PATH . 'views/maintenance/maintenance.php' ) ):

			 			require NAILS_COMMON_PATH . 'views/maintenance/maintenance.php';

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
		$_users = @json_decode( APP_STAGING_USERPASS );

		if ( strtoupper( ENVIRONMENT ) == 'STAGING' && $_users ) :

			$_users = (array) $_users;

			if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) ) :

				$this->_staging_request_credentials();

			endif;

			if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) :

				//	Determine the users
				$_users = array_filter( $_users );

				if ( ! isset( $_users[$_SERVER['PHP_AUTH_USER']] ) || $_users[$_SERVER['PHP_AUTH_USER']] != md5( trim( $_SERVER['PHP_AUTH_PW'] ) ) ) :

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
		header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 401 Unauthorized' );
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
		if ( DEPLOY_DB_USERNAME && DEPLOY_DB_DATABASE ) :

			$this->load->database();

		else :

			show_error( 'No database is configured.' );

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _instantiate_datetime()
	{
		//	Define default date format
		$_default = $this->datetime_model->get_date_format_default();

		if ( empty( $_default ) ) :

			show_fatal_error( 'No default date format has been set, or it\'s been set incorrectly.' );

		endif;

		define( 'APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG',	$_default->slug );
		define( 'APP_DEFAULT_DATETIME_FORMAT_DATE_LABEL',	$_default->label );
		define( 'APP_DEFAULT_DATETIME_FORMAT_DATE_FORMAT',	$_default->format );

		//	Define default time format
		$_default = $this->datetime_model->get_time_format_default();

		if ( empty( $_default ) ) :

			show_fatal_error( 'No default time format has been set, or it\'s been set incorrectly.' );

		endif;

		define( 'APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG',	$_default->slug );
		define( 'APP_DEFAULT_DATETIME_FORMAT_TIME_LABEL',	$_default->label );
		define( 'APP_DEFAULT_DATETIME_FORMAT_TIME_FORMAT',	$_default->format );

		// --------------------------------------------------------------------------

		//	Set the timezones.

		//	Choose the user's timezone - starting with their preference, followed by
		//	the app's default.

		if ( active_user( 'timezone' ) ) :

			$_timezone_user = active_user( 'timezone' );

		else :

			$_timezone_user = $this->datetime_model->get_timezone_default();

		endif;

		$this->datetime_model->set_timezones( 'UTC', $_timezone_user );

		// --------------------------------------------------------------------------

		//	Set the user date/time formats
		$_format_date	= active_user( 'datetime_format_date' ) ? active_user( 'datetime_format_date' ) : APP_DEFAULT_DATETIME_FORMAT_DATE_SLUG;
		$_format_time	= active_user( 'datetime_format_time' ) ? active_user( 'datetime_format_time' ) : APP_DEFAULT_DATETIME_FORMAT_TIME_SLUG;

		$this->datetime_model->set_formats( $_format_date, $_format_time );

		// --------------------------------------------------------------------------

		//	Make sure the system is running on UTC
		date_default_timezone_set( 'UTC' );

		// --------------------------------------------------------------------------

		//	Make sure the DB is thinking along the same lines
		$this->db->query( 'SET time_zone = \'+0:00\'' );
	}


	// --------------------------------------------------------------------------


	protected function _instantiate_languages()
	{
		//	Define default language
		$_default = $this->language_model->get_default();

		if ( empty( $_default ) ) :

			show_fatal_error( 'No default language has been set, or it\'s been set incorrectly.' );

		endif;

		define( 'APP_DEFAULT_LANG_CODE',	$_default->code );
		define( 'APP_DEFAULT_LANG_LABEL',	$_default->label );

		// --------------------------------------------------------------------------

		//	Set any global preferences for this user, e.g languages, fall back to
		//	the app's default language (defined in config.php).

		$_user_lang = active_user( 'language' );

		if ( ! empty( $_user_lang ) ) :

			define( 'RENDER_LANG_CODE',	$_user_lang );

		else :

			define( 'RENDER_LANG_CODE',	APP_DEFAULT_LANG_CODE );

		endif;

		//	Set the language config item which codeigniter will use.
		$this->config->set_item( 'language', RENDER_LANG_CODE );

		//	Load the Nails. generic lang file
		$this->lang->load( 'nails' );
	}


	// --------------------------------------------------------------------------


	protected function _autoload_items()
	{
		$_packages			= array();
		$_available_modules	= _NAILS_GET_AVAILABLE_MODULES();

		foreach ( $_available_modules AS $module ) :

			$_packages[] = FCPATH . 'vendor/' . $module . '/';

		endforeach;

		$_packages[] = NAILS_COMMON_PATH . '';

		foreach ( $_packages AS $package ) :

			$this->load->add_package_path( $package );

		endforeach;

		// --------------------------------------------------------------------------

		//	Load the user helper
		$_helpers		= array();
		$_helpers[]		= 'user';
		$_helpers[]		= 'app_setting';
		$_helpers[]		= 'app_notification';
		$_helpers[]		= 'datetime';
		$_helpers[]		= 'url';
		$_helpers[]		= 'cookie';
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

		$_libraries = array();

		//	Test that $_SERVER is available, the session library needs this
		//	Generally not available when running on the command line. If it's
		//	not available then load up the faux session which has the same methods
		//	as the session library, but behaves as if logged out - comprende?

		if ( $this->input->server( 'REMOTE_ADDR' ) ) :

			$_libraries[] = 'session';

		else :

			$_libraries[] = array( 'auth/faux_session', 'session' );

		endif;

		// --------------------------------------------------------------------------

		//	STOP! Before we load the session library, we need to check if we're using
		//	the database. If we are then check if `sess_table_name` is "nails_session".
		//	If it is, and NAILS_DB_PREFIX != nails_ then replace 'nails_' with NAILS_DB_PREFIX

		$_sess_table_name = $this->config->item( 'sess_table_name' );

		if ( $_sess_table_name === 'nails_session' && NAILS_DB_PREFIX !== 'nails_' ) :

			$_sess_table_name = str_replace( 'nails_', NAILS_DB_PREFIX, $_sess_table_name );
			$this->config->set_item( 'sess_table_name', $_sess_table_name );

		endif;

		// --------------------------------------------------------------------------

		$_models	= array();
		$_models[]	= 'system/app_setting_model';
		$_models[]	= 'system/user_model';
		$_models[]	= 'system/user_group_model';
		$_models[]	= 'system/user_password_model';
		$_models[]	= 'system/datetime_model';
		$_models[]	= 'system/language_model';

		foreach ( $_models AS $model ) :

			$this->load->model( $model );

		endforeach;

		// --------------------------------------------------------------------------

		$_libraries[]	= 'encrypt';
		$_libraries[]	= 'asset/asset';
		$_libraries[]	= 'email/emailer';
		$_libraries[]	= 'event/event';
		$_libraries[]	= 'logger';

		foreach ( $_libraries AS $library ) :

			if ( is_array( $library ) ) :

				$this->load->library( $library[0], array(), $library[1] );

			else :

				$this->load->library( $library );

			endif;

		endforeach;
	}


	// --------------------------------------------------------------------------


	protected function _fatal_error_handler()
	{
		$this->load->library( 'Fatal_error_handler' );
	}


	// --------------------------------------------------------------------------


	protected function _instantiate_user()
	{
		//	Find a remembered user and initialise the user model; this routine checks
		//	the user's cookies and set's up the session for an existing or new user.

		$this->user_model->init();

		// --------------------------------------------------------------------------

		//	Inject the user object into the user_group, user_password & datetime models
		$this->user_group_model->_set_user_object( $this->user_model );
		$this->user_password_model->_set_user_object( $this->user_model );
		$this->datetime_model->_set_user_object( $this->user_model );

		// --------------------------------------------------------------------------

		//	Shortcut/backwards compatibility
		$this->user = $this->user_model;

		//	Set a $user variable (for the views)
		$this->data['user']				= $this->user_model;
		$this->data['user_group']		= $this->user_group_model;
		$this->data['user_password']	= $this->user_password_model;
	}


	// --------------------------------------------------------------------------


	protected function _is_user_suspended()
	{
		//	Check if this user is suspended
		if ( $this->user_model->is_logged_in() && active_user( 'is_suspended' ) ) :

			//	Load models and langs
			$this->load->model( 'auth/auth_model' );
			$this->lang->load( 'auth/auth' );

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