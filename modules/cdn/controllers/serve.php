<?php

/**
 * Name:		Serve
 *
 * Description:	Serves a raw object from the upload directory
 * 
 **/

//	Include _cdn_local.php; executes common functionality
require_once '_cdn_local.php';

class Serve extends NAILS_CDN_Controller
{
	private $_bucket;
	private $_object;
	private $_bad_token;
	
	
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
		$_token = $this->input->get( 'token' );
		
		if ( $_token )  :
		
			//	Encrypted token/expiring URL
			$_token = $this->encrypt->decode( $_token, APP_PRIVATE_KEY );
			$_token = explode( '|', $_token );
			
			if ( count( $_token ) == 5 ) :
			
				$this->_bad_token	= FALSE;
				
				//	Seems to be ok, but verify the different parts
				list( $_bucket, $_object, $_expires, $_time, $_hash ) = $_token;
				
				if ( md5( $_time . $_bucket . $_object . $_expires . APP_PRIVATE_KEY ) == $_hash ) :
				
					//	Hash validates, URL expired?
					if ( time() <= ( $_time + $_expires ) ) :
					
						$this->_bucket		= $_bucket;
						$this->_object		= $_object;
						$this->_bad_token	= FALSE;
					
					else :
					
						$this->_bad_token	= TRUE;
					
					endif;
				
				else :
				
					$this->_bad_token	= TRUE;
				
				endif;
			
			else :
			
				$this->_bad_token	= TRUE;
			
			endif;
		
		else :
		
			$this->_bad_token	= FALSE;
			$this->_bucket		= $this->uri->segment( 3 );
			$this->_object		= urldecode( $this->uri->segment( 4 ) );
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function index()
	{
		//	Check if there was a bad token
		if ( $this->_bad_token ) :
		
			return $this->_bad_src();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Look up the object in the DB
		$_object = $this->cdn->get_object( $this->_object, $this->_bucket );

		// --------------------------------------------------------------------------

		//	Check the request headers; avoid hitting the disk at all if possible. If the Etag
		//	matches then send a Not-Modified header and terminate execution.
		
		if ( $this->_serve_not_modified( $this->_bucket . $this->_object ) ) :
		
			if ( $_object ) :

				if ( $this->input->get( 'dl' ) ) :

					$this->cdn->increment_count( 'DOWNLOAD', $_object->id );

				else :

					$this->cdn->increment_count( 'SERVE', $_object->id );

				endif;

			endif;
			return;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Check the object is set
		if ( $this->_bad_token || ! $this->_object || ! file_exists( CDN_PATH . $this->_bucket . '/' . $this->_object ) ) :
		
			return $this->_bad_src();
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Determine headers to send. Are we forcing the download?
		if ( $this->input->get( 'dl' ) ) :
		
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			
			//	If the object is known about, add some extra headers
			if ( $_object ) :
			
				header('Content-Disposition: attachment; filename="' . $_object->filename_display . '"');
				header( 'Content-Length: ' . $_object->filesize );
				
			else :
			
				header('Content-Disposition: attachment; filename="' . $this->_object . '"');
			
			endif;
		
		else :
		
			$_stats = stat( CDN_PATH . $this->_bucket . '/' . $this->_object );
			
			//	Determine headers to send
			$_finfo = new finfo( FILEINFO_MIME_TYPE ); // return mime type ala mimetype extension
			
			header( 'Content-type: ' . $_finfo->file( CDN_PATH . $this->_bucket . '/' . $this->_object ) );
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $_stats[9] ) . 'GMT' );
			header( 'ETag: "' . md5( $this->_bucket . $this->_object ) . '"' );
			
			// --------------------------------------------------------------------------
			
			//	If the object is known about, add some extra headers
			if ( $_object ) :
			
				header( 'Content-Length: ' . $_object->filesize );
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Send the contents of the file to the browser
		echo file_get_contents( CDN_PATH . $this->_bucket . '/' . $this->_object );

		// --------------------------------------------------------------------------

		//	Bump the counter
		if ( $_object ) :

			if ( $this->input->get( 'dl' ) ) :

				$this->cdn->increment_count( 'DOWNLOAD', $_object->id );

			else :

				$this->cdn->increment_count( 'SERVE', $_object->id );

			endif;

		endif;
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
			'message'	=> lang( 'invalid_request' )
		
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