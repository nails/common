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

class Emails extends Admin_Controller {
	
	
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
		//	Configurations
		$d->priority								= 14;						//	Module's order in nav (unique).
		$d->name									= 'E-mail Management';		//	Display name.
		$d->funcs['index']							= 'View Queued Messages';	//	Sub-nav function.
		$d->funcs['sent']							= 'View Sent Messages';		//	Sub-nav function.
		$d->funcs['log']							= 'View Message Log';		//	Sub-nav function.
		$d->funcs['templates']						= 'Manage Templates';		//	Sub-nav function.
		$d->funcs['campaigns']						= 'Manage Campaigns';		//	Sub-nav function.
		
		$d->announce_to					= array();								//	Which groups can access this module.
		$d->searchable					= FALSE;								//	Is module searchable?
		
		//	Dynamic
		$d->base_url		= basename( __FILE__, '.php' );	//	For link generation.
		
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

/* End of file admin.php */
/* Location: ./application/modules/admin/controllers/admin.php */