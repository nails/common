<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * user_has_permission()
 *
 * Alias to user->has_permission(); method
 *
 * @access	public
 * @param	mixed	$permission	The permission to check for
 * @param	string	$user	A user ID or object to check against, defaults to active_user()
 * @return	object
 */
if ( ! function_exists( 'user_has_permission' ) )
{
	function user_has_permission( $permission, $user = NULL )
	{
		$_usr_obj =& get_userobject();

		if ( $_usr_obj ) :

			return $_usr_obj->has_permission( $permission, $user );

		else :

			return FALSE;

		endif;
	}
}

/* End of file user_helper.php */
/* Location: ./helpers/user_helper.php */