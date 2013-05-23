<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_voucher_model.php
 *
 * Description:		This model handles everything to do with vouchers
 * 
 **/

class Shop_voucher_model extends NAILS_Model
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
		
		if ( $this->user->is_logged_in() ) :
		
			$this->db->set( 'created_by', active_user( 'id' ) );
			$this->db->set( 'modified_by', active_user( 'id' ) );
		
		endif;
		
		$this->db->insert( 'shop_voucher' );
		
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
		if ( $this->user->is_logged_in() ) :
		
			$this->db->set( 'modified_by', active_user( 'id' ) );
		
		endif;
		$this->db->where( 'id', $id );
		$this->db->update( 'shop_voucher' );
		
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
		$_data = array( 'is_deleted' => TRUE );
		return $this->update( $id, $_data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Permenantly deletes an existing object
	 * 
	 * @access public
	 * @param int $id The ID of the object to delete
	 * @return bool
	 **/
	public function destroy( $id )
	{
		$this->db->where( 'id', $id );
		$this->db->delete( 'shop_voucher' );
		return (bool) $this->db->affected_rows();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Recovers a deleted object
	 * 
	 * @access public
	 * @param int $id The ID of the object to recover
	 * @return bool
	 **/
	public function recover( $id )
	{
		$_data = array( 'is_deleted' => FALSE );
		return $this->update( $id, $_data );
	}


	// --------------------------------------------------------------------------


	public function redeem( $voucher, $order )
	{
		if ( is_numeric( $voucher ) ) :

			$voucher = $this->get_by_id( $voucher );

		endif;

		if ( is_numeric( $order ) ) :

			$this->load->model( 'shop/shop_order_model', 'order' );
			$order = $this->order->get_by_id( $order );

		endif;

		// --------------------------------------------------------------------------

		switch( $voucher->type ) :

			case 'GIFT_CARD' :	$this->_redeem_gift_card( $voucher, $order );	break;
			default:

				//	Bump the use count
				$this->db->set( 'last_used', 'NOW()', FALSE );
				$this->db->set( 'modified', 'NOW()', FALSE );
				$this->db->set( 'use_count', 'use_count+1', FALSE );

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _redeem_gift_card( $voucher, $order )
	{
		if ( $order->requires_shipping ) :

			if ( shop_setting( 'free_shipping_threshold' ) <= $order->totals->sub ) :

				//	The order qualifies for free shipping, ignore the discount
				//	given in discount->shipping

				$_spend = $order->discount->items;

			else :

				//	The order doesn't qualify for free shipping, include the
				//	discount given in discount->shipping

				$_spend = $order->discount->items + $order->discount->shipping;

			endif;
		else: 

			//	The discount given by the giftcard is that of discount->items
			$_spend = $order->discount->items;

		endif;
		
		//	Bump the use count
		$this->db->set( 'last_used', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->set( 'use_count', 'use_count+1', FALSE );

		// --------------------------------------------------------------------------

		//	Alter the available balance

		$this->db->set( 'gift_card_balance', 'gift_card_balance-' . $_spend , FALSE );

		$this->db->where( 'id', $voucher->id );
		$this->db->update( 'shop_voucher' );
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

			$this->db->where( 'v.is_active', TRUE );

		endif;

		$this->db->where( 'v.is_deleted', FALSE );

		$_vouchers = $this->db->get( 'shop_voucher v' )->result();

		foreach ( $_vouchers AS $voucher ) :

			$this->_format_voucher( $voucher );

		endforeach;

		// --------------------------------------------------------------------------

		return $_vouchers;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's code
	 * 
	 * @access public
	 * @param int $code The code of the object to fetch
	 * @return	stdClass
	 **/
	public function get_by_code( $code )
	{
		$this->db->where( 'v.code', $code );
		$_result = $this->get_all( FALSE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
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
		$this->db->where( 'v.id', $id );
		$_result = $this->get_all( FALSE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Validate a voucher
	 * 
	 * @access public
	 * @param int $id The voucher code to validate
	 * @return	boolean
	 **/
	public function validate( $code, $basket = NULL )
	{
		if ( ! $code ) :

			$this->_set_error( 'No voucher code supplied.' );
			return FALSE;

		endif;

		$_voucher = $this->get_by_code( $code );
		
		if ( ! $_voucher ) :

			$this->_set_error( 'Invalid voucher code.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Voucher exists, now we need to check it's still valid; this depends on the
		//	type of vocuher it is.

		//	Firstly, check common things

		//	Is active?
		if ( ! $_voucher->is_active ) :

			$this->_set_error( 'Invalid voucher code.' );
			return FALSE;

		endif;

		//	Voucher started?
		if ( strtotime( $_voucher->valid_from ) > time() ) :

			$this->_set_error( 'Voucher is not available yet! This voucher becomes available on the ' . date( 'jS F Y \a\t H:i', strtotime( $_voucher->valid_from ) ) . '.' );
			return FALSE;

		endif;

		//	Voucher expired?
		if ( ! is_null( $_voucher->valid_to ) && $_voucher->valid_to != '0000-00-00 00:00:00' && strtotime( $_voucher->valid_to ) < time() ) :

			$this->_set_error( 'Voucher has expired.' );
			return FALSE;

		endif;

		//	Is this a shipping voucher being applied to an order with no shippable items?
		if ( ! is_null( $basket ) && $_voucher->discount_application == 'SHIPPING' && ! $basket->requires_shipping ) :

			$this->_set_error( 'Your order does not contian any items which require shipping, voucher not needed!' );
			return FALSE;

		endif;

		//	Is there a shipping threshold? If so, and the voucher is type SHIPPING
		//	and the threshold has been reached then prevent it being added as it
		//	doesn't make sense.

		if ( ! is_null( $basket ) && shop_setting( 'free_shipping_threshold' ) && $_voucher->discount_application == 'SHIPPING' ) :

			if ( $basket->totals->sub >= shop_setting( 'free_shipping_threshold' ) ) :

				$this->_set_error( 'Your order qualifies for free shipping, voucher not needed!' );
				return FALSE;

			endif;

		endif;


		//	If the voucher applies to a particular product type, check the absket contains
		//	that product, otherwise it doesn't make sense to add it

		if ( ! is_null( $basket ) && $_voucher->discount_application == 'PRODUCT_TYPES' ) :

			$_matched = FALSE;

			foreach ( $basket->items AS $item ) :

				if ( $item->type->id == $_voucher->product_type_id ) :

					$_matched = TRUE;
				break;

				endif;

			endforeach;

			if ( ! $_matched ) :

				$this->_set_error( 'This voucher does not apply to any items in your basket.' );
				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Check custom voucher type conditions
		if ( method_exists( $this, '_validate_' . strtolower( $_voucher->type ) ) ) :

			return $this->{'_validate_' . strtolower( $_voucher->type )}( $_voucher );

		else :

			$this->_set_error( 'This voucher is corrupt and cannot be used just now.' );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Obligatory validation method for 'normal' vouchers
	 * 
	 * @access public
	 * @param stdClass $voucher The voucher we're validating
	 * @return	mixed
	 **/
	protected function _validate_normal( &$voucher )
	{
		//	So long as the voucher is within date limits then it's valid
		//	If we got here then it's valid and has not expired

		return $voucher;
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks that a limited use voucher has not been used too many times
	 * 
	 * @access public
	 * @param stdClass $voucher The voucher we're validating
	 * @return	mixed
	 **/
	protected function _validate_limited_use( &$voucher )
	{
		if ( $voucher->use_count < $voucher->limited_use_limit ) :

			return $voucher;

		else :

			$this->_set_error( 'Voucher has exceeded its use limit.' );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks that a gift card has available balance
	 * 
	 * @access public
	 * @param stdClass $voucher The voucher we're validating
	 * @return	mixed
	 **/

	protected function _validate_gift_card( &$voucher )
	{
		if ( $voucher->gift_card_balance ) :

			return $voucher;

		else :

			$this->_set_error( 'Gift card has no available balance.' );
			return FALSE;

		endif;
	}



	// --------------------------------------------------------------------------


	protected function _format_voucher( &$voucher )
	{
		$voucher->id				= (int) $voucher->id;
		$voucher->limited_use_limit	= (int) $voucher->limited_use_limit;

		$voucher->discount_value	= (float) $voucher->discount_value;
		$voucher->gift_card_balance	= (float) $voucher->gift_card_balance;

		$voucher->is_active			= (bool) $voucher->is_active;
		$voucher->is_deleted		= (bool) $voucher->is_deleted;
	}
}

/* End of file shop_voucher_model.php */
/* Location: ./application/models/shop_voucher_model.php */