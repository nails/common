<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Emailer
*
* Created:		13/11/2011
* Modified:		13/11/2012
*
* Description:	Easily manage the email queue
* 
*/

class Emailer {
	
	public $from;
	
	private $ci;
	private $email_type = array();
	
	
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
		$this->from			= new stdClass();
		$this->from->name	= APP_EMAIL_FROM_NAME;
		$this->from->email	= APP_EMAIL_FROM_EMAIL;
		
		// --------------------------------------------------------------------------
		
		//	Load the Email library
		$this->ci->load->library( 'email' );
		
		// --------------------------------------------------------------------------
		
		//	Load helpers
		$this->ci->load->helper( 'email' );
		$this->ci->load->helper( 'typography' );
		$this->ci->load->helper( 'string' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * The touchpoint for sending email. In short, this method will determine whether
	 * to send the mail immediately or to queue it up for the cron jobs
	 *
	 * @access	public
	 * @param	object	$input	The input object
	 * @return	void
	 * @author	Pablo
	 **/
	public function send( $input, $debug = FALSE )
	{
		//	We got something to work with?
		if ( empty( $input ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Ensure $input is an object
		if ( ! is_object( $input ) )
			$input = (object) $input;
		
		// --------------------------------------------------------------------------
		
		//	Check we have at least a user_id/email and an email type
		if ( ( empty( $input->to_id ) && empty( $input->to_email ) ) || empty( $input->type ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	If no email has been given make sure it's NULL
		if ( ! isset( $input->to_email ) ) :
		
			$input->to_email = NULL;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If no id has been given make sure it's NULL
		if ( ! isset( $input->to_id ) ) :
		
			$input->to_id = 'NULL';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If no internal_ref has been given make sure it's NULL
		if ( ! isset( $input->internal_ref ) ) :
		
			$input->internal_ref = 'NULL';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Make sure that at least empty data is available
		if ( ! isset( $input->data ) ) :
		
			$input->data = array();
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Lookup the email type (caching it as we go)
		if ( ! isset( $this->email_type[ $input->type ]) ) :
		
			$this->ci->db->select( 'eqt.id, eqt.cron_run, eqt.type' );
			$this->ci->db->where( 'eqt.id_string', $input->type );
			
			$this->email_type[ $input->type ] = $this->ci->db->get( 'email_queue_type eqt' )->row();
			
			if ( ! $this->email_type[ $input->type ] )
				return show_error( 'Invalid Email Type' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Check to see if the user is subscribed to this email type; service_acct emails
		//	always go out
		
		if ( $input->to_id && $this->email_type[ $input->type ]->type != 'service_acct' ) :
		
			if ( is_array( $input->to_id ) ) :
			
				//	If to_id is an array we'll need to prune out those who have unsubscribed
				foreach( $input->to_id AS $k => $id ) :
				
					if ( ! $this->_is_subscribed( $id, $this->email_type[ $input->type ]->type ) ) :
					
						unset( $input->to_id[$k] );
					
					endif;
				
				endforeach;
				
				//	If there is no one to send to then failover gracefully here
				if ( ! $input->to_id ) :
				
					return TRUE;
				
				else :
				
					$input->to_id = array_values( $input->to_id );
				
				endif;
			
			else :
			
				//	to_id is a single integer, check for this one user
				if ( ! $this->_is_subscribed( $input->to_id, $this->email_type[ $input->type ]->type ) ) :
				
					//	Failover gracefully, the calling method doest need to know the email wans't actually sent
					
					return TRUE;
				
				endif;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If we're sending to an email address, try and associate it to a registered user
		if ( $input->to_email ) :
		
			$_user = get_userobject()->get_user_by_email( $input->to_email );
			
			if ( $_user ) :
			
				$input->to_email	= $_user->email;
				$input->to_id		= $_user->id;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If the cron run is instant then send now, otherwise queue it up
		if ( $this->email_type[ $input->type ]->cron_run == 'instant' ) :
		
			return $this->_send_now( $input, $debug );
		
		else :
		
			return $this->_queue( $input );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Preps data and add's it to the queue
	 *
	 * @access	private
	 * @param	object	$input	The input object
	 * @param	boolean	$_queue	Whether to add to the queue (private parameter used only so the send_now function doesn't duplicate functionality)
	 * @return	array
	 * @author	Pablo
	 **/
	private function _queue( $input = FALSE, $_queue = TRUE )
	{
		//	Are we dealing with a single user or multiple users?
		if ( is_array( $input->to_id ) ) :
		
			$_refs = array();
			
			//	Basic queries
			$_sql				= array();
			$_sql['archive']	= 'INSERT INTO email_queue_archive (ref, internal_ref, user_id, user_email, time_queued, type_id, email_vars) VALUES';
			
			if ( $_queue ) :
			
				$_sql['queue']	= 'INSERT INTO email_queue (ref, internal_ref, user_id, user_email, time_queued, type_id, email_vars) VALUES ';
				
			endif;
			
			foreach ( $input->to_id AS $to_id ) :	
			
				//	Generate a new reference
				$_ref		= $this->_generate_reference( $_refs );
				$_refs[]	= $_ref;
				
				// --------------------------------------------------------------------------
				
				//	Append to queries
				if ( $_queue ) :
				
					$_sql['queue']	.= '( \'' . $_ref . '\', ' . $input->internal_ref . ', ' . $to_id . ', \'' . $input->to_email . '\', ' . time() . ', ' . $this->email_type[ $input->type ]->id . ', ' . $this->ci->db->escape( serialize( $input->data ) ) . ' ),';
				
				endif;
				
				$_sql['archive']	.= '( \'' . $_ref . '\', ' . $input->internal_ref . ', ' . $to_id . ', \'' . $input->to_email . '\', ' . time() . ', ' . $this->email_type[ $input->type ]->id . ', ' . $this->ci->db->escape( serialize( $input->data ) ) . ' ),';				
			
			endforeach;
			
			//	Trim queries
			if ( $_queue ) :
			
				$_sql['queue'] = substr( $_sql['queue'], 0, -1 );
			
			endif;
			
			$_sql['archive'] = substr( $_sql['archive'], 0, -1 );
		
		else :
		
			//	Generate a unique reference - ref is sent in each email and can allow the
			//	system to generate 'view online' links
			
			$input->ref = $this->_generate_reference();
			
			// --------------------------------------------------------------------------
			
			//	Insert into the appropriate tables
			$_sql	= array();
			
			if ( $_queue ) :
			
				$_sql[]	= '	INSERT INTO email_queue
							(ref, internal_ref, user_id, user_email, time_queued, type_id, email_vars) VALUES
							( \'' . $input->ref . '\', ' . $input->internal_ref . ', ' . $input->to_id . ', \'' . $input->to_email . '\', ' . time() . ', ' . $this->email_type[ $input->type ]->id . ', ' . $this->ci->db->escape( serialize( $input->data ) ) . ' );';
							
			endif;
						
			$_sql[]	= '	INSERT INTO email_queue_archive
						(ref, internal_ref, user_id, user_email, time_queued, type_id, email_vars) VALUES
						( \'' . $input->ref . '\', ' . $input->internal_ref . ', ' . $input->to_id . ', \'' . $input->to_email . '\', ' . time() . ', ' . $this->email_type[ $input->type ]->id . ', ' . $this->ci->db->escape( serialize( $input->data ) ) . ' );';
		
		endif;
		
		foreach( $_sql AS $sql ) :
		
			$this->ci->db->query( $sql );
			
			if ( is_array( $input->to_id ) ) :
			
				//	$this->ci->db->insert_id(); will return the ID of the FIRST inserted row,
				//	the following inserts are guaranteed to be sequential. In order to pass
				//	back the ID's of all the inserted rows we need to do a quick bit of maths.
				
				$_first		= $this->ci->db->insert_id();
				$_rows		= $this->ci->db->affected_rows() - 1; //	Adjusted as range() will go to the limit, rather than below.
				$input->id	= range( $_first, $_first + $_rows, 1 );
			
			else :
			
				$input->id = $this->ci->db->insert_id();
				
			endif;
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $input;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Sends an item immediately
	 *
	 * @access	private
	 * @param	object	$input	Data input
	 * @return	array
	 * @author	Pablo
	 **/
	private function _send_now( $input, $debug = FALSE )
	{
		//	Generate the correct input object and archive the email
		$_input = $this->_queue( $input, FALSE );
		
		if ( $_input ) :
		
			//	Send the email now.
			if ( is_array( $input->id ) ) :
			
				$_out = array();
				
				foreach ( $input->id AS $id ) :
				
					$this->_send( $id, $debug );
				
				endforeach;
			
			else :
			
				if ( $this->_send( $_input->id, $debug ) ) :
				
					return $_input->ref;
				
				else :
				
					return FALSE;
				
				endif;
				
			endif;
		
		else :
		
			return ( $debug ) ? show_error( 'Insert Failed.' ) : FALSE;
		
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
	 * @author	Pablo
	 **/
	public function get_all()
	{ 
		$this->ci->db->select( 'eqa.id, eqa.ref, eqa.time_queued, eqa.email_vars, eqa.user_email' );
		$this->ci->db->select( 'u.email send_to, um.first_name, um.last_name, u.id user_id, u.password user_password, u.group_id user_group, um.profile_img' );
		$this->ci->db->select( 'eqt.name, eqt.cron_run, eqt.template_file,eqt.template_file_plaintext, eqt.subject' );
		
		$this->ci->db->join( 'user u', 'u.id = eqa.user_id', 'LEFT' );
		$this->ci->db->join( 'user_meta um', 'um.user_id = eqa.user_id', 'LEFT' );
		$this->ci->db->join( 'email_queue_type eqt', 'eqt.id = eqa.type_id' );
		
		$this->ci->db->order_by( 'eqa.time_queued', 'ASC' );
		
		$_emails = $this->ci->db->get( 'email_queue_archive eqa' )->result();
		
		// --------------------------------------------------------------------------
		
		//	Process emails
		foreach ( $_emails AS $email ) :
		
			$email->email_vars = unserialize( $email->email_vars );
			
			// --------------------------------------------------------------------------
			
			//	If no subject is defined in the variables, if not check to see if one was
			//	defined in the emplate; if not, fall back to a default subject
			$_subject = isset( $email->email_vars['email_subject'] );
			
			if ( $_subject ) :
			
				$email->subject = $email->email_vars['email_subject'];
			
			else :
			
				//	Check the template
				if ( ! $email->subject ) :
				
					$email->subject = 'An E-mail from Intern Avenue';
				
				endif;
			
			endif;
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_emails;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches emails from the queue
	 *
	 * @access	public
	 * @return	object
	 * @author	Pablo
	 **/
	public function get_queue( $cron_run = NULL, $queue_id = NULL )
	{
		$this->ci->db->select( 'eq.id, eq.ref, eq.time_queued, eq.email_vars, eq.user_email' );
		$this->ci->db->select( 'u.email send_to, um.first_name, um.last_name, u.id user_id, u.password user_password, u.group_id user_group, um.profile_img' );
		$this->ci->db->select( 'eqt.name, eqt.cron_run, eqt.template_file,eqt.template_file_plaintext, eqt.subject' );
		
		$this->ci->db->join( 'user u', 'u.id = eq.user_id', 'LEFT' );
		$this->ci->db->join( 'user_meta um', 'um.user_id = eq.user_id', 'LEFT' );
		$this->ci->db->join( 'email_queue_type eqt', 'eqt.id = eq.type_id' );
		
		if ( $cron_run ) :
		
			$this->ci->db->where( 'eqt.cron_run', $cron_run );
			
		endif;
		
		if ( $queue_id ) :
		
			$this->ci->db->where( 'eq.queue_id', $queue_id );
		
		else :
		
			$this->ci->db->where( 'eq.queue_id IS NULL' );
		
		endif;
		
		$this->ci->db->order_by( 'eq.time_queued', 'ASC' );
		
		$_emails = $this->ci->db->get( 'email_queue eq' )->result();
		
		// --------------------------------------------------------------------------
		
		//	Process emails
		foreach ( $_emails AS $email ) :
		
			$email->email_vars = unserialize( $email->email_vars );
			
			// --------------------------------------------------------------------------
			
			//	If no subject is defined in the variables, if not check to see if one was
			//	defined in the emplate; if not, fall back to a default subject
			$_subject = isset( $email->email_vars['email_subject'] );
			
			if ( $_subject ) :
			
				$email->subject = $email->email_vars['email_subject'];
			
			else :
			
				//	Check the template
				if ( ! $email->subject ) :
				
					$email->subject = 'An email from Intern Avenue.';
				
				endif;
			
			endif;
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_emails;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Gets email from the archive by it's ID
	 *
	 * @access	public
	 * @return	object
	 * @author	Pablo
	 **/
	public function get_by_id( $id )
	{
		$this->ci->db->where( 'eqa.id', $id );
		$_item = $this->get_all();
		
		if ( ! $_item )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_item[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Gets email from the archive by it's ID
	 *
	 * @access	public
	 * @return	object
	 * @author	Pablo
	 **/
	public function get_queue_by_id( $id )
	{
		$this->ci->db->where( 'eq.id', $id );
		$_item = $this->get_queue();
		
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
	 * @author	Pablo
	 **/
	public function get_by_ref( $ref, $guid = FALSE, $hash = FALSE )
	{
		//	If guid and hash === FALSE then by pass the check
		if ( $guid !== FALSE && $hash !== FALSE ) :
		
			//	Check hash
			$_check = md5( $guid . APP_PRIVATE_KEY . $ref );
			
			if ( $_check !== $hash )
				return 'BAD_HASH';
		
		endif;
		// --------------------------------------------------------------------------
		
		$this->ci->db->where( 'eqa.ref', $ref );
		$_item = $this->get_all();
		
		if ( ! $_item )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	If this isn't an instant or prompt email then CRUNCH!
		if ( $_item[0]->cron_run != 'instant' && $_item[0]->cron_run != 'prompt' ) :
		
			$_crunch = $this->_crunch_data( $_item );
			return $_crunch['email'];
			
		else :
		
			return $_item[0];
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/* !QUEUE */
	
	
	// --------------------------------------------------------------------------
	
	
	public function process_queue( $cron_run )
	{
		$_stats = array();
		$_stats['total']	= 0;
		$_stats['failed']	= 0;
		
		//	Generate an ID for this queue
		$_queue_id = microtime( TRUE ) * 10000;
		
		// --------------------------------------------------------------------------
		
		//	If we're dealing with the 'prompt' cron run then we just need to send each
		//	email as we come across it. If we're dealing with any other cron run then
		//	we need to compile each email type into one and send it as one object.
		
		if ( $cron_run == 'prompt' ) :
		
		
			//	Fetch all emails in the queue
			$this->ci->logger->line( 'Fetching queue items for "' . $cron_run . '" cron run...' );
			$_emails = $this->get_queue( $cron_run );
			
			// --------------------------------------------------------------------------
			
			//	If there are no queue items then we're done
			if ( ! count( $_emails ) ) :
			
				$this->ci->logger->line( '...nothing here. Terminating.' );
				return '0 emails processed.';
				
			endif;
			
			// --------------------------------------------------------------------------
			
			//	We found something, process...
			$_stats['total'] = count( $_emails );
			$this->ci->logger->line( $_stats['total'] . ' queue items found. Processing...' );
			
			// --------------------------------------------------------------------------
			
			//	Assign all these items the same queue ID
			$_queue_ids = array();
			for ( $i = 0; $i < $_stats['total']; $i++ ) :
			
				$_queue_ids[] = $_emails[$i]->id;
			
			endfor;
			
			//$this->ci->db->set( 'eq.queue_id', $_queue_id );
			//$this->ci->db->where_in( 'eq.id', $_queue_ids );
			//$this->ci->db->update( 'email_queue eq' );
			
			// --------------------------------------------------------------------------
			
			$_unqueue = array();
			
			foreach ( $_emails AS $e ) :
			
				$this->ci->logger->line( '...sending email ref: ' . $e->ref . ' to: ' . $e->send_to . $e->user_email );
				
				if ( $this->_send( $e, FALSE, FALSE ) ) :
				
					$_unqueue[] = $e->id;
				
				else :
				
					$_stats['failed']++;
					$this->ci->logger->line( 'Email to  ' . $e->send_to . ' failed to send. Queue item: ' . $e->id );
				
				endif;
				
				// --------------------------------------------------------------------------
				
				//	Clean up
				_flush_db();
			
			endforeach;
		
		else :
		
			//	Get a distinct list of user ID's and types we're going to be sending to, we'll then use this to
			//	cherry pick the emails we need to compile and send
			
			$this->ci->logger->line( 'Fetching queue items for "' . $cron_run . '" cron run...' );
			
			//	Fetch user ID's
			$_sql = 'SELECT
					DISTINCT `eq`.`user_id`, `eq`.`type_id`
					FROM `email_queue` eq
					LEFT JOIN `email_queue_type` eqt ON `eqt`.`id` = `eq`.`type_id`
					WHERE `eqt`.`cron_run` = "' . $cron_run . '"
					AND `eq`.`user_id` != \'\'';
			
			$_values_id = $this->ci->db->query( $_sql )->result();
			
			//	Fetch emails (no user ID)
			$_sql = 'SELECT
					DISTINCT `eq`.`user_email`, `eq`.`type_id`
					FROM `email_queue` eq
					LEFT JOIN `email_queue_type` eqt ON `eqt`.`id` = `eq`.`type_id`
					WHERE `eqt`.`cron_run` = "' . $cron_run . '"
					AND `eq`.`user_email` != \'\'';
			
			$_values_email = $this->ci->db->query( $_sql )->result();
			
			//	Merge the two
			$_values = array_merge( $_values_id, $_values_email );
			
			// --------------------------------------------------------------------------
			
			//	If there are no queue items then we're done
			if ( ! count( $_values ) ) :
			
				$this->ci->logger->line( '...nothing here. Terminating.' );
				return '0 emails processed.';
				
			endif;
			
			// --------------------------------------------------------------------------
			
			//	We found something, process...
			$this->ci->logger->line( count( $_values ) . ' queue items found. Processing...' );
			
			//	We loop through these results and pull out the relevant emails from the database, each one of these
			//	represents a single email which must be crunched down into a single entity and then sent
			
			$_unqueue = array();
			
			foreach ( $_values AS $item ) :
			
				$this->ci->logger->line( '' );
				
				if ( isset( $item->user_id ) )
					$this->ci->logger->line( 'Beginning processing for user #' . $item->user_id );
					
				if ( isset( $item->user_email ) )
					$this->ci->logger->line( 'Beginning processing for email ' . $item->user_email );
				
				//	Fetch the appropriate queue items from the DB
				if ( isset( $item->user_id ) )
					$this->ci->db->where( 'eq.user_id', $item->user_id );
					
				if ( isset( $item->user_email ) )
					$this->ci->db->where( 'eq.user_email', $item->user_email );
				
				$this->ci->db->where( 'eq.type_id', $item->type_id );
				$_emails = $this->get_queue( $cron_run );
				$_count		= count( $_emails );
				
				// --------------------------------------------------------------------------
				
				if ( ! $_emails ) :
				
					$this->ci->logger->line( '...No queue items found, weird, oh well - finished for this guy.' );
					continue;
				
				else :
				
					$this->ci->logger->line( '...' . $_count . ' emails found, getting ready to crunch!' );
					
				endif;
				
				// --------------------------------------------------------------------------
				
				//	Assign all these items the same queue ID
				$_queue_ids = array();
				
				for ( $i = 0; $i < $_count; $i++ ) :
				
					$_queue_ids[] = $_emails[$i]->id;
				
				endfor;
				
				if ( $_queue_ids ) :
				
					$this->ci->db->set( 'eq.queue_id', $_queue_id );
					$this->ci->db->where_in( 'eq.id', $_queue_ids );
					$this->ci->db->update( 'email_queue eq' );
					
				endif;
				
				// --------------------------------------------------------------------------
				
				$_crunch = $this->_crunch_data( $_emails );
				
				$this->ci->logger->line( '...finished crunching data' );
				
				// --------------------------------------------------------------------------
				
				//	Send the email now
				$this->ci->logger->line( '...sending email' );
				
				if ( $this->_send( $_crunch['email'] ) ) :
				
					$_unqueue = array_merge( $_unqueue, $_crunch['ids'] );
					
					// --------------------------------------------------------------------------
					
					//	Update all the emails in the archive to have a common ref
					$this->ci->logger->line( '...sent successfully, reassigning email refs.' );
					
					$this->ci->db->where_in( 'ref', $_crunch['refs'] );
					$this->ci->db->set( 'ref', $_crunch['email']->ref );
					$this->ci->db->update( 'email_queue_archive' );
				
				else :
				
					$_stats['failed']++;
					$this->ci->logger->line( '...email failed to send.' );
				
				endif;
				
				// --------------------------------------------------------------------------
				
				//	Clean up
				_flush_db();
			
			endforeach;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Clearing the queue
		if ( $_unqueue ) :
		
			$this->ci->logger->line( 'Clearing ' . count( $_unqueue ) . ' items from the queue...');
			$this->ci->db->where_in( 'id', $_unqueue );
			$this->ci->db->delete( 'email_queue' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Releasing items from this queue_id
		$this->ci->db->set( 'queue_id', NULL );
		$this->ci->db->where( 'queue_id', $_queue_id );
		$this->ci->db->update( 'email_queue' );
		
		// --------------------------------------------------------------------------
		
		//	Handle the log message
		if ( $_stats['failed'] ) :
		
			return $_stats['total'] . ' emails processed, ' . $_stats['failed'] . ' failed to send.';
		
		else :
		
			return $_stats['total'] . ' emails processed.';
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/* !HELPERS */
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Send a templated email immediately
	 *
	 * @access	private
	 * @param	object	$input			The input object
	 * @param	boolean	$debug			Turn debugging on or off
	 * @param	boolean	$use_archive	Whetehr to use the archive or the live queue for the email look up
	 * @return	boolean
	 * @author	Pablo
	 **/
	private function _send( $email_id = FALSE, $debug = FALSE, $use_archive = TRUE )
	{
		//	Get the email if $email_id is not an object
		if ( ! is_object( $email_id ) ) :
		
			if ( $use_archive ) :
			
				$_email = $this->get_by_id( $email_id );
				
			else :
			
				$_email = $this->get_queue_by_id( $email_id );
			
			endif;
		
		else :
		
			$_email = $email_id;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( ! $_email )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$_send						= new stdClass();
		$_send->to					= new stdClass();
		$_send->to->email			= $_email->user_email ? $_email->user_email : $_email->send_to;
		$_send->to->first			= $_email->first_name;
		$_send->to->last			= $_email->last_name;
		$_send->to->id				= (int) $_email->user_id;
		$_send->to->group_id		= $_email->user_group;
		$_send->to->login_url		= $_email->user_id ? site_url( 'auth/login/with_hashes/' . md5( $_email->user_id ) . '/' . md5( $_email->user_password ) ) : NULL;
		$_send->subject				= $_email->subject;
		$_send->template			= $_email->template_file;
		$_send->template_pt			= $_email->template_file_plaintext;
		$_send->data				= $_email->email_vars;
		
		// --------------------------------------------------------------------------
		
		//	From user
		$_send->from				= new stdClass();
		
		if ( isset( $_send->data['email_from_email'] ) ) :
		
			$_send->from->email			= $_send->data['email_from_email'];
			$_send->from->name			= isset( $_send->data['email_from_name'] ) ? $_send->data['email_from_name'] : $_send->data['email_from_email'];
		
		else :
		
			$_send->from->email			= $this->from->email;
			$_send->from->name			= $this->from->name;
		
		endif;
		
		// --------------------------------------------------------------------------
			
		//	Fresh start please
		$this->ci->email->clear( TRUE );
		
		// --------------------------------------------------------------------------
		
		//	Add some extra, common variables for the template
		
		$_send->data['email_ref']		= $_email->ref;
		$_send->data['sent_from']		= $_send->from;
		$_send->data['sent_to']			= $_send->to;
		$_send->data['email_subject']	= $_send->subject;
		$_send->data['site_url']		= site_url();
		$_send->data['secret']			= APP_PRIVATE_KEY;
		
		// --------------------------------------------------------------------------
		
			
		//	If we're not on a production server, never queue or send out to any
		//	live addresses please
		
		$_send_to = ( ENVIRONMENT != 'production' ) ? EMAIL_OVERRIDE : $_send->to->email;
			
		// --------------------------------------------------------------------------
		
		//	Start prepping the email
		$this->ci->email->from( $this->from->email, $this->from->name );
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
		
		if ( $_error->error_has_occurred() ) :
		
			//	The templates error'd, abort the send and let dev know
			$_to		= 'hello@shedcollective.org';
			$_subject	= 'Email #' . $_email->id . ' failed to send due to errors occurring in the templates';
			$_message	= 'Hi,' . "\n";
			$_message	.= '' . "\n";
			$_message	.= 'Email #' . $_email->id . ' was aborted due to errors occurring while building the template' . "\n";
			$_message	.= '' . "\n";
			$_message	.= 'Please take a look as a matter or urgency; the errors are noted below:' . "\n";
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
			
			$_headers = 'From: ' . APP_EMAIL_FROM_NAME . ' <' . APP_EMAIL_FROM_EMAIL . '>' . "\r\n" .
						'Reply-To: hello@shedcollective.org' . "\r\n" .
						'X-Mailer: PHP/' . phpversion()  . "\r\n" .
						'X-Priority: 1 (Highest)' . "\r\n" .
						'X-Mailer: X-MSMail-Priority: High/' . "\r\n" .
						'Importance: High';
			
			@mail( $_to, $_subject , $_message, $_headers );
			
			// --------------------------------------------------------------------------
			
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set the email body
		$this->ci->email->message( $body );
		
		//	Set the plain text version
		$plaintext  = $this->ci->load->view( 'email/structure/header_plaintext',	$_send->data, TRUE );
		$plaintext .= $this->ci->load->view( 'email/' . $_send->template_pt,		$_send->data, TRUE );
		$plaintext .= $this->ci->load->view( 'email/structure/footer_plaintext',	$_send->data, TRUE );
		
		$this->ci->email->set_alt_message( $plaintext );
		
		// --------------------------------------------------------------------------
		
		//	Add any attachments
		if ( isset( $input->attachment ) ) :
		
			$input->attachment = (object) $input->attachment;
			
			foreach ( $input->attachment AS $file ) :
			
				if ( ! $this->_add_attachment( $file ) )
					return ( $debug ) ? show_error( 'Failed to add attachment: '.$file ) : FALSE;
				
			endforeach;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Debugging?
		if ( $debug || ( defined( 'EMAIL_DEBUG' ) && EMAIL_DEBUG ) ) :
		
			$this->_debugger( $_send, $body, $plaintext );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Send!
		return $this->ci->email->send() ? TRUE : FALSE;
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
			
			$this->ci->db->where( 'ref', $ref );
			$_query = $this->ci->db->get( 'email_queue_archive' );
			
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
		
		echo "\n\n" . '<strong>HTML:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo htmlentities( $body ) ."\n";
		
		echo "\n\n" . '<strong>Plain Text:</strong>' . "\n";
		echo '-----------------------------------------------------------------' . "\n";
		echo '</pre>' . nl2br( $plaintext ) . "\n";
		
		exit( 0 );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Crunches multiple emails into a single entity
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	private function _crunch_data( $emails )
	{
		$_out			= array();
		$_out['email']	= clone $emails[0];
		
		// --------------------------------------------------------------------------
		
		//	Reset the data
		$_out['email']->email_vars			= array();
		$_out['email']->email_vars['data']	= array();
		
		// --------------------------------------------------------------------------
		
		//	Crunch each email's data into a single item
		$_out['ids']	= array();
		$_out['refs']	= array();
		
		foreach( $emails AS $email ) :
		
			$_out['email']->email_vars['data'][] = $email->email_vars;
			
			// --------------------------------------------------------------------------
			
			//	Store the ID's so we can unqueue them when the main email sends successfully
			$_out['ids'][] = $email->id;
			
			// --------------------------------------------------------------------------
			
			//	Store the ref for this email so we can reset them later (so that all emails
			//	which were crunched together have a common ref)
			
			$_out['refs'][] = $email->ref;
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _is_subscribed( $user_id, $email_type )
	{
		$this->ci->db->where( 'u.id', $user_id );
		$this->ci->db->where( 'u.active', TRUE );
		$this->ci->db->where( 'um.email_' . $email_type, TRUE );
		
		$this->ci->db->join( 'user u', 'u.id = um.user_id' );
		
		return (bool) $this->ci->db->count_all_results( 'user_meta um' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/* !TRACKING */
	
	
	// --------------------------------------------------------------------------
	
	public function track_open( $ref, $guid, $hash )
	{
		$_email = $this->get_by_ref( $ref, $guid, $hash );
		
		if ( $_email && $_email != 'BAD_HASH' ) :
		
			//	Update the read count and a add a track data point
			$this->ci->db->set( 'read_count', 'read_count+1', FALSE );
			$this->ci->db->where( 'id', $_email->id );
			$this->ci->db->update( 'email_queue_archive' );
			
			$this->ci->db->set( 'created', 'NOW()', FALSE );
			$this->ci->db->set( 'email_id', $_email->id );
			$this->ci->db->insert( 'email_queue_track_open' );
			
			return TRUE;
		
		endif;
		
		return FALSE;
	}
}

/* End of file emailer.php */
/* Location: ./application/libraries/emailer.php */