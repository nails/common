<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class System_startup
{
	public function define_constants()
	{
		//	Define some generic Nails constants, allow dev to override these - just in case
		if ( ! defined( 'NAILS_VERSION' ) )					define( 'NAILS_VERSION',				'0.1.0' );
		if ( ! defined( 'NAILS_PACKAGE_NAME' ) )			define( 'NAILS_PACKAGE_NAME',			'Nails' );
		if ( ! defined( 'NAILS_PACKAGE_URL' ) )				define( 'NAILS_PACKAGE_URL',			'http://nailsapp.co.uk/' );
		if ( ! defined( 'NAILS_APP_STRAPLINE' ) )			define( 'NAILS_APP_STRAPLINE',			'A webapp powered by <a href="' . NAILS_PACKAGE_URL . '">' . NAILS_PACKAGE_NAME . '</a>, ooh la la!' );


		// --------------------------------------------------------------------------

		//	Environment
		if ( ! defined( 'ENVIRONMENT' ) )					define( 'ENVIRONMENT',					'development' );

		// --------------------------------------------------------------------------

		//	Cache Directory
		if ( ! defined( 'DEPLOY_CACHE_DIR' ) )				define( 'DEPLOY_CACHE_DIR',				FCPATH . APPPATH . 'cache/' );

		// --------------------------------------------------------------------------

		//	Check routes_app.php exists
		if ( is_file( DEPLOY_CACHE_DIR . 'routes_app.php' ) ) :

			define( 'NAILS_STARTUP_GENERATE_APP_ROUTES', FALSE );

		else :

			//	Not found, crude hook seeing as basically nothing has loaded yet
			define( 'NAILS_STARTUP_GENERATE_APP_ROUTES', TRUE );

		endif;

		// --------------------------------------------------------------------------

		//	Database
		if ( ! defined( 'DEPLOY_DB_HOST' ) )				define( 'DEPLOY_DB_HOST',				'localhost' );
		if ( ! defined( 'DEPLOY_DB_USERNAME' ) )			define( 'DEPLOY_DB_USERNAME',			'' );
		if ( ! defined( 'DEPLOY_DB_PASSWORD' ) )			define( 'DEPLOY_DB_PASSWORD',			'' );
		if ( ! defined( 'DEPLOY_DB_DATABASE' ) )			define( 'DEPLOY_DB_DATABASE',			'' );

		// --------------------------------------------------------------------------

		//	These settings can be specified wherever it makes most sense (e.g if
		//	maintenance mode needs enabled app wide, then specify it in app.php, if
		//	only a single server needs to be put in maintenance mode then define in
		//	deploy.php

		if ( ! defined( 'PROFILING') )						define( 'PROFILING',					FALSE );
		if ( ! defined( 'MAINTENANCE') )					define( 'MAINTENANCE',					FALSE );
		if ( ! defined( 'MAINTENANCE_WHITELIST') )			define( 'MAINTENANCE_WHITELIST',		'127.0.0.1' );

		// --------------------------------------------------------------------------

		//	Default app constants (if not already defined)
		//	These should be specified in config/app.php

		if ( ! defined( 'NAILS_DB_PREFIX' ) )				define( 'NAILS_DB_PREFIX',				'nails_' );
		if ( ! defined( 'APP_PRIVATE_KEY' ) )				define( 'APP_PRIVATE_KEY',				'' );
		if ( ! defined( 'APP_NAME' ) )						define( 'APP_NAME',						'Untitled' );

		if ( ! defined( 'APP_USER_ALLOW_REGISTRATION' ) )	define( 'APP_USER_ALLOW_REGISTRATION',	FALSE );
		if ( ! defined( 'APP_USER_DEFAULT_GROUP' ) )		define( 'APP_USER_DEFAULT_GROUP',		3 );
		if ( ! defined( 'APP_MULTI_LANG' ) )				define( 'APP_MULTI_LANG',				FALSE );
		if ( ! defined( 'APP_DEFAULT_LANG_SLUG' ) )			define( 'APP_DEFAULT_LANG_SLUG',		'english' );
		if ( ! defined( 'APP_NAILS_MODULES' ) )				define( 'APP_NAILS_MODULES',			'' );
		if ( ! defined( 'APP_STAGING_USERPASS' ) )			define( 'APP_STAGING_USERPASS',			serialize( array() ) );
		if ( ! defined( 'APP_SSL_ROUTING' ) )				define( 'APP_SSL_ROUTING',				FALSE );
		if ( ! defined( 'APP_DEFAULT_TIMEZONE' ) )			define( 'APP_DEFAULT_TIMEZONE',			'UTC' );
		if ( ! defined( 'APP_NATIVE_LOGIN_USING' ) )		define( 'APP_NATIVE_LOGIN_USING',		'EMAIL' );	//	[EMAIL|USERNAME|BOTH]
		if ( ! defined( 'APP_ADMIN_IP_WHITELIST' ) )		define( 'APP_ADMIN_IP_WHITELIST',		json_encode( array() ) );


		// --------------------------------------------------------------------------

		//	Deployment specific constants (if not already defined)
		//	These should be specified in config/deploy.php

		if ( ! defined( 'DEPLOY_SYSTEM_TIMEZONE') )			define( 'DEPLOY_SYSTEM_TIMEZONE',		'UTC' );

		//	If this is changed, update CORE_NAILS_Log.php too
		if ( ! defined( 'DEPLOY_LOG_DIR') )					define( 'DEPLOY_LOG_DIR',				FCPATH . APPPATH . 'logs/' );

		// --------------------------------------------------------------------------

		//	Email
		if ( ! defined( 'APP_DEVELOPER_EMAIL' ) )			define( 'APP_DEVELOPER_EMAIL',			'' );
		if ( ! defined( 'APP_EMAIL_FROM_NAME' ) )			define( 'APP_EMAIL_FROM_NAME',			APP_NAME );
		if ( ! defined( 'APP_EMAIL_FROM_EMAIL' ) )			define( 'APP_EMAIL_FROM_EMAIL',			'' );

		if ( ! defined( 'DEPLOY_SMTP_HOST' ) )				define( 'DEPLOY_SMTP_HOST',				'localhost' );
		if ( ! defined( 'DEPLOY_SMTP_USERNAME' ) )			define( 'DEPLOY_SMTP_USERNAME',			'' );
		if ( ! defined( 'DEPLOY_SMTP_PASSWORD' ) )			define( 'DEPLOY_SMTP_PASSWORD',			'' );
		if ( ! defined( 'DEPLOY_SMTP_PORT' ) )				define( 'DEPLOY_SMTP_PORT',				'25' );
		if ( ! defined( 'EMAIL_DEBUG' ) )					define( 'EMAIL_DEBUG',					FALSE );
		if ( ! defined( 'EMAIL_OVERRIDE' ) )				define( 'EMAIL_OVERRIDE',				'' );

		// --------------------------------------------------------------------------

		//	CDN
		if ( ! defined( 'APP_CDN_DRIVER' ) )				define( 'APP_CDN_DRIVER',				'LOCAL' );
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

		// --------------------------------------------------------------------------

		//	Set NAILS_URL here as it's dependent on knowing whether SSL is set or not
		//	and if the current page is secure.

		if ( ! defined( 'NAILS_URL') ) :

			if ( APP_SSL_ROUTING && page_is_secure() ) :

				define( 'NAILS_URL', SECURE_BASE_URL . 'vendor/shed/nails/' );

			else :

				define( 'NAILS_URL', BASE_URL . 'vendor/shed/nails/' );

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
	}
}