<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Event
*
* Description:	A library for creating and reading event objects.
*
*/

class Event {

	private $_ci;
	private $db;
	private $_event_table;
	private	$_error;
	private $_event_type;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 **/
	public function __construct()
	{
		$this->_ci	=& get_instance();
		$this->db	=& $this->_ci->db;

		// --------------------------------------------------------------------------

		//	Set defaults
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
	 * @param	mixed		$data				Any data to store alongside the event object
	 * @param	int			$ref				A numeric reference to store alongside the event (e.g the id of the object the event relates to)
	 * @param	string		$recorded			A strtotime() friendly string of the date to use instead of NOW() for the created date
	 * @return	int or boolean
	 **/
	public function create( $type, $created_by = NULL, $level = 0, $interested_parties = NULL, $data = NULL, $ref = NULL, $recorded = NULL )
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

			$this->db->select( 'id' );
			$this->db->where( 'slug', $type );
			$this->_event_type[$type] = $this->db->get( NAILS_DB_PREFIX . 'event_type' )->row();

			if ( ! $this->_event_type[$type] )
				show_error( 'Unrecognised event type.' );


		endif;

		// --------------------------------------------------------------------------

		//	Prep created by
		$created_by = (int) $created_by;
		if ( ! $created_by ) :

			$created_by = active_user( 'id' ) ? (int) active_user( 'id' ) : NULL;

		endif;

		// --------------------------------------------------------------------------

		//	Prep data
		$_data					= array();
		$_data['type_id']		= (int) $this->_event_type[$type]->id;
		$_data['created_by']	= $created_by;
		$_data['url']			= uri_string();
		$_data['data']			= ( $data ) ? serialize( $data ) : NULL;
		$_data['ref']			= (int) $ref;
		$_data['ref']			= $_data['ref'] ? $_data['ref'] : NULL;
		$_data['level']			= $level;

		// --------------------------------------------------------------------------

		$this->db->set( $_data );

		if ( $recorded ) :

			$_data['created'] = date( 'Y-m-d H:i:s', strtotime( $recorded ) );

		else :

			$this->db->set( 'created', 'NOW()', FALSE );

		endif;

		// --------------------------------------------------------------------------

		//	Create the event
		$this->db->insert( NAILS_DB_PREFIX . 'event' );

		// --------------------------------------------------------------------------

		if ( ! $this->db->affected_rows() ) :

			$this->_add_error( 'Event could not be created' );
			return FALSE;

		else :

			$_event_id = $this->db->insert_id();

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
			$this->db->insert_batch( NAILS_DB_PREFIX . 'event_interested_party', $_data );

			if ( $this->db->affected_rows() ) :

				//	All good! Return the new event ID
				return $_event_id;

			else :

				$this->_add_error( 'Interested parties failed to add, event not created' );

				//	Roll back the event
				$this->db->where( 'id', $_event_id );
				$this->db->delete( NAILS_DB_PREFIX . 'event' );

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
	 **/
	public function destroy( $id )
	{
		if ( empty( $id ) ) :

			$this->_add_error( 'Event ID not defined.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Perform delete
		$this->db->where( 'id', $id );
		$this->db->delete( NAILS_DB_PREFIX . 'event' );

		// --------------------------------------------------------------------------

		//	Spit back result
		if  ( $this->db->affected_rows() ) :

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
	 **/
	public function get_all( $order = NULL, $limit = NULL, $where = NULL, $include_interested_parties = FALSE )
	{
		//	Fetch all objects from the table
		$this->db->select( 'e.*, et.slug type_slug, et.label type_label, et.description type_description, et.ref_join_table, et.ref_join_column' );
		$this->db->select( 'ue.email,u.first_name,u.last_name,u.profile_img,u.gender' );

		//	Set Order
		if ( is_array( $order ) ) :

			$this->db->order_by( $order[0], $order[1] );

		else :

			$this->db->order_by( 'e.created', 'DESC' );

		endif;

		// --------------------------------------------------------------------------

		//	Set Limit
		if ( is_array( $limit ) ) :

			$this->db->limit( $limit[0], $limit[1] );

		endif;

		// --------------------------------------------------------------------------

		//	Build conditionals
		$this->_getcount_common( $where );

		// --------------------------------------------------------------------------

		$_events = $this->db->get( NAILS_DB_PREFIX . 'event' . ' e' )->result();

		// --------------------------------------------------------------------------

		//	Prep the output. Loop the results and organise into single events with
		//	interested parties as a sub-array. This method only requires a single
		//	query to the DB rather than one for each returned event.

		$_created_parts_keys	= array( 'year', 'month', 'day' );

		foreach( $_events AS $event ) :

			$this->_format_event_object( $event) ;

			// --------------------------------------------------------------------------

			if ( $include_interested_parties ) :

				$event->interested_parties = $this->_get_interested_parties_for_event( $event->id );

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		return $_events;
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts the total amount of events for a partricular query/search key. Essentially performs
	 * the same query as $this->get_all() but without limiting.
	 *
	 * @access	public
	 * @param	string	$where	An array of where conditions
	 * @param	mixed	$search	A string containing the search terms
	 * @return	int
	 *
	 **/
	public function count_all( $where = NULL )
	{
		$this->_getcount_common( $where );

		// --------------------------------------------------------------------------

		//	Execute Query
		return $this->db->count_all_results( NAILS_DB_PREFIX . 'event' . ' e' );
	}


	// --------------------------------------------------------------------------


	private function _getcount_common( $where = NULL, $search = NULL )
	{
		$this->db->join( NAILS_DB_PREFIX . 'event_type et', 'e.type_id = et.id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = e.created_by', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );

		// --------------------------------------------------------------------------

		//	Set Where
		if ( $where ) :

			$this->db->where( $where );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Get a particular event
	 *
	 * @access	public
	 * @param	int		$id		the ID of the event to fetch
	 * @return	array
	 **/
	public function get_by_id( $id )
	{
		$this->db->where( 'e.id', $id );
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
	 **/
	public function get_by_type( $type )
	{
		if ( is_numeric( $type ) ) :

			$this->db->where( 'et.id', $type );

		else :

			$this->db->where( 'et.slug', $type );

		endif;

		return $this->get_all();
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch event objects created by a particular user
	 *
	 * @access	public
	 * @param	int		$id		The Id of the user who created the objects
	 * @return	array
	 **/
	public function get_by_user( $user_id )
	{
		$this->db->where( 'e.created_by', $user_id );
		return $this->get_all();
	}


	// --------------------------------------------------------------------------


	public function get_types()
	{
		$this->db->order_by( 'label,slug' );
		return $this->db->get( NAILS_DB_PREFIX . 'event_type' )->result();
	}


	// --------------------------------------------------------------------------


	public function get_types_flat()
	{
		$_types = $this->get_types();

		$_out = array();

		foreach ( $_types AS $type ) :

			$_out[$type->id] = $type->label ? $type->label : title_case( str_replace( '_', ' ', $type->slug ) );

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Return the error array
	 *
	 * @access	public
	 * @param	none
	 * @return	array
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
	 **/
	private function _add_error( $error )
	{
		$this->_error[] = $error;
	}


	// --------------------------------------------------------------------------


	protected function _format_event_object( &$obj )
	{
		//	Ints
		$obj->id	= (int) $obj->id;
		$obj->level	= (int) $obj->level;
		$obj->ref	= NULL === $obj->ref ? NULL : (int) $obj->ref;

		//	Type
		$obj->type					= new stdClass();
		$obj->type->id				= $obj->type_id;
		$obj->type->slug			= $obj->type_slug;
		$obj->type->label			= $obj->type_label;
		$obj->type->description		= $obj->type_description;
		$obj->type->ref_join_table	= $obj->ref_join_table;
		$obj->type->ref_join_column	= $obj->ref_join_column;


		unset( $obj->type_id );
		unset( $obj->type_slug );
		unset( $obj->type_label );
		unset( $obj->type_description );
		unset( $obj->ref_join_table );
		unset( $obj->ref_join_column );

		//	Data
		$obj->data	= unserialize( $obj->data );

		//	User
		$obj->user				= new stdClass();
		$obj->user->id			= $obj->created_by;
		$obj->user->email		= $obj->email;
		$obj->user->first_name	= $obj->first_name;
		$obj->user->last_name	= $obj->last_name;
		$obj->user->profile_img	= $obj->profile_img;
		$obj->user->gender		= $obj->gender;

		unset( $obj->created_by );
		unset( $obj->email );
		unset( $obj->first_name );
		unset( $obj->last_name );
		unset( $obj->profile_img );
		unset( $obj->gender );
	}

}

/* End of file event.php */
/* Location: ./application/libraries/event.php */