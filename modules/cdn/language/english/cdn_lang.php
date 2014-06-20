<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			CDN Langfile
*
* Description:  Generic language file for Nails CDN module & libraries
*
*/

	//	General Errors
	$lang['cdn_error_not_configured']			= 'CDN Not configured correctly';
	$lang['cdn_error_no_file']					= 'You did not select a file to upload.';
	$lang['cdn_error_cache_write_fail']			= 'Cache directory is not writeable.';
	$lang['cdn_error_target_write_fail_mkdir']	= 'The target directory does not exist and could not be created. <small>(%s)</small>';
	$lang['cdn_error_target_write_fail']		= 'The target directory is not writable. <small>(%s)</small>';

	//	Bucket errors
	$lang['cdn_error_bucket_mkdir']			= 'Failed to create bucket directory.';
	$lang['cdn_error_bucket_mkdir_su']		= 'Failed to create bucket directory (%s).';
	$lang['cdn_error_bucket_insert']		= 'Failed to create bucket record.';
	$lang['cdn_error_bucket_unlink']		= 'Failed to destroy bucket.';
	$lang['cdn_error_bucket_invalid']		= 'Not a valid bucket';

	//	Object Errors
	$lang['cdn_error_object_invalid']		= 'Not a valid object';

	//	Object Upload Errors
	$lang['cdn_stream_content_type']		= 'A Content-Type must be defined for data stream uploads.';
	$lang['cdn_error_bad_mime']				= 'The file type is not allowed, accepted file type is %s.';
	$lang['cdn_error_bad_mime_plural']		= 'The file type is not allowed, accepted file types are: %s.';
	$lang['cdn_error_filesize']				= 'The file is too large, maximum file size is %s.';
	$lang['cdn_error_maxwidth']				= 'Image is too wide (max %spx)';
	$lang['cdn_error_maxheight']			= 'Image is too tall (max %spx)';
	$lang['cdn_error_minwidth']				= 'Image is too narrow (min %spx)';
	$lang['cdn_error_minheight']			= 'Image is too short (min %spx)';
	$lang['cdn_error_delete']				= 'File failed to delete, it may be in use.';
	$lang['cdn_error_delete_nofile']		= 'No file to delete.';
	$lang['cdn_error_couldnotmove']			= 'Failed to move uploaded file into the bucket.';
	$lang['cdn_error_bad_extension_mime']	= '%s is not a valid extension for this file type.';

	//	Tag Errors
	$lang['cdn_error_tag_exists']			= 'Tag already exists';
	$lang['cdn_error_tag_notexist']			= 'Tag does not exist';
	$lang['cdn_error_tag_invalid']			= 'Not a valid tag';


	//	Serve errors
	$lang['cdn_error_serve_invalid_request']	= 'Invalid Request';
	$lang['cdn_error_serve_bad_token']			= 'Bad Token';
	$lang['cdn_error_serve_object_not_defined']	= 'Object not defined';
	$lang['cdn_error_serve_file_not_found']		= 'File not found';




