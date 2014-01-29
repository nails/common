<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Generate a login as URL for a user
 *
 * @access	public
 * @param	int		$uid		The ID of the user we're logging in as
 * @param	string	$upassword	The encoded password of the user to log in as
 * @return	string
 */
if ( ! function_exists( 'login_as_url' ) )
{
	function login_as_url( $uid, $upassword )
	{
		if ( user_has_permission( 'admin.accounts.can_login_as' ) ) :

			//	Generate the return string
			$_url = uri_string();
			if ( $_GET ) :

				//	Remove common problematic GET vars (for instance, we don't want is_fancybox when we return)
				$_get = $_GET;
				unset( $_get['is_fancybox'] );
				unset( $_get['inline'] );

				if ( $_get ) :

					$_url .= '?' . http_build_query( $_get );

				endif;

			endif;

			$_return_string = '?return_to=' . urlencode( $_url );

			// --------------------------------------------------------------------------

			return site_url( 'auth/override/login_as/' . md5( $uid ) . '/' . md5( $upassword ) . $_return_string );

		else :

			return '';

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * Generate a login as button
 *
 * @access	public
 * @param	int		$uid		The ID of the user we're logging in as
 * @param	string	$upassword	The encoded password of the user to log in as
 * @return	string
 */
if ( ! function_exists( 'login_as_button' ) )
{
	function login_as_button( $uid, $upassword, $text = '', $attr = 'class="awesome small grey"' )
	{
		if ( user_has_permission( 'admin.accounts.can_login_as' ) ) :

			$text =  ! $text ? lang( 'admin_login_as' ) : $text;
			return anchor( login_as_url( $uid, $upassword ), $text, $attr );

		else :

			return '';

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * Generate a login as button
 *
 * @access	public
 * @param	int		$uid		The ID of the user we're logging in as
 * @param	string	$upassword	The encoded password of the user to log in as
 * @return	string
 */
if ( ! function_exists( '_ADMIN_CHANGE_ADD' ) )
{
	function _ADMIN_CHANGE_ADD( $verb, $article, $item, $item_id, $title, $url, $field, $old_value, $new_value, $strict_comparison = TRUE )
	{
		return get_instance()->admin_changelog_model->add( $verb, $article, $item, $item_id, $title, $url, $field, $old_value, $new_value, $strict_comparison );
	}
}

/* End of file admin_helper.php */
/* Location: ./application/modules/admin/helpers/admin_helper.php */