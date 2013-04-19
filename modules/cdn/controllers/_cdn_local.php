<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
		$this->lang->load( 'cdn', RENDER_LANG );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _serve_from_cache( $file )
	{
		//	Cache object exists, set the appropriate headers and return the
		//	contents of the file.
		
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
}