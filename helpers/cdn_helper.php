<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
/**
 * cdn_thumb()
 *
 * Returns the URL for a thumbnail generated on the CDN
 *
 * @param	string	$dir	The path to the image relative to the CDN's root
 * @param	string	$file	The filename of the image we're 'thumbing'
 * @param	string	$width	The width of the thumbnail
 * @param	string	$height	The height of the thumbnail
 * @return	string
 */
if ( ! function_exists( 'cdn_thumb' ) )
{
	function cdn_thumb( $dir, $file, $width, $height )
	{
		$_out  = CDN_SERVER;
		$_out .= 'util/thumb/';
		$_out .= $width . '/' . $height . '/';
		$_out .= str_replace( '/', ':', $dir ) . '/';
		$_out .= $file;
		
		//	If the connection is secure then we'll need to swap the URLs
		if ( $_SERVER['SERVER_PORT'] == 443 ) :
		
			$_out = str_replace( CDN_SERVER , get_instance()->config->config['base_url'] . 'cdn/' , $_out );
			$_out = str_replace( 'http://', 'https://', $_out );
			
		endif;
		
		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * cdn_scale()
 *
 * Returns the URL for a scaled image generated on the CDN
 *
 * @param	string	$dir	The path to the image relative to the CDN's root
 * @param	string	$file	The filename of the image we're scaling
 * @param	string	$width	The width of the scaled image
 * @param	string	$height	The height of the scaled image
 * @return	string
 */
if ( ! function_exists( 'cdn_scale' ) )
{
	function cdn_scale( $dir, $file, $width, $height )
	{
		$_out  = CDN_SERVER;
		$_out .= 'util/scale/';
		$_out .= $width . '/' . $height . '/';
		$_out .= str_replace( '/', ':', $dir ) . '/';
		$_out .= $file;
		
		//	If the connection is secure then we'll need to swap the URLs
		if ( $_SERVER['SERVER_PORT'] == 443 ) :
		
			$_out = str_replace( CDN_SERVER , get_instance()->config->config['base_url'] . 'cdn/' , $_out );
			$_out = str_replace( 'http://', 'https://', $_out );
			
		endif;
		
		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * cdn_placeholder()
 *
 * Returns the URL for a placeholder graphic
 *
 * @param	string	$width	The width of the placeholder
 * @param	string	$height	The height of the placeholder
 * @param	string	$border	The width of the border, if any
 * @return	string
 */
if ( ! function_exists( 'cdn_placeholder' ) )
{
	function cdn_placeholder( $width = 100, $height = 100, $border = 0 )
	{
		$_out  = CDN_SERVER;
		$_out .= 'util/placeholder/';
		$_out .= $width . '/' . $height . '/' . $border;
		
		//	If the connection is secure then we'll need to swap the URLs
		if ( $_SERVER['SERVER_PORT'] == 443 ) :
		
			$_out = str_replace( CDN_SERVER , get_instance()->config->config['base_url'] . 'cdn/' , $_out );
			$_out = str_replace( 'http://', 'https://', $_out );
			
		endif;
		
		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * cdn_marker()
 *
 * Returns the URL for a marker graphic
 *
 * @param	string	$dir	The path to the image relative to the CDN's root
 * @param	string	$file	The filename of the image we're scaling
 * @return	string
 */
if ( ! function_exists( 'cdn_marker' ) )
{
	function cdn_marker( $dir, $file )
	{
		$_out  = CDN_SERVER;
		$_out .= 'util/marker/';
		$_out .= str_replace( '/', ':', $dir ) . '/';
		$_out .= $file;
		
		//	If the connection is secure then we'll need to swap the URLs
		if ( $_SERVER['SERVER_PORT'] == 443 ) :
		
			$_out = str_replace( CDN_SERVER , get_instance()->config->config['base_url'] . 'cdn/' , $_out );
			$_out = str_replace( 'http://', 'https://', $_out );
			
		endif;
		
		return $_out;
	}
}