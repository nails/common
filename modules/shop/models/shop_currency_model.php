<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_currency_model.php
 *
 * Description:		This model handles everything to do with currencies
 * 
 **/

class Shop_currency_model extends NAILS_Model
{
	private $table;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Model constructor
	 * 
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		$this->_table = 'shop_currency';
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
	public function create( $data = array(), $return_obj = FALSE )
	{
		if ( $data )
			$this->db->set( $data );
		
		// --------------------------------------------------------------------------
		
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->set( 'created_by', active_user( 'id' ) );
		
		$this->db->insert( $this->_table );
		
		if ( $return_obj ) :
		
			if ( $this->db->affected_rows() ) :
			
				$_id = $this->db->insert_id();
				
				return $this->get_by_id( $_id );
			
			else :
			
				return FALSE;
			
			endif;
		
		else :
		
			return $this->db->affected_rows() ? $this->db->insert_id() : FALSE;
		
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
		if ( ! $data )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->db->set( $data );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->where( 'id', $id );
		$this->db->update( $this->_table );
		
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
		$this->db->where( 'id', $id );
		$this->db->delete( $this->_table );
		
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
		return $this->db->get( $this->_table )->result();
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
		$this->db->where( 'id', $id );
		$_result = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
}

/* End of file  */
/* Location: ./application/models/ */