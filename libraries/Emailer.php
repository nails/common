<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Emailer
*
* Description:	Easily send email within apps
*
*/

class Emailer
{
	public $from;
	private $ci;
	private $db;
	private $email_type;
	private $track_link_cache;
	private $_errors;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 **/
	public function __construct( $config = array() )
	{
		$this->ci	=& get_instance();
		$this->db	=& $this->ci->db;

		// --------------------------------------------------------------------------

		//	Set email related settings
		$this->from			= new stdClass();
		$this->from->name	= APP_EMAIL_FROM_NAME;

		if ( APP_EMAIL_FROM_EMAIL ) :

			$this->from->email = APP_EMAIL_FROM_EMAIL;

		else :

			$_url = parse_url( site_url() );
			$this->from->email = 'nobody@' . $_url['host'];

		endif;

		// --------------------------------------------------------------------------

		//	Load the Email library
		$this->ci->load->library( 'email' );

		// --------------------------------------------------------------------------

		//	Load helpers
		$this->ci->load->helper( 'email' );
		$this->ci->load->helper( 'typography' );
		$this->ci->load->helper( 'string' );

		// --------------------------------------------------------------------------

		//	Set defaults
		$this->email_type		= array();
		$this->track_link_cache	= array();
		$this->_errors			= array();

		// --------------------------------------------------------------------------

		//	Check SMTP is configured
		if ( ! DEPLOY_SMTP_HOST || ! DEPLOY_SMTP_PORT ) :

			$_error = 'EMAILER: SMTP not configured';

			if ( ! empty( $config['graceful_startup'] ) ) :

				$this->_set_error( $_error );

			else :

				show_error( $_error );

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Send an email
	 *
	 * @access	public
	 * @param	object	$input		The input object
	 * @param	bool	$graceful	Whether to gracefully fail or not
	 * @return	void
	 **/
	public function send( $input, $graceful = FALSE )
	{
		//	We got something to work with?
		if ( empty( $input ) ) :

			$this->_set_error( 'EMAILER: No input' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Ensure $input is an object
		if ( ! is_object( $input ) ) :

			$input = (object) $input;

		endif;

		// --------------------------------------------------------------------------

		//	Check we have at least a user_id/email and an email type
		if ( ( empty( $input->to_id ) && empty( $input->to_email ) ) || empty( $input->type ) ) :

			$this->_set_error( 'EMAILER: Missing user ID, user email or email type' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	If no email has been given make sure it's NULL
		if ( empty( $input->to_email ) ) :

			$input->to_email = NULL;

		endif;

		// --------------------------------------------------------------------------

		//	If no id has been given make sure it's NULL
		if ( empty( $input->to_id ) ) :

			$input->to_id = NULL;

		endif;

		// --------------------------------------------------------------------------

		//	If no internal_ref has been given make sure it's NULL
		if ( empty( $input->internal_ref ) ) :

			$input->internal_ref = NULL;

		endif;

		// --------------------------------------------------------------------------

		//	Make sure that at least empty data is available
		if ( empty( $input->data ) ) :

			$input->data = array();

		endif;

		// --------------------------------------------------------------------------

		//	Lookup the email type (caching it as we go)
		if ( empty( $this->email_type[ $input->type ] ) ) :

			$this->db->where( 'et.slug', $input->type );

			$this->email_type[ $input->type ] = $this->db->get( NAILS_DB_PREFIX . 'email_type et' )->row();

			if ( ! $this->email_type[ $input->type ] ) :

				if ( ! $graceful ) :

					show_error( 'EMAILER: Invalid Email Type "' . $input->type . '"' );

				else :

					$this->_set_error( 'EMAILER: Invalid Email Type "' . $input->type . '"' );

				endif;

				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	If we're sending to an email address, try and associate it to a registered user
		if ( $input->to_email ) :

			$_user = get_userobject()->get_by_email( $input->to_email );

			if ( $_user ) :

				$input->to_id		= $_user->id;

			endif;

		else :

			//	Sending to an ID, fetch the user's email
			$_user = get_userobject()->get_by_id( $input->to_id );

			if ( ! empty( $_user->email ) ) :

				$input->to_email = $_user->email;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Check to see if the user has opted out of receiving these emails
		if ( $input->to_id ) :

			if ( $this->user_has_unsubscribed( $input->to_id, $this->email_type[ $input->type ]->id ) ) :

				//	User doesn't want to receive these notifications; abort.
				return TRUE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Generate a unique reference - ref is sent in each email and can allow the
		//	system to generate 'view online' links

		$input->ref = $this->_generate_reference();

		// --------------------------------------------------------------------------

		//	Double check we have an email address (a user may exist but not have an
		//	email address set)

		if ( empty( $input->to_email ) ) :

			if ( ! $graceful ) :

				show_error( 'EMAILER: No email address to send to.' );

			else :

				$this->_set_error( 'EMAILER: No email address to send to.' );
				FALSE;

			endif;


		endif;

		// --------------------------------------------------------------------------

		//	Add to the archive table
		$this->db->set( 'ref',			$input->ref );
		$this->db->set( 'user_id',		$input->to_id );
		$this->db->set( 'user_email',	$input->to_email );
		$this->db->set( 'type_id',		$this->email_type[ $input->type ]->id );
		$this->db->set( 'email_vars',	serialize( $input->data ) );
		$this->db->set( 'internal_ref',	$input->internal_ref );

		$this->db->insert( NAILS_DB_PREFIX . 'email_archive' );

		if ( $this->db->affected_rows() ) :

			$input->id = $this->db->insert_id();

		else :

			if ( ! $graceful ) :

				show_error( 'EMAILER: Insert Failed.' );

			else :

				$this->_set_error( 'EMAILER: Insert Failed.' );
				FALSE;

			endif;

		endif;

		if ( $this->_send( $input->id, $graceful ) ) :

			return $input->ref;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function user_has_unsubscribed( $user_id, $type_id )
	{
		$this->db->where( 'user_id', $user_id );
		$this->db->where( 'type_id', $type_id );

		return (bool) $this->db->count_all_results( NAILS_DB_PREFIX . 'user_email_blocker' );
	}


	// --------------------------------------------------------------------------


	public function unsubscribe_user( $user_id, $type_id )
	{
		if ( $this->user_has_unsubscribed( $user_id, $type_id ) ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->set( 'user_id', $user_id );
		$this->db->set( 'type_id', $type_id );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->insert( NAILS_DB_PREFIX . 'user_email_blocker' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function subscribe_user( $user_id, $type_id )
	{
		if ( ! $this->user_has_unsubscribed( $user_id, $type_id ) ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( 'user_id', $user_id );
		$this->db->where( 'type_id', $type_id );
		$this->db->delete( NAILS_DB_PREFIX . 'user_email_blocker' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	/**
	 * Send a templated email immediately
	 *
	 * @access	private
	 * @param	object	$input			The input object
	 * @param	boolean	$graceful		Whether to fail gracefully or not
	 * @return	boolean
	 **/
	private function _send( $email_id = FALSE, $graceful = FALSE )
	{
		//	Get the email if $email_id is not an object
		if ( ! is_object( $email_id ) ) :

			$_email = $this->get_by_id( $email_id );

		else :

			$_email = $email_id;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $_email ) :

			$this->_set_error( 'EMAILER: Unable to fetch email object' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_send							= new stdClass();
		$_send->to						= new stdClass();
		$_send->to->email				= $_email->user->email;
		$_send->to->email_verified		= (bool) $_email->email_verified;
		$_send->to->email_verified_code	= $_email->email_verified_code;
		$_send->to->first				= $_email->user->first_name;
		$_send->to->last				= $_email->user->last_name;
		$_send->to->id					= (int) $_email->user->id;
		$_send->to->username			= $_email->user->username;
		$_send->to->group_id			= $_email->user->group_id;
		$_send->to->login_url			= $_email->user->id ? site_url( 'auth/login/with_hashes/' . md5( $_email->user->id ) . '/' . md5( $_email->user->password ) ) : NULL;
		$_send->email_type_id			= $_email->type_id;
		$_send->subject					= $_email->subject;
		$_send->template				= $_email->template_file;
		$_send->template_pt				= $_email->template_file . '_plaintext';
		$_send->data					= $_email->email_vars;
		$_send->data['ci']				=& get_instance();

		//	Check login URLs are allowed
		get_instance()->config->load( 'auth' );

		if ( ! get_instance()->config->item( 'auth_enable_hashed_login' ) ) :

			$_send->to->login_url = '';

		endif;

		if ( ! is_array( $_send->data ) ) :

			$_send->data = array();

		endif;

		// --------------------------------------------------------------------------

		//	From user
		$_send->from = new stdClass();

		if ( ! empty( $_send->data['email_from_email'] ) ) :

			$_send->from->email	= $_send->data['email_from_email'];
			$_send->from->name	= ! empty( $_send->data['email_from_name'] ) ? $_send->data['email_from_name'] : $_send->data['email_from_email'];

		else :

			$_send->from->email	= $this->from->email;
			$_send->from->name	= $this->from->name;

		endif;

		// --------------------------------------------------------------------------

		//	Fresh start please
		$this->ci->email->clear( TRUE );

		// --------------------------------------------------------------------------

		//	Add some extra, common variables for the template
		$_send->data['email_type_id']	= $_email->type_id;
		$_send->data['email_ref']		= $_email->ref;
		$_send->data['sent_from']		= $_send->from;
		$_send->data['sent_to']			= $_send->to;
		$_send->data['email_subject']	= $_send->subject;
		$_send->data['site_url']		= site_url();
		$_send->data['secret']			= APP_PRIVATE_KEY;

		// --------------------------------------------------------------------------

		//	If we're not on a production server, never send out to any live addresses
		$_send_to = $_send->to->email;

		if ( ENVIRONMENT != 'production' || EMAIL_OVERRIDE ) :

			if ( EMAIL_OVERRIDE ) :

				$_send_to = EMAIL_OVERRIDE;

			elseif ( APP_DEVELOPER_EMAIL ) :

				$_send_to = APP_DEVELOPER_EMAIL;

			else :

				//	Not sure where this is going; fall over *waaaa*
				show_error( 'EMAILER: Non production environment and neither EMAIL_OVERRIDE nor APP_DEVELOPER_EMAIL is set.' );
				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Start prepping the email
		$this->ci->email->from( $this->from->email, $_send->from->name );
		$this->ci->email->reply_to( $_send->from->email, $_send->from->name );
		$this->ci->email->to( $_send_to );
		$this->ci->email->subject( $_send->subject );

		// --------------------------------------------------------------------------

		//	Clear any errors which might have happened previously
		$_error =& load_class( 'Exceptions', 'core' );
		$_error->clear_errors();

		//	Load the template
		$body  = $this->ci->load->view( 'email/structure/header',		$_send->data, TRUE );
		$body .= $this->ci->load->view( 'email/' . $_send->template,	$_send->data, TRUE );
		$body .= $this->ci->load->view( 'email/structure/footer',		$_send->data, TRUE );

		//	If any errors occurred while attempting to generate the body of this email
		//	then abort the sending and log it

		if ( EMAIL_DEBUG && APP_DEVELOPER_EMAIL && $_error->error_has_occurred() ) :

			//	The templates error'd, abort the send and let dev know
			$_subject	= 'Email #' . $_email->id . ' failed to send due to errors occurring in the templates';
			$_message	= 'Hi,' . "\n";
			$_message	.= '' . "\n";
			$_message	.= 'Email #' . $_email->id . ' was aborted due to errors occurring while building the template' . "\n";
			$_message	.= '' . "\n";
			$_message	.= 'Please take a look as a matter of urgency; the errors are noted below:' . "\n";
			$_message	.= '' . "\n";
			$_message	.= '- - - - - - - - - - - - - - - - - - - - - -' . "\n";
			$_message	.= '' . "\n";

			$_errors = $_error->recent_errors();

			foreach ( $_errors AS $error ) :

				$_message	.= 'Severity: ' . $_error->levels[$error->severity] . "\n";
				$_message	.= 'Message: ' . $error->message . "\n";
				$_message	.= 'File: ' . $error->filepath . "\n";
				$_message	.= 'Line: ' . $error->line . "\n";
				$_message	.= '' . "\n";

			endforeach;

			$_message	.= '' . "\n";
			$_message	.= '- - - - - - - - - - - - - - - - - - - - - -' . "\n";
			$_message	.= '' . "\n";
			$_message	.= 'Additional debugging information:' . "\n";
			$_message	.= '' . "\n";
			$_message	.= '- - - - - - - - - - - - - - - - - - - - - -' . "\n";
			$_message	.= '' . "\n";
			$_message	.= print_r( $_send, TRUE ) . "\n";

			send_developer_mail( $_subject, $_message );

			// --------------------------------------------------------------------------

			$this->_set_error( 'EMAILER: Errors in email template, developers informed' );

			// --------------------------------------------------------------------------]

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Parse the body for <a> links and replace with a tracking URL
		//	First clear out any previous link caches (production only)

		$this->track_link_cache = array();

		if ( ENVIRONMENT == 'production' ) :

			if ( $_send->to->id && ! $_send->to->email_verified ) :

				$_needs_verified = array(
					'id' => $_send->to->id,
					'code' => $_send->to->email_verified_code
				);

			else :

				$_needs_verified = FALSE;

			endif;

			$body = $this->_parse_links( $body, $_email->id, $_email->ref, TRUE, $_needs_verified );

		endif;

		// --------------------------------------------------------------------------

		//	Set the email body
		$this->ci->email->message( $body );

		// --------------------------------------------------------------------------

		//	Set the plain text version
		$plaintext  = $this->ci->load->view( 'email/structure/header_plaintext',	$_send->data, TRUE );
		$plaintext .= $this->ci->load->view( 'email/' . $_send->template_pt,		$_send->data, TRUE );
		$plaintext .= $this->ci->load->view( 'email/structure/footer_plaintext',	$_send->data, TRUE );

		// --------------------------------------------------------------------------

		//	Parse the body for URLs and replace with a tracking URL (production only)
		if ( ENVIRONMENT == 'production' ) :

			$plaintext = $this->_parse_links( $plaintext, $_email->id, $_email->ref, FALSE, $_needs_verified );

		endif;

		// --------------------------------------------------------------------------

		$this->ci->email->set_alt_message( $plaintext );

		// --------------------------------------------------------------------------

		//	Add any attachments
		if ( isset( $_send->data['attachments'] ) && is_array( $_send->data['attachments'] ) && $_send->data['attachments'] ) :

			foreach ( $_send->data['attachments'] AS $file ) :

				if ( ! $this->_add_attachment( $file ) ) :

					if ( ! $graceful ) :

						show_error( 'EMAILER: Failed to add attachment: ' . $file );

					else :

						$this->_set_error( 'EMAILER: Insert Failed.' );
						return FALSE;

					endif;

				endif;

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Debugging?
		if ( EMAIL_DEBUG ) :

			$this->_debugger( $_send, $body, $plaintext, $_error->recent_errors() );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Send! Turn off error reporting, if it fails we should handle it gracefully
		$_previous_error_reporting = error_reporting();
		error_reporting(0);

		if ( $this->ci->email->send() ) :

			//	Put error reporting back as it was
			error_reporting( $_previous_error_reporting );

			// --------------------------------------------------------------------------

			//	Mail sent, mark the time
			$this->db->set( 'time_sent', 'NOW()', FALSE );
			$this->db->where( 'id', $_email->id );
			$this->db->update( NAILS_DB_PREFIX . 'email_archive' );

			return TRUE;

		else:

			//	Put error reporting back as it was
			error_reporting( $_previous_error_reporting );

			// --------------------------------------------------------------------------

			//	Failed to send, notify developers
			$_subject	= 'Email #' . $_email->id . ' failed to send at SMTP time';
			$_message	= 'Hi,' . "\n";
			$_message	.= '' . "\n";
			$_message	.= 'Email #' . $_email->id . ' failed to send at SMTP time' . "\n";
			$_message	.= '' . "\n";
			$_message	.= 'Please take a look as a matter of urgency; debugging data is below:' . "\n";
			$_message	.= '' . "\n";
			$_message	.= '- - - - - - - - - - - - - - - - - - - - - -' . "\n";
			$_message	.= '' . "\n";

			$_message	.= $this->ci->email->print_debugger();

			$_message	.= '' . "\n";
			$_message	.= '- - - - - - - - - - - - - - - - - - - - - -' . "\n";
			$_message	.= '' . "\n";
			$_message	.= 'Additional debugging information:' . "\n";
			$_message	.= '' . "\n";
			$_message	.= '- - - - - - - - - - - - - - - - - - - - - -' . "\n";
			$_message	.= '' . "\n";
			$_message	.= print_r( $_send, TRUE ) . "\n";

			if ( ENVIRONMENT == 'production' ) :

				$this->_set_error( 'Email failed to send at SMTP time, developers informed' );
				send_developer_mail( $_subject, $_message );

			else :

				//	On non-production environments halt execution, this is an error with the configs
				//	and should probably be addressed

				if ( ! $graceful ) :

					show_error( 'Email failed to send at SMTP time. Potential configuration error. Investigate, debugging data below: <div style="padding:20px;background:#EEE">' . $this->ci->email->print_debugger() . '</div>' );

				else :

					$this->_set_error( 'Email failed to send at SMTP time.' );

				endif;

			endif;

			// --------------------------------------------------------------------------

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/* !GETTING */


	// --------------------------------------------------------------------------


	/**
	 * Gets email from the archive
	 *
	 * @access	public
	 * @return	array
	 **/
	public function get_all( $order = NULL, $sort = NULL, $offset = NULL, $per_page = NULL )
	{
		//	Set defaults
		$order		= $order	? $order	: 'ea.time_sent';
		$sort		= $sort		? $sort		: 'ASC';
		$offset		= $offset	? $offset	: 0;
		$per_page	= $per_page	? $per_page	: 25;

		// --------------------------------------------------------------------------

		$this->db->select( 'ea.id, ea.ref, ea.type_id, ea.email_vars, ea.user_email sent_to, ue.is_verified email_verified, ue.code email_verified_code, ea.time_sent, ea.read_count, ea.link_click_count' );
		$this->db->select( 'u.first_name, u.last_name, u.id user_id, u.password user_password, u.group_id user_group, u.profile_img, u.gender, u.username' );
		$this->db->select( 'et.name, et.template_file, et.default_subject' );

		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = ea.user_id OR u.id = ea.user_email', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'email_type et', 'et.id = ea.type_id' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.email = ea.user_email', 'LEFT' );

		$this->db->order_by( $order, $sort );
		$this->db->limit( $per_page, $offset );

		$_emails = $this->db->get( NAILS_DB_PREFIX . 'email_archive ea' )->result();

		// --------------------------------------------------------------------------

		//	Format emails
		foreach ( $_emails AS $email ) :

			$this->_format_email( $email );

		endforeach;

		// --------------------------------------------------------------------------

		return $_emails;
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the number of items in the arcive
	 *
	 * @access	public
	 * @return	int
	 **/
	public function count_all()
	{
		return $this->db->count_all_results( NAILS_DB_PREFIX . 'email_archive' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Gets email from the archive by it's ID
	 *
	 * @access	public
	 * @return	object
	 **/
	public function get_by_id( $id )
	{
		$this->db->where( 'ea.id', $id );
		$_item = $this->get_all();

		if ( ! $_item )
			return FALSE;

		// --------------------------------------------------------------------------

		return $_item[0];
	}



	// --------------------------------------------------------------------------


	/**
	 * Gets items from the archive by it's reference
	 *
	 * @access	public
	 * @param	string	$ref	The reference of the item to get
	 * @return	array
	 **/
	public function get_by_ref( $ref, $guid = FALSE, $hash = FALSE )
	{
		//	If guid and hash === FALSE then by-pass the check
		if ( $guid !== FALSE && $hash !== FALSE ) :

			//	Check hash
			$_check = md5( $guid . APP_PRIVATE_KEY . $ref );

			if ( $_check !== $hash )
				return 'BAD_HASH';

		endif;
		// --------------------------------------------------------------------------

		$this->db->where( 'ea.ref', $ref );
		$_item = $this->get_all();

		if ( ! $_item )
			return FALSE;

		return $_item[0];
	}


	// --------------------------------------------------------------------------


	/* !HELPERS */


	// --------------------------------------------------------------------------


	/**
	 * Add an attachment to the email
	 *
	 * @access	private
	 * @param	string	file to add
	 * @return	boolean
	 **/
	private function _add_attachment( $file )
	{
		if ( ! file_exists( $file ) ) :

			return FALSE;

		endif;

		if ( ! $this->ci->email->attach( $file ) ) :

			return FALSE;

		else :

			return TRUE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates a unique reference for an email, optionally exclude strings
	 *
	 * @access	public
	 * @param	array $exclude An array of strings to exclude
	 * @return	array
	 **/
	private function _generate_reference( $exclude = array() )
	{
		do
		{
			$_ref_ok = FALSE;
			do
			{
				$ref = random_string( 'alnum', 10 );
				if ( array_search( $ref, $exclude ) === FALSE ) :

					$_ref_ok = TRUE;

				endif;

			} while( ! $_ref_ok );

			$this->db->where( 'ref', $ref );
			$_query = $this->db->get( NAILS_DB_PREFIX . 'email_archive' );

		} while( $_query->num_rows() );

		// --------------------------------------------------------------------------

		return $ref;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the debugger
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	void
	 **/
	private function _debugger( $input, $body, $plaintext, $recent_errors )
	{
		//	Debug mode, output data and don't actually send

		//	Remove the reference to CI; takes up a ton'na space
		if ( isset( $input->data['ci'] ) ) :

			$input->data['ci'] = '**REFERENCE TO CODEIGNITER INSTANCE**';

		endif;

		// --------------------------------------------------------------------------

		//	Input variables
		echo '<pre>';

		//	Who's the email going to?
		echo '<strong>Sending to:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo 'email: ' . $input->to->email . "\n";
		echo 'first: ' . $input->to->first . "\n";
		echo 'last:  ' . $input->to->last . "\n";

		//	Who's the email being sent from?
		echo "\n\n" . '<strong>Sending from:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo 'name:	' . $input->from->name . "\n";
		echo 'email:	' . $input->from->email . "\n";

		//	Input data (system & supplied)
		echo "\n\n" . '<strong>Input variables (supplied + system):</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		print_r( $input->data );

		//	Template
		echo "\n\n" . '<strong>Email body:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo 'Subject:	' . $input->subject . "\n";
		echo 'template:	' . $input->template . "\n";

		if ( $recent_errors ) :

			echo "\n\n" . '<strong>Template Errors (' . count( $recent_errors ) . '):</strong>' . "\n";
			echo '-----------------------------------------------------------------' . "\n";

			foreach ( $recent_errors AS $error ) :

				echo 'Severity: ' . $error->severity . "\n";
				echo 'Mesage: ' . $error->message . "\n";
				echo 'Filepath: ' . $error->filepath . "\n";
				echo 'Line: ' . $error->line . "\n\n";

			endforeach;

		endif;

		echo "\n\n" . '<strong>Rendered HTML:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";

		$_rendered_body = str_replace( '"', '\\"', $body );
		$_rendered_body = str_replace( array("\r\n", "\r"), "\n", $_rendered_body );
		$_lines = explode("\n", $_rendered_body);
		$_new_lines = array();

		foreach ( $_lines AS $line ) :

		    if ( ! empty( $line ) ) :

		        $_new_lines[] = $line;

		       endif;

		endforeach;

		$_rendered_body = implode( $_new_lines );

		echo '<iframe width="100%" height="900" src="" id="renderframe"></iframe>' ."\n";
		echo '<script type="text/javascript">' . "\n";
		echo 'var _body = "' . $_rendered_body. '";' . "\n";
		echo 'document.getElementById(\'renderframe\').src = "data:text/html;charset=utf-8," + escape(_body);' . "\n";
		echo '</script>' . "\n";

		echo "\n\n" . '<strong>HTML:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo htmlentities( $body ) ."\n";

		echo "\n\n" . '<strong>Plain Text:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo '</pre>' . nl2br( $plaintext ) . "\n";

		exit( 0 );
	}


	// --------------------------------------------------------------------------


	/* !TRACKING */


	// --------------------------------------------------------------------------


	/**
	 * Increments an email's open count and adds a tracking note
	 *
	 * @access	public
	 * @param	string $ref The email's reference
	 * @param	string $guid the unique counter used to generate the hash
	 * @param	string $hash The secutiry hash to check (i.e verify the ref and guid).
	 * @return	bool
	 **/
	public function track_open( $ref, $guid, $hash )
	{
		$_email = $this->get_by_ref( $ref, $guid, $hash );

		if ( $_email && $_email != 'BAD_HASH' ) :

			//	Update the read count and a add a track data point
			$this->db->set( 'read_count', 'read_count+1', FALSE );
			$this->db->where( 'id', $_email->id );
			$this->db->update( NAILS_DB_PREFIX . 'email_archive' );

			$this->db->set( 'created', 'NOW()', FALSE );
			$this->db->set( 'email_id', $_email->id );

			if ( active_user( 'id' ) ) :

				$this->db->set( 'user_id', active_user( 'id' ) );

			endif;

			$this->db->insert( NAILS_DB_PREFIX . 'email_archive_track_open' );

			return TRUE;

		endif;

		return FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Increments a link's open count and adds a tracking note
	 *
	 * @access	public
	 * @param	string $ref The email's reference
	 * @param	string $guid the unique counter used to generate the hash
	 * @param	string $hash The secutiry hash to check (i.e verify the ref and guid).
	 * @param	string $url
	 * @param	string $type
	 * @return	bool
	 **/
	public function track_link( $ref, $guid, $hash, $link_id )
	{
		$_email = $this->get_by_ref( $ref, $guid, $hash );

		if ( $_email && $_email != 'BAD_HASH' ) :

			//	Get the link which was clicked
			$this->db->select( 'url' );
			$this->db->where( 'email_id', $_email->id );
			$this->db->where( 'id', $link_id );
			$_link = $this->db->get( NAILS_DB_PREFIX . 'email_archive_link' )->row();

			if ( $_link ) :

				//	Update the read count and a add a track data point
				$this->db->set( 'link_click_count', 'link_click_count+1', FALSE );
				$this->db->where( 'id', $_email->id );
				$this->db->update( NAILS_DB_PREFIX . 'email_archive' );

				//	Add a link trackback
				$this->db->set( 'created', 'NOW()', FALSE );
				$this->db->set( 'email_id', $_email->id );
				$this->db->set( 'link_id', $link_id );

				if ( active_user( 'id' ) ) :

					$this->db->set( 'user_id', active_user( 'id' ) );

				endif;

				$this->db->insert( NAILS_DB_PREFIX . 'email_archive_track_link' );

				//	Return the URL to go to
				return $_link->url;

			else :

				return 'BAD_LINK';

			endif;

		endif;

		return 'BAD_HASH';
	}


	// --------------------------------------------------------------------------


	/**
	 * Parses a string for <a> links and replaces them with a trackable URL
	 *
	 * @access	public
	 * @param	string $body The string to parse
	 * @param	int $email_id The ID of the email being processed
	 * @param	bool $is_html Whether body is HTML (i.e look for <a> tags) or plaintext (i.e look for plain URL)
	 * @return	string
	 **/
	private function _parse_links( $body, $email_id, $email_ref, $is_html = TRUE, $needs_verified = FALSE )
	{
		//	Set the class variables for the ID and ref (need those in the callbacks)
		$this->_generate_tracking_email_id			= $email_id;
		$this->_generate_tracking_email_ref			= $email_ref;
		$this->_generate_tracking_needs_verified	= $needs_verified;

		// --------------------------------------------------------------------------

		if ( $is_html ) :

			$body = preg_replace_callback( '/<a .*?(href="(.*?)").*?>(.*?)<\/a>/', array( $this, '__process_link_html' ), $body );

		else :

			$body = preg_replace_callback( '/(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?/  ', array( $this, '__process_link_url' ), $body );

		endif;

		// --------------------------------------------------------------------------

		//	And null these again, so nothing gets confused
		$this->_generate_tracking_email_id			= NULL;
		$this->_generate_tracking_email_ref			= NULL;
		$this->_generate_tracking_needs_verified	= NULL;

		// --------------------------------------------------------------------------

		return $body;
	}


	// --------------------------------------------------------------------------


	private function __process_link_html( $link )
	{
		$_html	= ! empty( $link[0] ) ? $link[0] : '';
		$_href	= ! empty( $link[1] ) ? $link[1] : '';
		$_url	= ! empty( $link[2] ) ? $link[2] : '';
		$_title	= isset( $link[3] ) && strip_tags( $link[3] ) ? strip_tags( $link[3] ) : $_url;

		// --------------------------------------------------------------------------

		//	Only process if there's at least the HTML tag and a detected URL
		//	otherwise it's not worth it/possible to accurately replace the tag

		if ( $_html && $_url ) :

			$_html = $this->__process_link_generate( $_html, $_url, $_title, TRUE );

		endif;

		return $_html;
	}


	// --------------------------------------------------------------------------


	private function __process_link_url( $url )
	{
		$_html	= ! empty( $url[0] ) ? $url[0] : '';
		$_url	= $_html;
		$_title	= $_html;

		// --------------------------------------------------------------------------

		//	Only process if theres a URL to process
		if ( $_html && $_url ) :

			$_html = $this->__process_link_generate( $_html, $_url, $_title, FALSE );

		endif;

		return $_html;
	}


	// --------------------------------------------------------------------------


	private function __process_link_generate( $html, $url, $title, $is_html )
	{
		//	Generate a tracking URL for this link
		//	Firstly, check this URL hasn't been processed already (for this email)

		if ( isset( $this->track_link_cache[md5( $url )] ) ) :

			$_tracking_url = $this->track_link_cache[md5( $url )];

			//	Replace the URL	and return the new tag
			$html = str_replace( $url, $_tracking_url, $html );

		else :

			//	New URL, needs processed. We take the URL and the Title, store it in the
			//	database and generate the new tracking link (inc. hashes etc). We'll cache
			//	this link so we don't have to process it again.

			//	If the email we're sending to hasn't been verified yet we should set the
			//	actual URL as the return_to value of the email verifier, that means that
			//	every link in this email behaves as a verifying email. Obviously we shouldn't
			//	do this for the actual email verifier...

			if ( $this->_generate_tracking_needs_verified ) :

				//	Make sure we're not applying this to an activation URL
				if ( ! preg_match( '#email/verify/[0-9]*?/(.*?)#', $url ) ) :

					$_user_id	= $this->_generate_tracking_needs_verified['id'];
					$_code		= $this->_generate_tracking_needs_verified['code'];
					$_return	= urlencode( $url );

					$_url = site_url( 'email/verify/' . $_user_id . '/' . $_code . '?return_to=' . $_return );

				else :

					$_url = $url;

				endif;

			else :

				$_url = $url;

			endif;

			$this->db->set( 'email_id', $this->_generate_tracking_email_id );
			$this->db->set( 'url', $_url );
			$this->db->set( 'title', $title );
			$this->db->set( 'created', 'NOW()', FALSE );
			$this->db->set( 'is_html', $is_html );
			$this->db->insert( NAILS_DB_PREFIX . 'email_archive_link' );

			$_id = $this->db->insert_id();

			if ( $_id ) :

				$_time			= time();
				$_tracking_url	= site_url( 'email/tracker/link/' . $this->_generate_tracking_email_ref . '/' . $_time . '/' . md5( $_time . APP_PRIVATE_KEY . $this->_generate_tracking_email_ref ). '/' . $_id );

				$this->track_link_cache[md5( $url )] = $_tracking_url;

				// --------------------------------------------------------------------------

				//	Replace the URL	and return the new tag
				$html = str_replace( $url, $_tracking_url, $html );

			endif;

		endif;

		return $html;
	}


	// --------------------------------------------------------------------------


	/* !ERRORS */


	// --------------------------------------------------------------------------


	public function get_errors()
	{
		return $this->_errors;
	}


	// --------------------------------------------------------------------------


	public function last_error()
	{
		return end( $this->_errors );
	}


	// --------------------------------------------------------------------------


	private function _set_error( $message )
	{
		$this->_errors[] = $message;
	}


	// --------------------------------------------------------------------------


	protected function _format_email( &$email )
	{
		$email->email_vars = unserialize( $email->email_vars );

		// --------------------------------------------------------------------------

		//	If a subject is defined in the variables use that, if not check to see if one was
		//	defined in the template; if not, fall back to a default subject

		if ( isset( $email->email_vars['email_subject'] ) ) :

			$email->subject = $email->email_vars['email_subject'];

		elseif ( $email->default_subject ) :

			$email->subject = $email->default_subject;

		else :

			$email->subject = 'An E-mail from ' . APP_NAME;

		endif;

		// --------------------------------------------------------------------------

		//	Sent to
		$email->user 				= new stdClass();
		$email->user->id			= $email->user_id;
		$email->user->group_id		= $email->user_group;
		$email->user->email			= $email->sent_to;
		$email->user->username		= $email->username;
		$email->user->password		= $email->user_password;
		$email->user->first_name	= $email->first_name;
		$email->user->last_name		= $email->last_name;
		$email->user->profile_img	= $email->profile_img;
		$email->user->gender		= $email->gender;


		unset( $email->user_id );
		unset( $email->sent_to );
		unset( $email->username );
		unset( $email->first_name );
		unset( $email->last_name );
		unset( $email->profile_img );
		unset( $email->gender );
		unset( $email->user_group );
		unset( $email->user_password );

	}

}

/* End of file emailer.php */
/* Location: ./application/libraries/emailer.php */