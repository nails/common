<?php

/**
* Name:			Serve
*
* Created:		12/11/2012
* Modified:		12/11/2012
*
* Description:	Serves a raw object from the upload directory
* 
*/

//	Include _cdn_local.php; executes common functionality
require_once '_cdn_local.php';

class Serve extends CDN_Controller
{
	private $_bucket;
	private $_object;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Construct the class; set defaults
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function __construct()
	{	
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Work out some variables
		$this->_bucket	= $this->uri->segment( 3 );
		$this->_object	= $this->uri->segment( 4 );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function index()
	{
		//	Check the request headers; avoid hitting the disk at all if possible. If the Etag
		//	matches then send a Not-Modified header and terminate execution.
		
		$_headers = apache_request_headers();	
		
		if ( $this->_serve_not_modified( $this->_bucket . $this->_object ) )
			return;
		
		// --------------------------------------------------------------------------
		
		//	Check the object is set
		if ( ! $this->_object || ! file_exists( CDN_PATH . $this->_bucket . '/' . $this->_object ) )
			return $this->_bad_src();
		
		// --------------------------------------------------------------------------
		
		//	All good, output the file
		$_stats = stat( CDN_PATH . $this->_bucket . '/' . $this->_object );
		
		//	Determine headers to send
		$_finfo = new finfo( FILEINFO_MIME_TYPE ); // return mime type ala mimetype extension
		
		header( 'Content-type: ' . $_finfo->file( CDN_PATH . $this->_bucket . '/' . $this->_object ) );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $_stats[9] ) . 'GMT' );
		header( 'ETag: "' . md5( $this->_bucket . $this->_object ) . '"' );
		
		// --------------------------------------------------------------------------
		
		//	Send the contents of the file to the browser
		echo file_get_contents( CDN_PATH . $this->_bucket . '/' . $this->_object );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _bad_src()
	{
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Content-type: application/json' );
		header( 'HTTP/1.0 400 Bad Request' );
		
		// --------------------------------------------------------------------------
		
		$_out = array(
		
			'status'	=> 400,
			'message'	=> 'Invalid request'
		
		);
		
		echo json_encode( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function _remap()
	{
		$this->index();
	}
}


/* End of file serve.php */
/* Location: ./application/modules/cdn_local/controllers/server.php */