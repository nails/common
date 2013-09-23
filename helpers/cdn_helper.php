<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Returns the URL for serving raw content from the CDN
 *
 * @param	string	$object	The ID or filename of the object to serve
 * @return	string
 */
if ( ! function_exists( 'cdn_serve' ) )
{
	function cdn_serve( $object, $force_download = FALSE )
	{
		get_instance()->load->library( 'cdn' );

		return get_instance()->cdn->url_serve( $object, $force_download );
	}
}


// --------------------------------------------------------------------------


/**
 * Returns the URL for a thumbnail generated on the CDN
 *
 * @param	string	$bucket	The bucket which the image resides in
 * @param	string	$file	The filename of the image we're 'thumbing'
 * @param	string	$width	The width of the thumbnail
 * @param	string	$height	The height of the thumbnail
 * @return	string
 */
if ( ! function_exists( 'cdn_thumb' ) )
{
	function cdn_thumb( $object, $width, $height )
	{
		get_instance()->load->library( 'cdn' );

		return get_instance()->cdn->url_thumb( $object, $width, $height );
	}
}


// --------------------------------------------------------------------------


/**
 * Returns the URL for a scaled image generated on the CDN
 *
 * @param	string	$bucket	The bucket which the image resides in
 * @param	string	$file	The filename of the image we're 'scaling'
 * @param	string	$width	The width of the scaled image
 * @param	string	$height	The height of the scaled image
 * @return	string
 */
if ( ! function_exists( 'cdn_scale' ) )
{
	function cdn_scale( $object, $width, $height )
	{
		get_instance()->load->library( 'cdn' );

		return get_instance()->cdn->url_scale( $object, $width, $height );
	}
}


// --------------------------------------------------------------------------


/**
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
		get_instance()->load->library( 'cdn' );

		return get_instance()->cdn->url_placeholder( $width, $height, $border );
	}
}


// --------------------------------------------------------------------------


/**
 * Returns the URL for a blank avatar graphic
 *
 * @param	string	$width	The width of the placeholder
 * @param	string	$height	The height of the placeholder
 * @param	string	$border	The width of the border, if any
 * @return	string
 */
if ( ! function_exists( 'cdn_blank_avatar' ) )
{
	function cdn_blank_avatar( $width = 100, $height = 100, $sex = 'male' )
	{
		get_instance()->load->library( 'cdn' );

		return get_instance()->cdn->url_blank_avatar( $width, $height, $sex );
	}
}


// --------------------------------------------------------------------------


/**
 * Returns an expiring url
 *
 * @param	string	$bucket		The bucket which the image resides in
 * @param	string	$object		The object to be served
 * @param	string	$expires	The length of time the URL should be valid for, in seconds
 * @return	string
 */
if ( ! function_exists( 'cdn_expiring_url' ) )
{
	function cdn_expiring_url( $object, $expires )
	{
		get_instance()->load->library( 'cdn' );

		return get_instance()->cdn->url_expiring( $object, $expires );
	}
}


// --------------------------------------------------------------------------


/**
 * Get the extension of a file from it's mimetype
 *
 * @param	string	$mime_type	The mimetype to look up
 * @return	string
 */
if ( ! function_exists( 'get_ext_from_mimetype' ) )
{
	function get_ext_from_mimetype( $mime_type )
	{
		get_instance()->load->library( 'cdn' );

		return get_instance()->cdn->get_ext_from_mimetype( $mime_type );
	}
}


// --------------------------------------------------------------------------


/**
 * Get the mimetype of a file from it's extension
 *
 * @param	string	$ext	The extension to look up
 * @return	string
 */
if ( ! function_exists( 'get_mimetype_from_ext' ) )
{
	function get_mimetype_from_ext( $ext )
	{
		get_instance()->load->library( 'cdn' );

		return get_instance()->cdn->get_mimetype_from_ext( $ext );
	}
}


// --------------------------------------------------------------------------


/**
 * Get the mimetype from a file on disk
 *
 * @param	string	$file	The file to look up
 * @return	string
 */
if ( ! function_exists( 'get_mime_type_from_file' ) )
{
	function get_mime_type_from_file( $file )
	{
		get_instance()->load->library( 'cdn' );

		return get_instance()->cdn->get_mime_type_from_file( $file );
	}
}


/* End of file cdn_helper.php */
/* Location: ./helpers/cdn.php */