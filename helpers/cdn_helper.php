<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists( '_cdn_include_library' ) )
{
	function _cdn_include_library()
	{
		//	Check to see if the library is being overloaded by the app
		
		if ( file_exists( FCPATH . APPPATH . 'libraries/Cdn.php' ) ) :
		
			include_once FCPATH . APPPATH . 'libraries/Cdn.php';
		
		else :
		
			include_once NAILS_PATH . 'libraries/Cdn.php';
			
		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * Returns the URL for serving raw content from the CDN
 *
 * @param	string	$bucket	The bucket which the file resides in
 * @param	string	$file	The filename of the object
 * @return	string
 */
if ( ! function_exists( 'cdn_serve' ) )
{
	function cdn_serve( $bucket, $file )
	{
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::cdn_serve_url( $bucket, $file );
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
	function cdn_thumb( $bucket, $file, $width, $height )
	{
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::cdn_thumb_url( $bucket, $file, $width, $height );
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
	function cdn_scale( $bucket, $file, $width, $height )
	{
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::cdn_scale_url( $bucket, $file, $width, $height );
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
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::cdn_placeholder_url( $width, $height, $border );
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
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::cdn_placeholder_url( $width, $height, $border );
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
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::cdn_blank_avatar_url( $width, $height, $sex );
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
	function cdn_expiring_url( $bucket, $object, $expires )
	{
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::cdn_expiring_url( $bucket, $object, $expires );
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
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::get_ext_from_mimetype( $mime_type );
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
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::get_mimetype_from_ext( $ext );
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
		_cdn_include_library();
		
		// --------------------------------------------------------------------------
		
		return CDN::get_mime_type_from_file( $file );
	}
}


/* End of file cdn.php */
/* Location: ./application/libraries/cdn.php */