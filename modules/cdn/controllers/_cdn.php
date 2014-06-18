<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CDN_Controller extends NAILS_Controller
{
	protected $_cdn_root;
	protected $_cachedir;
	protected $_cache_headers_set;
	protected $_cache_headers_max_age;
	protected $_cache_headers_last_modified;
	protected $_cache_headers_expires;
	protected $_cache_headers_file;
	protected $_cache_headers_hit;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Sanity checks; driver specific
		switch ( APP_CDN_DRIVER ) :

			case 'AWS_LOCAL' :

				//	TODO: Sanity checks, if any.

			break;

			case 'LOCAL' :

				//	TODO: Sanity checks, if any.

			break;

		endswitch;

		//	TODO: Sanity checks: common

		// --------------------------------------------------------------------------

		//	Define variables
		$this->_cdn_root	= NAILS_PATH . 'modules/cdn/';
		$this->_cachedir	= DEPLOY_CACHE_DIR;

		$this->_cache_headers_set			= FALSE;
		$this->_cache_headers_max_age		= APP_CDN_CACHE_MAX_AGE;
		$this->_cache_headers_last_modified	= '';
		$this->_cache_headers_expires		= '';
		$this->_cache_headers_file			= '';
		$this->_cache_headers_hit			= 'MISS';

		// --------------------------------------------------------------------------

		//	Load language file
		$this->lang->load( 'cdn' );

		// --------------------------------------------------------------------------

		//	Load CDN library
		$this->load->library( 'cdn' );
	}


	// --------------------------------------------------------------------------


	protected function _serve_from_cache( $file, $hit = TRUE )
	{
		//	Cache object exists, set the appropriate headers and return the
		//	contents of the file.

		$_stats = stat( $this->_cachedir . $file );

		//	Set cache headers
		$this->_set_cache_headers( $_stats[9], $file, $hit );

		//	Work out content type
		$_mime = $this->cdn->get_mime_type_from_file( $this->_cachedir . $file );

		header( 'Content-Type: ' . $_mime, TRUE );

		// --------------------------------------------------------------------------

		//	Send the contents of the file to the browser
		echo file_get_contents( $this->_cachedir . $file );

		//	Kill script, th, th, that's all folks.
		//	Stop the output class from hijacking our headers and
		//	setting an incorrect Content-Type

		exit(0);
	}


	// --------------------------------------------------------------------------


	protected function _set_cache_headers( $last_modified, $file, $hit )
	{
		//	Set some flags
		$this->_cache_headers_set			= TRUE;
		$this->_cache_headers_max_age		= APP_CDN_CACHE_MAX_AGE;
		$this->_cache_headers_last_modified	= $last_modified;
		$this->_cache_headers_expires		= time() + APP_CDN_CACHE_MAX_AGE;
		$this->_cache_headers_file			= $file;
		$this->_cache_headers_hit			= $hit ? 'HIT' : 'MISS';

		// --------------------------------------------------------------------------

		header( 'Cache-Control: max-age=' . $this->_cache_headers_max_age . ', must-revalidate', TRUE );
		header( 'Last-Modified: ' . date( 'r', $this->_cache_headers_last_modified ), TRUE );
		header( 'Expires: ' . date( 'r', $this->_cache_headers_expires ), TRUE );
		header( 'ETag: "' . md5( $this->_cache_headers_file ) . '"', TRUE );
		header( 'X-CDN-CACHE: ' . $this->_cache_headers_hit, TRUE );
	}


	// --------------------------------------------------------------------------


	protected function _unset_cache_headers()
	{
		if ( empty( $this->_cache_headers_set ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Remove previously set headers
		header_remove( 'Cache-Control' );
		header_remove( 'Last-Modified' );
		header_remove( 'Expires' );
		header_remove( 'ETag' );
		header_remove( 'X-CDN-CACHE' );

		// --------------------------------------------------------------------------

		//	Set new "do not cache" headers
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT', TRUE );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT', TRUE );
		header( 'Cache-Control: no-store, no-cache, must-revalidate', TRUE );
		header( 'Cache-Control: post-check=0, pre-check=0', FALSE );
		header( 'Pragma: no-cache', TRUE );
		header( 'X-CDN-CACHE: MISS', TRUE );
	}


	// --------------------------------------------------------------------------


	protected function _serve_not_modified( $file )
	{
		if ( function_exists( 'apache_request_headers' ) ) :

			$_headers = apache_request_headers();

		elseif ( $this->input->server( 'HTTP_IF_NONE_MATCH' ) ) :

			$_headers					= array();
			$_headers['If-None-Match']	= $this->input->server( 'HTTP_IF_NONE_MATCH' );

		elseif( isset( $_SERVER ) ) :

			//	Can we work the headers out for ourself?
			//	Credit: http://www.php.net/manual/en/function.apache-request-headers.php#70810

			$_headers	= array();
			$rx_http	= '/\AHTTP_/';
			foreach ( $_SERVER as $key => $val ) :

				if ( preg_match( $rx_http, $key ) ) :

					$arh_key	= preg_replace($rx_http, '', $key);
					$rx_matches	= array();

					// do some nasty string manipulations to restore the original letter case
					// this should work in most cases
					$rx_matches = explode('_', $arh_key);

					if ( count( $rx_matches ) > 0 && strlen( $arh_key ) > 2 ) :

						foreach ( $rx_matches as $ak_key => $ak_val ) :

							$rx_matches[$ak_key] = ucfirst( $ak_val );

						endforeach;

						$arh_key = implode( '-', $rx_matches );

					endif;

					$_headers[$arh_key] = $val;

				endif;

			endforeach;

		else :

			//	Give up.
			return FALSE;

		endif;

		if ( isset( $_headers['If-None-Match'] ) && $_headers['If-None-Match'] == '"' . md5( $file ) . '"' ) :

			header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 304 Not Modified', TRUE, 304 );
			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		return FALSE;
	}


	// --------------------------------------------------------------------------


	protected function _fetch_sourcefile( $bucket, $object )
	{
		//	If we're using the AWS Driver then we must check that a source file exists;
		//	if it doesn't then pull it down and save it. Cache maintenance scripts will
		//	clear this out every now and then.

		if ( APP_CDN_DRIVER == 'AWS_LOCAL' ) :

			//	Do we have the original sourcefile?
			$_filename	= strtolower( substr( $object, 0, strrpos( $object, '.' ) ) );
			$_extension	= strtolower( substr( $object, strrpos( $object, '.' ) ) );
			$_srcfile	= $this->_cachedir . $bucket . '-' . $_filename . '-SRC' . $_extension;

			//	Check filesystem for sourcefile
			if ( file_exists( $_srcfile ) ) :

				//	Yup, it's there, so use it
				return $_srcfile;

			else :

				//	Not in the cache, will need to pull it down from S3
				if ( $this->cdn->fetch_from_s3( $bucket, $object, $_srcfile ) ) :

					//	Got it!
					return $_srcfile;

				else :

					//	Failed to fetch, maybe it doesnt exist on S3?
					log_message( 'error', 'CDN: Fetch Source File: Error fetching from S3; S3 said: ' . $this->cdn->last_error() );
					return FALSE;

				endif;

			endif;


		// --------------------------------------------------------------------------

		//	If we're using the local driver then we need to check the source file exists
		//	in the DEPLOY_CDN_PATH.

		elseif ( APP_CDN_DRIVER == 'LOCAL' && file_exists( DEPLOY_CDN_PATH . $bucket . '/' . $object ) ) :

			//	Object exists, time for manipulation fun times :>
			return DEPLOY_CDN_PATH . $bucket . '/' . $object;

		else :

			//	This object does not exist / something went wrong
			log_message( 'error', 'CDN: Fetch Source File: File not found; ' . DEPLOY_CDN_PATH . $bucket . '/' . $object );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Generate a fail image
	 *
	 * @access	public
	 * @return	void
	 **/
	protected function _bad_src( $width = 100, $height = 100 )
	{
		//	Make sure this doesn't get cached
		$this->_unset_cache_headers();

		// --------------------------------------------------------------------------

		//	Create the icon
		$_icon = @imagecreatefrompng( $this->_cdn_root . '_resources/img/fail.png' );
		$_icon_w = imagesx( $_icon );
		$_icon_h = imagesy( $_icon );

		// --------------------------------------------------------------------------

		//	Create the background
		$_bg	= imagecreatetruecolor( $width, $height );
		$_white	= imagecolorallocate( $_bg, 255, 255, 255);
		imagefill( $_bg, 0, 0, $_white );

		// --------------------------------------------------------------------------

		//	Merge the two
		$_center_x = ( $width / 2 ) - ( $_icon_w / 2 );
		$_center_y = ( $height / 2 ) - ( $_icon_h / 2 );
		imagecopymerge( $_bg, $_icon, $_center_x, $_center_y, 0, 0, $_icon_w, $_icon_h, 100 );

		// --------------------------------------------------------------------------

		//	Output to browser
		header( 'Content-Type: image/png', TRUE );
		header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 400 Bad Request', TRUE, 400 );
		imagepng( $_bg );

		// --------------------------------------------------------------------------

		//	Destroy the images
		imagedestroy( $_icon );
		imagedestroy( $_bg );

		// --------------------------------------------------------------------------

		//	Kill script, th, th, that's all folks.
		//	Stop the output class from hijacking our headers and
		//	setting an incorrect Content-Type

		exit(0);
	}
}