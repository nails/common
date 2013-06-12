<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Model extends CI_Model {

	protected $data;
	protected $user;
	protected $_error = array();
	protected $_table;
	protected $_table_prefix;
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Construct the model
	 *
	 * @access	protected
	 * @param	string	$error	The error message
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct( )
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Ensure models all have access to the NAILS_USR_OBJ if it's defined
		$this->user =& get_userobject();
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Set a generic error
	 *
	 * @access	protected
	 * @param	string	$error	The error message
	 * @return	void
	 * @author	Pablo
	 **/
	protected function _set_error( $error )
	{
		$this->_error[] = $error;
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Get any errors
	 *
	 * @access	public
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_error()
	{
		return $this->_error;
	}


	// --------------------------------------------------------------------------


	/**
	 * Creates a new object
	 * 
	 * @access public
	 * @param array $data The data to create the object with
	 * @param bool $return_obj Whether to return just the new ID or the full object
	 * @return mixed
	 **/
	public function create( $data, $return_object = FALSE )
	{
		if ( ! $this->_table ) :

			show_error( 'Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';
			$_table		= $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------

		if ( $data )
			$this->db->set( $data );
		
		// --------------------------------------------------------------------------
		
		$this->db->set( $_prefix . 'created', 'NOW()', FALSE );
		$this->db->set( $_prefix . 'modified', 'NOW()', FALSE );

		if ( $this->user->is_logged_in() ) :

			$this->db->set( $_prefix . 'created_by', active_user( 'id' ) );
			$this->db->set( $_prefix . 'modified_by', active_user( 'id' ) );

		endif;
		
		$this->db->insert( $_table );
		
		if ( $this->db->affected_rows() ) :

			$_id =  $this->db->insert_id();
		
			if ( $return_object ) :
				
				return $this->get_by_id( $_id );
			
			else :
			
				return $_id;
			
			endif;
		
		else :
		
			return FALSE;
		
		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Updates an existing object
	 * 
	 * @access public
	 * @param int $id The ID of the object to update
	 * @param array $data The data to update the object with
	 * @return bool
	 **/
	public function update( $id, $data = array() )
	{
		if ( ! $this->_table ) :

			show_error( 'Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';
			$_table		= $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $data )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->db->set( $data );
		$this->db->set( $_prefix . 'modified', 'NOW()', FALSE );

		if ( $this->user->is_logged_in() ) :

			$this->db->set( $_prefix . 'modified_by', active_user( 'id' ) );

		endif;

		$this->db->where( $_prefix . 'id', $id );
		$this->db->update( $_table );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Deletes an existing object
	 * 
	 * @access public
	 * @param int $id The ID of the object to delete
	 * @return bool
	 **/
	public function delete( $id )
	{
		if ( ! $this->_table ) :

			show_error( 'Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';
			$_table		= $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( $_prefix . 'id', $id );
		$this->db->delete( $_table );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches all objects
	 * 
	 * @access public
	 * @param none
	 * @return array
	 **/
	public function get_all()
	{
		if ( ! $this->_table ) :

			show_error( 'Table variable not set' );

		else :

			$_table		= $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------
		
		return $this->db->get( $_table )->result();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's ID
	 * 
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @return	stdClass
	 **/
	public function get_by_id( $id )
	{
		if ( ! $this->_table ) :

			show_error( 'Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( $_prefix . 'id', $id );
		$_result = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result ) :

			return FALSE;

		endif;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's slug
	 * 
	 * @access public
	 * @param int $slug The slug of the object to fetch
	 * @return	stdClass
	 **/
	public function get_by_slug( $slug )
	{
		if ( ! $this->_table ) :

			show_error( 'Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( $_prefix . 'slug', $slug );
		$_result = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result ) :

			return FALSE;

		endif;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
}

/* End of file CORE_NAILS_Model.php */
/* Location: ./core/CORE_NAILS_Model.php */