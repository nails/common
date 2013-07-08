<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CDN_Controller extends NAILS_Controller
{
	protected $_cdn_root;
	
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
		
		$this->_cdn_root = NAILS_PATH . 'modules/cdn/';
		
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
	
		if ( ! defined( 'CACHE_DIR' ) )
			return FALSE;

		$_stats = stat( CACHE_DIR . $file );
		
		header( 'content-type: image/png' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $_stats[9] ) . 'GMT' );
		header( 'ETag: "' . md5( $file ) . '"' );
		header( 'X-CDN-CACHE: HIT' );
		
		// --------------------------------------------------------------------------
		
		//	Send the contents of the file to the browser
		echo file_get_contents( CACHE_DIR . $file );
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


	/**
	 * Generate a fail image
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	protected function _bad_src( $width = 100, $height = 100 )
	{
		if ( ! defined( 'CDN_PATH' ) ) :

			header( 'Cache-Control: no-cache, must-revalidate' );
			header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
			header( 'Content-type: application/json' );
			header( 'HTTP/1.0 400 Bad Request' );
			
			// --------------------------------------------------------------------------
			
			$_out = array(
			
				'status'	=> 400,
				'message'	=> lang( 'cdn_not_configured' )
			
			);
			
			echo json_encode( $_out );

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
			imagepng( $_bg );
			
			// --------------------------------------------------------------------------
			
			//	Destroy the images
			imagedestroy( $_icon );
			imagedestroy( $_bg );

		endif;
	}
}