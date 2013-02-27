<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Track email opens
 *
 * Description:	Allows users to view an email sent to them in their browser
 * 
 **/

class Tracker extends NAILS_Controller
{
	
	/**
	 * Track an email open.
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function track_open()
	{
		//	Fetch data; return a string if not set so as not to accidentally skip the 
		//	hash check in get_by_ref();
		
		$_ref	= $this->uri->segment( 3, 'NULL' );
		$_guid	= $this->uri->segment( 4, 'NULL' );
		$_hash	= $this->uri->segment( 5, 'NULL' );
		
		// --------------------------------------------------------------------------
		
		//	Check the reference is present
		if ( ! $_ref ) :
		
			show_error( 'MISSING ARGUMENT: EMAIL_REF' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Fetch the email
		$this->load->library( 'emailer' );
		
		$this->emailer->track_open( $_ref, $_guid, $_hash );
		
		// --------------------------------------------------------------------------
		
		//	Render out a tiny, tiny image
		//	Thanks http://probablyprogramming.com/2009/03/15/the-tiniest-gif-ever
		
		header( 'Content-Type: image/gif' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', FALSE );
		header( 'Pragma: no-cache' );
		
		echo base64_decode('R0lGODlhAQABAIABAP///wAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Track a link click and forward through
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function track_link()
	{
		//	Fetch data; return a string if not set so as not to accidentally skip the 
		//	hash check in get_by_ref();
		
		$_ref		= $this->uri->segment( 4, 'NULL' );
		$_guid		= $this->uri->segment( 5, 'NULL' );
		$_hash		= $this->uri->segment( 6, 'NULL' );
		$_link_id	= $this->uri->segment( 7, 'NULL' );
		
		// --------------------------------------------------------------------------
		
		//	Check the reference is present
		if ( ! $_ref ) :
		
			show_error( 'MISSING ARGUMENT: EMAIL_REF' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Fetch the email
		$this->load->library( 'emailer' );
		
		$_url = $this->emailer->track_link( $_ref, $_guid, $_hash, $_link_id );
		
		switch ( $_url ) :
		
			case 'BAD_HASH' :
			
				$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
				$this->output->set_header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
				$this->output->set_header( 'Content-type: application/json' ); 
				$this->output->set_header( 'Pragma: no-cache' );
				$this->output->set_header( 'HTTP/1.0 400 Bad Request' );
				$this->output->set_output( json_encode( array( 'status' => 400, 'error' => 'Could not validate email' ) ) );
			
			break;
			
			case 'BAD_LINK' :
			
				$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
				$this->output->set_header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
				$this->output->set_header( 'Content-type: application/json' ); 
				$this->output->set_header( 'Pragma: no-cache' );
				$this->output->set_header( 'HTTP/1.0 400 Bad Request' );
				$this->output->set_output( json_encode( array( 'status' => 400, 'error' => 'Could not validate link' ) ) );
			
			break;
			
			default :
			
				redirect( $_url );
			
			break;
		
		endswitch;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Map all requests to index
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function _remap( $method )
	{
		if ( $method == 'link' ) :
		
			$this->track_link();
		
		else :
		
			$this->track_open();
			
		endif;
	}
}

/* End of file view_online.php */
/* Location: ./application/modules/email/controllers/view_online.php */