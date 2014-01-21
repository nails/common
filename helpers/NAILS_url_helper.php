<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('site_url'))
{
	function site_url( $uri = '', $force_secure = FALSE )
	{
		$CI =& get_instance();
		return $CI->config->site_url( $uri, $force_secure );
	}
}


// --------------------------------------------------------------------------


/**
 * unique_for_url
 *
 * Takes a string, makes it URL safe and returns it (will check the DB to make sure that the string is unique)
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists( 'unique_for_url' ) )
{
	function unique_for_url( $str, $table, $col )
	{
		//	Prep the string
		$str = url_title( $str, 'dash', TRUE );

		//	Check if unique
		$ci =& get_instance();
		$i	= 0;

		do {

			$check = ( $i === 0 ) ? $str : $str. '-' . $i;
			$ci->db->where( $col, $check );

			if ( $ci->db->count_all_results( $table ) === 0 )
				break;

			$i++;

		} while ( 1 );

		return $check;
	}
}


// --------------------------------------------------------------------------


/**
 * shorten_url
 *
 * Takes a URL and shortens it using the mini_model
 *
 * @access	public
 * @param	string	$url	The URL to shorten
 * @return	string
 */
if ( ! function_exists( 'shorten_url' ) )
{
	function shorten_url( $url )
	{
		$ci =& get_instance();
		$ci->load->model( 'mini_model' );
		return $ci->mini_model->shorten( $url );
	}
}


// --------------------------------------------------------------------------


if ( ! function_exists('secure_site_url'))
{
	function secure_site_url($uri = '')
	{
		return SECURE_BASE_URL . $uri;
	}
}


/* End of file url_helper.php */
/* Location: ./helpers/url_helper.php */