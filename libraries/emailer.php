<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Emailer
**
*
* Docs:			http://nails.shedcollective.org/docs/libraries/emailer
*
* Created:		29/11/2010
* Modified:		27/02/2012
*
* Description:	Easily manage the email queue
* 
*/

class Emailer {
	
	private $ci;
	private $data;
	private $settings;
	private $email_config;
	public	$error = NULL;
	public 	$secret;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		$this->ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Set email related settings
		$this->secret = md5( DEPLOY_PRIVATE_KEY . APP_PRIVATE_KEY );
		
		// --------------------------------------------------------------------------
		
		//	Load the Email library
		$this->ci->load->library( 'email' );
		
		// --------------------------------------------------------------------------
		
		//	Load helpers
		$this->ci->load->helper( 'email' );
		$this->ci->load->helper( 'typography' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Send a templated email immediately
	 *
	 * @access	public
	 * @param	object
	 * @return	boolean
	 * @author	Pablo
	 **/
	private function _send( $input = FALSE, $debug = FALSE )
	{
		//	Start prepping the email
		$this->ci->email->from( APP_EMAIL_FROM_EMAIL, APP_EMAIL_FROM_NAME );
		$this->ci->email->to( $input->to );
		$this->ci->email->subject( $input->subject );
		
		// --------------------------------------------------------------------------
		
		//	Load the template		
		$body  = $this->ci->load->view( 'email/structure/header',		$input->data, TRUE );
		$body .= $this->ci->load->view( 'email/' . $input->template,	$input->data, TRUE );
		$body .= $this->ci->load->view( 'email/structure/footer',		$input->data, TRUE );
		
		// --------------------------------------------------------------------------
		
		//	Set the email body
		$this->ci->email->message( $body );
		
		// --------------------------------------------------------------------------
		
		//	Set the plain text version
		$plaintext  = $this->ci->load->view( 'email/structure/header_plaintext',	$input->data, TRUE );
		$plaintext .= $this->ci->load->view( 'email/'.$input->template_pt,			$input->data, TRUE );
		$plaintext .= $this->ci->load->view( 'email/structure/footer_plaintext',	$input->data, TRUE );
		
		$this->ci->email->set_alt_message( $plaintext );
		
		// --------------------------------------------------------------------------
		
		//	Add any attachments
		if ( isset( $input->attachment ) ) :
		
			$input->attachment = (object) $input->attachment;
			
			foreach ( $input->attachment AS $file ) :
			
				if ( ! $this->_add_attachment( $file ) )
					return ( $debug ) ? show_error( 'Failed to add attachment: ' . $file ) : FALSE;
				
			endforeach;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Debugging? Print debugger and don't send
		if ( $debug ) :
		
			$this->_debugger( $input, $body, $plaintext );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Send!
		return ( $this->ci->email->send() ) ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Sends an item immediately
	 *
	 * @access	public
	 * @param	object	$input	Data input
	 * @return	array
	 * @author	Pablo
	 **/
	public function send_now( $input, $debug = FALSE )
	{
		//	If we're not on a production server, NEVER send out to any live addresses
		if( ENVIRONMENT != 'production' )
			$input->to = EMAIL_OVERRIDE;

		// --------------------------------------------------------------------------

		//	Fresh start please
		$this->ci->email->clear( TRUE );
		
		// --------------------------------------------------------------------------
		
		//	We got something to work with?
		if ( empty( $input ) )
			return ( $debug ) ? show_error( 'No input.' ) : FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Ensure $input is an object
		if ( ! is_object( $input ) )
			$input = (object) $input;
		
		// --------------------------------------------------------------------------
		
		//	Check we have at least a to address and an email type
		if ( empty( $input->to ) || empty( $input->type ) )
			return ( $debug ) ? show_error( 'To or type field is missing.' ) : FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Generate a unique reference - ref is sent in each email and can allows
		//	the system to auto generate 'view online' links
		do
		{
			$_ref = random_string( 'alnum', 10 );
			$this->ci->db->where( 'ref', $_ref );
			
		} while( $this->ci->db->get( 'emailer_sent' )->num_rows() );
		
		// --------------------------------------------------------------------------
		
		//	Prep the email vars, prepend some common vars and merge with the input data
		//	This is going to be the data available to the template.
		
		$_data							= array();
		$_data['app_name']				= APP_NAME;
		$_data['app_url']				= site_url();
		$_data['sent_to']['email']		= $input->to;
		$_data['sent_from']['email']	= APP_EMAIL_FROM_EMAIL;
		$_data['sent_from']['name']		= APP_EMAIL_FROM_NAME;
		$_data['secret']				= $this->secret;
		$_data['ref']					= $_ref;
		
		if ( isset( $input->data ) && is_array( $input->data ) ) :
		
			$_data = array_merge( $_data, $input->data );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Prepare the archive item (or sent item)
		$_email					= array();
		$_email['to']			= $input->to;
		$_email['time_sent']	= time();
		$_email['type_id']		= $input->type_id;
		$_email['email_vars']	= serialize( $_data );
		
		// --------------------------------------------------------------------------
		
		//	Add to the 'sent items' table
		$this->ci->db->set( 'date_archived', 'NOW()', FALSE );
		
		if ( $this->ci->db->insert( 'emailer_sent', $data ) ) :
		
			//	Fetch the newly added email and prepare to send
			$_email = $this->fetch_by_ref( $data['ref'] );
			
			// --------------------------------------------------------------------------
			
			//	Prepare the email object to send
			$_send->to			= $_email->to;
			$_send->template	= 'email/' . $_email->template_file;
			$_send->template_pt	= 'email/' . $_email->template_file_plaintext;
			$_send->data		= unserialize( $_email['email_vars'] );
			
			// --------------------------------------------------------------------------
			
			//	If an email subject has been set in the data, use that over the template one
			$_send->subject = ( isset( $input->data['email_subject'] ) && $input->data['email_subject'] ) ? $input->data['email_subject'] : $_email->subject;
			
			// --------------------------------------------------------------------------
			
			//	Send the email now.
			return ( $this->_send( $_send, $debug ) ) ? $_ref : FALSE;
		
		else :
		
			return ( $debug ) ? show_error( 'Insert Failed.' ) : FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches items from the sent items table by it's reference
	 *
	 * @access	public
	 * @param	string	$ref	The reference of the item to fetch
	 * @return	array
	 * @author	Pablo
	 **/
	public function fetch_by_ref( $ref, $guid = FALSE, $hash = FALSE )
	{
		//	If guid and hash === FALSE then by pass the check
		if ( $guid !== FALSE && $hash !== FALSE ) :
		
			//	Check hash
			$_check = md5( $guid . $this->secret . $ref );
			
			if ( $_check !== $hash )
				return 'BAD_HASH';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Fetch record
		$this->ci->db->select( 'eqa.id, eqa.ref, eqa.to, eqa.email_vars, eqt.template_file, eqa.from_name, eqa.from_email, eqt.template_file_plaintext, eqt.subject' );
		$this->ci->db->join( 'email_queue_type eqt', 'eqt.id_string = eqa.type' );
		$this->ci->db->where( 'eqa.ref', $ref );
		return $this->ci->db->get( 'email_queue_archive eqa' )->row();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Add an attachment to the email
	 *
	 * @access	private
	 * @param	string	file to add
	 * @return	boolean
	 * @author	Pablo
	 **/
	private function _add_attachment( $file )
	{
		if ( ! $this->ci->email->attach( $file ) ) :
		
			return FALSE;
			
		else :
		
			return TRUE;
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Renders the debugger
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	private function _debugger( $input, $body, $plaintext )
	{
		//	Debug mode, output data and don't actually send
		
		//	Input variables
		echo '<pre>';
		
		//	Who's the email going to?
		echo '<strong>Sending to:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo 'email: ' . $input->to . "\n";
		
		//	Who's the email being sent from?
		echo "\n\n" . '<strong>Sending from:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo 'name:	' . $input->from->name . "\n";
		echo 'email:	' . $input->from->email . "\n";
		
		//	Input data (system & supplied)
		echo "\n\n" . '<strong>Input variables (system + supplied):</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		print_r( $input->data );
		
		//	Template
		echo "\n\n" . '<strong>Email body:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo 'Subject:	' . $input->subject . "\n";
		echo 'template:	' . $input->template . "\n";
		
		echo "\n\n" . '<strong>HTML:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo htmlentities( $body ) ."\n";
		
		echo "\n\n" . '<strong>Plain Text:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo '</pre>' . nl2br( $plaintext ) . "\n";
		
		exit( 0 );
	}
}

/* End of file emailer.php */
/* Location: ./application/libraries/emailer.php */