<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_basket_model.php
 *
 * Description:		This model handles everything to do with the user's basket
 * 
 **/

class Shop_basket_model extends NAILS_Model
{
	private $_items;
	private $_payment_gateway;
	private $_shipping_details;
	private $_sess_var;
	
	
	// --------------------------------------------------------------------------
	
	
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		$this->sess_var = 'shop_basket';
		
		// --------------------------------------------------------------------------
		
		$this->_items = $this->session->userdata( $this->sess_var );
		
		if ( ! $this->_items ) :
		
			//	Check the active_user data in case it exists there
			$_saved_basket = unserialize( active_user( 'shop_basket' ) );
			
			if ( $_saved_basket ) :
			
				$this->_items = $_saved_basket;
			
			else :
			
				$this->_items = array();
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->_payment_gateway		= (int) $this->session->userdata( $this->sess_var . '_pg' );
		$this->_shipping_details	= $this->session->userdata( $this->sess_var . '_sd' );
		
		if ( ! $this->_shipping_details ) :
		
			//	Clear addressing as per: http://www.royalmail.com/personal/help-and-support/How-do-I-address-my-mail-correctly
			
			$this->_shipping_details			= new stdClass();
			$this->_shipping_details->addressee	= '';	//	Named addresse
			$this->_shipping_details->line_1	= '';	//	Building number and street name
			$this->_shipping_details->line_2	= '';	//	Locality name, if required
			$this->_shipping_details->town		= '';	//	Town
			$this->_shipping_details->postcode	= '';	//	Postcode
			$this->_shipping_details->state		= '';	//	State
			$this->_shipping_details->country	= '';	//	Country
		
