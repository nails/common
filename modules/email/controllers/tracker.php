<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Track email opens
*
* Created:		26/01/2012
* Modified:		26/01/2012
*
* Description:	Allows users to view an email sent to them in their browser
* 
*/

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
		if ( ! $_ref )
			show_error( 'MISSING ARGUMENT: EMAIL_REF' );
		
		// --------------------------------------------------------------------------
		
		//	Fetch the email
		$this->load->library( 'emailer' );
		
		$this->emailer->track_open( $_ref, $_guid, $_hash );
		
		// --------------------------------------------------------------------------
		
		//	Render out a tiny, tiny image, thanks http://probablyprogramming.com/2009/03/15/the-tiniest-gif-ever
		header('Content-Type: image/gif');
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