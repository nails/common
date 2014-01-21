<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * get_loaded_modules()
 *
 * Fetch the loaded modules for this app
 *
 * @access	public
 * @param	none
 * @return	object
 */
if ( ! function_exists( 'get_loaded_modules' ) )
{
	function get_loaded_modules()
	{
		//	If we already know which modules are loaded then return that, save
		//	the [small] overhead of working out the modules again and again.

		if ( isset( $GLOBALS['NAILS_LOADED_MODULES'] ) ) :

			return $GLOBALS['NAILS_LOADED_MODULES'];

		endif;

		// --------------------------------------------------------------------------

		//	Determine which modules are to be loaded
		$_app_modules	= explode( ',', APP_NAILS_MODULES );
		$_app_modules	= array_unique( $_app_modules );
		$_app_modules	= array_filter( $_app_modules );

		//	Prevent errors from being thrown if there are no elements
		if ( $_app_modules ) :

			$_app_modules	= array_combine( $_app_modules, $_app_modules );

		endif;

		$_nails_modules = array();
		foreach ( $_app_modules AS $module ) :

			preg_match( '/^(.*?)(\[(.*?)\])?$/', $module, $_matches );

			if ( isset( $_matches[1] ) && isset( $_matches[3] ) ) :

				$_nails_modules[$_matches[1]] = explode( '|', $_matches[3] );

			elseif ( isset( $_matches[1] ) ) :

				$_nails_modules[$_matches[1]] = array();

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Save as a $GLOBAL for next time
		$GLOBALS['NAILS_LOADED_MODULES'] = $_nails_modules;

		// --------------------------------------------------------------------------

		return $_nails_modules;
	}
}


// --------------------------------------------------------------------------


/**
 * module_is_enabled()
 *
 * Handy way of determining whether a module is enabled or not in the app's config
 *
 * @access	public
 * @param	string	$key	The key(s) to fetch
 * @return	object
 */
if ( ! function_exists( 'module_is_enabled' ) )
{
	function module_is_enabled( $module )
	{
		$_nails_modules = get_loaded_modules();

		// --------------------------------------------------------------------------

		//	Allow wildcard
		reset( $_nails_modules );
		$_wildcard = key( $_nails_modules );

		if ( $_wildcard == '*' ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		preg_match( '/^(.*?)(\[(.*?)\])?$/', $module, $_matches );

		$_module	= isset( $_matches[1] ) ? $_matches[1] : '';
		$_submodule	= isset( $_matches[3] ) ? $_matches[3] : '';

		if ( isset( $_nails_modules[$_module] ) ) :

			//	Are we testing for a submodule in particular?
			if ( $_submodule ) :

				return array_search( $_submodule, $_nails_modules[$_module] ) !== FALSE;

			else :

				return TRUE;

			endif;

		else :

			return FALSE;

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * Detects whether the current page is secure or not
 *
 * @access	public
 * @param	string
 * @return	bool
 */
function page_is_secure()
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


// --------------------------------------------------------------------------


/**
 * send_developer_mail()
 *
 * Quickly send a high priority email via mail() to the APP_DEVELOPER
 *
 *
 * @access	public
 * @param	string $subject The subject of the email
 * @param	string $message The message of the email
 * @return	object
 */
if ( ! function_exists( 'send_developer_mail' ) )
{
	function send_developer_mail( $subject, $message )
	{
		if ( ! defined( 'APP_DEVELOPER_EMAIL' ) || ! APP_DEVELOPER_EMAIL ) :

			//	Log the fact there's no email
			log_message( 'error', 'Attempting to send developer email, but APP_DEVELOPER_EMAIL is not defined.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_to		= ENVIRONMENT != 'production' && defined( 'EMAIL_OVERRIDE' ) && EMAIL_OVERRIDE ? EMAIL_OVERRIDE : APP_DEVELOPER_EMAIL;
		$_headers	= 'From: ' . APP_EMAIL_FROM_NAME . ' <' . 'root@' . gethostname() . '>' . "\r\n" .
					  'Reply-To: ' . APP_EMAIL_FROM_EMAIL . "\r\n" .
					  'X-Mailer: PHP/' . phpversion()  . "\r\n" .
					  'X-Priority: 1 (Highest)' . "\r\n" .
					  'X-Mailer: X-MSMail-Priority: High/' . "\r\n" .
					  'Importance: High';

		$_message	 = $message;

		// --------------------------------------------------------------------------

		$_ci =& get_instance();

		$_info = array(
			'uri'				=> isset( $_ci->uri )			? $_ci->uri->uri_string()				: '',

			'session'			=> isset( $_ci->session )		? serialize( $_ci->session->userdata )	: '',
			'post'				=> isset( $_POST )				? serialize( $_POST )					: '',
			'get'				=> isset( $_GET )				? serialize( $_GET )					: '',
			'server'			=> isset( $_SERVER )			? serialize( $_SERVER )					: '',
			'globals'			=> isset( $GLOBALS['error'] )	? serialize( $GLOBALS['error'] )		: '',

			'debug_backtrace'	=> serialize( debug_backtrace() )
		);

		$_message	.= '' . "\n";
		$_message	.= '- - - - - - - - - - - - - - - - - - - - - -' . "\n";
		$_message	.= '' . "\n";
		$_message	.= 'DEBUGGING DATA' . "\n";
		$_message	.= '' . "\n";
		$_message	.= 'URI: ' .		$_info['uri'] . "\n\n";
		$_message	.= 'SESSION: ' .	$_info['session'] . "\n\n";
		$_message	.= 'POST: ' .		$_info['post'] . "\n\n";
		$_message	.= 'GET: ' .		$_info['get'] . "\n\n";
		$_message	.= 'SERVER: ' .		$_info['server'] . "\n\n";
		$_message	.= 'GLOBALS: ' .	$_info['globals'] . "\n\n";
		$_message	.= 'BACKTRACE: ' .	$_info['debug_backtrace'] . "\n\n";

		if ( defined( 'NAILS_DB_ENABLED' ) && NAILS_DB_ENABLED ) :

			$_message	.= 'LAST KNOWN QUERY: ' . $_ci->db->last_query() . "\n\n";

		endif;

		@mail( $_to, '!! ' . $subject . ' - ' . APP_NAME , $message, $_headers );
	}
}


/* End of file system_helper.php */
/* Location: ./helpers/system_helper.php */