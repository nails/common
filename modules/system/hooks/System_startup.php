<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * OVERLOADING NAILS' HOOKS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_System_startup
{
	public function define_constants()
	{
		//	Load up nails.json and set NAILS_DATA
		$_nails_data = @file_get_contents( NAILS_PATH . 'nails.json' );

		if ( empty( $_nails_data ) ) :

			_NAILS_ERROR( 'Could not load nails.json.' );

		endif;

		$_nails_data = json_decode( $_nails_data );

		if ( empty( $_nails_data ) ) :

			_NAILS_ERROR( 'Could not parse nails.json.' );

		endif;

		// --------------------------------------------------------------------------

		//	Define some generic Nails constants, allow dev to override these - just in case
		if ( ! defined( 'NAILS_VERSION' ) )				define( 'NAILS_VERSION',			$_nails_data->version );
		if ( ! defined( 'NAILS_VERSION_RELEASED' ) )	define( 'NAILS_VERSION_RELEASED',	$_nails_data->released );
		if ( ! defined( 'NAILS_PACKAGE_NAME' ) )		define( 'NAILS_PACKAGE_NAME',		'Nails' );
		if ( ! defined( 'NAILS_PACKAGE_URL' ) )			define( 'NAILS_PACKAGE_URL',		'http://nailsapp.co.uk/' );
		if ( ! defined( 'NAILS_APP_STRAPLINE' ) )		define( 'NAILS_APP_STRAPLINE',		'A webapp powered by <a href="' . NAILS_PACKAGE_URL . '">' . NAILS_PACKAGE_NAME . '</a>, ooh la la!' );

		// --------------------------------------------------------------------------

		//	Environment
		if ( ! defined( 'ENVIRONMENT' ) ) define( 'ENVIRONMENT', 'development' );

		// --------------------------------------------------------------------------

		//	Cache Directory
		if ( ! defined( 'DEPLOY_CACHE_DIR' ) ) define( 'DEPLOY_CACHE_DIR', FCPATH . APPPATH . 'cache/' );

		// --------------------------------------------------------------------------

		//	Check routes_app.php exists
		if ( ! defined( 'NAILS_STARTUP_GENERATE_APP_ROUTES' ) ) :

			if ( is_file( DEPLOY_CACHE_DIR . 'routes_app.php' ) ) :

				define( 'NAILS_STARTUP_GENERATE_APP_ROUTES', FALSE );

			else :

				//	Not found, crude hook seeing as basically nothing has loaded yet
				define( 'NAILS_STARTUP_GENERATE_APP_ROUTES', TRUE );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Database
		if ( ! defined( 'DEPLOY_DB_HOST' ) )		define( 'DEPLOY_DB_HOST',		'localhost' );
		if ( ! defined( 'DEPLOY_DB_USERNAME' ) )	define( 'DEPLOY_DB_USERNAME',	'' );
		if ( ! defined( 'DEPLOY_DB_PASSWORD' ) )	define( 'DEPLOY_DB_PASSWORD',	'' );
		if ( ! defined( 'DEPLOY_DB_DATABASE' ) )	define( 'DEPLOY_DB_DATABASE',	'' );

		// --------------------------------------------------------------------------

		//	These settings can be specified wherever it makes most sense (e.g if
		//	maintenance mode needs enabled app wide, then specify it in app.php, if
		//	only a single server needs to be put in maintenance mode then define in
		//	deploy.php

		if ( ! defined( 'PROFILING') )				define( 'PROFILING',				FALSE );
		if ( ! defined( 'MAINTENANCE') )			define( 'MAINTENANCE',				FALSE );
		if ( ! defined( 'MAINTENANCE_WHITELIST') )	define( 'MAINTENANCE_WHITELIST',	'127.0.0.1' );

		// --------------------------------------------------------------------------

		//	Default app constants (if not already defined)
		//	These should be specified in config/app.php

		if ( ! defined( 'NAILS_DB_PREFIX' ) )			define( 'NAILS_DB_PREFIX',			'nails_' );
		if ( ! defined( 'APP_PRIVATE_KEY' ) )			define( 'APP_PRIVATE_KEY',			'' );
		if ( ! defined( 'APP_NAME' ) )					define( 'APP_NAME',					'Untitled' );
		if ( ! defined( 'APP_NAILS_MODULES' ) )			define( 'APP_NAILS_MODULES',		'' );
		if ( ! defined( 'APP_STAGING_USERPASS' ) )		define( 'APP_STAGING_USERPASS',		serialize( array() ) );
		if ( ! defined( 'APP_SSL_ROUTING' ) )			define( 'APP_SSL_ROUTING',			FALSE );
		if ( ! defined( 'APP_NATIVE_LOGIN_USING' ) )	define( 'APP_NATIVE_LOGIN_USING',	'EMAIL' );	//	[EMAIL|USERNAME|BOTH]
		if ( ! defined( 'APP_ADMIN_IP_WHITELIST' ) )	define( 'APP_ADMIN_IP_WHITELIST',	json_encode( array() ) );


		// --------------------------------------------------------------------------

		//	Deployment specific constants (if not already defined)
		//	These should be specified in config/deploy.php

		if ( ! defined( 'DEPLOY_SYSTEM_TIMEZONE') ) define( 'DEPLOY_SYSTEM_TIMEZONE', 'UTC' );

		//	If this is changed, update CORE_NAILS_Log.php too
		if ( ! defined( 'DEPLOY_LOG_DIR') ) define( 'DEPLOY_LOG_DIR', FCPATH . APPPATH . 'logs/' );

		// --------------------------------------------------------------------------

		//	Email
		if ( ! defined( 'APP_DEVELOPER_EMAIL' ) )	define( 'APP_DEVELOPER_EMAIL',	'' );
		if ( ! defined( 'APP_EMAIL_FROM_NAME' ) )	define( 'APP_EMAIL_FROM_NAME',	APP_NAME );
		if ( ! defined( 'APP_EMAIL_FROM_EMAIL' ) )	define( 'APP_EMAIL_FROM_EMAIL',	'' );
		if ( ! defined( 'DEPLOY_SMTP_HOST' ) )		define( 'DEPLOY_SMTP_HOST',		'localhost' );
		if ( ! defined( 'DEPLOY_SMTP_USERNAME' ) )	define( 'DEPLOY_SMTP_USERNAME',	'' );
		if ( ! defined( 'DEPLOY_SMTP_PASSWORD' ) )	define( 'DEPLOY_SMTP_PASSWORD',	'' );
		if ( ! defined( 'DEPLOY_SMTP_PORT' ) )		define( 'DEPLOY_SMTP_PORT',		'25' );
		if ( ! defined( 'EMAIL_DEBUG' ) )			define( 'EMAIL_DEBUG',			FALSE );
		if ( ! defined( 'EMAIL_OVERRIDE' ) )		define( 'EMAIL_OVERRIDE',		'' );

		// --------------------------------------------------------------------------

		//	CDN
		if ( ! defined( 'APP_CDN_DRIVER' ) )	define( 'APP_CDN_DRIVER',	'LOCAL' );
		if ( ! defined( 'DEPLOY_CDN_MAGIC') )	define( 'DEPLOY_CDN_MAGIC',	'' );
		if ( ! defined( 'DEPLOY_CDN_PATH') )	define( 'DEPLOY_CDN_PATH',	FCPATH . 'assets/uploads/' );

		//	Define how long CDN items should be cached for, this is a maximum age in seconds
		//	According to http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html this shouldn't be
		//	more than 1 year.

		if ( ! defined( 'APP_CDN_CACHE_MAX_AGE' ) ) define( 'APP_CDN_CACHE_MAX_AGE', '31536000' ); // 1 year

		// --------------------------------------------------------------------------

		//	SSL
		//	If a SECURE_BASE_URL is not defined then assume the secure URL is simply
		//	https://BASE_URL

		if ( ! defined( 'SECURE_BASE_URL' ) ) :

			//	Not defined, play it safe and just copy the BASE_URL
			define( 'SECURE_BASE_URL', BASE_URL );

		endif;

		// --------------------------------------------------------------------------

		//	Set NAILS_URL here as it's dependent on knowing whether SSL is set or not
		//	and if the current page is secure.

		if ( ! defined( 'NAILS_URL') ) :

			if ( APP_SSL_ROUTING && page_is_secure() ) :

				define( 'NAILS_URL', SECURE_BASE_URL . 'vendor/nailsapp/common/' );

			else :

				define( 'NAILS_URL', BASE_URL . 'vendor/nailsapp/common/' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Set the NAILS_ASSETS_URL
		if ( ! defined( 'NAILS_ASSETS_URL') ) :

			define( 'NAILS_ASSETS_URL', NAILS_URL . 'assets/' );

		endif;

		// --------------------------------------------------------------------------

		//	Database Debug
		if ( ! defined( 'DEPLOY_DB_DEBUG' ) ) :

			if ( ENVIRONMENT == 'production' ) :

				define( 'DEPLOY_DB_DEBUG', FALSE );

			else :

				define( 'DEPLOY_DB_DEBUG', TRUE );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Bootstrap columns
		//	Define constants for Bootstrap columns and offsets. Two flavours of the
		//	Bootstrap grid are available: 12 col and 24 col. Modules use these constants
		//	to specify their column widths so that it can accommodate either grid cleanly.

		//	Firstly, which grid? Assume 12 unless specified otherwise
		if ( ! defined( 'APP_BOOTSTRAP_GRID' ) ) :

			define( 'APP_BOOTSTRAP_GRID', 12 );

		endif;

		switch( (int) APP_BOOTSTRAP_GRID ) :

			case 24 :

				//	COLUMNS

				//	LG
				if ( ! defined( 'BS_COL_LG_1' ) ) define( 'BS_COL_LG_1', 'col-lg-2' );
				if ( ! defined( 'BS_COL_LG_2' ) ) define( 'BS_COL_LG_2', 'col-lg-4' );
				if ( ! defined( 'BS_COL_LG_3' ) ) define( 'BS_COL_LG_3', 'col-lg-6' );
				if ( ! defined( 'BS_COL_LG_4' ) ) define( 'BS_COL_LG_4', 'col-lg-8' );
				if ( ! defined( 'BS_COL_LG_5' ) ) define( 'BS_COL_LG_5', 'col-lg-10' );
				if ( ! defined( 'BS_COL_LG_6' ) ) define( 'BS_COL_LG_6', 'col-lg-12' );
				if ( ! defined( 'BS_COL_LG_7' ) ) define( 'BS_COL_LG_7', 'col-lg-14' );
				if ( ! defined( 'BS_COL_LG_8' ) ) define( 'BS_COL_LG_8', 'col-lg-16' );
				if ( ! defined( 'BS_COL_LG_9' ) ) define( 'BS_COL_LG_9', 'col-lg-18' );
				if ( ! defined( 'BS_COL_LG_10' ) ) define( 'BS_COL_LG_10', 'col-lg-20' );
				if ( ! defined( 'BS_COL_LG_11' ) ) define( 'BS_COL_LG_11', 'col-lg-22' );
				if ( ! defined( 'BS_COL_LG_12' ) ) define( 'BS_COL_LG_12', 'col-lg-24' );

				//	MD
				if ( ! defined( 'BS_COL_MD_1' ) ) define( 'BS_COL_MD_1', 'col-md-2' );
				if ( ! defined( 'BS_COL_MD_2' ) ) define( 'BS_COL_MD_2', 'col-md-4' );
				if ( ! defined( 'BS_COL_MD_3' ) ) define( 'BS_COL_MD_3', 'col-md-6' );
				if ( ! defined( 'BS_COL_MD_4' ) ) define( 'BS_COL_MD_4', 'col-md-8' );
				if ( ! defined( 'BS_COL_MD_5' ) ) define( 'BS_COL_MD_5', 'col-md-10' );
				if ( ! defined( 'BS_COL_MD_6' ) ) define( 'BS_COL_MD_6', 'col-md-12' );
				if ( ! defined( 'BS_COL_MD_7' ) ) define( 'BS_COL_MD_7', 'col-md-14' );
				if ( ! defined( 'BS_COL_MD_8' ) ) define( 'BS_COL_MD_8', 'col-md-16' );
				if ( ! defined( 'BS_COL_MD_9' ) ) define( 'BS_COL_MD_9', 'col-md-18' );
				if ( ! defined( 'BS_COL_MD_10' ) ) define( 'BS_COL_MD_10', 'col-md-20' );
				if ( ! defined( 'BS_COL_MD_11' ) ) define( 'BS_COL_MD_11', 'col-md-22' );
				if ( ! defined( 'BS_COL_MD_12' ) ) define( 'BS_COL_MD_12', 'col-md-24' );

				//	SM
				if ( ! defined( 'BS_COL_SM_1' ) ) define( 'BS_COL_SM_1', 'col-sm-2' );
				if ( ! defined( 'BS_COL_SM_2' ) ) define( 'BS_COL_SM_2', 'col-sm-4' );
				if ( ! defined( 'BS_COL_SM_3' ) ) define( 'BS_COL_SM_3', 'col-sm-6' );
				if ( ! defined( 'BS_COL_SM_4' ) ) define( 'BS_COL_SM_4', 'col-sm-8' );
				if ( ! defined( 'BS_COL_SM_5' ) ) define( 'BS_COL_SM_5', 'col-sm-10' );
				if ( ! defined( 'BS_COL_SM_6' ) ) define( 'BS_COL_SM_6', 'col-sm-12' );
				if ( ! defined( 'BS_COL_SM_7' ) ) define( 'BS_COL_SM_7', 'col-sm-14' );
				if ( ! defined( 'BS_COL_SM_8' ) ) define( 'BS_COL_SM_8', 'col-sm-16' );
				if ( ! defined( 'BS_COL_SM_9' ) ) define( 'BS_COL_SM_9', 'col-sm-18' );
				if ( ! defined( 'BS_COL_SM_10' ) ) define( 'BS_COL_SM_10', 'col-sm-20' );
				if ( ! defined( 'BS_COL_SM_11' ) ) define( 'BS_COL_SM_11', 'col-sm-22' );
				if ( ! defined( 'BS_COL_SM_12' ) ) define( 'BS_COL_SM_12', 'col-sm-24' );

				//	XS
				if ( ! defined( 'BS_COL_XS_1' ) ) define( 'BS_COL_XS_1', 'col-xs-2' );
				if ( ! defined( 'BS_COL_XS_2' ) ) define( 'BS_COL_XS_2', 'col-xs-4' );
				if ( ! defined( 'BS_COL_XS_3' ) ) define( 'BS_COL_XS_3', 'col-xs-6' );
				if ( ! defined( 'BS_COL_XS_4' ) ) define( 'BS_COL_XS_4', 'col-xs-8' );
				if ( ! defined( 'BS_COL_XS_5' ) ) define( 'BS_COL_XS_5', 'col-xs-10' );
				if ( ! defined( 'BS_COL_XS_6' ) ) define( 'BS_COL_XS_6', 'col-xs-12' );
				if ( ! defined( 'BS_COL_XS_7' ) ) define( 'BS_COL_XS_7', 'col-xs-14' );
				if ( ! defined( 'BS_COL_XS_8' ) ) define( 'BS_COL_XS_8', 'col-xs-16' );
				if ( ! defined( 'BS_COL_XS_9' ) ) define( 'BS_COL_XS_9', 'col-xs-18' );
				if ( ! defined( 'BS_COL_XS_10' ) ) define( 'BS_COL_XS_10', 'col-xs-20' );
				if ( ! defined( 'BS_COL_XS_11' ) ) define( 'BS_COL_XS_11', 'col-xs-22' );
				if ( ! defined( 'BS_COL_XS_12' ) ) define( 'BS_COL_XS_12', 'col-xs-24' );

				// --------------------------------------------------------------------------

				//	OFFSETS

				//	LG
				if ( ! defined( 'BS_COL_LG_OFFSET_1' ) ) define( 'BS_COL_LG_OFFSET_1', 'col-lg-offset-2' );
				if ( ! defined( 'BS_COL_LG_OFFSET_2' ) ) define( 'BS_COL_LG_OFFSET_2', 'col-lg-offset-4' );
				if ( ! defined( 'BS_COL_LG_OFFSET_3' ) ) define( 'BS_COL_LG_OFFSET_3', 'col-lg-offset-6' );
				if ( ! defined( 'BS_COL_LG_OFFSET_4' ) ) define( 'BS_COL_LG_OFFSET_4', 'col-lg-offset-8' );
				if ( ! defined( 'BS_COL_LG_OFFSET_5' ) ) define( 'BS_COL_LG_OFFSET_5', 'col-lg-offset-10' );
				if ( ! defined( 'BS_COL_LG_OFFSET_6' ) ) define( 'BS_COL_LG_OFFSET_6', 'col-lg-offset-12' );
				if ( ! defined( 'BS_COL_LG_OFFSET_7' ) ) define( 'BS_COL_LG_OFFSET_7', 'col-lg-offset-14' );
				if ( ! defined( 'BS_COL_LG_OFFSET_8' ) ) define( 'BS_COL_LG_OFFSET_8', 'col-lg-offset-16' );
				if ( ! defined( 'BS_COL_LG_OFFSET_9' ) ) define( 'BS_COL_LG_OFFSET_9', 'col-lg-offset-18' );
				if ( ! defined( 'BS_COL_LG_OFFSET_10' ) ) define( 'BS_COL_LG_OFFSET_10', 'col-lg-offset-20' );
				if ( ! defined( 'BS_COL_LG_OFFSET_11' ) ) define( 'BS_COL_LG_OFFSET_11', 'col-lg-offset-22' );
				if ( ! defined( 'BS_COL_LG_OFFSET_12' ) ) define( 'BS_COL_LG_OFFSET_12', 'col-lg-offset-24' );

				//	MD
				if ( ! defined( 'BS_COL_MD_OFFSET_1' ) ) define( 'BS_COL_MD_OFFSET_1', 'col-md-offset-2' );
				if ( ! defined( 'BS_COL_MD_OFFSET_2' ) ) define( 'BS_COL_MD_OFFSET_2', 'col-md-offset-4' );
				if ( ! defined( 'BS_COL_MD_OFFSET_3' ) ) define( 'BS_COL_MD_OFFSET_3', 'col-md-offset-6' );
				if ( ! defined( 'BS_COL_MD_OFFSET_4' ) ) define( 'BS_COL_MD_OFFSET_4', 'col-md-offset-8' );
				if ( ! defined( 'BS_COL_MD_OFFSET_5' ) ) define( 'BS_COL_MD_OFFSET_5', 'col-md-offset-10' );
				if ( ! defined( 'BS_COL_MD_OFFSET_6' ) ) define( 'BS_COL_MD_OFFSET_6', 'col-md-offset-12' );
				if ( ! defined( 'BS_COL_MD_OFFSET_7' ) ) define( 'BS_COL_MD_OFFSET_7', 'col-md-offset-14' );
				if ( ! defined( 'BS_COL_MD_OFFSET_8' ) ) define( 'BS_COL_MD_OFFSET_8', 'col-md-offset-16' );
				if ( ! defined( 'BS_COL_MD_OFFSET_9' ) ) define( 'BS_COL_MD_OFFSET_9', 'col-md-offset-18' );
				if ( ! defined( 'BS_COL_MD_OFFSET_10' ) ) define( 'BS_COL_MD_OFFSET_10', 'col-md-offset-20' );
				if ( ! defined( 'BS_COL_MD_OFFSET_11' ) ) define( 'BS_COL_MD_OFFSET_11', 'col-md-offset-22' );
				if ( ! defined( 'BS_COL_MD_OFFSET_12' ) ) define( 'BS_COL_MD_OFFSET_12', 'col-md-offset-24' );

				//	SM
				if ( ! defined( 'BS_COL_SM_OFFSET_1' ) ) define( 'BS_COL_SM_OFFSET_1', 'col-sm-offset-2' );
				if ( ! defined( 'BS_COL_SM_OFFSET_2' ) ) define( 'BS_COL_SM_OFFSET_2', 'col-sm-offset-4' );
				if ( ! defined( 'BS_COL_SM_OFFSET_3' ) ) define( 'BS_COL_SM_OFFSET_3', 'col-sm-offset-6' );
				if ( ! defined( 'BS_COL_SM_OFFSET_4' ) ) define( 'BS_COL_SM_OFFSET_4', 'col-sm-offset-8' );
				if ( ! defined( 'BS_COL_SM_OFFSET_5' ) ) define( 'BS_COL_SM_OFFSET_5', 'col-sm-offset-10' );
				if ( ! defined( 'BS_COL_SM_OFFSET_6' ) ) define( 'BS_COL_SM_OFFSET_6', 'col-sm-offset-12' );
				if ( ! defined( 'BS_COL_SM_OFFSET_7' ) ) define( 'BS_COL_SM_OFFSET_7', 'col-sm-offset-14' );
				if ( ! defined( 'BS_COL_SM_OFFSET_8' ) ) define( 'BS_COL_SM_OFFSET_8', 'col-sm-offset-16' );
				if ( ! defined( 'BS_COL_SM_OFFSET_9' ) ) define( 'BS_COL_SM_OFFSET_9', 'col-sm-offset-18' );
				if ( ! defined( 'BS_COL_SM_OFFSET_10' ) ) define( 'BS_COL_SM_OFFSET_10', 'col-sm-offset-20' );
				if ( ! defined( 'BS_COL_SM_OFFSET_11' ) ) define( 'BS_COL_SM_OFFSET_11', 'col-sm-offset-22' );
				if ( ! defined( 'BS_COL_SM_OFFSET_12' ) ) define( 'BS_COL_SM_OFFSET_12', 'col-sm-offset-24' );

				//	XS
				if ( ! defined( 'BS_COL_XS_OFFSET_1' ) ) define( 'BS_COL_XS_OFFSET_1', 'col-xs-offset-2' );
				if ( ! defined( 'BS_COL_XS_OFFSET_2' ) ) define( 'BS_COL_XS_OFFSET_2', 'col-xs-offset-4' );
				if ( ! defined( 'BS_COL_XS_OFFSET_3' ) ) define( 'BS_COL_XS_OFFSET_3', 'col-xs-offset-6' );
				if ( ! defined( 'BS_COL_XS_OFFSET_4' ) ) define( 'BS_COL_XS_OFFSET_4', 'col-xs-offset-8' );
				if ( ! defined( 'BS_COL_XS_OFFSET_5' ) ) define( 'BS_COL_XS_OFFSET_5', 'col-xs-offset-10' );
				if ( ! defined( 'BS_COL_XS_OFFSET_6' ) ) define( 'BS_COL_XS_OFFSET_6', 'col-xs-offset-12' );
				if ( ! defined( 'BS_COL_XS_OFFSET_7' ) ) define( 'BS_COL_XS_OFFSET_7', 'col-xs-offset-14' );
				if ( ! defined( 'BS_COL_XS_OFFSET_8' ) ) define( 'BS_COL_XS_OFFSET_8', 'col-xs-offset-16' );
				if ( ! defined( 'BS_COL_XS_OFFSET_9' ) ) define( 'BS_COL_XS_OFFSET_9', 'col-xs-offset-18' );
				if ( ! defined( 'BS_COL_XS_OFFSET_10' ) ) define( 'BS_COL_XS_OFFSET_10', 'col-xs-offset-20' );
				if ( ! defined( 'BS_COL_XS_OFFSET_11' ) ) define( 'BS_COL_XS_OFFSET_11', 'col-xs-offset-22' );
				if ( ! defined( 'BS_COL_XS_OFFSET_12' ) ) define( 'BS_COL_XS_OFFSET_12', 'col-xs-offset-24' );

				// --------------------------------------------------------------------------

				//	ORDERING - PULL

				//	LG
				if ( ! defined( 'BS_COL_LG_PULL_1' ) ) define( 'BS_COL_LG_PULL_1', 'col-lg-pull-2' );
				if ( ! defined( 'BS_COL_LG_PULL_2' ) ) define( 'BS_COL_LG_PULL_2', 'col-lg-pull-4' );
				if ( ! defined( 'BS_COL_LG_PULL_3' ) ) define( 'BS_COL_LG_PULL_3', 'col-lg-pull-6' );
				if ( ! defined( 'BS_COL_LG_PULL_4' ) ) define( 'BS_COL_LG_PULL_4', 'col-lg-pull-8' );
				if ( ! defined( 'BS_COL_LG_PULL_5' ) ) define( 'BS_COL_LG_PULL_5', 'col-lg-pull-10' );
				if ( ! defined( 'BS_COL_LG_PULL_6' ) ) define( 'BS_COL_LG_PULL_6', 'col-lg-pull-12' );
				if ( ! defined( 'BS_COL_LG_PULL_7' ) ) define( 'BS_COL_LG_PULL_7', 'col-lg-pull-14' );
				if ( ! defined( 'BS_COL_LG_PULL_8' ) ) define( 'BS_COL_LG_PULL_8', 'col-lg-pull-16' );
				if ( ! defined( 'BS_COL_LG_PULL_9' ) ) define( 'BS_COL_LG_PULL_9', 'col-lg-pull-18' );
				if ( ! defined( 'BS_COL_LG_PULL_10' ) ) define( 'BS_COL_LG_PULL_10', 'col-lg-pull-20' );
				if ( ! defined( 'BS_COL_LG_PULL_11' ) ) define( 'BS_COL_LG_PULL_11', 'col-lg-pull-22' );
				if ( ! defined( 'BS_COL_LG_PULL_12' ) ) define( 'BS_COL_LG_PULL_12', 'col-lg-pull-24' );

				//	MD
				if ( ! defined( 'BS_COL_MD_PULL_1' ) ) define( 'BS_COL_MD_PULL_1', 'col-md-pull-2' );
				if ( ! defined( 'BS_COL_MD_PULL_2' ) ) define( 'BS_COL_MD_PULL_2', 'col-md-pull-4' );
				if ( ! defined( 'BS_COL_MD_PULL_3' ) ) define( 'BS_COL_MD_PULL_3', 'col-md-pull-6' );
				if ( ! defined( 'BS_COL_MD_PULL_4' ) ) define( 'BS_COL_MD_PULL_4', 'col-md-pull-8' );
				if ( ! defined( 'BS_COL_MD_PULL_5' ) ) define( 'BS_COL_MD_PULL_5', 'col-md-pull-10' );
				if ( ! defined( 'BS_COL_MD_PULL_6' ) ) define( 'BS_COL_MD_PULL_6', 'col-md-pull-12' );
				if ( ! defined( 'BS_COL_MD_PULL_7' ) ) define( 'BS_COL_MD_PULL_7', 'col-md-pull-14' );
				if ( ! defined( 'BS_COL_MD_PULL_8' ) ) define( 'BS_COL_MD_PULL_8', 'col-md-pull-16' );
				if ( ! defined( 'BS_COL_MD_PULL_9' ) ) define( 'BS_COL_MD_PULL_9', 'col-md-pull-18' );
				if ( ! defined( 'BS_COL_MD_PULL_10' ) ) define( 'BS_COL_MD_PULL_10', 'col-md-pull-20' );
				if ( ! defined( 'BS_COL_MD_PULL_11' ) ) define( 'BS_COL_MD_PULL_11', 'col-md-pull-22' );
				if ( ! defined( 'BS_COL_MD_PULL_12' ) ) define( 'BS_COL_MD_PULL_12', 'col-md-pull-24' );

				//	SM
				if ( ! defined( 'BS_COL_SM_PULL_1' ) ) define( 'BS_COL_SM_PULL_1', 'col-sm-pull-2' );
				if ( ! defined( 'BS_COL_SM_PULL_2' ) ) define( 'BS_COL_SM_PULL_2', 'col-sm-pull-4' );
				if ( ! defined( 'BS_COL_SM_PULL_3' ) ) define( 'BS_COL_SM_PULL_3', 'col-sm-pull-6' );
				if ( ! defined( 'BS_COL_SM_PULL_4' ) ) define( 'BS_COL_SM_PULL_4', 'col-sm-pull-8' );
				if ( ! defined( 'BS_COL_SM_PULL_5' ) ) define( 'BS_COL_SM_PULL_5', 'col-sm-pull-10' );
				if ( ! defined( 'BS_COL_SM_PULL_6' ) ) define( 'BS_COL_SM_PULL_6', 'col-sm-pull-12' );
				if ( ! defined( 'BS_COL_SM_PULL_7' ) ) define( 'BS_COL_SM_PULL_7', 'col-sm-pull-14' );
				if ( ! defined( 'BS_COL_SM_PULL_8' ) ) define( 'BS_COL_SM_PULL_8', 'col-sm-pull-16' );
				if ( ! defined( 'BS_COL_SM_PULL_9' ) ) define( 'BS_COL_SM_PULL_9', 'col-sm-pull-18' );
				if ( ! defined( 'BS_COL_SM_PULL_10' ) ) define( 'BS_COL_SM_PULL_10', 'col-sm-pull-20' );
				if ( ! defined( 'BS_COL_SM_PULL_11' ) ) define( 'BS_COL_SM_PULL_11', 'col-sm-pull-22' );
				if ( ! defined( 'BS_COL_SM_PULL_12' ) ) define( 'BS_COL_SM_PULL_12', 'col-sm-pull-24' );

				//	XS
				if ( ! defined( 'BS_COL_XS_PULL_1' ) ) define( 'BS_COL_XS_PULL_1', 'col-xs-pull-2' );
				if ( ! defined( 'BS_COL_XS_PULL_2' ) ) define( 'BS_COL_XS_PULL_2', 'col-xs-pull-4' );
				if ( ! defined( 'BS_COL_XS_PULL_3' ) ) define( 'BS_COL_XS_PULL_3', 'col-xs-pull-6' );
				if ( ! defined( 'BS_COL_XS_PULL_4' ) ) define( 'BS_COL_XS_PULL_4', 'col-xs-pull-8' );
				if ( ! defined( 'BS_COL_XS_PULL_5' ) ) define( 'BS_COL_XS_PULL_5', 'col-xs-pull-10' );
				if ( ! defined( 'BS_COL_XS_PULL_6' ) ) define( 'BS_COL_XS_PULL_6', 'col-xs-pull-12' );
				if ( ! defined( 'BS_COL_XS_PULL_7' ) ) define( 'BS_COL_XS_PULL_7', 'col-xs-pull-14' );
				if ( ! defined( 'BS_COL_XS_PULL_8' ) ) define( 'BS_COL_XS_PULL_8', 'col-xs-pull-16' );
				if ( ! defined( 'BS_COL_XS_PULL_9' ) ) define( 'BS_COL_XS_PULL_9', 'col-xs-pull-18' );
				if ( ! defined( 'BS_COL_XS_PULL_10' ) ) define( 'BS_COL_XS_PULL_10', 'col-xs-pull-20' );
				if ( ! defined( 'BS_COL_XS_PULL_11' ) ) define( 'BS_COL_XS_PULL_11', 'col-xs-pull-22' );
				if ( ! defined( 'BS_COL_XS_PULL_12' ) ) define( 'BS_COL_XS_PULL_12', 'col-xs-pull-24' );

				// --------------------------------------------------------------------------

				//	ORDERING - PUSH

				//	LG
				if ( ! defined( 'BS_COL_LG_PUSH_1' ) ) define( 'BS_COL_LG_PUSH_1', 'col-lg-push-2' );
				if ( ! defined( 'BS_COL_LG_PUSH_2' ) ) define( 'BS_COL_LG_PUSH_2', 'col-lg-push-4' );
				if ( ! defined( 'BS_COL_LG_PUSH_3' ) ) define( 'BS_COL_LG_PUSH_3', 'col-lg-push-6' );
				if ( ! defined( 'BS_COL_LG_PUSH_4' ) ) define( 'BS_COL_LG_PUSH_4', 'col-lg-push-8' );
				if ( ! defined( 'BS_COL_LG_PUSH_5' ) ) define( 'BS_COL_LG_PUSH_5', 'col-lg-push-10' );
				if ( ! defined( 'BS_COL_LG_PUSH_6' ) ) define( 'BS_COL_LG_PUSH_6', 'col-lg-push-12' );
				if ( ! defined( 'BS_COL_LG_PUSH_7' ) ) define( 'BS_COL_LG_PUSH_7', 'col-lg-push-14' );
				if ( ! defined( 'BS_COL_LG_PUSH_8' ) ) define( 'BS_COL_LG_PUSH_8', 'col-lg-push-16' );
				if ( ! defined( 'BS_COL_LG_PUSH_9' ) ) define( 'BS_COL_LG_PUSH_9', 'col-lg-push-18' );
				if ( ! defined( 'BS_COL_LG_PUSH_10' ) ) define( 'BS_COL_LG_PUSH_10', 'col-lg-push-20' );
				if ( ! defined( 'BS_COL_LG_PUSH_11' ) ) define( 'BS_COL_LG_PUSH_11', 'col-lg-push-22' );
				if ( ! defined( 'BS_COL_LG_PUSH_12' ) ) define( 'BS_COL_LG_PUSH_12', 'col-lg-push-24' );

				//	MD
				if ( ! defined( 'BS_COL_MD_PUSH_1' ) ) define( 'BS_COL_MD_PUSH_1', 'col-md-push-2' );
				if ( ! defined( 'BS_COL_MD_PUSH_2' ) ) define( 'BS_COL_MD_PUSH_2', 'col-md-push-4' );
				if ( ! defined( 'BS_COL_MD_PUSH_3' ) ) define( 'BS_COL_MD_PUSH_3', 'col-md-push-6' );
				if ( ! defined( 'BS_COL_MD_PUSH_4' ) ) define( 'BS_COL_MD_PUSH_4', 'col-md-push-8' );
				if ( ! defined( 'BS_COL_MD_PUSH_5' ) ) define( 'BS_COL_MD_PUSH_5', 'col-md-push-10' );
				if ( ! defined( 'BS_COL_MD_PUSH_6' ) ) define( 'BS_COL_MD_PUSH_6', 'col-md-push-12' );
				if ( ! defined( 'BS_COL_MD_PUSH_7' ) ) define( 'BS_COL_MD_PUSH_7', 'col-md-push-14' );
				if ( ! defined( 'BS_COL_MD_PUSH_8' ) ) define( 'BS_COL_MD_PUSH_8', 'col-md-push-16' );
				if ( ! defined( 'BS_COL_MD_PUSH_9' ) ) define( 'BS_COL_MD_PUSH_9', 'col-md-push-18' );
				if ( ! defined( 'BS_COL_MD_PUSH_10' ) ) define( 'BS_COL_MD_PUSH_10', 'col-md-push-20' );
				if ( ! defined( 'BS_COL_MD_PUSH_11' ) ) define( 'BS_COL_MD_PUSH_11', 'col-md-push-22' );
				if ( ! defined( 'BS_COL_MD_PUSH_12' ) ) define( 'BS_COL_MD_PUSH_12', 'col-md-push-24' );

				//	SM
				if ( ! defined( 'BS_COL_SM_PUSH_1' ) ) define( 'BS_COL_SM_PUSH_1', 'col-sm-push-2' );
				if ( ! defined( 'BS_COL_SM_PUSH_2' ) ) define( 'BS_COL_SM_PUSH_2', 'col-sm-push-4' );
				if ( ! defined( 'BS_COL_SM_PUSH_3' ) ) define( 'BS_COL_SM_PUSH_3', 'col-sm-push-6' );
				if ( ! defined( 'BS_COL_SM_PUSH_4' ) ) define( 'BS_COL_SM_PUSH_4', 'col-sm-push-8' );
				if ( ! defined( 'BS_COL_SM_PUSH_5' ) ) define( 'BS_COL_SM_PUSH_5', 'col-sm-push-10' );
				if ( ! defined( 'BS_COL_SM_PUSH_6' ) ) define( 'BS_COL_SM_PUSH_6', 'col-sm-push-12' );
				if ( ! defined( 'BS_COL_SM_PUSH_7' ) ) define( 'BS_COL_SM_PUSH_7', 'col-sm-push-14' );
				if ( ! defined( 'BS_COL_SM_PUSH_8' ) ) define( 'BS_COL_SM_PUSH_8', 'col-sm-push-16' );
				if ( ! defined( 'BS_COL_SM_PUSH_9' ) ) define( 'BS_COL_SM_PUSH_9', 'col-sm-push-18' );
				if ( ! defined( 'BS_COL_SM_PUSH_10' ) ) define( 'BS_COL_SM_PUSH_10', 'col-sm-push-20' );
				if ( ! defined( 'BS_COL_SM_PUSH_11' ) ) define( 'BS_COL_SM_PUSH_11', 'col-sm-push-22' );
				if ( ! defined( 'BS_COL_SM_PUSH_12' ) ) define( 'BS_COL_SM_PUSH_12', 'col-sm-push-24' );

				//	XS
				if ( ! defined( 'BS_COL_XS_PUSH_1' ) ) define( 'BS_COL_XS_PUSH_1', 'col-xs-push-2' );
				if ( ! defined( 'BS_COL_XS_PUSH_2' ) ) define( 'BS_COL_XS_PUSH_2', 'col-xs-push-4' );
				if ( ! defined( 'BS_COL_XS_PUSH_3' ) ) define( 'BS_COL_XS_PUSH_3', 'col-xs-push-6' );
				if ( ! defined( 'BS_COL_XS_PUSH_4' ) ) define( 'BS_COL_XS_PUSH_4', 'col-xs-push-8' );
				if ( ! defined( 'BS_COL_XS_PUSH_5' ) ) define( 'BS_COL_XS_PUSH_5', 'col-xs-push-10' );
				if ( ! defined( 'BS_COL_XS_PUSH_6' ) ) define( 'BS_COL_XS_PUSH_6', 'col-xs-push-12' );
				if ( ! defined( 'BS_COL_XS_PUSH_7' ) ) define( 'BS_COL_XS_PUSH_7', 'col-xs-push-14' );
				if ( ! defined( 'BS_COL_XS_PUSH_8' ) ) define( 'BS_COL_XS_PUSH_8', 'col-xs-push-16' );
				if ( ! defined( 'BS_COL_XS_PUSH_9' ) ) define( 'BS_COL_XS_PUSH_9', 'col-xs-push-18' );
				if ( ! defined( 'BS_COL_XS_PUSH_10' ) ) define( 'BS_COL_XS_PUSH_10', 'col-xs-push-20' );
				if ( ! defined( 'BS_COL_XS_PUSH_11' ) ) define( 'BS_COL_XS_PUSH_11', 'col-xs-push-22' );
				if ( ! defined( 'BS_COL_XS_PUSH_12' ) ) define( 'BS_COL_XS_PUSH_12', 'col-xs-push-24' );

			break;

			case 12 :
			default :

				//	COLUMNS

				//	LG
				if ( ! defined( 'BS_COL_LG_1' ) ) define( 'BS_COL_LG_1', 'col-lg-1' );
				if ( ! defined( 'BS_COL_LG_2' ) ) define( 'BS_COL_LG_2', 'col-lg-2' );
				if ( ! defined( 'BS_COL_LG_3' ) ) define( 'BS_COL_LG_3', 'col-lg-3' );
				if ( ! defined( 'BS_COL_LG_4' ) ) define( 'BS_COL_LG_4', 'col-lg-4' );
				if ( ! defined( 'BS_COL_LG_5' ) ) define( 'BS_COL_LG_5', 'col-lg-5' );
				if ( ! defined( 'BS_COL_LG_6' ) ) define( 'BS_COL_LG_6', 'col-lg-6' );
				if ( ! defined( 'BS_COL_LG_7' ) ) define( 'BS_COL_LG_7', 'col-lg-7' );
				if ( ! defined( 'BS_COL_LG_8' ) ) define( 'BS_COL_LG_8', 'col-lg-8' );
				if ( ! defined( 'BS_COL_LG_9' ) ) define( 'BS_COL_LG_9', 'col-lg-9' );
				if ( ! defined( 'BS_COL_LG_10' ) ) define( 'BS_COL_LG_10', 'col-lg-10' );
				if ( ! defined( 'BS_COL_LG_11' ) ) define( 'BS_COL_LG_11', 'col-lg-11' );
				if ( ! defined( 'BS_COL_LG_12' ) ) define( 'BS_COL_LG_12', 'col-lg-12' );

				//	MD
				if ( ! defined( 'BS_COL_MD_1' ) ) define( 'BS_COL_MD_1', 'col-md-1' );
				if ( ! defined( 'BS_COL_MD_2' ) ) define( 'BS_COL_MD_2', 'col-md-2' );
				if ( ! defined( 'BS_COL_MD_3' ) ) define( 'BS_COL_MD_3', 'col-md-3' );
				if ( ! defined( 'BS_COL_MD_4' ) ) define( 'BS_COL_MD_4', 'col-md-4' );
				if ( ! defined( 'BS_COL_MD_5' ) ) define( 'BS_COL_MD_5', 'col-md-5' );
				if ( ! defined( 'BS_COL_MD_6' ) ) define( 'BS_COL_MD_6', 'col-md-6' );
				if ( ! defined( 'BS_COL_MD_7' ) ) define( 'BS_COL_MD_7', 'col-md-7' );
				if ( ! defined( 'BS_COL_MD_8' ) ) define( 'BS_COL_MD_8', 'col-md-8' );
				if ( ! defined( 'BS_COL_MD_9' ) ) define( 'BS_COL_MD_9', 'col-md-9' );
				if ( ! defined( 'BS_COL_MD_10' ) ) define( 'BS_COL_MD_10', 'col-md-10' );
				if ( ! defined( 'BS_COL_MD_11' ) ) define( 'BS_COL_MD_11', 'col-md-11' );
				if ( ! defined( 'BS_COL_MD_12' ) ) define( 'BS_COL_MD_12', 'col-md-12' );

				//	SM
				if ( ! defined( 'BS_COL_SM_1' ) ) define( 'BS_COL_SM_1', 'col-sm-1' );
				if ( ! defined( 'BS_COL_SM_2' ) ) define( 'BS_COL_SM_2', 'col-sm-2' );
				if ( ! defined( 'BS_COL_SM_3' ) ) define( 'BS_COL_SM_3', 'col-sm-3' );
				if ( ! defined( 'BS_COL_SM_4' ) ) define( 'BS_COL_SM_4', 'col-sm-4' );
				if ( ! defined( 'BS_COL_SM_5' ) ) define( 'BS_COL_SM_5', 'col-sm-5' );
				if ( ! defined( 'BS_COL_SM_6' ) ) define( 'BS_COL_SM_6', 'col-sm-6' );
				if ( ! defined( 'BS_COL_SM_7' ) ) define( 'BS_COL_SM_7', 'col-sm-7' );
				if ( ! defined( 'BS_COL_SM_8' ) ) define( 'BS_COL_SM_8', 'col-sm-8' );
				if ( ! defined( 'BS_COL_SM_9' ) ) define( 'BS_COL_SM_9', 'col-sm-9' );
				if ( ! defined( 'BS_COL_SM_10' ) ) define( 'BS_COL_SM_10', 'col-sm-10' );
				if ( ! defined( 'BS_COL_SM_11' ) ) define( 'BS_COL_SM_11', 'col-sm-11' );
				if ( ! defined( 'BS_COL_SM_12' ) ) define( 'BS_COL_SM_12', 'col-sm-12' );

				//	XS
				if ( ! defined( 'BS_COL_XS_1' ) ) define( 'BS_COL_XS_1', 'col-xs-1' );
				if ( ! defined( 'BS_COL_XS_2' ) ) define( 'BS_COL_XS_2', 'col-xs-2' );
				if ( ! defined( 'BS_COL_XS_3' ) ) define( 'BS_COL_XS_3', 'col-xs-3' );
				if ( ! defined( 'BS_COL_XS_4' ) ) define( 'BS_COL_XS_4', 'col-xs-4' );
				if ( ! defined( 'BS_COL_XS_5' ) ) define( 'BS_COL_XS_5', 'col-xs-5' );
				if ( ! defined( 'BS_COL_XS_6' ) ) define( 'BS_COL_XS_6', 'col-xs-6' );
				if ( ! defined( 'BS_COL_XS_7' ) ) define( 'BS_COL_XS_7', 'col-xs-7' );
				if ( ! defined( 'BS_COL_XS_8' ) ) define( 'BS_COL_XS_8', 'col-xs-8' );
				if ( ! defined( 'BS_COL_XS_9' ) ) define( 'BS_COL_XS_9', 'col-xs-9' );
				if ( ! defined( 'BS_COL_XS_10' ) ) define( 'BS_COL_XS_10', 'col-xs-10' );
				if ( ! defined( 'BS_COL_XS_11' ) ) define( 'BS_COL_XS_11', 'col-xs-11' );
				if ( ! defined( 'BS_COL_XS_12' ) ) define( 'BS_COL_XS_12', 'col-xs-12' );

				// --------------------------------------------------------------------------

				//	OFFSETS

				//	LG
				if ( ! defined( 'BS_COL_LG_OFFSET_1' ) ) define( 'BS_COL_LG_OFFSET_1', 'col-lg-offset-1' );
				if ( ! defined( 'BS_COL_LG_OFFSET_2' ) ) define( 'BS_COL_LG_OFFSET_2', 'col-lg-offset-2' );
				if ( ! defined( 'BS_COL_LG_OFFSET_3' ) ) define( 'BS_COL_LG_OFFSET_3', 'col-lg-offset-3' );
				if ( ! defined( 'BS_COL_LG_OFFSET_4' ) ) define( 'BS_COL_LG_OFFSET_4', 'col-lg-offset-4' );
				if ( ! defined( 'BS_COL_LG_OFFSET_5' ) ) define( 'BS_COL_LG_OFFSET_5', 'col-lg-offset-5' );
				if ( ! defined( 'BS_COL_LG_OFFSET_6' ) ) define( 'BS_COL_LG_OFFSET_6', 'col-lg-offset-6' );
				if ( ! defined( 'BS_COL_LG_OFFSET_7' ) ) define( 'BS_COL_LG_OFFSET_7', 'col-lg-offset-7' );
				if ( ! defined( 'BS_COL_LG_OFFSET_8' ) ) define( 'BS_COL_LG_OFFSET_8', 'col-lg-offset-8' );
				if ( ! defined( 'BS_COL_LG_OFFSET_9' ) ) define( 'BS_COL_LG_OFFSET_9', 'col-lg-offset-9' );
				if ( ! defined( 'BS_COL_LG_OFFSET_10' ) ) define( 'BS_COL_LG_OFFSET_10', 'col-lg-offset-10' );
				if ( ! defined( 'BS_COL_LG_OFFSET_11' ) ) define( 'BS_COL_LG_OFFSET_11', 'col-lg-offset-11' );
				if ( ! defined( 'BS_COL_LG_OFFSET_12' ) ) define( 'BS_COL_LG_OFFSET_12', 'col-lg-offset-12' );

				//	MD
				if ( ! defined( 'BS_COL_MD_OFFSET_1' ) ) define( 'BS_COL_MD_OFFSET_1', 'col-md-offset-1' );
				if ( ! defined( 'BS_COL_MD_OFFSET_2' ) ) define( 'BS_COL_MD_OFFSET_2', 'col-md-offset-2' );
				if ( ! defined( 'BS_COL_MD_OFFSET_3' ) ) define( 'BS_COL_MD_OFFSET_3', 'col-md-offset-3' );
				if ( ! defined( 'BS_COL_MD_OFFSET_4' ) ) define( 'BS_COL_MD_OFFSET_4', 'col-md-offset-4' );
				if ( ! defined( 'BS_COL_MD_OFFSET_5' ) ) define( 'BS_COL_MD_OFFSET_5', 'col-md-offset-5' );
				if ( ! defined( 'BS_COL_MD_OFFSET_6' ) ) define( 'BS_COL_MD_OFFSET_6', 'col-md-offset-6' );
				if ( ! defined( 'BS_COL_MD_OFFSET_7' ) ) define( 'BS_COL_MD_OFFSET_7', 'col-md-offset-7' );
				if ( ! defined( 'BS_COL_MD_OFFSET_8' ) ) define( 'BS_COL_MD_OFFSET_8', 'col-md-offset-8' );
				if ( ! defined( 'BS_COL_MD_OFFSET_9' ) ) define( 'BS_COL_MD_OFFSET_9', 'col-md-offset-9' );
				if ( ! defined( 'BS_COL_MD_OFFSET_10' ) ) define( 'BS_COL_MD_OFFSET_10', 'col-md-offset-10' );
				if ( ! defined( 'BS_COL_MD_OFFSET_11' ) ) define( 'BS_COL_MD_OFFSET_11', 'col-md-offset-11' );
				if ( ! defined( 'BS_COL_MD_OFFSET_12' ) ) define( 'BS_COL_MD_OFFSET_12', 'col-md-offset-12' );

				//	SM
				if ( ! defined( 'BS_COL_SM_OFFSET_1' ) ) define( 'BS_COL_SM_OFFSET_1', 'col-sm-offset-1' );
				if ( ! defined( 'BS_COL_SM_OFFSET_2' ) ) define( 'BS_COL_SM_OFFSET_2', 'col-sm-offset-2' );
				if ( ! defined( 'BS_COL_SM_OFFSET_3' ) ) define( 'BS_COL_SM_OFFSET_3', 'col-sm-offset-3' );
				if ( ! defined( 'BS_COL_SM_OFFSET_4' ) ) define( 'BS_COL_SM_OFFSET_4', 'col-sm-offset-4' );
				if ( ! defined( 'BS_COL_SM_OFFSET_5' ) ) define( 'BS_COL_SM_OFFSET_5', 'col-sm-offset-5' );
				if ( ! defined( 'BS_COL_SM_OFFSET_6' ) ) define( 'BS_COL_SM_OFFSET_6', 'col-sm-offset-6' );
				if ( ! defined( 'BS_COL_SM_OFFSET_7' ) ) define( 'BS_COL_SM_OFFSET_7', 'col-sm-offset-7' );
				if ( ! defined( 'BS_COL_SM_OFFSET_8' ) ) define( 'BS_COL_SM_OFFSET_8', 'col-sm-offset-8' );
				if ( ! defined( 'BS_COL_SM_OFFSET_9' ) ) define( 'BS_COL_SM_OFFSET_9', 'col-sm-offset-9' );
				if ( ! defined( 'BS_COL_SM_OFFSET_10' ) ) define( 'BS_COL_SM_OFFSET_10', 'col-sm-offset-10' );
				if ( ! defined( 'BS_COL_SM_OFFSET_11' ) ) define( 'BS_COL_SM_OFFSET_11', 'col-sm-offset-11' );
				if ( ! defined( 'BS_COL_SM_OFFSET_12' ) ) define( 'BS_COL_SM_OFFSET_12', 'col-sm-offset-12' );

				//	XS
				if ( ! defined( 'BS_COL_XS_OFFSET_1' ) ) define( 'BS_COL_XS_OFFSET_1', 'col-xs-offset-1' );
				if ( ! defined( 'BS_COL_XS_OFFSET_2' ) ) define( 'BS_COL_XS_OFFSET_2', 'col-xs-offset-2' );
				if ( ! defined( 'BS_COL_XS_OFFSET_3' ) ) define( 'BS_COL_XS_OFFSET_3', 'col-xs-offset-3' );
				if ( ! defined( 'BS_COL_XS_OFFSET_4' ) ) define( 'BS_COL_XS_OFFSET_4', 'col-xs-offset-4' );
				if ( ! defined( 'BS_COL_XS_OFFSET_5' ) ) define( 'BS_COL_XS_OFFSET_5', 'col-xs-offset-5' );
				if ( ! defined( 'BS_COL_XS_OFFSET_6' ) ) define( 'BS_COL_XS_OFFSET_6', 'col-xs-offset-6' );
				if ( ! defined( 'BS_COL_XS_OFFSET_7' ) ) define( 'BS_COL_XS_OFFSET_7', 'col-xs-offset-7' );
				if ( ! defined( 'BS_COL_XS_OFFSET_8' ) ) define( 'BS_COL_XS_OFFSET_8', 'col-xs-offset-8' );
				if ( ! defined( 'BS_COL_XS_OFFSET_9' ) ) define( 'BS_COL_XS_OFFSET_9', 'col-xs-offset-9' );
				if ( ! defined( 'BS_COL_XS_OFFSET_10' ) ) define( 'BS_COL_XS_OFFSET_10', 'col-xs-offset-10' );
				if ( ! defined( 'BS_COL_XS_OFFSET_11' ) ) define( 'BS_COL_XS_OFFSET_11', 'col-xs-offset-11' );
				if ( ! defined( 'BS_COL_XS_OFFSET_12' ) ) define( 'BS_COL_XS_OFFSET_12', 'col-xs-offset-12' );

				// --------------------------------------------------------------------------

				//	ORDERING - PULL

				//	LG
				if ( ! defined( 'BS_COL_LG_PULL_1' ) ) define( 'BS_COL_LG_PULL_1', 'col-lg-pull-1' );
				if ( ! defined( 'BS_COL_LG_PULL_2' ) ) define( 'BS_COL_LG_PULL_2', 'col-lg-pull-2' );
				if ( ! defined( 'BS_COL_LG_PULL_3' ) ) define( 'BS_COL_LG_PULL_3', 'col-lg-pull-3' );
				if ( ! defined( 'BS_COL_LG_PULL_4' ) ) define( 'BS_COL_LG_PULL_4', 'col-lg-pull-4' );
				if ( ! defined( 'BS_COL_LG_PULL_5' ) ) define( 'BS_COL_LG_PULL_5', 'col-lg-pull-5' );
				if ( ! defined( 'BS_COL_LG_PULL_6' ) ) define( 'BS_COL_LG_PULL_6', 'col-lg-pull-6' );
				if ( ! defined( 'BS_COL_LG_PULL_7' ) ) define( 'BS_COL_LG_PULL_7', 'col-lg-pull-7' );
				if ( ! defined( 'BS_COL_LG_PULL_8' ) ) define( 'BS_COL_LG_PULL_8', 'col-lg-pull-8' );
				if ( ! defined( 'BS_COL_LG_PULL_9' ) ) define( 'BS_COL_LG_PULL_9', 'col-lg-pull-9' );
				if ( ! defined( 'BS_COL_LG_PULL_10' ) ) define( 'BS_COL_LG_PULL_10', 'col-lg-pull-10' );
				if ( ! defined( 'BS_COL_LG_PULL_11' ) ) define( 'BS_COL_LG_PULL_11', 'col-lg-pull-11' );
				if ( ! defined( 'BS_COL_LG_PULL_12' ) ) define( 'BS_COL_LG_PULL_12', 'col-lg-pull-12' );

				//	MD
				if ( ! defined( 'BS_COL_MD_PULL_1' ) ) define( 'BS_COL_MD_PULL_1', 'col-md-pull-1' );
				if ( ! defined( 'BS_COL_MD_PULL_2' ) ) define( 'BS_COL_MD_PULL_2', 'col-md-pull-2' );
				if ( ! defined( 'BS_COL_MD_PULL_3' ) ) define( 'BS_COL_MD_PULL_3', 'col-md-pull-3' );
				if ( ! defined( 'BS_COL_MD_PULL_4' ) ) define( 'BS_COL_MD_PULL_4', 'col-md-pull-4' );
				if ( ! defined( 'BS_COL_MD_PULL_5' ) ) define( 'BS_COL_MD_PULL_5', 'col-md-pull-5' );
				if ( ! defined( 'BS_COL_MD_PULL_6' ) ) define( 'BS_COL_MD_PULL_6', 'col-md-pull-6' );
				if ( ! defined( 'BS_COL_MD_PULL_7' ) ) define( 'BS_COL_MD_PULL_7', 'col-md-pull-7' );
				if ( ! defined( 'BS_COL_MD_PULL_8' ) ) define( 'BS_COL_MD_PULL_8', 'col-md-pull-8' );
				if ( ! defined( 'BS_COL_MD_PULL_9' ) ) define( 'BS_COL_MD_PULL_9', 'col-md-pull-9' );
				if ( ! defined( 'BS_COL_MD_PULL_10' ) ) define( 'BS_COL_MD_PULL_10', 'col-md-pull-10' );
				if ( ! defined( 'BS_COL_MD_PULL_11' ) ) define( 'BS_COL_MD_PULL_11', 'col-md-pull-11' );
				if ( ! defined( 'BS_COL_MD_PULL_12' ) ) define( 'BS_COL_MD_PULL_12', 'col-md-pull-12' );

				//	SM
				if ( ! defined( 'BS_COL_SM_PULL_1' ) ) define( 'BS_COL_SM_PULL_1', 'col-sm-pull-1' );
				if ( ! defined( 'BS_COL_SM_PULL_2' ) ) define( 'BS_COL_SM_PULL_2', 'col-sm-pull-2' );
				if ( ! defined( 'BS_COL_SM_PULL_3' ) ) define( 'BS_COL_SM_PULL_3', 'col-sm-pull-3' );
				if ( ! defined( 'BS_COL_SM_PULL_4' ) ) define( 'BS_COL_SM_PULL_4', 'col-sm-pull-4' );
				if ( ! defined( 'BS_COL_SM_PULL_5' ) ) define( 'BS_COL_SM_PULL_5', 'col-sm-pull-5' );
				if ( ! defined( 'BS_COL_SM_PULL_6' ) ) define( 'BS_COL_SM_PULL_6', 'col-sm-pull-6' );
				if ( ! defined( 'BS_COL_SM_PULL_7' ) ) define( 'BS_COL_SM_PULL_7', 'col-sm-pull-7' );
				if ( ! defined( 'BS_COL_SM_PULL_8' ) ) define( 'BS_COL_SM_PULL_8', 'col-sm-pull-8' );
				if ( ! defined( 'BS_COL_SM_PULL_9' ) ) define( 'BS_COL_SM_PULL_9', 'col-sm-pull-9' );
				if ( ! defined( 'BS_COL_SM_PULL_10' ) ) define( 'BS_COL_SM_PULL_10', 'col-sm-pull-10' );
				if ( ! defined( 'BS_COL_SM_PULL_11' ) ) define( 'BS_COL_SM_PULL_11', 'col-sm-pull-11' );
				if ( ! defined( 'BS_COL_SM_PULL_12' ) ) define( 'BS_COL_SM_PULL_12', 'col-sm-pull-12' );

				//	XS
				if ( ! defined( 'BS_COL_XS_PULL_1' ) ) define( 'BS_COL_XS_PULL_1', 'col-xs-pull-1' );
				if ( ! defined( 'BS_COL_XS_PULL_2' ) ) define( 'BS_COL_XS_PULL_2', 'col-xs-pull-2' );
				if ( ! defined( 'BS_COL_XS_PULL_3' ) ) define( 'BS_COL_XS_PULL_3', 'col-xs-pull-3' );
				if ( ! defined( 'BS_COL_XS_PULL_4' ) ) define( 'BS_COL_XS_PULL_4', 'col-xs-pull-4' );
				if ( ! defined( 'BS_COL_XS_PULL_5' ) ) define( 'BS_COL_XS_PULL_5', 'col-xs-pull-5' );
				if ( ! defined( 'BS_COL_XS_PULL_6' ) ) define( 'BS_COL_XS_PULL_6', 'col-xs-pull-6' );
				if ( ! defined( 'BS_COL_XS_PULL_7' ) ) define( 'BS_COL_XS_PULL_7', 'col-xs-pull-7' );
				if ( ! defined( 'BS_COL_XS_PULL_8' ) ) define( 'BS_COL_XS_PULL_8', 'col-xs-pull-8' );
				if ( ! defined( 'BS_COL_XS_PULL_9' ) ) define( 'BS_COL_XS_PULL_9', 'col-xs-pull-9' );
				if ( ! defined( 'BS_COL_XS_PULL_10' ) ) define( 'BS_COL_XS_PULL_10', 'col-xs-pull-10' );
				if ( ! defined( 'BS_COL_XS_PULL_11' ) ) define( 'BS_COL_XS_PULL_11', 'col-xs-pull-11' );
				if ( ! defined( 'BS_COL_XS_PULL_12' ) ) define( 'BS_COL_XS_PULL_12', 'col-xs-pull-12' );

				// --------------------------------------------------------------------------

				//	ORDERING - PUSH

				//	LG
				if ( ! defined( 'BS_COL_LG_PUSH_1' ) ) define( 'BS_COL_LG_PUSH_1', 'col-lg-push-1' );
				if ( ! defined( 'BS_COL_LG_PUSH_2' ) ) define( 'BS_COL_LG_PUSH_2', 'col-lg-push-2' );
				if ( ! defined( 'BS_COL_LG_PUSH_3' ) ) define( 'BS_COL_LG_PUSH_3', 'col-lg-push-3' );
				if ( ! defined( 'BS_COL_LG_PUSH_4' ) ) define( 'BS_COL_LG_PUSH_4', 'col-lg-push-4' );
				if ( ! defined( 'BS_COL_LG_PUSH_5' ) ) define( 'BS_COL_LG_PUSH_5', 'col-lg-push-5' );
				if ( ! defined( 'BS_COL_LG_PUSH_6' ) ) define( 'BS_COL_LG_PUSH_6', 'col-lg-push-6' );
				if ( ! defined( 'BS_COL_LG_PUSH_7' ) ) define( 'BS_COL_LG_PUSH_7', 'col-lg-push-7' );
				if ( ! defined( 'BS_COL_LG_PUSH_8' ) ) define( 'BS_COL_LG_PUSH_8', 'col-lg-push-8' );
				if ( ! defined( 'BS_COL_LG_PUSH_9' ) ) define( 'BS_COL_LG_PUSH_9', 'col-lg-push-9' );
				if ( ! defined( 'BS_COL_LG_PUSH_10' ) ) define( 'BS_COL_LG_PUSH_10', 'col-lg-push-10' );
				if ( ! defined( 'BS_COL_LG_PUSH_11' ) ) define( 'BS_COL_LG_PUSH_11', 'col-lg-push-11' );
				if ( ! defined( 'BS_COL_LG_PUSH_12' ) ) define( 'BS_COL_LG_PUSH_12', 'col-lg-push-12' );

				//	MD
				if ( ! defined( 'BS_COL_MD_PUSH_1' ) ) define( 'BS_COL_MD_PUSH_1', 'col-md-push-1' );
				if ( ! defined( 'BS_COL_MD_PUSH_2' ) ) define( 'BS_COL_MD_PUSH_2', 'col-md-push-2' );
				if ( ! defined( 'BS_COL_MD_PUSH_3' ) ) define( 'BS_COL_MD_PUSH_3', 'col-md-push-3' );
				if ( ! defined( 'BS_COL_MD_PUSH_4' ) ) define( 'BS_COL_MD_PUSH_4', 'col-md-push-4' );
				if ( ! defined( 'BS_COL_MD_PUSH_5' ) ) define( 'BS_COL_MD_PUSH_5', 'col-md-push-5' );
				if ( ! defined( 'BS_COL_MD_PUSH_6' ) ) define( 'BS_COL_MD_PUSH_6', 'col-md-push-6' );
				if ( ! defined( 'BS_COL_MD_PUSH_7' ) ) define( 'BS_COL_MD_PUSH_7', 'col-md-push-7' );
				if ( ! defined( 'BS_COL_MD_PUSH_8' ) ) define( 'BS_COL_MD_PUSH_8', 'col-md-push-8' );
				if ( ! defined( 'BS_COL_MD_PUSH_9' ) ) define( 'BS_COL_MD_PUSH_9', 'col-md-push-9' );
				if ( ! defined( 'BS_COL_MD_PUSH_10' ) ) define( 'BS_COL_MD_PUSH_10', 'col-md-push-10' );
				if ( ! defined( 'BS_COL_MD_PUSH_11' ) ) define( 'BS_COL_MD_PUSH_11', 'col-md-push-11' );
				if ( ! defined( 'BS_COL_MD_PUSH_12' ) ) define( 'BS_COL_MD_PUSH_12', 'col-md-push-12' );

				//	SM
				if ( ! defined( 'BS_COL_SM_PUSH_1' ) ) define( 'BS_COL_SM_PUSH_1', 'col-sm-push-1' );
				if ( ! defined( 'BS_COL_SM_PUSH_2' ) ) define( 'BS_COL_SM_PUSH_2', 'col-sm-push-2' );
				if ( ! defined( 'BS_COL_SM_PUSH_3' ) ) define( 'BS_COL_SM_PUSH_3', 'col-sm-push-3' );
				if ( ! defined( 'BS_COL_SM_PUSH_4' ) ) define( 'BS_COL_SM_PUSH_4', 'col-sm-push-4' );
				if ( ! defined( 'BS_COL_SM_PUSH_5' ) ) define( 'BS_COL_SM_PUSH_5', 'col-sm-push-5' );
				if ( ! defined( 'BS_COL_SM_PUSH_6' ) ) define( 'BS_COL_SM_PUSH_6', 'col-sm-push-6' );
				if ( ! defined( 'BS_COL_SM_PUSH_7' ) ) define( 'BS_COL_SM_PUSH_7', 'col-sm-push-7' );
				if ( ! defined( 'BS_COL_SM_PUSH_8' ) ) define( 'BS_COL_SM_PUSH_8', 'col-sm-push-8' );
				if ( ! defined( 'BS_COL_SM_PUSH_9' ) ) define( 'BS_COL_SM_PUSH_9', 'col-sm-push-9' );
				if ( ! defined( 'BS_COL_SM_PUSH_10' ) ) define( 'BS_COL_SM_PUSH_10', 'col-sm-push-10' );
				if ( ! defined( 'BS_COL_SM_PUSH_11' ) ) define( 'BS_COL_SM_PUSH_11', 'col-sm-push-11' );
				if ( ! defined( 'BS_COL_SM_PUSH_12' ) ) define( 'BS_COL_SM_PUSH_12', 'col-sm-push-12' );

				//	XS
				if ( ! defined( 'BS_COL_XS_PUSH_1' ) ) define( 'BS_COL_XS_PUSH_1', 'col-xs-push-1' );
				if ( ! defined( 'BS_COL_XS_PUSH_2' ) ) define( 'BS_COL_XS_PUSH_2', 'col-xs-push-2' );
				if ( ! defined( 'BS_COL_XS_PUSH_3' ) ) define( 'BS_COL_XS_PUSH_3', 'col-xs-push-3' );
				if ( ! defined( 'BS_COL_XS_PUSH_4' ) ) define( 'BS_COL_XS_PUSH_4', 'col-xs-push-4' );
				if ( ! defined( 'BS_COL_XS_PUSH_5' ) ) define( 'BS_COL_XS_PUSH_5', 'col-xs-push-5' );
				if ( ! defined( 'BS_COL_XS_PUSH_6' ) ) define( 'BS_COL_XS_PUSH_6', 'col-xs-push-6' );
				if ( ! defined( 'BS_COL_XS_PUSH_7' ) ) define( 'BS_COL_XS_PUSH_7', 'col-xs-push-7' );
				if ( ! defined( 'BS_COL_XS_PUSH_8' ) ) define( 'BS_COL_XS_PUSH_8', 'col-xs-push-8' );
				if ( ! defined( 'BS_COL_XS_PUSH_9' ) ) define( 'BS_COL_XS_PUSH_9', 'col-xs-push-9' );
				if ( ! defined( 'BS_COL_XS_PUSH_10' ) ) define( 'BS_COL_XS_PUSH_10', 'col-xs-push-10' );
				if ( ! defined( 'BS_COL_XS_PUSH_11' ) ) define( 'BS_COL_XS_PUSH_11', 'col-xs-push-11' );
				if ( ! defined( 'BS_COL_XS_PUSH_12' ) ) define( 'BS_COL_XS_PUSH_12', 'col-xs-push-12' );

			break;

		endswitch;
	}
}


/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * The following block of code makes it simple to extend one of the core admin
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SYSTEM_STARTUP' ) ) :

	class System_startup extends NAILS_System_startup
	{
	}

endif;

/* End of file System_startup.php */
/* Location: ./modules/system/hooks/System_startup.php */