		endif;

	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_basket()
	{
		$this->load->model( 'shop/shop_product_model', 'product' );
		
		// --------------------------------------------------------------------------
		
		$_basket					= new stdClass();
		$_basket->items				= array();
		$_basket->totals			= new stdClass();
		$_basket->totals->tax		= 0.00;
		$_basket->totals->shipping	= 0.00;
		$_basket->totals->sub		= 0.00;
		$_basket->totals->grand		= 0.00;
		$_basket->not_available		= array();
		$_basket->quantity_adjusted	= array();
		$_basket->requires_shipping	= FALSE;
		$_basket->shipping_details	= $this->_shipping_details;
		$_basket->payment_gateway	= $this->_payment_gateway;
		
		$_not_available				= array();
		
		// --------------------------------------------------------------------------
		
		foreach ( $this->_items AS $basket_key => $item ) :
		
			//	Fetch details about product and check availability
			$_product = $this->product->get_by_id( $item->product_id );
			
			if ( $_product && $_product->is_active && ( is_null( $_product->quantity_available ) || $_product->quantity_available ) ) :
			
				//	Product is still available, calculate all we need to calculate
				//	and format the basket object
				
				//	Do we need to adjust quantities?
				if ( ! is_null( $_product->quantity_available ) && $_product->quantity_available < $item->quantity ) :
				
					$_basket->quantity_adjusted = $_product->title;
				
				endif;
				
				// --------------------------------------------------------------------------
				
				$_item				= new stdClass();
				$_item->id			= $_product->id;
				$_item->title		= $_product->title;
				$_item->type		= $_product->type;
				$_item->tax			= $_product->tax;
				$_item->quantity	= $item->quantity;
				$_item->price		= $_product->price;
				$_item->sale_price	= $_product->sale_price;
				$_item->is_on_sale	= $_product->is_on_sale;
				
				// --------------------------------------------------------------------------
				
				//	Work out currency conversion rate from the default currency to the user's
				//	preferred currency.
				//	TODO
				$_conversion_rate = 1;
				
				// --------------------------------------------------------------------------
				
				//	Calculate shipping costs
				if ( $_item->type->requires_shipping ) :
				
					//	TODO - calculate the shipping cost
					$_shipping = $_conversion_rate * 2.5;
					
					// --------------------------------------------------------------------------
					
					//	At least one item in this basket requires shipping, change the flag
					$_basket->requires_shipping = TRUE;
					
				else :
				
					$_shipping = 0;
				
				endif;
				
				$_item->shipping = number_format( $_shipping, 2 );
				
				// --------------------------------------------------------------------------
				
				//	Calculate Total
				if ( $_item->is_on_sale ):
				
					$_item->total = ( $_conversion_rate * $_item->sale_price ) * $_item->quantity;
					
				else :
				
					$_item->total = ( $_conversion_rate * $_item->price ) * $_item->quantity;
				
				endif;
				
				$_item->total += $_item->shipping;
				
				// --------------------------------------------------------------------------
				
				//	Calculate TAX
				$_item->tax_rate	= round_to_precision( 100 * $_product->tax->rate, 2 ) . '%';
				$_item->tax			= number_format( round_to_precision( ( $_conversion_rate * $_item->total ) * $_product->tax->rate, 2 ), 2 );
				
				// --------------------------------------------------------------------------
				
				//	Update basket totals
				$_basket->totals->tax		+= $_item->tax;
				$_basket->totals->shipping	+= $_item->shipping;
				$_basket->totals->sub		+= $_item->total;
				$_basket->totals->grand		+= $_item->tax + $_item->total + $_item->shipping;
				
				// --------------------------------------------------------------------------
				
				$_basket->items[] = $_item;
			
			else :
			
				//	No longer available
				$_not_available[] = $basket_key;
			
			endif;
					
		endforeach;
		
		// --------------------------------------------------------------------------
		
		//	Remove any unavailable items
		if ( $_not_available ) :
		
			foreach ( $_not_available AS $basket_key ) :
			
				$_basket->not_available[] = $this->_items[$basket_key]->title;
				unset( $this->_items[$basket_key] );
				
				// --------------------------------------------------------------------------
				
				$this->_update_session();
			
			endforeach;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Tidy up totals
		$_basket->totals->shipping	= number_format( $_basket->totals->shipping, 2);
		$_basket->totals->tax		= number_format( $_basket->totals->tax, 2);
		$_basket->totals->sub		= number_format( $_basket->totals->sub, 2);
		$_basket->totals->grand		= number_format( $_basket->totals->grand, 2);
		
		// --------------------------------------------------------------------------
		
		//	Update the session
		
		// --------------------------------------------------------------------------
		
		return $_basket;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_basket_count( $respect_quantity = TRUE )
	{
		if ( $respect_quantity ) :
		
			$_count = 0;
			
			foreach ( $this->_items AS $item ) :
			
				$_count += $item->quantity;
			
			endforeach;
			
			return $_count;
		
		else:
		
			return count( $this->_items );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function add( $product_id, $quantity = NULL )
	{
		if ( ! $quantity ) :
		
			$quantity = 1;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->model( 'shop/shop_product_model', 'product' );
		
		//	Check item isn't already in the basket
		$_key = $this->_get_basket_key_by_product_id( $product_id );
		
		// --------------------------------------------------------------------------
		
		if ( $_key !== FALSE ) :
		
			$this->_set_error( 'Item already in the basket.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Check the product ID is valid
		$_product = $this->product->get_by_id( $product_id );
		
		if ( ! $_product ) :
		
			$this->_set_error( 'Invalid Product ID.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Check product is active
		if ( ! $_product->is_active ) :
		
			$this->_set_error( 'Product is not available.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Check quantity is available, if more are being requested, then reduce.
		if ( ! is_null( $_product->quantity_available ) && $quantity > $_product->quantity_available ) :
		
			$quantity = $_product->quantity_available;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	All good, add to basket
		$_temp = new stdClass();
		$_temp->product_id	= $_product->id;
		$_temp->title		= $_product->title;
		$_temp->quantity	= $quantity;
		
		$this->_items[]		= $_temp;
		
		unset( $_temp );
		
		// --------------------------------------------------------------------------
		
		//	Update the session
		$this->_update_session();
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function remove( $product_id )
	{
		$_key = $this->_get_basket_key_by_product_id( $product_id );
		
		// --------------------------------------------------------------------------
		
		if ( $_key !== FALSE ) :
		
			unset( $this->_items[$_key] );
			
			// --------------------------------------------------------------------------
			
			//	Update the session
			$this->_update_session();
			
			// --------------------------------------------------------------------------
			
			return TRUE;
			
		else :
		
			$this->_set_error( 'This item is not in your basket.' );
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	public function destroy()
	{
		$this->_items = array();
		
		// --------------------------------------------------------------------------
		
		//	Update the session
		$this->_update_session();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function increment( $product_id )
	{
		$_key = $this->_get_basket_key_by_product_id( $product_id );
		
		// --------------------------------------------------------------------------
		
		if ( $_key !== FALSE ) :
		
			//	Check we can increment the product
			
			//	TODO
			
			$_can_increment = TRUE;
			
			if ( $_can_increment ) :
			
				//	Increment
				$this->_items[$_key]->quantity++;
				
				// --------------------------------------------------------------------------
				
				//	Update the session
				$this->_update_session();
				
				// --------------------------------------------------------------------------
				
				return TRUE;
			
			else :
			
				$this->_set_error( 'You cannot increment this item any further.' );
				return FALSE;
			
			endif;
		
		else :
		
			$this->_set_error( 'This item is not in your basket.' );
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function decrement( $product_id )
	{
		$_key = $this->_get_basket_key_by_product_id( $product_id );
		
		// --------------------------------------------------------------------------
		
		if ( $_key !== FALSE ) :
		
			//	Check we can decrement the product
			
			//	TODO
			
			$_can_decrement = TRUE;
			
			if ( $_can_decrement ) :
			
				//	Decrement
				$this->_items[$_key]->quantity--;
				
				//	If the new quantity is 0 then remove the item
				if ( $this->_items[$_key]->quantity <= 0 ) :
				
					unset( $this->_items[$_key] );
				
				endif;
				
				// --------------------------------------------------------------------------
				
				//	Update the session
				$this->_update_session();
				
				// --------------------------------------------------------------------------
				
				return TRUE;
			
			else :
			
				$this->_set_error( 'You cannot decrement this item any further.' );
				return FALSE;
			
			endif;
		
		else :
		
			$this->_set_error( 'This item is not in your basket.' );
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function add_shipping_details( $details )
	{
		$this->session->set_userdata( $this->sess_var . '_sd', $details );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function add_payment_gateway( $payment_gateway )
	{
		$this->session->set_userdata( $this->sess_var . '_pg', $payment_gateway );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function is_in_basket( $product_id )
	{
		if ( $this->_get_basket_key_by_product_id( $product_id ) !== FALSE ) :
		
			return TRUE;
		
		else :
		
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _update_session()
	{
		$this->session->set_userdata( $this->sess_var, $this->_items );
	}
	
	// --------------------------------------------------------------------------
	
	
	private function _get_basket_key_by_product_id( $product_id )
	{
		foreach( $this->_items AS $key => $item ) :
		
			if ( $product_id == $item->product_id ) :
			
				return $key;
				break;
			
			endif;
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function __destruct()
	{
		//	If logged in, save the basket to the user's meta data for safe keeping.
		if ( $this->user->is_logged_in() ) :
		
			$_data = array( 'shop_basket' => serialize( $this->_items ) );
			$this->user->update( active_user( 'id' ), $_data );
		
		endif;
	}
}

/* End of file shop_basket_model.php */
/* Location: ./application/models/shop_basket_model.php */