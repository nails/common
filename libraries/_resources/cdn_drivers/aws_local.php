<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			CDN
*
* Description:	A Library for dealing with content stored on Amazon's Web Services but using local processing
*
*/

//	Namespace malarky
use Aws\S3\S3Client;

class Aws_local_CDN
{
	private $cdn;
	public $errors;
	private $_bucket_url;
	private $_bucket;
	private $_s3;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function __construct( $options = NULL )
	{
		//	Shortcut to CDN Library (mainly for setting errors)
		$this->cdn		=& get_instance()->cdn;

		//	Define error array
		$this->errors	= array();

		// --------------------------------------------------------------------------

		//	Load langfile
		get_instance()->lang->load( 'cdn/cdn_driver_aws_local' );

		// --------------------------------------------------------------------------

		//	Check all the constants are defined properly
		//	DEPLOY_CDN_DRIVER_AWS_IAM_ACCESS_ID
		//	DEPLOY_CDN_DRIVER_AWS_IAM_ACCESS_SECRET
		//	DEPLOY_CDN_DRIVER_AWS_S3_BUCKET

		if ( ! defined( 'DEPLOY_CDN_DRIVER_AWS_IAM_ACCESS_ID' ) ) :

			//	TODO: Specify correct lang
			show_error( lang( 'cdn_error_not_configured' ) );

		endif;

		if ( ! defined( 'DEPLOY_CDN_DRIVER_AWS_IAM_ACCESS_SECRET' ) ) :

			//	TODO: Specify correct lang
			show_error( lang( 'cdn_error_not_configured' ) );

		endif;

		if ( ! defined( 'DEPLOY_CDN_DRIVER_AWS_S3_BUCKET' ) ) :

			//	TODO: Specify correct lang
			show_error( lang( 'cdn_error_not_configured' ) );

		endif;

		// --------------------------------------------------------------------------

		//	Instanciate the AWS PHP SDK
		$this->_s3 = S3Client::factory(array(
			'key'		=> DEPLOY_CDN_DRIVER_AWS_IAM_ACCESS_ID,
			'secret'	=> DEPLOY_CDN_DRIVER_AWS_IAM_ACCESS_SECRET,
		));

		// --------------------------------------------------------------------------

		//	Set the bucket we're using
		$this->_bucket = DEPLOY_CDN_DRIVER_AWS_S3_BUCKET;

		// --------------------------------------------------------------------------

		//	Finally, define the bucket endpoint/url, in case they change it.
		$this->_bucket_url = '.s3.amazonaws.com/';
	}


	// --------------------------------------------------------------------------


	/*	! OBJECT METHODS */


	// --------------------------------------------------------------------------


	/**
	 * Creates a new object
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function object_create( $data )
	{
		$_bucket		= ! empty( $data->bucket->slug )	? $data->bucket->slug	: '';
		$_filename_orig	= ! empty( $data->filename )		? $data->filename		: '';

		$_filename	= strtolower( substr( $_filename_orig, 0, strrpos( $_filename_orig, '.' ) ) );
		$_extension	= strtolower( substr( $_filename_orig, strrpos( $_filename_orig, '.' ) ) );

		$_source	= ! empty( $data->file )			? $data->file			: '';
		$_mime		= ! empty( $data->mime )			? $data->mime			: '';
		$_name		= ! empty( $data->name )			? $data->name			: 'file' . $_extension;

		// --------------------------------------------------------------------------

		try
		{
			$_result = $this->_s3->putObject(array(
				'Bucket'		=> $this->_bucket,
				'Key'			=> $_bucket . '/' . $_filename . $_extension,
				'SourceFile'	=> $_source,
				'ContentType'	=> $_mime,
				'ACL'			=> 'public-read'
			));

			//	Now try to duplicate the file and set the appropriate meta tag so there's
			//	a downloadable version

			try
			{

				$_result = $this->_s3->copyObject(array(
					'Bucket'				=> $this->_bucket,
					'CopySource'			=> $this->_bucket . '/' . $_bucket . '/' . $_filename . $_extension,
					'Key'					=> $_bucket . '/' . $_filename . '-download' . $_extension,
					'ContentType'			=> 'application/octet-stream',
					'ContentDisposition'	=> 'attachment; filename="' . str_replace( '"', '', $_name ) . '" ',
					'MetadataDirective'		=> 'REPLACE',
					'ACL'					=> 'public-read'
				));

				return TRUE;
			}
			catch( Exception $e )
			{
				$this->cdn->set_error( 'AWS-SDK EXCEPTION: ' . get_class( $e ) . ': ' . $e->getMessage() );
				return FALSE;
			}
		}
		catch ( Exception $e )
		{
			$this->cdn->set_error( 'AWS-SDK EXCEPTION: ' . get_class( $e ) . ': ' . $e->getMessage() );
			return FALSE;
		}
	}


	// --------------------------------------------------------------------------


	/**
	 * Destroys (permenantly deletes) an object
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function object_destroy( $object, $bucket )
	{
		try
		{

			$_filename	= strtolower( substr( $object, 0, strrpos( $object, '.' ) ) );
			$_extension	= strtolower( substr( $object, strrpos( $object, '.' ) ) );

			$_options				= array();
			$_options['Bucket']		= $this->_bucket;
			$_options['Objects']	= array();
			$_options['Objects'][]	= array( 'Key' => $bucket . '/' . $_filename . $_extension );
			$_options['Objects'][]	= array( 'Key' => $bucket . '/' . $_filename . '-download' . $_extension );

			$_result = $this->_s3->deleteObjects( $_options );

			return TRUE;
		}
		catch ( Exception $e )
		{
			$this->cdn->set_error( 'AWS-SDK EXCEPTION: ' . get_class( $e ) . ': ' . $e->getMessage() );
			return FALSE;
		}
	}


	// --------------------------------------------------------------------------


	/*	! BUCKET METHODS */


