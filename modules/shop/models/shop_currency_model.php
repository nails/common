<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_currency_model.php
 *
 * Description:		This model handles everything to do with currencies
 * 
 **/

/**
 * OVERLOADING NAILS'S MODELS
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

class NAILS_Shop_currency_model extends NAILS_Model
{
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
		
		$this->db->insert( 'shop_currency' );
		
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
		$this->db->update( 'shop_currency' );
		
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
		$this->db->delete( 'shop_currency' );
		
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
	public function get_all( $only_active = TRUE )
	{
		if ( $only_active ) :

			$this->db->where( 'c.is_active', TRUE );

		endif;

		$this->db->order_by( 'c.code' );

		// --------------------------------------------------------------------------

		return $this->db->get( 'shop_currency c' )->result();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches all objects
	 * 
	 * @access public
	 * @param none
	 * @return array
	 **/
	public function get_all_flat( $only_active = TRUE )
	{
		$_currencies = $this->get_all( $only_active );
		$_out = array();

		foreach ( $_currencies AS $currency ) :

			$_out[$currency->id] = $currency->code . ' - ' . $currency->label;

		endforeach;

		return $_out;
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
		$this->db->where( 'c.id', $id );
		$_result = $this->get_all( FALSE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's code
	 * 
	 * @access public
	 * @param string $code The code of the object to fetch
	 * @return	stdClass
	 **/
	public function get_by_code( $code )
	{
		$this->db->where( 'c.code', $code );
		$_result = $this->get_all( FALSE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}


	// --------------------------------------------------------------------------


	public function set_active_currencies( $ids )
	{
		if ( ! is_array( $ids ) || ! $ids ) :

			$this->_set_error( 'At least one currency is required to be active.' );
			return FALSE;

		endif;

		$this->db->set( 'is_active', FALSE );
		$this->db->update( 'shop_currency' );

		if ( $this->db->affected_rows() ) :

			$this->db->set( 'is_active', TRUE );
			$this->db->where_in( 'id', $ids );
			$this->db->update( 'shop_currency' );

			if ( $this->db->affected_rows() ) :

				return TRUE;
			
			else :

				$this->_set_error( 'Unable to enable currencies' );
				return FALSE;

			endif;

		else :

			$this->_set_error( 'Unable to disabled all currencies' );
			return FALSE;

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S MODELS
 * 
 * The following block of code makes it simple to extend one of the core shop
 * models. Some might argue it's a little hacky but it's a simple 'fix'
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_CURRENCY_MODEL' ) ) :

	class Shop_currency_model extends NAILS_Shop_currency_model
	{
	}

endif;

/* End of file  */
/* Location: ./application/models/ */