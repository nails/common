<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Event
*
* Description:	A library for creating and reading event objects.
* 
*/

class Event {
	
	private $_ci;
	private $_event_table;
	private	$_error;
	private $_event_type;
	
	
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
		$this->_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Set defaults
		$this->_event_table		= 'event';
		$this->_event_table_ip	= 'event_interested_party';
		$this->_error			= array();
		$this->_event_type		= array();
		
		// --------------------------------------------------------------------------
		
		//	Load helper
		$this->_ci->load->helper( 'event' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Create an event object
	 *
	 * @access	public
	 * @param	string		$type				The type of event to create
	 * @param	int			$created_by			The event creator (NULL == system)
	 * @param	int/array	$interested_party	The ID of an interested aprty (array for multiple interested parties)
	 * @param	mixed		$vars				Any data to store alongside the event object
	 * @param	int			$ref				A numeric reference to store alongside the event (e.g the id of the object the event relates to)
	 * @param	string		$recorded			A strtotime() friendly string of the date to use instead of NOW() for the created date
	 * @return	int or boolean
	 * @author	Pablo
	 **/
	public function create( $type, $created_by, $level = 0, $interested_parties = NULL, $vars = NULL, $ref = NULL, $recorded = NULL )
	{
		//	Admins logged in as people shouldn't be creating events, GHOST MODE, woooooooo
		//	Ghost mode runs on production only, all other environments generate events (for testing)
		
		if ( ENVIRONMENT == 'production' && get_userobject()->was_admin() ) :
		
			return TRUE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( empty( $type ) ) :
		
			$this->_add_error( 'Event type not defined.' );
			return FALSE;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( ! is_string( $type ) ) :
		
			$this->_add_error( 'Event type must be a string.' );
			return FALSE;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Get the event type
		if ( ! isset( $this->_event_type[$type] ) ) :
		
			$this->_ci->db->select( 'id' );
			$this->_ci->db->where( 'id_string', $type );
			$this->_event_type[$type] = $this->_ci->db->get( 'event_type' )->row();
			
			if ( ! $this->_event_type[$type] )
				show_error( 'Unrecognised event type.' );
			
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Prep data
		$_data					= array();
		$_data['type_id']		= $this->_event_type[$type]->id;
		$_data['created_by']	= ( ! $created_by ) ? NULL : $created_by;
		$_data['vars']			= ( $vars ) ? serialize( $vars ) : NULL;
		$_data['ref']			= (int) $ref;
		$_data['ref']			= $_data['ref'] ? $_data['ref'] : NULL;
		$_data['level']			= $level;
		
		// --------------------------------------------------------------------------
		
		$this->_ci->db->set( $_data );
		
		if ( $recorded ) :
		
			$_data['created'] = date( 'Y-m-d H:i:s', strtotime( $recorded ) );
		
		else :
		
			$this->_ci->db->set( 'created', 'NOW()', FALSE );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Create the event
		$this->_ci->db->insert( $this->_event_table );
		
		// --------------------------------------------------------------------------
		
		if ( ! $this->_ci->db->affected_rows() ) :
		
			$this->_add_error( 'Event could not be created' );
			return FALSE;
		
		else :
		
			$_event_id = $this->_ci->db->insert_id();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		/**
		 *	Add the interested parties.
		 *	The creator (if one is defined) will also be added as an interested party
		 *	however it will be immediately marked as read (so as not to generate a
		 *	notification badge for them.
		 * 
		 **/
		
		//	Prep the $_data array
		$_data = array();
		
		if ( $created_by ) :
		
			$_data[] = array(
				'event_id'	=> $_event_id,	
				'user_id'	=> $created_by,
				'is_read'	=> TRUE
			);
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Add the other interested parties (if any)
		
		if ( $interested_parties !== NULL ) :
		
			if ( is_numeric( $interested_parties ) )
				$interested_parties = array( $interested_parties );
			
			// --------------------------------------------------------------------------
			
			foreach( $interested_parties AS $ip ) :
			
				//	Don't add the creator as an interested party
				if ( $ip == $created_by )
					continue;
					
				// --------------------------------------------------------------------------
				
				$_data[] = array(
					'event_id'	=> $_event_id,	
					'user_id'	=> $ip,
					'is_read'	=> FALSE
				);
			
			endforeach;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $_data ) :
		
			//	Attempt to add interested parties
			$this->_ci->db->insert_batch( $this->_event_table_ip, $_data );
			
			if ( $this->_ci->db->affected_rows() ) :
			
				//	All good! Return the new event ID
				return $_event_id;
			
			else :
			
				$this->_add_error( 'Interested parties failed to add, event not created' );
				
				//	Roll back the event
				$this->_ci->db->where( 'id', $_event_id );
				$this->_ci->db->delete( $this->_event_table );
				
				return FALSE;
			
			endif;
			
		else :
		
			//	No interested parties, so simply return the event ID
			return $_event_id;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Return result
		return TRUE;
			
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Destroy an event object
	 *
	 * @access	public
	 * @param	int		$id		The ID of the event object to destroy
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function destroy( $id )
	{
		if ( empty( $id ) ) :
		
			$this->_add_error( 'Event ID not defined.' );
			return FALSE;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Perform delete
		$this->_ci->db->where( 'id', $id );	
		$this->_ci->db->delete( $this->_event_table );
		
		// --------------------------------------------------------------------------
		
		//	Spit back result
		if  ( $this->_ci->db->affected_rows() ) :
		
			return TRUE;
		
		else :
		
			$this->_add_error( 'Event failed to delete' );
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get all event objects
	 *
	 * @access	public
	 * @param	array	$limit	An optional limit value
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_all( $limit = NULL )
	{
		//	Fetch all objects from the table
		$this->_ci->db->select( 'e.id, e.type_id, et.type_id_string, et.type_name, et.type_description, e.created_by, um.first_name, um.last_name, um.profile_img, e.vars, e.created' );
		$this->_ci->db->select( 'UNIX_TIMESTAMP( e.created ) created_time, eip.user_id interested_party, eip.is_read, e.level' );
		
		$this->_ci->db->join( 'event_type et', 'e.type_id = et.id', 'LEFT' );
		$this->_ci->db->join( 'event_interested_party eip', 'e.id = eip.event_id', 'LEFT' );
		$this->_ci->db->join( 'user_meta um', 'um.user_id = e.created_by', 'LEFT' );
		$this->_ci->db->join( 'user u', 'u.id = um.user_id', 'LEFT' );
		
		if ( is_array( $limit ) )
			$this->_ci->db->order_by( $limit[0], $limit[1] );
		
		$this->_ci->db->order_by( 'e.created', 'DESC' );
		$this->_ci->db->order_by( 'e.level', 'DESC' );
		
		$_events = $this->_ci->db->get( $this->_event_table . ' e' )->result();
		
		// --------------------------------------------------------------------------
		
		//	Prep the output. Loop the results and organise into single events with
		//	interested parties as a sub-array. This method only requires a single
		//	query to the DB rather than one for each returned event.
		
		$_out					= array();
		$_created_parts_keys	= array( 'year', 'month', 'day' );
		
		foreach( $_events AS $event ) :
		
			if ( isset( $_out[ $event->id ] ) ) :
			
				//	Object has already been created, add this interested party
				//	Store interested parties using their user_id as the key so
				//	it can be easily referenced.
				
				$_out[ $event->id ]['interested_parties'][$event->interested_party] = array(
					'user_id' => $event->interested_party,
					'is_read' => (bool) $event->is_read
				);
			
			else :
			
				//	Not yet set, define the base array
				
				$_out[ $event->id ] = array(
					'id'					=> $event->id,
					'type'					=> new stdClass(),
					'creator'				=> new stdClass(),
					'vars'					=> unserialize( $event->vars ),
					'created'				=> $event->created,
					'created_time'			=> $event->created_time,
					'created_parts'			=> array_combine( $_created_parts_keys, explode( '|', date( 'Y|F|d', strtotime( $event->created ) ) ) ),
					'level'					=> $event->level,
					
					//	Store interested parties using their user_id as the key so
					//	it can be easily referenced.
					
					'interested_parties'	=>	array(
						$event->interested_party => array(
							'user_id' => $event->interested_party,
							'is_read' => (bool) $event->is_read
						)
					),
				
				);
				
				$_out[ $event->id ]['creator']->id			= $event->type_id;
				$_out[ $event->id ]['creator']->first_name	= $event->first_name;
				$_out[ $event->id ]['creator']->last_name	= $event->last_name;
				$_out[ $event->id ]['creator']->profile_img	= $event->profile_img;
				
				$_out[ $event->id ]['type']->id				= $event->type_id;
				$_out[ $event->id ]['type']->id_string		= $event->type_id_string;
				$_out[ $event->id ]['type']->name			= $event->type_name;
				$_out[ $event->id ]['type']->description	= $event->type_description;
			
			endif;
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		//	Reset the array indexes
		$_out = array_values( $_out );
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get a particular event
	 *
	 * @access	public
	 * @param	int		$id		the ID of the event to fetch
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_by_id( $id )
	{
		$this->_ci->db->where( 'e.id', $id );
		$_event = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( $_event ) :
		
			return $_event[0];
		
		else :
					
			$this->_add_error( 'No event by that ID (' . $id . ').' );
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch event objects of a particular type
	 *
	 * @access	public
	 * @param	string	$type	The Type of event to fetch
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_by_type( $type )
	{
		if ( is_numeric( $type ) ) :
		
			$this->_ci->db->where( 'et.id', $type );
		
		else :
		
			$this->_ci->db->where( 'et.id_string', $type );
		
		endif;
		
		return $this->get_all();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch event objects of [or not of] a particular type
	 *
	 * @access	public
	 * @param	array	$filter		The types of event to include/exclude
	 * @param	string	$method		The method of search, either include or exclude
	 * @param	array	$limit		An optional limit to pass to get_all()
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_by_types( $filter = NULL, $method = 'include', $limit = NULL )
	{
		if ( $filter && $method == 'include' ) :
		
			$this->_ci->db->where_in( 'et.id_string', $filter );
		
		elseif ( $filter && $method == 'exclude' ) :
		
			$this->_ci->db->where_not_in( 'et.id_string', $filter );
		
		elseif ( $filter ):
		
			$this->_add_error( 'Invalid method' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return $this->get_all( $limit );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch event objects which pertain to an interested party
	 *
	 * @access	public
	 * @param	int		$id			The ID of the interested party
	 * @param	array	$filter		The types of event to include
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_for_interested_party( $id, $level = 0, $limit = 150, $show_unread = TRUE )
	{
		$this->_ci->db->where( 'eip.user_id', $id );
		
		// --------------------------------------------------------------------------
		
		//	Define the level threshold
		$this->_ci->db->where( 'e.level >=', $level );
		
		// --------------------------------------------------------------------------
		
		//	Include unread?
		if ( ! $show_unread ) :
		
			$this->_ci->db->where( 'eip.is_read', FALSE );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return $this->get_all( $limit );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get count of events
	 *
	 * @access	public
	 * @param	int		$id			The ID of the interested party
	 * @param	array	$filter		The types of event to include
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_count_for_interested_party( $type = 'notifications', $id, $level = 1, $limit = 150, $show_unread = TRUE )
	{
	
		if ( $type == 'notifications' ) :
			
			$result = $this->get_for_interested_party( $id, $level = 1, $limit = 150, $show_unread = FALSE ); 

			return count( $result );
		
		elseif ( $type == 'messages' ) :
		
			$_count = 0;
			
			// --------------------------------------------------------------------------
			
			//	Fetch 'did_post_message' events which are of interest to this user which have not been read
			$_event_types = array(
				'did_post_message', 'did_post_interview', 'did_request_connection', 'did_authorise_connection'
			);
			$this->_ci->db->where_in( 'e.type', $_event_types );
			$result = $this->get_for_interested_party( $id, 0, 150, FALSE );

			// --------------------------------------------------------------------------
				
			return count( $result );
		
		endif;

	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch event objects created by a particular user
	 *
	 * @access	public
	 * @param	int		$id		The Id of the user who created the objects
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_by_user( $user_id )
	{
		$this->_ci->db->where( 'e.created_by', $user_id );
		return $this->get_all();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch event objects created on or between a particular date(s)
	 *
	 * @access	public
	 * @param	string	$start	The start date
	 * @param	string	$end	The end date
	 * @param	array	$opts	An array defining a user_id and/or type to restrict the lookup by (i.e all events created between X and Y by user X of type 'test')
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_by_date( $start, $end = NULL, $opts = NULL )
	{	
		//	Set range
		if ( $end ) :
			
			$start	= strtotime( $start );
			
			// --------------------------------------------------------------------------
			
			//	Alter 'special' cases a little
			if ( strtolower( $end ) == 'today' || strtolower( $end ) == 'yesterday' ) :
			
				$end	= mktime( 23, 59, 59,	date( 'm', strtotime( $end ) ), date( 'd', strtotime( $end ) ), date( 'Y', strtotime( $end ) ) );
			
			else:
			
				$end	= strtotime( $end );
			
			endif;
			
			// --------------------------------------------------------------------------
				
			$this->_ci->db->where( 'e.created >= ',	$start,	FALSE );
			$this->_ci->db->where( 'e.created <= ',	$end,	FALSE );
			
		else :
		
			$start	= strtotime( $start );
			$start	= mktime(0,	 0,	 0,		date( 'm', $start ), date( 'd', $start ), date( 'Y', $start ) );
			$end	= mktime(23, 59, 59,	date( 'm', $start ), date( 'd', $start ), date( 'Y', $start ) );
			
			// --------------------------------------------------------------------------
			
			$this->_ci->db->where( 'e.created >= ',	$start,	FALSE );
			$this->_ci->db->where( 'e.created <= ',	$end,	FALSE );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set options (allows the developer to specify events created by a certain
		//	user of a certain type within a specific date range
		
		if ( isset( $opts['user_id'] ) )
			$this->_ci->db->where( 'user_id', $opts['user_id'] );
			
		if ( isset( $opts['type'] ) )
			$this->_ci->db->where( 'type', $opts['type'] );
		
		// --------------------------------------------------------------------------
		
		//	Execute query
		return $this->get_all();
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Mark events for a particular user as read
	 *
	 * @access	public
	 * @param	int		$user_id	The user ID to update
	 * @param	array	$event_ids	An array of event IDs to specifically mark as read, defaults to *
	 * @return	array
	 * @author	Pablo
	 **/
	public function mark_read( $user_id, $event_ids = NULL )
	{
		//	Admins logged in as people shouldn't be marking events as read, GHOST MODE, woooooooo
		//	Ghost mode runs on production only, all other environments behave as normal (for testing)
		
		if ( ENVIRONMENT == 'production' && get_userobject()->was_admin() ) :
		
			return TRUE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $event_ids ) :
		
			if ( is_array( $event_ids ) ) :
			
				$this->_ci->db->where_in( 'event_id', $event_ids );
			
			else :
			
				$this->_ci->db->where( 'event_id', $event_ids );
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->_ci->db->where( 'user_id', $user_id );
		
		// --------------------------------------------------------------------------
		
		$this->_ci->db->set( 'is_read', TRUE );
		$this->_ci->db->update( 'event_interested_party' );
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Mark events of a particular type for a particular user as read
	 *
	 * @access	public
	 * @param	int		$user_id	The user ID to update
	 * @param	array	$types		An array of event IDs to specifically mark as read, defaults to *
	 * @return	array
	 * @author	Pablo
	 **/
	public function mark_type_read( $user_id, $types = NULL )
	{
		//	Admins logged in as people shouldn't be marking events as read, GHOST MODE, woooooooo
		//	Ghost mode runs on production only, all other environments behave as normal (for testing)
		
		if ( ENVIRONMENT == 'production' && get_userobject()->was_admin() ) :
		
			return TRUE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $types ) :
		
			if ( is_array( $types ) ) :
			
				$this->_ci->db->where_in( 'e.id_string', $types );
			
			else :
			
				if ( is_numeric( $types ) ) :
				
					$this->_ci->db->where( 'et.id', $types );
					
				else :
				
					$this->_ci->db->where( 'et.id_string', $types );
				
				endif;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->_ci->db->where( 'e.id = eip.event_id' );
		
		// --------------------------------------------------------------------------
		
		$this->_ci->db->where( 'eip.user_id', $user_id );
		
		// --------------------------------------------------------------------------
		
		$this->_ci->db->set( 'eip.is_read', TRUE );
		$this->_ci->db->update( 'event_interested_party eip, event e' );
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Return the error array
	 *
	 * @access	public
	 * @param	none
	 * @return	array
	 * @author	Pablo
	 **/
	public function errors()
	{
		return $this->_error;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Set an error
	 *
	 * @access	private
	 * @param	string	$error the error to add to the $_error array
	 * @return	void
	 * @author	Pablo
	 **/
	private function _add_error( $error )
	{
		$this->_error[] = $error;
	}

}

/* End of file event.php */
/* Location: ./application/libraries/event.php */