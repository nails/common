<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			CDN Langfile
*
* Description:  Language file for Nails Local CDN
* 
*/

	//	Errors
	$lang['cdn_local_no_file']					= 'You did not select a file to upload.';
	$lang['cdn_local_stream_content_type']		= 'A Content-Type must be defined for data stream uploads.';
	$lang['cdn_local_error_cache_write_fail']	= 'Cache directory is not writeable.';
	$lang['cdn_local_error_target_write_fail']	= 'The target directory is not writable.';
	$lang['cdn_local_error_bad_mime']			= 'The file type is not allowed, accepted file type is %s.';
	$lang['cdn_local_error_bad_mime_plural']	= 'The file type is not allowed, accepted file types are: %s.';
	$lang['cdn_local_error_filesize']			= 'The file is too large, maximum file size is %s.';
	$lang['cdn_local_error_maxwidth']			= 'Image is too wide (max %spx)';
	$lang['cdn_local_error_maxheight']			= 'Image is too tall (max %spx)';
	$lang['cdn_local_error_minwidth']			= 'Image is too narrow (min %spx)';
	$lang['cdn_local_error_minheight']			= 'Image is too short (min %spx)';
	$lang['cdn_local_error_delete']				= 'File failed to delete';
	$lang['cdn_local_error_delete_nofile']		= 'No file to delete.';