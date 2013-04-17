<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * get_userobject()
 *
 * Gets a reference to the IA user object
 *
 * @access	public
 * @return	object
 */
if ( ! function_exists( 'get_userobject' ) )
{
	function get_userobject()
	{
		if ( ! defined( 'NAILS_USR_OBJ' ) )
			return FALSE;
			
		$_ci =& get_instance();
		
		if ( ! isset( $_ci->{NAILS_USR_OBJ} ) )
			return FALSE;
		
		return $_ci->{NAILS_USR_OBJ};
	}
}


// --------------------------------------------------------------------------



/**
 * active_user()
 *
 * Handy way of getting data from the active user object
 *
 * @access	public
 * @param	string	$key	The key(s) to fetch
 * @return	object
 */
if ( ! function_exists( 'active_user' ) )
{
	function active_user( $keys = FALSE, $delimiter = ' ' )
	{
		$_usr_obj =& get_userobject();
		
		if ( $_usr_obj ) :
		
			return $_usr_obj->active_user( $keys, $delimiter );
			
		else :
		
			return FALSE;
		
		endif;
	}
}


// --------------------------------------------------------------------------



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
		$_to		= APP_EMAIL_DEVELOPER;
		$_headers	= 'From: ' . APP_EMAIL_FROM_NAME . ' <' . 'root@' . gethostname() . '>' . "\r\n" .
					  'Reply-To: ' . APP_EMAIL_FROM_EMAIL . "\r\n" .
					  'X-Mailer: PHP/' . phpversion()  . "\r\n" .
					  'X-Priority: 1 (Highest)' . "\r\n" .
					  'X-Mailer: X-MSMail-Priority: High/' . "\r\n" .
					  'Importance: High';
					  
		@mail( $_to, $subject , $message, $_headers );
	}
}


/* End of file system_helper.php */
/* Location: ./application/helpers/system_helper.php */