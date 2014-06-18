<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			CDN
*
* Description:	A Library for dealing with content in the Local CDN
*
*/

class Local_CDN
{

	private $cdn;
	public $errors;


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
		//	Shortcut to CDN
		$this->cdn		=& get_instance()->cdn;
		$this->errors	= array();

		// --------------------------------------------------------------------------

		//	Load langfile
		get_instance()->lang->load( 'cdn/cdn_driver_local' );
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
		$_bucket	= ! empty( $data->bucket->slug )	? $data->bucket->slug	: '';
		$_filename	= ! empty( $data->filename )		? $data->filename		: '';
		$_source	= ! empty( $data->file )			? $data->file			: '';

		// --------------------------------------------------------------------------

		//	Check directory exists
		if ( ! is_dir( DEPLOY_CDN_PATH . $_bucket ) ) :

			//	Hmm, not writeable, can we create it?
			if ( ! @mkdir( DEPLOY_CDN_PATH . $_bucket ) ) :

				//	Nope, failed to create the directory - we iz gonna have problems if we continue, innit.
				$this->cdn->set_error( lang( 'cdn_error_target_write_fail_mkdir', DEPLOY_CDN_PATH . $_bucket ) );
				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Check bucket is writeable
		if ( ! is_really_writable( DEPLOY_CDN_PATH . $_bucket ) ) :

			$this->cdn->set_error( lang( 'cdn_error_target_write_fail', DEPLOY_CDN_PATH . $_bucket ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Move the file
		$_dest = DEPLOY_CDN_PATH . $_bucket . '/' . $_filename;

		if ( @move_uploaded_file( $_source, $_dest ) ) :

			return TRUE;

		//	Hmm, failed to move, try copying it.
		elseif( @copy( $_source, $_dest ) ) :

			return TRUE;

		else :

			$this->cdn->set_error( lang( 'cdn_error_couldnotmove' ) );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function object_exists( $filename, $bucket )
	{
		return is_file( DEPLOY_CDN_PATH . $bucket . '/' . $filename );
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
		$_file		= urldecode( $object );
		$_bucket	= urldecode( $bucket );

		if ( file_exists( DEPLOY_CDN_PATH . $bucket . '/' . $_file ) ) :

			if ( @unlink( DEPLOY_CDN_PATH . $bucket . '/' . $_file ) ) :

				//	TODO: Delete Cache items

				return TRUE;

			else :

				$this->cdn->set_error( lang( 'cdn_error_delete' ) );
				return FALSE;

			endif;

		else :

			$this->cdn->set_error( lang( 'cdn_error_delete_nofile' ) );
			return FALSE;

		endif;
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
		$_dir = DEPLOY_CDN_PATH . $bucket;
		if ( is_dir( $_dir ) && is_writeable( $_dir ) ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		if ( @mkdir( $_dir ) ) :

			return TRUE;

		else :

			if ( get_userobject()->is_superuser() ) :

				$this->cdn->set_error( lang( 'cdn_error_bucket_mkdir_su', $_dir ) );

			else :

				$this->cdn->set_error( lang( 'cdn_error_bucket_mkdir' ) );

			endif;
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function bucket_destroy( $bucket )
	{
		if ( rmdir( DEPLOY_CDN_PATH . $bucket ) ) :

			return TRUE;

		else :

			$this->cdn->set_error( lang( 'cdn_error_bucket_unlink' ) );
			return FALSE;

		endif;
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
		$_out  = 'cdn/serve/';
		$_out .= $bucket . '/';
		$_out .= $object;

		if ( $force_download ) :

			$_out .= '?dl=1';

		endif;

		return $this->_url_make_secure( $_out );
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
		$_out = 'cdn/serve/{{bucket}}/{{filename}}{{extension}}';

		if ( $force_download ) :

			$_out .= '?dl=1';

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
		return $this->_url_make_secure( 'cdn/zip/' . $object_ids . '/' . $hash . $filename );
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
		return $this->_url_make_secure( 'cdn/zip/{{ids}}/{{hash}}/{{filename}}' );
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
		$_out  = 'cdn/thumb/';
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
		$_out = 'cdn/thumb/{{width}}/{{height}}/{{bucket}}/{{filename}}{{extension}}';

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
		$_out  = 'cdn/scale/';
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
		$_out = 'cdn/scale/{{width}}/{{height}}/{{bucket}}/{{filename}}{{extension}}';

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
		$_out  = 'cdn/placeholder/';
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
		$_out = 'cdn/placeholder/{{width}}/{{height}}/{{border}}';

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
		$_out  = 'cdn/blank_avatar/';
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
		$_out = 'cdn/blank_avatar/{{width}}/{{height}}/{{sex}}';

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
		//	Hash the expirey time
		$_hash = get_instance()->encrypt->encode( $bucket . '|' . $object . '|' . $expires . '|' . time() . '|' . md5( time() . $bucket . $object . $expires . APP_PRIVATE_KEY ), APP_PRIVATE_KEY );
		$_hash = urlencode( $_hash );

		$_out  = 'cdn/serve?token=' . $_hash;

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
		$_out = 'cdn/serve?token={{token}}';

		return $this->_url_make_secure( $_out );
	}


	// --------------------------------------------------------------------------


	protected function _url_make_secure( $url )
	{
		return site_url( $url, page_is_secure() );
	}
}

/* End of file local.php */
/* Location: ./libraries/_resources/cdn_drivers/local.php */