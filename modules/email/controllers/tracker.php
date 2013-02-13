<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Track email opens
 *
 * Description:	Allows users to view an email sent to them in their browser
 * 
 **/

class Tracker extends IA_Controller
{
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
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
	 * Map all requests to index
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function _remap()
	{
		$this->index();
	}
}

/* End of file view_online.php */
/* Location: ./application/modules/email/controllers/view_online.php */