	// --------------------------------------------------------------------------


	/**
	 * Creates a new bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 **/
	public function bucket_create( $bucket )
	{
		//	Attempt to create a 'folder' object on S3
		if ( ! $this->_s3->doesObjectExist( $this->_bucket, $bucket . '/' ) ) :

			try
			{
				$_result = $this->_s3->putObject(array(
					'Bucket'	=> $this->_bucket,
					'Key'		=> $bucket . '/',
					'Body'		=> ''
				));

				return TRUE;
			}
			catch ( Exception $e )
			{
				$this->cdn->set_error( 'AWS-SDK ERROR: ' . $e->getMessage() );
				return FALSE;
			}

		else :

			//	Bucket already exists.
			return TRUE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function bucket_destroy( $bucket )
	{
		try
		{
			$_result = $this->_s3->deleteMatchingObjects( $this->_bucket, $bucket . '/' );

			return TRUE;
		}
		catch ( Exception $e )
		{
			$this->cdn->set_error( 'AWS-SDK ERROR: ' . $e->getMessage() );
			return FALSE;
		}
	}


	// --------------------------------------------------------------------------


	/*	! URL GENERATOR METHODS */


	// --------------------------------------------------------------------------


	/**
	 * Generates the correct URL for serving up a file
	 *
	 * @access	public
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$object	The filename of the object
	 * @return	string
	 **/
	public function url_serve( $object, $bucket, $force_download )
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_SERVING;
		$_out .= $bucket . '/';

		if ( $force_download ) :

			//	If we're forcing the download we need to reference a slightly different file
			//	On upload two instances were created, the "normal" streaming type one and another
			//	with the appropriate content-types set so that the browser downloads as oppossed
			//	to renders it

			$_filename	= strtolower( substr( $object, 0, strrpos( $object, '.' ) ) );
			$_extension	= strtolower( substr( $object, strrpos( $object, '.' ) ) );

			$_out .= $_filename;
			$_out .= '-download';
			$_out .= $_extension;

		else :

			//	If we're not forcing the download we can serve straight out of S3
			$_out .= $object;

		endif;

		return $this->_url_make_secure( $_out, FALSE );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'serve' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 **/
	public function url_serve_scheme( $force_download )
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_SERVING;
		$_out .= '{{bucket}}/';

		if ( $force_download ) :

			//	If we're forcing the download we need to reference a slightly different file
			//	On upload two instances were created, the "normal" streaming type one and another
			//	with the appropriate content-types set so that the browser downloads as oppossed
			//	to renders it

			$_out .= '{{filename}}-download{{extension}}';

		else :

			//	If we're not forcing the download we can serve straight out of S3
			$_out .= '{{filename}}{{extension}}';

		endif;

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates the correct URL for serving up a file
	 *
	 * @access	public
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$object	The filename of the object
	 * @return	string
	 **/
	public function url_serve_zipped( $object_ids, $hash, $filename )
	{
		$filename = $filename ? '/' . urlencode( $filename ) : '';
		return $this->_url_make_secure( DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/zip/' . $object_ids . '/' . $hash . $filename );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'serve' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 **/
	public function url_serve_zipped_scheme()
	{
		return $this->_url_make_secure( DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/zip/{{ids}}/{{hash}}/{{filename}}' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates the correct URL for using the thumb utility
	 *
	 * @access	public
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$object	The filename of the image we're 'thumbing'
	 * @param	string	$width	The width of the thumbnail
	 * @param	string	$height	The height of the thumbnail
	 * @return	string
	 **/
	public function url_thumb( $object, $bucket, $width, $height )
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/thumb/';
		$_out .= $width . '/' . $height . '/';
		$_out .= $bucket . '/';
		$_out .= $object;

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'thumb' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 **/
	public function url_thumb_scheme()
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/thumb/{{width}}/{{height}}/{{bucket}}/{{filename}}{{extension}}';

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates the correct URL for using the scale utility
	 *
	 * @access	public
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$object	The filename of the image we're 'scaling'
	 * @param	string	$width	The width of the scaled image
	 * @param	string	$height	The height of the scaled image
	 * @return	string
	 **/
	public function url_scale( $object, $bucket, $width, $height )
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/scale/';
		$_out .= $width . '/' . $height . '/';
		$_out .= $bucket . '/';
		$_out .= $object;

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'scale' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 **/
	public function url_scale_scheme()
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/scale/{{width}}/{{height}}/{{bucket}}/{{filename}}{{extension}}';

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates the correct URL for using the placeholder utility
	 *
	 * @access	public
	 * @param	int		$width	The width of the placeholder
	 * @param	int		$height	The height of the placeholder
	 * @param	int		border	The width of the border round the placeholder
	 * @return	string
	 **/
	public function url_placeholder( $width = 100, $height = 100, $border = 0 )
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/placeholder/';
		$_out .= $width . '/' . $height . '/' . $border;

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'placeholder' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 **/
	public function url_placeholder_scheme()
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/placeholder/{{width}}/{{height}}/{{border}}';

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates the correct URL for using the placeholder utility
	 *
	 * @access	public
	 * @param	int		$width	The width of the placeholder
	 * @param	int		$height	The height of the placeholder
	 * @param	int		border	The width of the border round the placeholder
	 * @return	string
	 **/
	public function url_blank_avatar( $width = 100, $height = 100, $sex = 'male' )
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/blank_avatar/';
		$_out .= $width . '/' . $height . '/' . $sex;

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'blank_avatar' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 **/
	public function url_blank_avatar_scheme()
	{
		$_out  = DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING . 'cdn/blank_avatar/{{width}}/{{height}}/{{sex}}';

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates a properly hashed expiring url
	 *
	 * @access	public
	 * @param	string	$bucket		The bucket which the image resides in
	 * @param	string	$object		The object to be served
	 * @param	string	$expires	The length of time the URL should be valid for, in seconds
	 * @return	string
	 **/
	public function url_expiring( $object, $bucket, $expires )
	{
		dumpanddie( 'TODO: If cloudfront is configured, then generate a secure url and pass pack, if not serve through the processing mechanism. Maybe.' );

		//	Hash the expirey time
		$_hash = get_instance()->encrypt->encode( $bucket . '|' . $object . '|' . $expires . '|' . time() . '|' . md5( time() . $bucket . $object . $expires . APP_PRIVATE_KEY ), APP_PRIVATE_KEY );
		$_hash = urlencode( $_hash );

		$_out  = 'cdn/serve?token=' . $_hash;
		$_out  = site_url( $_out );

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'expiring' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 **/
	public function url_expiring_scheme()
	{
		//	TODO: Generate expiring CloudFront URLS
		return FALSE;

		// --------------------------------------------------------------------------

		$_out = site_url( 'cdn/serve?token={{token}}' );

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	protected function _url_make_secure( $url, $is_processing = TRUE )
	{
		if ( page_is_secure() ) :

			//	Make the URL secure
			if ( $is_processing ) :

				$url = str_replace( DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING, DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_PROCESSING_SECURE, $url );

			else :

				$url = str_replace( DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_SERVING, DEPLOY_CDN_DRIVER_AWS_CLOUDFRONT_URL_SERVING_SECURE, $url );

			endif;

		endif;

		return $url;
	}


	// --------------------------------------------------------------------------


	public function fetch_from_s3( $bucket, $object, $save_as )
	{
		//	Now attempt to save the S3 Object
		try
		{
			$_result = $this->_s3->getObject(array(
				'Bucket'	=> $this->_bucket,
				'Key'		=> $bucket . '/' . $object,
				'SaveAs'	=> $save_as
			));

			return TRUE;
		}
		catch ( \Aws\S3\Exception\S3Exception $e )
		{
			//	Clean up
			@unlink( $save_as );

			//	Note the error
			$this->cdn->set_error( 'AWS-SDK EXCEPTION: ' . get_class( $e ) . ': ' . $e->getMessage() );

			return FALSE;
		}
	}
}

/* End of file local.php */
/* Location: ./libraries/_resources/cdn_drivers/local.php */