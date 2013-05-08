<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_order_model.php
 *
 * Description:		This model handles everything to do with orders
 * 
 **/

class Shop_order_model extends NAILS_Model
{
	private $_table;
	private $_table_product;
	
	
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
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Creates a new order object
	 * 
	 * @access public
	 * @param array $basket The basket object to create the order with
	 * @param bool $return_obj Whether to return just the new ID or the full object
	 * @return mixed
	 **/
	public function create( &$basket, $return_obj = FALSE )
	{
		//	Basket has items?
		if ( ! isset( $basket->items ) || ! $basket->items ) :
		
			$this->_set_error( 'Basket is empty.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Is the basket already associated with an order?
		if ( isset( $basket->order_id ) && $basket->order_id ) :
		
			$this->abandon( $basket->order_id );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_order = new stdClass();
		
		//	Generate a reference
		do
		{
			//	Generate the string
			$_order->ref = date( 'Y' ) . '-' . strtoupper( random_string( 'alnum', 8 ) );
			
			//	Test it
			$this->db->where( 'ref', $_order->ref );
			
		} while ( $this->db->count_all_results( 'shop_order' ) );
		
		// --------------------------------------------------------------------------
		
		//	User's IP address
		$_order->ip_address = $this->input->ip_address();
		
		// --------------------------------------------------------------------------
		
		//	Generate a code (used as a secondary verification method)
		$_order->code = $this->input->ip_address() . '|'. time() . '|' . random_string( 'alnum', 15 );
		
		// --------------------------------------------------------------------------
		
		//	Set the user, if not defined, or empty, look for the logged in user
		if ( isset( $basket->personal_details->email ) && $basket->personal_details->email ) :
		
			$_order->user_email			= $basket->personal_details->email;
			$_order->user_first_name	= $basket->personal_details->first_name;
			$_order->user_last_name		= $basket->personal_details->last_name;
			
			//	Double check to make sure this isn't a user we can associate with instead
			$_user = $this->user->get_user_by_email( $_order->user_email );
			
			if ( $_user ) :
			
				$_order->user_id = $_user->id;
			
			endif;
			
			unset( $_user );
		
		elseif ( $this->user->is_logged_in() ) :
		
			$_order->user_id			= active_user( 'id' );
			$_order->user_email			= active_user( 'email' );
			$_order->user_first_name	= active_user( 'first_name' );
			$_order->user_last_name		= active_user( 'last_name' );
		
		else :
		
			$this->_set_error( 'No user to associate order with.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Payment gateway
		$_order->payment_gateway_id = $basket->payment_gateway;
		
		// --------------------------------------------------------------------------
		
		//	Set currency and exchange rates
		$_order->currency_id		= SHOP_USER_CURRENCY_ID;
		$_order->base_currency_id	= SHOP_BASE_CURRENCY_ID;
		$_order->exchange_rate		= SHOP_USER_CURRENCY_EXCHANGE;
		
		// --------------------------------------------------------------------------
		
		//	Shipping details
		if ( $basket->requires_shipping ) :
		
			$_order->requires_shipping	= TRUE;
			$_order->shipping_addressee	= $basket->shipping_details->addressee;
			$_order->shipping_line_1	= $basket->shipping_details->line_1;
			$_order->shipping_line_2	= $basket->shipping_details->line_2;
			$_order->shipping_town		= $basket->shipping_details->town;
			$_order->shipping_postcode	= $basket->shipping_details->postcode;
			$_order->shipping_state		= $basket->shipping_details->state;
			$_order->shipping_country	= $basket->shipping_details->country;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set totals
		$_order->sub_total		= $basket->totals->sub;
		$_order->grand_total	= $basket->totals->grand;
		$_order->taxes			= $basket->totals->tax;
		$_order->shipping		= $basket->totals->shipping;
		
		//	TODO when vouchers are implemented
		//$_order->deductions	= $basket->totals->deductions;
		
		// --------------------------------------------------------------------------
		
		//	Set this data
		$this->db->set( $_order );
		
		// --------------------------------------------------------------------------
		
		//	Set timestamps
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		
		$this->db->insert( 'shop_order' );

		if ( $this->db->affected_rows() ) :
		
			//	Grab the order's ID
			$_order->id = $this->db->insert_id();
			
			//	Add the items
			$_items = array();
			
			foreach( $basket->items AS $item ) :
			
				$_temp					= array();
				$_temp['order_id']		= $_order->id;
				$_temp['product_id']	= $item->id;
				$_temp['quantity']		= $item->quantity;
				$_temp['title']			= $item->title;
				$_temp['price']			= $item->price;
				$_temp['sale_price']	= $item->sale_price;
				$_temp['tax']			= $item->tax;
				$_temp['shipping']		= $item->shipping;
				$_temp['was_on_sale']	= $item->is_on_sale;
				
				$_items[] = $_temp;
				unset( $_temp );
			
			endforeach;
			
			$this->db->insert_batch( 'shop_order_product', $_items );
			
			if ( $this->db->affected_rows() ) :
			
				//	Associate the basket with this order
				$this->basket->add_order_id( $_order->id );
				
				// --------------------------------------------------------------------------
				
				if ( $return_obj ) :
				
					return $this->get_by_id( $_order->id );
				
				else :
				
					return $this->db->insert_id();
				
				endif;
			
			else :
			
				//	Failed to insert products, delete order
				$this->db->where( 'id', $_order->id );
				$this->db->delete( 'shop_order' );
				
				//	Set error message
				$this->_set_error( 'Unable to add products to order, aborting.' );
				
				return FALSE;
			
			endif;
		
		else :
		
			//	Failed to create order
			$this->_set_error( 'An error occurred while creating the order.' );
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
		if ( ! $data )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->db->set( $data );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->where( 'id', $id );
		$this->db->update( 'shop_order' );
		
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
		$this->db->delete( 'shop_order' );
		
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
		$this->db->select( 'o.*' );
		$this->db->select( 'u.email, um.first_name, um.last_name, um.gender, um.profile_img' );
		$this->db->select( 'pg.slug pg_slug, pg.label pg_label, pg.logo pg_logo' );
		$this->db->select( 'oc.code oc_code,oc.symbol oc_symbol, oc.decimal_precision oc_precision,bc.code bc_code,bc.symbol bc_symbol,bc.decimal_precision bc_precision' );
		
		$this->db->join( 'user u', 'u.id = o.user_id', 'LEFT' );
		$this->db->join( 'user_meta um', 'um.user_id = o.user_id', 'LEFT' );
		$this->db->join( 'shop_payment_gateway pg', 'pg.id = o.payment_gateway_id', 'LEFT' );
		$this->db->join( 'shop_currency oc', 'oc.id = o.currency_id', 'LEFT' );
		$this->db->join( 'shop_currency bc', 'bc.id = o.base_currency_id', 'LEFT' );
		
		$_orders = $this->db->get( 'shop_order o' )->result();
		
		foreach ( $_orders AS $order ) :
		
			//	Format order object
			$this->_format_order( $order );
			
			// --------------------------------------------------------------------------
			
			//	Fetch items associated with this order
			$order->items = $this->get_items_for_order( $order->id );
		
		endforeach;
		
		return $_orders;
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
		$this->db->where( 'o.id', $id );
		$_result = $this->get_all();
		
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
	public function get_by_ref( $ref )
	{
		$this->db->where( 'o.ref', $ref );
		$_result = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_items_for_order( $order_id )
	{
		$this->db->select( 'op.id,op.product_id,op.quantity,op.title,op.price,op.sale_price,op.tax,op.shipping' );
		$this->db->select( 'op.was_on_sale,op.processed,op.refunded,op.refunded_date' );
		$this->db->select( 'pt.id pt_id, pt.slug pt_slug, pt.label pt_label, pt.ipn_method pt_ipn_method' );
		
		$this->db->join( 'shop_product p', 'p.id = op.product_id' );
		$this->db->join( 'shop_product_type pt', 'pt.id = p.type_id' );
		
		$this->db->where( 'op.order_id', $order_id );	
		$_items = $this->db->get( 'shop_order_product op' )->result();
		
		foreach ( $_items AS $item ) :
		
			$this->db->where( 'product_id', $item->product_id );
			$item->meta = $this->db->get( 'shop_product_meta' )->row();
			$this->_format_item( $item );
		
		endforeach;
		
		return $_items;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_for_user( $user_id, $email )
	{
		$this->db->where_in( 'o.status', array( 'VERIFIED', 'PENDING' ) );
		$this->db->where( '(o.user_id = ' . $user_id . ' OR o.user_email = \'' . $email . '\')' );
		return $this->get_all();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_items_for_user( $user_id, $email, $type = NULL )
	{
		$this->db->select( 'op.id,op.product_id,op.quantity,op.title,op.price,op.sale_price,op.tax,op.shipping' );
		$this->db->select( 'op.was_on_sale,op.processed,op.refunded,op.refunded_date' );
		$this->db->select( 'pt.id pt_id, pt.slug pt_slug, pt.label pt_label, pt.ipn_method pt_ipn_method' );
		
		$this->db->join( 'shop_order o', 'o.id = op.order_id' );
		$this->db->join( 'shop_product p', 'p.id = op.product_id' );
		$this->db->join( 'shop_product_type pt', 'pt.id = p.type_id' );
		
		$this->db->where( '(o.user_id = ' . $user_id . ' OR o.user_email = \'' . $email . '\')' );
		$this->db->where( 'o.status', 'VERIFIED' );	
		
		if ( $type ) :
		
			if ( is_numeric( $type ) ) :
			
				$this->db->where( 'pt.id', $type );
				
			else :
			
				$this->db->where( 'pt.slug', $type );
			
			endif;
		
		endif;
		
		$_items = $this->db->get( 'shop_order_product op' )->result();
		
		foreach ( $_items AS $item ) :
		
			$this->db->where( 'product_id', $item->product_id );
			$item->meta = $this->db->get( 'shop_product_meta' )->row();
			$this->_format_item( $item );
		
		endforeach;
		
		return $_items;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function abandon( $order_id, $data = array() )
	{
		$data['status'] = 'ABANDONED';
		return $this->update( $order_id, $data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function fail( $order_id, $data = array() )
	{
		$data['status'] = 'FAILED';
		return $this->update( $order_id, $data );
	}
	
	
	
	// --------------------------------------------------------------------------
	
	
	public function verify( $order_id, $data = array() )
	{
		$data['status'] = 'VERIFIED';
		return $this->update( $order_id, $data );	
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function cancel( $order_id, $data = array() )
	{
		$data['status'] = 'CANCELLED';
		return $this->update( $order_id, $data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function process( $order, &$logger = NULL )
	{
		//	Check to see if a logger object has been passed, if not create
		//	a dummy method so we don't get errors
		
		if ( ! method_exists( $logger, 'line' ) ) :
		
			//	It hasn't, define a dummy
			$_logger = function( $line ) {};
			
		else :
		
			$_logger = function( $line ) use ( &$logger) { $logger->line( $line ); };
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If an ID has been passed, look it up
		if ( is_numeric( $order ) ) :
		
			$_logger( 'Looking up order #' . $order );
			$order = $this->get_by_id( $order );
			
			if ( ! $order ) :
			
				$_logger( 'Invalid order ID' );
				$this->_set_error( 'Invalid order ID' );
				return FALSE;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_logger( 'Processing order #' . $order->id );
		
		//	Loop through all the items in the order. If there's a proccessor method
		//	for the object type then begin grouping the products so we can execute
		//	the processor in a oner with all the associated products
		
		$_processors = array();
		
		foreach ( $order->items AS $item ) :
		
			$_logger( 'Processing item #' . $item->id . ': ' . $item->title . ' (' . $item->type->label . ')' );
		
			if ( $item->type->ipn_method && method_exists( $this, '_process_' . $item->type->ipn_method ) ) :
			
				if ( ! isset( $_processors['_process_' . $item->type->ipn_method] ) ) :
				
					$_processors['_process_' . $item->type->ipn_method] = array();
				
				endif;
				
				$_processors['_process_' . $item->type->ipn_method][] = $item;
			
			endif;
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		//	Execute the processors
		if ( $_processors ) :
		
			$_logger( 'Executing processors...' );
			
			foreach ( $_processors AS $method => $products ) :
			
				$_logger( '... ' . $method . '(); with ' . count( $products ) . ' items.' );
				call_user_func_array( array( $this, $method), array( &$_logger, &$products, &$order ) );
			
			endforeach;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _process_download( &$_logger, &$items, &$order )
	{
		//	Generate links for all the items
		$_urls		= array();
		$_expires	= 172800; //	48 hours
		foreach( $items AS $item ) :
		
			$_temp			= new stdClass();
			$_temp->title	= $item->title;
			$_temp->url		= cdn_expiring_url( $item->meta->download_bucket, $item->meta->download_filename, $_expires ) . '&dl=1';
			$_urls[]		= $_temp;
			
			unset( $_temp );
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		//	Send the user an email with the links
		$_logger( 'Sending download email to ' . $order->user->email  . '; email contains ' . count( $_urls ) . ' expiring URLs' );
		
		$this->load->library( 'emailer' );
		
		$_email							= new stdClass();
		$_email->type					= 'shop_product_type_download';
		$_email->to_email				= $order->user->email;
		$_email->data					= array();
		$_email->data['order']			= new stdClass();
		$_email->data['order']->id		= $order->id;
		$_email->data['order']->ref		= $order->ref;
		$_email->data['order']->created	= $order->created;
		$_email->data['expires']		= $_expires;
		$_email->data['urls']			= $_urls;
		
		if ( ! $this->emailer->send( $_email, TRUE ) ) :
		
			//	Email failed to send, alert developers
			$_logger( '!! Failed to send download links, alerting developers' );
			$_logger( implode( "\n", $this->emailer->get_errors() ) );
			
			send_developer_mail( '!! Unable to send download email', 'Unable to send the email with download links to ' . $_email->to_email . '; order: #' . $order->id . "\n\nEmailer errors:\n\n" . print_r( $this->emailer->get_errors(), TRUE ) );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function send_receipt( $order, $logger = NULL )
	{
		//	Check to see if a logger object has been passed, if not create
		//	a dummy method so we don't get errors
		
		if ( ! method_exists( $logger, 'line' ) ) :
		
			//	It hasn't, define a dummy
			$_logger = function( $line ) {};
			
		else :
		
			$_logger = function( $line ) use ( &$logger) { $logger->line( $line ); };
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If an ID has been passed, look it up
		if ( is_numeric( $order ) ) :
		
			$_logger( 'Looking up order #' . $order );
			$order = $this->get_by_id( $order );
			
			if ( ! $order ) :
			
				$_logger( 'Invalid order ID' );
				$this->_set_error( 'Invalid order ID' );
				return FALSE;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->library( 'emailer' );
		
		$_email							= new stdClass();
		$_email->type					= 'shop_receipt';
		$_email->to_email				= $order->user->email;
		$_email->data					= array();
		$_email->data['order']			= $order;
		
		if ( ! $this->emailer->send( $_email, TRUE ) ) :
		
			//	Email failed to send, alert developers
			$_logger( '!! Failed to send receipt, alerting developers' );
			$_logger( implode( "\n", $this->emailer->get_errors() ) );
			
			send_developer_mail( '!! Unable to send receipt email', 'Unable to send the email receipt to ' . $_email->to_email . '; order: #' . $order->id . "\n\nEmailer errors:\n\n" . print_r( $this->emailer->get_errors(), TRUE ) );
			
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _format_order( &$order )
	{
		//	Generic
		$order->id	= (int) $order->id;
		$order->requires_shipping	= (bool) $order->requires_shipping;
		$order->fulfilled	= (bool) $order->fulfilled;
		
		// --------------------------------------------------------------------------
		
		//	User
		$order->user				= new stdClass();
		$order->user->id			= $order->user_id;
		
		if ( $order->user_email ) :
		
			$order->user->email		= $order->user_email;
		
		else :
		
			$order->user->email		= $order->email;
		
		endif;
		
		if ( $order->user_first_name ) :
		
			$order->user->first_name	= $order->user_first_name;
		
		else :
		
			$order->user->first_name	= $order->first_name;
		
		endif;
		
		if ( $order->user_last_name ) :
		
			$order->user->last_name		= $order->user_last_name;
		
		else :
		
			$order->user->last_name		= $order->last_name;
		
		endif;
		
		$order->user->gender		= $order->gender;
		$order->user->profile_img	= $order->profile_img;
		
		unset( $order->user_id );
		unset( $order->user_email );
		unset( $order->user_first_name );
		unset( $order->user_last_name );
		unset( $order->email );
		unset( $order->first_name );
		unset( $order->last_name );
		unset( $order->gender );
		unset( $order->profle_img );
		
		// --------------------------------------------------------------------------
		
		//	Totals
		$order->totals 				= new stdClass();
		$order->totals->sub			= $order->sub_total;
		$order->totals->grand		= $order->grand_total;
		$order->totals->tax			= $order->taxes;
		$order->totals->shipping	= $order->shipping;
		$order->totals->deductions	= $order->deductions;
		$order->totals->fees		= $order->fees_deducted;
		
		unset( $order->sub_total );
		unset( $order->grand_total );
		unset( $order->taxes );
		unset( $order->shipping );
		unset( $order->deductions );
		unset( $order->fees_deducted );
		
		// --------------------------------------------------------------------------
		
		//	Payment gateway
		$order->payment_gateway 		= new stdClass();
		$order->payment_gateway->id		= (int) $order->payment_gateway_id;
		$order->payment_gateway->slug	= $order->pg_slug;
		$order->payment_gateway->label	= $order->pg_label;
		$order->payment_gateway->logo	= $order->pg_logo;
		
		unset( $order->payment_gateway_id );
		unset( $order->pg_slug );
		unset( $order->pg_label );
		unset( $order->pg_logo );
		
		// --------------------------------------------------------------------------
		
		//	Shipping
		$order->shipping_details 			= new stdClass();
		$order->shipping_details->addressee	= $order->shipping_addressee;
		$order->shipping_details->line_1	= $order->shipping_line_1;
		$order->shipping_details->line_2	= $order->shipping_line_2;
		$order->shipping_details->town		= $order->shipping_town;
		$order->shipping_details->postcode	= $order->shipping_postcode;
		$order->shipping_details->state		= $order->shipping_state;
		$order->shipping_details->country	= $order->shipping_country;
		
		unset( $order->shipping_addressee );
		unset( $order->shipping_line_1 );
		unset( $order->shipping_line_2 );
		unset( $order->shipping_town );
		unset( $order->shipping_postcode );
		unset( $order->shipping_state );
		unset( $order->shipping_country );

		
		// --------------------------------------------------------------------------
		
		//	Currencies
		$order->currency					= new stdClass();
		
		$order->currency->order				= new stdClass();
		$order->currency->order->id			= (int) $order->currency_id;
		$order->currency->order->code		= $order->oc_code;
		$order->currency->order->symbol		= $order->oc_symbol;
		$order->currency->order->precision	= $order->oc_precision;
		
		$order->currency->base				= new stdClass();
		$order->currency->base->id			= (int) $order->base_currency_id;
		$order->currency->base->code		= $order->bc_code;
		$order->currency->base->symbol		= $order->bc_symbol;
		$order->currency->base->precision	= $order->bc_precision;

		
		$order->currency->exchange_rate	= (float) $order->exchange_rate;
		
		unset( $order->exchange_rate );
		unset( $order->currency_id );
		unset( $order->oc_code );
		unset( $order->oc_symbol );
		unset( $order->base_currency_id );
		unset( $order->bc_code );
		unset( $order->bc_symbol );
		
		// --------------------------------------------------------------------------
		
		//	Vouchers
		$order->voucher		= new stdClass();
		$order->voucher->id	= (int) $order->voucher_id;
		
		//	TODO
		
		unset( $order->voucher_id );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _format_item( &$item )
	{
		$item->id				= (int) $item->id;
		$item->quantity			= (int) $item->quantity;
		$item->price			= (float) $item->price;
		$item->sale_price		= (float) $item->sale_price;
		$item->tax				= (float) $item->tax;
		$item->shipping			= (float) $item->shipping;
		$item->was_on_sale		= (bool) $item->was_on_sale;
		$item->processed		= (bool) $item->processed;
		$item->refunded			= (bool) $item->refunded;
		
		// --------------------------------------------------------------------------
		
		//	Product type
		$item->type				= new stdClass();
		$item->type->id			= (int) $item->pt_id;
		$item->type->slug		= $item->pt_slug;
		$item->type->label		= $item->pt_label;
		$item->type->ipn_method	= $item->pt_ipn_method;
		
		unset( $item->pt_id );
		unset( $item->pt_slug );
		unset( $item->pt_label );
		unset( $item->pt_ipn_method );
		
		// --------------------------------------------------------------------------
		
		//	Meta
		unset( $item->meta->id );
		unset( $item->meta->product_id );
	}
}

/* End of file shop_order_model.php */
/* Location: ./application/models/shop_order_model.php */