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
	private $_personal_details;
	private $_payment_gateway;
	private $_shipping_method;
	private $_shipping_details;
	private $_order_id;
	private $_voucher_code;
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
		
		$this->_personal_details	= $this->session->userdata( $this->sess_var . '_pd' );
		
		if ( ! $this->_personal_details ) :
		
			$this->_personal_details				= new stdClass();
			$this->_personal_details->first_name	= '';
			$this->_personal_details->last_name		= '';
			$this->_personal_details->email			= '';
		
		endif;
		
		// --------------------------------------------------------------------------

		$this->_payment_gateway		= (int) $this->session->userdata( $this->sess_var . '_pg' );

		// --------------------------------------------------------------------------

		$this->_shipping_method	= $this->session->userdata( $this->sess_var . '_sm' );
		
		$this->load->model( 'shop/shop_shipping_model', 'shipping' );

		if ( ! $this->_shipping_method ) :

			$this->_shipping_method = $this->shipping->get_default_id();

		endif;


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

		// --------------------------------------------------------------------------

		$this->_order_id			= (int) $this->session->userdata( $this->sess_var . '_oi' );

		// --------------------------------------------------------------------------

		//	Handle voucher
		$this->_voucher_code		= $this->session->userdata( $this->sess_var . '_vc' );

		//	Check voucher is still valid
		if ( $this->_voucher_code ) :

			$this->load->model( 'shop/shop_voucher_model', 'voucher' );
			$_voucher = $this->voucher->validate( $this->_voucher_code );

			if ( ! $_voucher ) :

				$this->_voucher_code = FALSE;
				$this->remove_voucher();

			else :

				//	Apply the voucher object
				$this->_voucher_code = $_voucher;

			endif;

		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_basket()
	{
		$this->load->model( 'shop/shop_product_model', 'product' );

		// --------------------------------------------------------------------------

		$_basket						= new stdClass();
		$_basket->items					= array();
		$_basket->totals				= new stdClass();
		$_basket->totals->shipping		= 0.00;
		$_basket->totals->sub			= 0.00;
		$_basket->totals->tax_shipping	= 0.00;
		$_basket->totals->tax_items		= 0.00;
		$_basket->totals->grand			= 0.00;
		$_basket->discount				= new stdClass;
		$_basket->discount->shipping	= 0.00;
		$_basket->discount->items		= 0.00;
		$_basket->not_available			= array();
		$_basket->quantity_adjusted		= array();
		$_basket->requires_shipping		= FALSE;
		$_basket->personal_details		= $this->_personal_details;
		$_basket->shipping_method		= $this->_shipping_method;
		$_basket->shipping_details		= $this->_shipping_details;
		$_basket->payment_gateway		= $this->_payment_gateway;
		$_basket->order_id				= $this->_order_id;
		$_basket->voucher				= $this->_voucher_code;
		
		$_not_available					= array();

		//	Variable to track the amount of a discount which has been used
		$_discount_total				= 0;
		
		// --------------------------------------------------------------------------
		
		foreach ( $this->_items AS $basket_key => $item ) :
		
			//	Fetch details about product and check availability
			$_product = $this->product->get_by_id( $item->product_id );
			
			//	Fetch shipping costs for this product
			if ( $_product->type->requires_shipping ) :

				$_product->shipping = $this->shipping->get_price_for_product( $_product->id, $_basket->shipping_method );

			else :

				$_product->shipping						= new stdClass();
				$_product->shipping->price				= 0;
				$_product->shipping->price_additional	= 0;
				$_product->shipping->tax_rate			= 0;


			endif;

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
				$_item->shipping	= $_product->shipping;
				
				// --------------------------------------------------------------------------
				
				//	Work out currency conversion rate from the default currency to the user's
				//	preferred currency.

				//	TODO

				$_conversion_rate = 1;
				
				// --------------------------------------------------------------------------
				
				//	Calculate shipping costs
				if ( $_item->type->requires_shipping ) :
				
					//	TODO - calculate the shipping cost
					//	TODO - calculate shipping taxes

					if ( $_item->quantity == 1 ) :

						//	Just one item, flat rate
						$_shipping = $_item->shipping->price;

					else :

						//	Multiple items, first item costs price, then the rest are charged at price_additional
						$_shipping = $_item->shipping->price + ($_item->shipping->price_additional * ( $_item->quantity-1 ) );

					endif;

					//	Shipping tax
					$_shipping_tax = $_shipping * $_item->shipping->tax_rate;

					// --------------------------------------------------------------------------
					
					//	At least one item in this basket requires shipping, change the flag
					$_basket->requires_shipping = TRUE;
					
				else :
				
					$_shipping		= 0;
					$_shipping_tax	= 0;
				
				endif;
				
				$_item->shipping				= $_shipping;
				$_item->shipping_tax			= $_shipping_tax;
				
				// --------------------------------------------------------------------------
				
				//	Calculate Total
				if ( $_item->is_on_sale ):
				
					$_item->total = ( $_conversion_rate * $_item->sale_price ) * $_item->quantity;
					
				else :
				
					$_item->total = ( $_conversion_rate * $_item->price ) * $_item->quantity;
				
				endif;

				// --------------------------------------------------------------------------

				//	Calculate TAX
				$_item->tax_rate		= new stdClass();
				$_item->tax_rate->id	= $_product->tax->id;
				$_item->tax_rate->label	= round_to_precision( 100 * $_product->tax->rate, 2 ) . '%';
				$_item->tax_rate->rate	= round_to_precision( $_product->tax->rate, 2 );
				$_item->tax				= round_to_precision( ( $_conversion_rate * ( $_item->total ) ) * $_product->tax->rate, 2 );

				// --------------------------------------------------------------------------

				//	Is there a voucher which applies to products, or a particular product type?
				$_discount = 0;
				if ( $_basket->voucher && $_basket->voucher->discount_application == 'PRODUCT_TYPES' ) :

					if ( $_basket->voucher->discount_application == 'PRODUCTS' || $_basket->voucher->product_type_id == $_item->type->id ) :

						if ( $_basket->voucher->discount_type == 'PERCENTAGE' ) :

							//	Simple percentage, just knock that off the product total
							//	and be done with it.

							$_discount = ( $_item->total + $_item->tax ) * ( $_basket->voucher->discount_value / 100 );
							$_basket->discount->items += $_discount;

						elseif ( $_basket->voucher->discount_type == 'AMOUNT' ) :

							//	Specific amount, if the product price is greater than the discount amount
							//	then simply knock that off the price, if it's less then  keep track of what's
							//	been deducted

							if ( $_discount_total < $_basket->voucher->discount_value ) :

								if ( $_basket->voucher->discount_value > ( $_item->total + $_item->tax ) ) :

									//	There'll be some of the discount left over after it's been applied
									//	to this product, work out how much

									$_discount = $_basket->voucher->discount_value - ( $_item->total + $_item->tax );

								else :

									//	There'll be no discount left over, use the whole thing! ($)($)($)
									$_discount = $_basket->voucher->discount_value;

								endif;

								$_discount_total += $_discount;
								$_basket->discount->items += $_discount;

							endif;

						endif;

					endif;

				endif;
				
				// --------------------------------------------------------------------------
				
				//	Update basket totals
				$_basket->totals->sub			+= $_item->total;
				$_basket->totals->shipping		+= $_item->shipping;
				$_basket->totals->tax_shipping	+= $_item->shipping_tax;
				$_basket->totals->tax_items		+= $_item->tax;
				$_basket->totals->grand			+= $_item->tax + $_item->shipping_tax + $_item->total + $_item->shipping - $_discount;

				// --------------------------------------------------------------------------

				$_basket->items[] = $_item;
			
			else :
			
				//	No longer available
				$_not_available[] = $basket_key;
			
			endif;
					
		endforeach;

		// --------------------------------------------------------------------------

		//	If there's a free-shipping threshold, and it's been reached, apply a discount to the shipping
		if ( shop_setting( 'free_shipping_threshold' ) && $_basket->requires_shipping ) :

			if ( $_basket->totals->sub >= shop_setting( 'free_shipping_threshold' ) ) :

				$_basket->discount->shipping	= ( $_basket->totals->shipping + $_basket->totals->tax_shipping );
				$_basket->totals->grand			-=$_basket->discount->shipping;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Apply any vouchers which apply to just shipping
		if ( $_basket->voucher && $_basket->voucher->discount_application == 'SHIPPING' && shop_setting( 'free_shipping_threshold' ) > $_basket->totals->sub ) :
		
			if ( $_basket->voucher->discount_type == 'PERCENTAGE' ) :

				//	Simple percentage, just knock that off the shipping total
				//	and be done with it.

				$_discount = ( $_basket->totals->shipping + $_basket->totals->tax_shipping ) * ( $_basket->voucher->discount_value / 100 );
				$_basket->discount->shipping += $_discount;

			elseif ( $_basket->voucher->discount_type == 'AMOUNT' ) :

				//	Specific amount, if the product price is greater than the discount amount
				//	then simply knock that off the price, if it's less then  just discount the
				//	total cost of shipping

				if ( $_basket->voucher->discount_value > ( $_basket->totals->shipping + $_basket->totals->tax_shipping ) ) :

					//	There'll be some of the discount left over after it's been applied
					//	to this product, work out how much

					$_discount = $_basket->voucher->discount_value - ( $_basket->totals->shipping + $_basket->totals->tax_shipping );
					$_discount = $_basket->voucher->discount_value - $_discount;

				else :

					//	There'll be no discount left over, use the whole thing! ($)($)($)
					$_discount = $_basket->voucher->discount_value;

				endif;

				$_basket->discount->shipping += $_discount;

			endif;

			// --------------------------------------------------------------------------

			//	Recalculate grand total
			$_basket->totals->grand = $_basket->totals->sub + $_basket->totals->shipping + $_basket->totals->tax_shipping + $_basket->totals->tax_items - $_basket->discount->shipping;

		elseif ( $_basket->voucher && $_basket->voucher->discount_application == 'SHIPPING' && shop_setting( 'free_shipping_threshold' ) < $_basket->totals->sub ) :

			//	Voucher no longer makes sense. Remove it.
			$this->_voucher_code		= FALSE;
			$_basket->voucher			= FALSE;
			$_basket->voucher_removed	= 'Your order qualifies for free shipping. Voucher no longer needed!';
			$this->remove_voucher();

		endif;


		if ( $_basket->voucher && $_basket->voucher->discount_application == 'SHIPPING' && ! $_basket->requires_shipping ) :

			//	Voucher no longer makes sense. Remove it.
			$this->_voucher_code		= FALSE;
			$_basket->voucher			= FALSE;
			$_basket->voucher_removed	= 'Your order does not contian any items which require shipping, voucher not needed!';
			$this->remove_voucher();

		endif;

		// --------------------------------------------------------------------------

		//	Apply any vouchers which apply to just items
		if ( $_basket->voucher && $_basket->voucher->discount_application == 'PRODUCTS' ) :

			if ( $_basket->voucher->discount_type == 'PERCENTAGE' ) :

				//	Simple percentage, just knock that off the shipping total
				//	and be done with it.

				$_discount = ( $_basket->totals->sub + $_basket->totals->tax_items ) * ( $_basket->voucher->discount_value / 100 );
				$_basket->discount->items += $_discount;

			elseif ( $_basket->voucher->discount_type == 'AMOUNT' ) :

				//	Specific amount, if the product price is greater than the discount amount
				//	then simply knock that off the price, if it's less then  just discount the
				//	total cost of shipping

				if ( $_basket->voucher->discount_value > ( $_basket->totals->sub + $_basket->totals->tax_items ) ) :

					//	There'll be some of the discount left over after it's been applied
					//	to this product, work out how much

					$_discount = $_basket->voucher->discount_value - ( $_basket->totals->sub + $_basket->totals->tax_items );
					$_discount = $_basket->voucher->discount_value - $_discount;

				else :
					
					//	There'll be no discount left over, use the whole thing! ($)($)($)
					$_discount = $_basket->voucher->discount_value;

				endif;

				$_basket->discount->items += $_discount;

			endif;

			// --------------------------------------------------------------------------

			//	Recalculate grand total
			$_basket->totals->grand = $_basket->totals->sub + $_basket->totals->shipping + $_basket->totals->tax_shipping + $_basket->totals->tax_items - $_basket->discount->items;

		endif;

		// --------------------------------------------------------------------------

		//	Apply any vouchers which apply to both shipping and items
		if ( $_basket->voucher && $_basket->voucher->discount_application == 'ALL' ) :

			if ( $_basket->voucher->discount_type == 'PERCENTAGE' ) :

				//	Simple percentage, just knock that off the product and shipping totals

				//	Check free shipping threshold
				if ( $_basket->totals->sub < shop_setting( 'free_shipping_threshold' ) ) :

					$_discount_shipping = ( $_basket->totals->shipping + $_basket->totals->tax_shipping ) * ( $_basket->voucher->discount_value / 100 );
					$_basket->discount->shipping += $_discount_shipping;

				endif;

				$_discount_items = ( $_basket->totals->sub + $_basket->totals->tax_items ) * ( $_basket->voucher->discount_value / 100 );
				$_basket->discount->items += $_discount_items;

			elseif ( $_basket->voucher->discount_type == 'AMOUNT' ) :

				//	Specific amount; if the discount is less than the product total then deduct it from
				//	that and be done, otherwise zero the products and deduct the remaining amount from the shipping

				//	If the voucher is a giftcard then the dicount value should be the remaining balance
				if ( $_basket->voucher->type == 'GIFT_CARD' ) :

					$_discount_value = $_basket->voucher->gift_card_balance;

				else :

					$_discount_value = $_basket->voucher->discount_value;

				endif;

				if ( $_discount_value <= ( $_basket->totals->sub + $_basket->totals->tax_items ) ) :

					//	Discount is the same as, or less than, the product total, just apply discount to the products
					$_basket->discount->items = $_discount_value;

				else :
					
					//	The discount is greater than the products, apply to the shipping too
					$_basket->discount->items = $_basket->totals->sub + $_basket->totals->tax_items;
					$_discount = $_discount_value - $_basket->discount->items;

					if ( $_discount <= ( $_basket->totals->shipping + $_basket->totals->tax_shipping ) ) :

						//	Discount is less than, or the same as, the total of shipping - just remove it all
						$_basket->discount->shipping = $_discount;

					else :

						//	Discount is greater than the shipping amount, just discount the whole shipping price
						$_basket->discount->shipping = ( $_basket->totals->shipping + $_basket->totals->tax_shipping );

					endif;

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	Recalculate grand total
			$_basket->totals->grand = $_basket->totals->sub + $_basket->totals->shipping + $_basket->totals->tax_shipping + $_basket->totals->tax_items - $_basket->discount->shipping - $_basket->discount->items;

		endif;
		
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
		
		$this->basket = $_basket;
		return $this->basket;
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
		$this->remove_voucher();
		$this->remove_personal_details();
		$this->remove_payment_gateway();
		$this->remove_shipping_method();
		$this->remove_shipping_details();
		$this->remove_order_id();
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
	
	
	public function add_personal_details( $details )
	{
		$this->session->set_userdata( $this->sess_var . '_pd', $details );
	}


	// --------------------------------------------------------------------------


	public function remove_personal_details()
	{
		$this->session->unset_userdata( $this->sess_var . '_pd' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function add_shipping_method( $method )
	{
		$this->session->set_userdata( $this->sess_var . '_sm', $method );
	}


	// --------------------------------------------------------------------------


	public function remove_shipping_method()
	{
		$this->session->unset_userdata( $this->sess_var . '_sm' );
	}


	// --------------------------------------------------------------------------


	public function add_shipping_details( $details )
	{
		$this->session->set_userdata( $this->sess_var . '_sd', $details );
	}


	// --------------------------------------------------------------------------


	public function remove_shipping_details()
	{
		$this->session->unset_userdata( $this->sess_var . '_sd' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function add_payment_gateway( $payment_gateway )
	{
		$this->session->set_userdata( $this->sess_var . '_pg', $payment_gateway );
	}


	// --------------------------------------------------------------------------


	public function remove_payment_gateway()
	{
		$this->session->unset_userdata( $this->sess_var . '_pg' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function add_order_id( $order_id )
	{
		$this->session->set_userdata( $this->sess_var . '_oi', $order_id );
	}


	// --------------------------------------------------------------------------


	public function remove_order_id()
	{
		$this->session->unset_userdata( $this->sess_var . '_oi' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function add_voucher( $voucher_code )
	{
		$this->session->set_userdata( $this->sess_var . '_vc', $voucher_code );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function remove_voucher()
	{
		$this->session->unset_userdata( $this->sess_var . '_vc' );
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
		
		if ( $this->user->is_logged_in() ) :
		
			$_data = array( 'shop_basket' => serialize( $this->_items ) );
			$this->user->update( active_user( 'id' ), $_data );
		
		endif;
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