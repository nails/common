<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CDN_Controller extends NAILS_Controller
{
	protected $_driver;
	protected $_cdn_root;
	protected $_cachedir;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Check this module is enabled in settings
		if ( ! module_is_enabled( 'cdn' ) ) :

			//	Cancel execution, module isn't enabled
			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Which driver is being used?
		$this->_driver = strtolower( APP_CDN_DRIVER );

		//	Sanity checks; driver specific
		switch ( $this->_driver ) :

			case 'aws_local' :

				//	TODO: Sanity checks, if any.

			break;

			// --------------------------------------------------------------------------

			case 'local' :

				//	DEPLOY_CDN_PATH must be defined, while it might not be used for all scenarios
				//	it's lack of presence is a configuration issue and so execution should
				//	be halted.

				if ( ! defined( 'DEPLOY_CDN_PATH' ) ) :

					log_message( 'error', 'CDN: DEPLOY_CDN_PATH not defined' );
					show_error( 'CDN: DEPLOY_CDN_PATH not defined' );

				endif;

			break;

		endswitch;

		//	Common

		//	CACHE_DIR must be defined; the CDN can be very process heavy so caching is a
		//	requirement and if not defined it is considered a configuration error and
		//	execution should be halted

		if ( ! defined( 'CACHE_DIR' ) ) :

			log_message( 'error', 'CDN: CACHE_DIR not defined' );
			show_error( 'CDN: CACHE_DIR not defined' );

		endif;

		//	Cache must be writeable
		if ( ! is_writable( CACHE_DIR ) ) :

			//	Inform developers
			$_subject	= 'Cache (CDN) dir not writeable';
			$_message	= 'The CDN cannot write to the cache directory.'."\n\n";
			$_message	.= 'Dir: ' . CACHE_DIR . "\n\n";
			$_message	.= 'URL: ' . $_SERVER['REQUEST_URI'];

			send_developer_mail( $_subject, $_message );

			show_error( 'CDN: CACHE_DIR not writeable' );

		endif;

		// --------------------------------------------------------------------------

		//	Define variables
		$this->_cdn_root	= NAILS_PATH . 'modules/cdn/';
		$this->_cachedir	= CACHE_DIR;

		// --------------------------------------------------------------------------

		//	Load language file
		$this->lang->load( 'cdn', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------

		//	Load CDN library
		$this->load->library( 'cdn' );
	}


	// --------------------------------------------------------------------------


	protected function _serve_from_cache( $file )
	{
		//	Cache object exists, set the appropriate headers and return the
		//	contents of the file.

		$_stats = stat( $this->_cachedir . $file );

		header( 'content-type: image/png' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $_stats[9] ) . 'GMT' );
		header( 'ETag: "' . md5( $file ) . '"' );
		header( 'X-CDN-CACHE: HIT' );

		// --------------------------------------------------------------------------

		//	Send the contents of the file to the browser
		echo file_get_contents( $this->_cachedir . $file );
	}


	// --------------------------------------------------------------------------


	protected function _serve_not_modified( $file )
	{
		$_headers = apache_request_headers();

		if ( isset( $_headers['If-None-Match'] ) && ( $_headers['If-None-Match'] == '"' . md5( $file ) . '"' ) ) :

			header( 'Not Modified', TRUE, 304 );
			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		return FALSE;
	}


	// --------------------------------------------------------------------------


	protected function _fetch_sourcefile( $bucket, $object )
	{
		//	If we're using the AWS Driver then we must check that a source file eists;
		//	if it doesn't then pull it down and save it. Cache maintenance scripts will
		//	clear this out every now and then.

		if ( $this->_driver == 'aws_local' ) :

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
					log_message( 'error', 'CDN: Thumb: Error fetching from S3; S3 said: ' . $this->cdn->last_error() );
					return FALSE;

				endif;

			endif;


		// --------------------------------------------------------------------------

		//	If we're using the local driver then we need to check the source file exists
		//	in the DEPLOY_CDN_PATH.

		elseif ( $this->_driver == 'local' && file_exists( DEPLOY_CDN_PATH . $this->_bucket . '/' . $this->_object ) ) :

			//	Object exists, time for manipulation fun times :>
			return DEPLOY_CDN_PATH . $this->_bucket . '/' . $this->_object;

		else :

			//	This object does not exist / something went wrong
			log_message( 'error', 'CDN: Thumb: File not found; ' . DEPLOY_CDN_PATH . $this->_bucket . '/' . $this->_object );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Generate a fail image
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	protected function _bad_src( $width = 100, $height = 100 )
	{
		if ( $this->_driver == 'local' && ! defined( 'DEPLOY_CDN_PATH' ) ) :

			$this->output->set_header( 'Cache-Control: no-cache, must-revalidate' );
			$this->output->set_header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
			$this->output->set_header( 'Content-type: application/json' );
			$this->output->set_header( 'HTTP/1.0 400 Bad Request' );

			// --------------------------------------------------------------------------

			$_out = array(

				'status'	=> 400,
				'message'	=> lang( 'cdn_not_configured' )

			);

			$this->output->set_output( json_encode( $_out ) );

		else :

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
			header( 'Content-type: image/png' );
			header( 'HTTP/1.0 400 Bad Request' );
			imagepng( $_bg );

			// --------------------------------------------------------------------------

			//	Destroy the images
			imagedestroy( $_icon );
			imagedestroy( $_bg );

		endif;
	}
}