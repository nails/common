<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin - Emails
*
* Docs:			-
*
* Created:		27/06/2011
* Modified:		10/01/2012
*
* Description:	-
* 
*/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

/**
 * OVERLOADING NAILS'S ADMIN MODULES
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/
 
class NAILS_Emails extends Admin_Controller {
	
	
	/**
	 * Announces this module's details to anyone who asks.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function announce()
	{
		$d = new stdClass();
		
		// --------------------------------------------------------------------------
		
		//	Configurations
		$d->name				= 'Email Management';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['index']		= 'View Queued Messages';	//	Sub-nav function.
		$d->funcs['sent']		= 'View Sent Messages';		//	Sub-nav function.
		$d->funcs['log']		= 'View Message Log';		//	Sub-nav function.
		$d->funcs['templates']	= 'Manage Templates';		//	Sub-nav function.
		$d->funcs['campaigns']	= 'Manage Campaigns';		//	Sub-nav function.

		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		$_acl = active_user( 'acl' );
		if ( active_user( 'group_id' ) != 1 && ( ! isset( $_acl['admin'] ) || array_search( basename( __FILE__, '.php' ), $_acl['admin'] ) === FALSE ) )
			return NULL;
		
		// --------------------------------------------------------------------------
		
		//	Hey user! Pick me! Pick me!
		return $d;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		parent::__construct();
		
		//	Load models
		$this->load->model( 'admin_emails_model' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * View Queued messages
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function index()
	{
		//	Check if we're previewing
		if ( $this->uri->segment( 4 ) == 'preview' )
			return $this->_preview( 'queued' );
		
		// --------------------------------------------------------------------------
		
		$this->data['page']->admin_m = 'index';
		
		// --------------------------------------------------------------------------
		
		$_func  = $this->uri->segment( 4 );
		$_eid	= $this->uri->segment( 5 );
		
		// --------------------------------------------------------------------------
		
		switch ( $_func ) :
		
			case 'delete' :
				
				if ( $this->admin_emails_model->delete_queued_item( $_eid ) ) :
				
					$this->session->set_flashdata( 'success', 'Queue item deleted.' );
					redirect( 'admin/emails' );
				
				else:
				
					$this->session->set_flashdata( 'error', 'Queue item failed to delete.' );
					redirect( 'admin/emails' );
				
				endif;
			
			break;
			
			default :
		
				//	Get data
				$_search = $this->input->get( 'search' );
				$this->data['queued_mail'] = $this->admin_emails_model->get_queued( $_search );
				
				// --------------------------------------------------------------------------
				
				//	Load views
				$this->load->view( 'structure/header',				$this->data );
				$this->load->view( 'emails/index',					$this->data );
				$this->load->view( 'structure/footer',				$this->data );
				
			break;
		
		endswitch;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * View sent messages
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function sent()
	{
		//	Check if we're previewing
		if ( $this->uri->segment( 4 ) == 'preview' )
			return $this->_preview( 'sent' );
		
		// --------------------------------------------------------------------------
		
		$this->data['page']->admin_m = 'index';
		
		// --------------------------------------------------------------------------
			
		//	Get data
		$_search = $this->input->get( 'search' );
		$this->data['sent_mail'] = $this->admin_emails_model->get_sent( $_search );
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'emails/sent',					$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Preview a message in it's template
	 *
	 * @access	private
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	private function _preview( $type )
	{
		$_id = $this->uri->segment( 5 );
		
		//	Get the message
		if ( $type = 'queued' ) :
		
			$_email = $this->admin_emails_model->get_queued_item( $_id );
			
		else :
		
			$_email = $this->admin_emails_model->get_sent_item( $_id );
		
		endif;
		
		//	Prep data
		$_data					= unserialize( $_email->email_vars );
		$_data['email_subject']	= $_email->subject;
		
		//	Load template
		$this->load->view( 'email/structure/header',				$_data );
		$this->load->view( 'email/cron/' . $_email->template_file,	$_data );
		$this->load->view( 'email/structure/footer',				$_data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * View Email logs
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function log()
	{
		$this->data['page']->admin_m = 'log';
		
		$_func	= $this->uri->segment( 4 );
		
		// --------------------------------------------------------------------------
		
		switch ( $_func ) :
		
			case 'view' :
			
				// Get the log file to load and include it
				$_logfile	= $this->input->get( 'logfile' );
				$_log		= $this->admin_emails_model->read_log( $_logfile );
				
				$this->output->set_output( $_log );
			
			break;
			
			// --------------------------------------------------------------------------
			
			case 'delete' :
			
				$_logfile	= $this->input->get( 'logfile' );
				
				if ( ! $this->admin_emails_model->delete_log( $_logfile ) ) :
				
					$this->session->set_flashdata( 'error', 'Log "' . $_logfile . '" does not exist' );
					redirect( 'admin/emails/log' );
					
				else :
				
					$this->session->set_flashdata( 'success', 'Log "' . $_logfile . '" deleted' );
					redirect( 'admin/emails/log' );
				
				endif;
			
			break;
			
			// --------------------------------------------------------------------------
			
			case 'delete_all' :
			
				$this->admin_emails_model->flush_logs();
				
				$this->session->set_flashdata( 'success', 'All email log files deleted.' );
				redirect( 'admin/emails/log' );
			
			break;
			
			// --------------------------------------------------------------------------
			
			default:
		
				//	Get data
				$this->data['logs'] = $this->admin_emails_model->get_logs();
				
				//	Load views
				$this->load->view( 'structure/header',				$this->data );
				$this->load->view( 'emails/log',					$this->data );
				$this->load->view( 'structure/footer',				$this->data );
			
			break;
			
		endswitch;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Manage email templates
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function templates()
	{
		$this->data['page']->admin_m = 'templates';
		
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'emails/templates',				$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Manage Campaigns
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function campaigns()
	{
		$this->data['page']->admin_m = 'campaigns';
		
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'emails/campaigns',				$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S ADMIN MODULES
 * 
 * The following block of code makes it simple to extend one of the core admin
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 * 
 * Here's how it works:
 * 
 * CodeIgniter  instanciate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 * 
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
 * 
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 * 
 **/
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION' ) ) :

	class Emails extends NAILS_Emails
	{
	}

endif;


/* End of file admin.php */
/* Location: ./application/modules/admin/controllers/admin.php */