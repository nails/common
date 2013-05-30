<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			View Online
*
* Description:	Allows users to view an email sent to them in their browser
* 
*/

//	Include _email.php; executes common functionality
require_once '_email.php';

class View_Online extends NAILS_Email_Controller
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
		
		if ( $this->user->is_admin() ) :
		
			$_guid	= FALSE;
			$_hash	= FALSE;
			
		else:
		
			$_guid	= $this->uri->segment( 4, 'NULL' );
			$_hash	= $this->uri->segment( 5, 'NULL' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Fetch the email
		$this->load->library( 'emailer' );
		
		$_email = $this->emailer->get_by_ref( $_ref, $_guid, $_hash );
		
		if ( ! $_email || $_email == 'BAD_HASH' )
			show_error( lang( 'invalid_email' ) );
		
		// --------------------------------------------------------------------------
		
		//	Prep data
		$_data					= $_email->email_vars;
		
		$_data['email_ref']		= $_email->ref;
		$_data['sent_from']		= $this->emailer->from;
		$_data['email_subject']	= $_email->subject;
		$_data['site_url']		= site_url();
		$_data['secret']		= APP_PRIVATE_KEY;

		
		$_data['sent_to']				= new stdClass();
		$_data['sent_to']->email		= $_email->user_email ? $_email->user_email : $_email->send_to;
		$_data['sent_to']->first		= $_email->first_name;
		$_data['sent_to']->last			= $_email->last_name;
		$_data['sent_to']->id			= (int) $_email->user_id;
		$_data['sent_to']->group_id		= $_email->user_group;
		$_data['sent_to']->login_url	= $_email->user_id ? site_url( 'auth/login/with_hashes/' . md5( $_email->user_id ) . '/' . md5( $_email->user_password ) ) : NULL;

		// --------------------------------------------------------------------------
		
		//	Load template
		$_out  = $this->load->view( 'email/structure/header',			$_data, TRUE );
		$_out .= $this->load->view( 'email/' . $_email->template_file,	$_data, TRUE );
		$_out .= $this->load->view( 'email/structure/footer',			$_data, TRUE );

		// --------------------------------------------------------------------------
		
		//	Sanitise a little
		$_out = preg_replace( '/{unwrap}(.*?){\/unwrap}/', '$1', $_out );
		
		// --------------------------------------------------------------------------
		
		//	Output
		$this->output->set_output( $_out );
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