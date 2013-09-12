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
	private $_bucket;
	private $_s3;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct( $options = NULL )
	{
		//	Shortcut to CDN Library (mainly for setting errors)
		$this->cdn		=& get_instance()->cdn;

		//	Define error array
		$this->errors	= array();

		// --------------------------------------------------------------------------

		//	Load langfile
		get_instance()->lang->load( 'cdn/cdn_driver_aws_local', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------

		//	Check all the constants are defined properly
		//	CDN_DRIVER_AWS_IAM_ACCESS_ID
		//	CDN_DRIVER_AWS_IAM_ACCESS_SECRET
		//	CDN_DRIVER_AWS_S3_BUCKET

		if ( ! defined( 'CDN_DRIVER_AWS_IAM_ACCESS_ID' ) ) :

			//	TODO: Specify correct lang
			show_error( lang( 'cdn_error_not_configured' ) );

		endif;

		if ( ! defined( 'CDN_DRIVER_AWS_IAM_ACCESS_SECRET' ) ) :

			//	TODO: Specify correct lang
			show_error( lang( 'cdn_error_not_configured' ) );

		endif;

		if ( ! defined( 'CDN_DRIVER_AWS_S3_BUCKET' ) ) :

			//	TODO: Specify correct lang
			show_error( lang( 'cdn_error_not_configured' ) );

		endif;

		// --------------------------------------------------------------------------

		//	Instanciate the AWS PHP SDK
		$this->_s3 = S3Client::factory(array(
			'key'		=> CDN_DRIVER_AWS_IAM_ACCESS_ID,
			'secret'	=> CDN_DRIVER_AWS_IAM_ACCESS_SECRET,
		));

		// --------------------------------------------------------------------------

		//	Set the bucket we're using
		$this->_bucket = CDN_DRIVER_AWS_S3_BUCKET;
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
	 * @author	Pablo
	 **/
	public function object_create( $bucket, $file, $filename )
	{
		try
		{
			$_result = $this->_s3->putObject(array(
				'Bucket'		=> $this->_bucket,
				'Key'			=> $bucket . '/' . $filename,
				'SourceFile'	=> $file
			));

			return TRUE;
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
	 * @author	Pablo
	 **/
	public function object_delete( $object, $bucket )
	{
		try
		{
			$_result = $this->_s3->deleteObject(array(
				'Bucket'	=> $this->_bucket,
				'Key'		=> $bucket . '/' . $object
			));

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
	 * @author	Pablo
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


	public function bucket_delete( $bucket )
	{
		//	TODO
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
	 * @author	Pablo
	 **/
	public function url_serve( $object, $bucket )
	{
		$_out  = 'cdn/serve/';
		$_out .= $bucket . '/';
		$_out .= $object;
		$_out  = site_url( $_out );

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'serve' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_serve_scheme()
	{
		$_out = site_url( 'cdn/serve/{{bucket}}/{{file}}' );

		return $this->_url_make_secure( $_out );
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
	 * @author	Pablo
	 **/
	public function url_thumb( $object, $bucket, $width, $height )
	{
		$_out  = 'cdn/thumb/';
		$_out .= $width . '/' . $height . '/';
		$_out .= $bucket . '/';
		$_out .= $object;
		$_out  = site_url( $_out );

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'thumb' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_thumb_scheme()
	{
		$_out = site_url( 'cdn/thumb/{{width}}/{{height}}/{{bucket}}/{{file}}' );

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
	 * @author	Pablo
	 **/
	public function url_scale( $object, $bucket, $width, $height )
	{
		$_out  = 'cdn/scale/';
		$_out .= $width . '/' . $height . '/';
		$_out .= $bucket . '/';
		$_out .= $object;
		$_out  = site_url( $_out );

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'scale' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_scale_scheme()
	{
		$_out = site_url( 'cdn/scale/{{width}}/{{height}}/{{bucket}}/{{file}}' );

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
	 * @author	Pablo
	 **/
	public function url_placeholder( $width = 100, $height = 100, $border = 0 )
	{
		$_out  = 'cdn/placeholder/';
		$_out .= $width . '/' . $height . '/' . $border;
		$_out  = site_url( $_out );

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'placeholder' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_placeholder_scheme()
	{
		$_out = site_url( 'cdn/placeholder/{{width}}/{{height}}/{{border}}' );

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
	 * @author	Pablo
	 **/
	public function url_blank_avatar( $width = 100, $height = 100, $sex = 'male' )
	{
		$_out  = 'cdn/blank_avatar/';
		$_out .= $width . '/' . $height . '/' . $sex;
		$_out  = site_url( $_out );

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the scheme of 'blank_avatar' urls
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_blank_avatar_scheme()
	{
		$_out = site_url( 'cdn/blank_avatar/{{width}}/{{height}}/{{sex}}' );

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
	 * @author	Pablo
	 **/
	public function url_expiring( $object, $bucket, $expires )
	{
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
	 * @author	Pablo
	 **/
	public function url_expiring_scheme()
	{
		$_out = site_url( 'cdn/serve?token={{token}}' );

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	protected function _url_make_secure( $url )
	{
		if ( is_https() ) :

			//	Make the URL secure
			$url = preg_replace( '/^http:\/\//', 'https://', $url );

		endif;

		return $url;
	}


	// --------------------------------------------------------------------------


	public function testybobber()
	{
		dumpanddie( 'boobs' );
	}
}

/* End of file local.php */
/* Location: ./libraries/_resources/cdn_drivers/local.php */