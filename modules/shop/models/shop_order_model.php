<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_order_model.php
 *
 * Description:		This model handles everything to do with orders
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_order_model extends NAILS_Model
{
	protected $_table;
	protected $_table_product;


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

		} while ( $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_order' ) );

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
			$_user = $this->user_model->get_by_email( $_order->user_email );

			if ( $_user ) :

				$_order->user_id = $_user->id;

			endif;

			unset( $_user );

		elseif ( $this->user_model->is_logged_in() ) :

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
		if ( $basket->payment_gateway ) :

			$_order->payment_gateway_id = $basket->payment_gateway;

		endif;

		// --------------------------------------------------------------------------

		//	Set voucher ID
		if ( $basket->voucher ) :

			$_order->voucher_id = $basket->voucher->id;

		endif;

		// --------------------------------------------------------------------------

		//	Set currency and exchange rates
		$_order->currency_id		= SHOP_USER_CURRENCY_ID;
		$_order->base_currency_id	= SHOP_BASE_CURRENCY_ID;
		$_order->exchange_rate		= SHOP_USER_CURRENCY_BASE_EXCHANGE;

		// --------------------------------------------------------------------------

		//	Shipping details
		if ( $basket->requires_shipping ) :

			$_order->requires_shipping	= TRUE;
			$_order->shipping_method_id	= $basket->shipping_method;
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
		$_order->shipping_total				= $basket->totals->shipping;
		$_order->shipping_total_render		= $basket->totals->shipping_render;
		$_order->sub_total					= $basket->totals->sub;
		$_order->sub_total_render			= $basket->totals->sub_render;
		$_order->tax_shipping				= $basket->totals->tax_shipping;
		$_order->tax_shipping_render		= $basket->totals->tax_shipping_render;
		$_order->tax_items					= $basket->totals->tax_items;
		$_order->tax_items_render			= $basket->totals->tax_items_render;
		$_order->discount_shipping			= $basket->discount->shipping;
		$_order->discount_shipping_render	= $basket->discount->shipping_render;
		$_order->discount_items				= $basket->discount->items;
		$_order->discount_items_render		= $basket->discount->items_render;
		$_order->grand_total				= $basket->totals->grand;
		$_order->grand_total_render			= $basket->totals->grand_render;

		// --------------------------------------------------------------------------

		//	Set this data
		$this->db->set( $_order );

		// --------------------------------------------------------------------------

		//	Set timestamps
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );

		$this->db->insert( NAILS_DB_PREFIX . 'shop_order' );

		if ( $this->db->affected_rows() ) :

			//	Grab the order's ID
			$_order->id = $this->db->insert_id();

			//	Add the items
			$_items = array();

			foreach( $basket->items AS $item ) :

				$_temp							= array();
				$_temp['order_id']				= $_order->id;
				$_temp['product_id']			= $item->id;
				$_temp['quantity']				= $item->quantity;
				$_temp['title']					= $item->title;
				$_temp['price']					= $item->price;
				$_temp['price_render']			= $item->price_render;
				$_temp['sale_price']			= $item->sale_price;
				$_temp['sale_price_render']		= $item->sale_price_render;
				$_temp['tax']					= $item->tax;
				$_temp['tax_render']			= $item->tax_render;
				$_temp['shipping']				= $item->shipping;
				$_temp['shipping_render']		= $item->shipping_render;
				$_temp['shipping_tax']			= $item->shipping_tax;
				$_temp['shipping_tax_render']	= $item->shipping_tax_render;
				$_temp['total']					= $item->total;
				$_temp['total_render']			= $item->total_render;
				$_temp['tax_rate_id']			= $item->tax_rate->id;
				$_temp['was_on_sale']			= $item->is_on_sale;

				if ( isset( $item->extra_data ) && $item->extra_data ) :

					$_temp['extra_data'] = serialize( $item->extra_data );

				endif;

				$_items[] = $_temp;
				unset( $_temp );

			endforeach;

			$this->db->insert_batch( 'shop_order_product', $_items );

			if ( $this->db->affected_rows() ) :

				//	Associate the basket with this order
				$this->shop_basket_model->add_order_id( $_order->id );

				// --------------------------------------------------------------------------

				if ( $return_obj ) :

					return $this->get_by_id( $_order->id );

				else :

					return $_order->id;

				endif;

			else :

				//	Failed to insert products, delete order
				$this->db->where( 'id', $_order->id );
				$this->db->delete( NAILS_DB_PREFIX . 'shop_order' );

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
		$this->db->update( NAILS_DB_PREFIX . 'shop_order' );

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
		$this->db->delete( NAILS_DB_PREFIX . 'shop_order' );

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
	public function get_all( $order = NULL, $limit = NULL, $where = NULL, $search = NULL )
	{
		$this->db->select( 'o.*' );
		$this->db->select( 'ue.email, u.first_name, u.last_name, u.gender, u.profile_img,ug.id user_group_id,ug.label user_group_label' );
		$this->db->select( 'pg.slug pg_slug, pg.label pg_label, pg.logo pg_logo' );
		$this->db->select( 'oc.code oc_code,oc.symbol oc_symbol, oc.decimal_precision oc_precision,bc.code bc_code,bc.symbol bc_symbol,bc.decimal_precision bc_precision' );
		$this->db->select( 'v.code v_code,v.label v_label, v.type v_type, v.discount_type v_discount_type, v.discount_value v_discount_value, v.discount_application v_discount_application' );
		$this->db->select( 'v.product_type_id v_product_type_id, v.is_active v_is_active, v.is_deleted v_is_deleted, v.valid_from v_valid_from, v.valid_to v_valid_to' );
		$this->db->select( 'sm.courier sm_courier, sm.method sm_method' );

		// --------------------------------------------------------------------------

		//	Set Order
		if ( is_array( $order ) ) :

			$this->db->order_by( $order[0], $order[1] );

		endif;

		// --------------------------------------------------------------------------

		//	Set Limit
		if ( is_array( $limit ) ) :

			$this->db->limit( $limit[0], $limit[1] );

		endif;

		// --------------------------------------------------------------------------

		//	Build conditionals
		$this->_getcount_orders_common( $where, $search );

		// --------------------------------------------------------------------------

		$_orders = $this->db->get( NAILS_DB_PREFIX . 'shop_order o' )->result();

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
	 * Counts the total amount of orders for a partricular query/search key. Essentially performs
	 * the same query as $this->get_all() but without limiting.
	 *
	 * @access	public
	 * @param	string	$where	An array of where conditions
	 * @param	mixed	$search	A string containing the search terms
	 * @return	int
	 *
	 **/
	public function count_orders( $where = NULL, $search = NULL )
	{
		$this->_getcount_orders_common( $where, $search );

		// --------------------------------------------------------------------------

		//	Execute Query
		return $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_order o' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts the total amount of orders for a partricular query/search key. Essentially performs
	 * the same query as $this->get_all() but without limiting.
	 *
	 * @access	public
	 * @param	string	$where	An array of where conditions
	 * @param	mixed	$search	A string containing the search terms
	 * @return	int
	 *
	 **/
	public function count_unfulfilled_orders( $where = NULL, $search = NULL )
	{
		$this->db->where( 'fulfilment_status', 'UNFULFILLED' );
		$this->db->where( 'status', 'PAID' );

		// --------------------------------------------------------------------------

		//	Execute Query
		return $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_order o' );
	}


	// --------------------------------------------------------------------------


	protected function _getcount_orders_common( $where = NULL, $search = NULL )
	{
		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = o.user_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_group ug', 'ug.id = u.group_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_payment_gateway pg', 'pg.id = o.payment_gateway_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_currency oc', 'oc.id = o.currency_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_currency bc', 'bc.id = o.base_currency_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_voucher v', 'v.id = o.voucher_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_shipping_method sm', 'sm.id = o.shipping_method_id', 'LEFT' );

		// --------------------------------------------------------------------------

		//	Set Where
		if ( $where ) :

			$this->db->where( $where );

		endif;

		// --------------------------------------------------------------------------

		//	Set Search
		if ( $search && is_string( $search ) ) :

			//	Search is a simple string, no columns are being specified to search across
			//	so define a default set to search across

			$search								= array( 'keywords' => $search, 'columns' => array() );
			$search['columns']['id']			= 'o.id';
			$search['columns']['ref']			= 'o.ref';
			$search['columns']['user_id']		= 'o.user_id';
			$search['columns']['user_email']	= 'o.user_email';
			$search['columns']['pp_txn_id']		= 'o.pp_txn_id';

		endif;

		//	If there is a search term to use then build the search query
		if ( isset( $search[ 'keywords' ] ) && $search[ 'keywords' ] ) :

			//	Parse the keywords, look for specific column searches
			preg_match_all('/\(([a-zA-Z0-9\.\- ]+):([a-zA-Z0-9\.\- ]+)\)/', $search['keywords'], $_matches );

			if ( $_matches[1] && $_matches[2] ) :

				$_specifics = array_combine( $_matches[1], $_matches[2] );

			else :

				$_specifics = array();

			endif;

			//	Match the specific labels to a column
			if ( $_specifics ) :

				$_temp = array();
				foreach ( $_specifics AS $col => $value ) :

					if ( isset( $search['columns'][ strtolower( $col )] ) ) :

						$_temp[] = array(
							'cols'	=> $search['columns'][ strtolower( $col )],
							'value'	=> $value
						);

					endif;

				endforeach;
				$_specifics = $_temp;
				unset( $_temp );

				// --------------------------------------------------------------------------

				//	Remove controls from search string
				$search['keywords'] = preg_replace('/\(([a-zA-Z0-9\.\- ]+):([a-zA-Z0-9\.\- ]+)\)/', '', $search['keywords'] );

			endif;

			if ( $_specifics ) :

				//	We have some specifics
				foreach( $_specifics AS $specific ) :

					if ( is_array( $specific['cols'] ) ) :

						$_separator = array_shift( $specific['cols'] );
						$this->db->like( 'CONCAT_WS( \'' . $_separator . '\', ' . implode( ',', $specific['cols'] ) . ' )', $specific['value'] );

					else :

						$this->db->like( $specific['cols'], $specific['value'] );

					endif;

				endforeach;

			endif;


			// --------------------------------------------------------------------------

			if ( $search['keywords'] ) :

				$_where  = '(';

				if ( isset( $search[ 'columns' ] ) && $search[ 'columns' ] ) :

					//	We have some specifics
					foreach( $search[ 'columns' ] AS $col ) :

						if ( is_array( $col ) ) :

							$_separator = array_shift( $col );
							$_where .= 'CONCAT_WS( \'' . $_separator . '\', ' . implode( ',', $col ) . ' ) LIKE \'%' . trim( $search['keywords'] ) . '%\' OR ';

						else :

							$_where .= $col . ' LIKE \'%' . trim( $search['keywords'] ) . '%\' OR ';

						endif;

					endforeach;

				endif;

				$this->db->where( substr( $_where, 0, -3 ) . ')' );

			endif;

		endif;
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
		$this->db->select( 'op.id,op.product_id,op.quantity,op.title,op.price,op.sale_price,op.tax,op.shipping,op.shipping_tax,op.total' );
		$this->db->select( 'op.price_render,op.sale_price_render,op.tax_render,op.shipping_render,op.shipping_tax_render,op.total_render' );
		$this->db->select( 'op.was_on_sale,op.processed,op.refunded,op.refunded_date,op.extra_data' );
		$this->db->select( 'pt.id pt_id, pt.slug pt_slug, pt.label pt_label, pt.ipn_method pt_ipn_method' );
		$this->db->select( 'tr.id tax_rate_id, tr.label tax_rate_label, tr.rate tax_rate_rate' );

		$this->db->join( NAILS_DB_PREFIX . 'shop_product p', 'p.id = op.product_id' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_product_type pt', 'pt.id = p.type_id' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_tax_rate tr', 'tr.id = p.tax_rate_id', 'LEFT' );

		$this->db->where( 'op.order_id', $order_id );
		$_items = $this->db->get( NAILS_DB_PREFIX . 'shop_order_product op' )->result();

		foreach ( $_items AS $item ) :

			$this->db->where( 'product_id', $item->product_id );
			$item->meta = $this->db->get( NAILS_DB_PREFIX . 'shop_product_meta' )->row();
			$this->_format_item( $item );

		endforeach;

		return $_items;
	}


	// --------------------------------------------------------------------------


	public function get_for_user( $user_id, $email )
	{
		$this->db->where_in( 'o.status', array( 'PAID', 'UNPAID' ) );
		$this->db->where( '(o.user_id = ' . $user_id . ' OR o.user_email = \'' . $email . '\')' );
		return $this->get_all();
	}


	// --------------------------------------------------------------------------




	// --------------------------------------------------------------------------


	public function get_items_for_user( $user_id, $email, $type = NULL )
	{
		$this->db->select( 'op.id,op.product_id,op.quantity,op.title,op.price,op.sale_price,op.tax,op.shipping,op.shipping_tax,op.total' );
		$this->db->select( 'op.price_render,op.sale_price_render,op.tax_render,op.shipping_render,op.shipping_tax_render,op.total_render' );
		$this->db->select( 'op.was_on_sale,op.processed,op.refunded,op.refunded_date,op.extra_data' );
		$this->db->select( 'pt.id pt_id, pt.slug pt_slug, pt.label pt_label, pt.ipn_method pt_ipn_method' );
		$this->db->select( 'tr.id tax_rate_id, tr.label tax_rate_label, tr.rate tax_rate_rate' );

		$this->db->join( NAILS_DB_PREFIX . 'shop_order o', 'o.id = op.order_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_product p', 'p.id = op.product_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_product_type pt', 'pt.id = p.type_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_tax_rate tr', 'tr.id = p.tax_rate_id', 'LEFT' );

		$this->db->where( '(o.user_id = ' . $user_id . ' OR o.user_email = \'' . $email . '\')' );
		$this->db->where( 'o.status', 'PAID' );

		if ( $type ) :

			if ( is_numeric( $type ) ) :

				$this->db->where( 'pt.id', $type );

			else :

				$this->db->where( 'pt.slug', $type );

			endif;

		endif;

		$_items = $this->db->get( NAILS_DB_PREFIX . 'shop_order_product op' )->result();

		foreach ( $_items AS $item ) :

			$this->db->where( 'product_id', $item->product_id );
			$item->meta = $this->db->get( NAILS_DB_PREFIX . 'shop_product_meta' )->row();
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


	public function paid( $order_id, $data = array() )
	{
		$data['status'] = 'PAID';
		return $this->update( $order_id, $data );
	}


	// --------------------------------------------------------------------------


	public function unpaid( $order_id, $data = array() )
	{
		$data['status'] = 'UNPAID';
		return $this->update( $order_id, $data );
	}


	// --------------------------------------------------------------------------


	public function cancel( $order_id, $data = array() )
	{
		$data['status'] = 'CANCELLED';
		return $this->update( $order_id, $data );
	}


	// --------------------------------------------------------------------------


	public function fulfil( $order_id, $data = array() )
	{
		$data['fulfilment_status']	= 'FULFILLED';
		$data['fulfilled']			= date( 'Y-m-d H:i:s' );

		return $this->update( $order_id, $data );
	}


	// --------------------------------------------------------------------------


	public function unfulfil( $order_id, $data = array() )
	{
		$data['fulfilment_status']	= 'UNFULFILLED';
		$data['fulfilled']			= NULL;

		return $this->update( $order_id, $data );
	}

	// --------------------------------------------------------------------------

	public function pending( $order_id, $data = array() )
	{
		$data['status'] = 'PENDING';
		return $this->update( $order_id, $data );
	}


	// --------------------------------------------------------------------------


	public function process( $order )
	{
		//	If an ID has been passed, look it up
		if ( is_numeric( $order ) ) :

			_LOG( 'Looking up order #' . $order );
			$order = $this->get_by_id( $order );

			if ( ! $order ) :

				_LOG( 'Invalid order ID' );
				$this->_set_error( 'Invalid order ID' );
				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		_LOG( 'Processing order #' . $order->id );

		//	Loop through all the items in the order. If there's a proccessor method
		//	for the object type then begin grouping the products so we can execute
		//	the processor in a oner with all the associated products

		$_processors = array();

		foreach ( $order->items AS $item ) :

			_LOG( 'Processing item #' . $item->id . ': ' . $item->title . ' (' . $item->type->label . ')' );

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

			_LOG( 'Executing processors...' );

			foreach ( $_processors AS $method => $products ) :

				_LOG( '... ' . $method . '(); with ' . count( $products ) . ' items.' );
				call_user_func_array( array( $this, $method), array( &$products, &$order ) );

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Has the order been fulfilled? If all products in the order are processed
		//	then consider this order fulfilled.

		$this->db->where( 'order_id', $order->id );
		$this->db->where( 'processed', FALSE );

		if ( ! $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_order_product' ) ) :

			//	No unprocessed items, consider order FULFILLED
			$this->fulfil( $order->id );

		endif;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	protected function _process_download( &$items, &$order )
	{
		//	Generate links for all the items
		$_urls		= array();
		$_ids		= array();
		$_expires	= 172800; //	48 hours

		foreach( $items AS $item ) :

			$_temp			= new stdClass();
			$_temp->title	= $item->title;
			$_temp->url		= cdn_expiring_url( $item->meta->download_id, $_expires ) . '&dl=1';
			$_urls[]		= $_temp;

			$_ids[]			= $item->id;

			unset( $_temp );

		endforeach;

		// --------------------------------------------------------------------------

		//	Send the user an email with the links
		_LOG( 'Sending download email to ' . $order->user->email  . '; email contains ' . count( $_urls ) . ' expiring URLs' );

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

		if ( $this->emailer->send( $_email, TRUE ) ) :

			//	Mark items as processed
			$this->db->set( 'processed', TRUE );
			$this->db->where_in( 'id', $_ids );
			$this->db->update( NAILS_DB_PREFIX . 'shop_order_product' );

		else :

			//	Email failed to send, alert developers
			_LOG( '!! Failed to send download links, alerting developers' );
			_LOG( implode( "\n", $this->emailer->get_errors() ) );

			send_developer_mail( 'Unable to send download email', 'Unable to send the email with download links to ' . $_email->to_email . '; order: #' . $order->id . "\n\nEmailer errors:\n\n" . print_r( $this->emailer->get_errors(), TRUE ) );

		endif;
	}


	// --------------------------------------------------------------------------


	public function send_receipt( $order )
	{
		//	If an ID has been passed, look it up
		if ( is_numeric( $order ) ) :

			_LOG( 'Looking up order #' . $order );
			$order = $this->get_by_id( $order );

			if ( ! $order ) :

				_LOG( 'Invalid order ID' );
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
			_LOG( '!! Failed to send receipt, alerting developers' );
			_LOG( implode( "\n", $this->emailer->get_errors() ) );

			send_developer_mail( 'Unable to send receipt email', 'Unable to send the email receipt to ' . $_email->to_email . '; order: #' . $order->id . "\n\nEmailer errors:\n\n" . print_r( $this->emailer->get_errors(), TRUE ) );

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	public function send_order_notification( $order )
	{
		//	If an ID has been passed, look it up
		if ( is_numeric( $order ) ) :

			_LOG( 'Looking up order #' . $order );
			$order = $this->get_by_id( $order );

			if ( ! $order ) :

				_LOG( 'Invalid order ID' );
				$this->_set_error( 'Invalid order ID.' );
				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		$this->load->library( 'emailer' );
		$this->load->helper( 'email' );

		$_email							= new stdClass();
		$_email->type					= 'shop_notify';
		$_email->data					= array();
		$_email->data['order']			= $order;

		$_recipients = explode( ',', notification( 'notify_order', 'shop' ) );

		foreach ( $_recipients AS $recipient ) :

			$_email->to_email = $recipient;

			if ( ! $this->emailer->send( $_email, TRUE ) ) :

				//	Email failed to send, alert developers
				_LOG( '!! Failed to send order notification to ' . $_email->to_email . ', alerting developers.' );
				_LOG( implode( "\n", $this->emailer->get_errors() ) );

				send_developer_mail( 'Unable to send order notification email', 'Unable to send the order notification to ' . $_email->to_email . '; order: #' . $order->id . "\n\nEmailer errors:\n\n" . print_r( $this->emailer->get_errors(), TRUE ) );

			endif;

		endforeach;
	}


	// --------------------------------------------------------------------------


	protected function _format_order( &$order )
	{
		//	Generic
		$order->id					= (int) $order->id;
		$order->requires_shipping	= (bool) $order->requires_shipping;

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

		$order->user->group			= new stdClass();
		$order->user->group->id		= $order->user_group_id;
		$order->user->group->label	= $order->user_group_label;

		unset( $order->user_id );
		unset( $order->user_email );
		unset( $order->user_first_name );
		unset( $order->user_last_name );
		unset( $order->email );
		unset( $order->first_name );
		unset( $order->last_name );
		unset( $order->gender );
		unset( $order->profile_img );
		unset( $order->user_group_id );
		unset( $order->user_group_label );

		// --------------------------------------------------------------------------

		//	Totals
		$order->totals 						= new stdClass();
		$order->totals->shipping			= (float) $order->shipping_total;
		$order->totals->shipping_render		= (float) $order->shipping_total_render;
		$order->totals->sub					= (float) $order->sub_total;
		$order->totals->sub_render			= (float) $order->sub_total_render;
		$order->totals->tax_shipping		= (float) $order->tax_shipping;
		$order->totals->tax_shipping_render	= (float) $order->tax_shipping_render;
		$order->totals->tax_items			= (float) $order->tax_items;
		$order->totals->tax_items_render	= (float) $order->tax_items_render;
		$order->totals->grand				= (float) $order->grand_total;
		$order->totals->grand_render		= (float) $order->grand_total_render;

		$order->discount 					= new stdClass();
		$order->discount->shipping			= (float) $order->discount_shipping;
		$order->discount->shipping_render	= (float) $order->discount_shipping_render;
		$order->discount->items				= (float) $order->discount_items;
		$order->discount->items_render		= (float) $order->discount_items_render;

		$order->totals->fees			= (float) $order->fees_deducted;

		unset( $order->shipping_total );
		unset( $order->shipping_total_render );
		unset( $order->sub_total );
		unset( $order->sub_total_render );
		unset( $order->tax_shipping );
		unset( $order->tax_shipping_render );
		unset( $order->tax_items );
		unset( $order->tax_items_render );
		unset( $order->grand_total );
		unset( $order->grand_total_render );
		unset( $order->discount_shipping );
		unset( $order->discount_shipping_render );
		unset( $order->discount_items );
		unset( $order->discount_items_render );
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

		//	Shipping details
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


		//	Shipping method
		$order->shipping_method 			= new stdClass();
		$order->shipping_method->id 		= $order->shipping_method_id;
		$order->shipping_method->courier 	= $order->sm_courier;
		$order->shipping_method->method 	= $order->sm_method;

		unset( $order->shipping_method_id );
		unset( $order->sm_courier );
		unset( $order->sm_method );


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


		$order->currency->exchange_rate		= (float) $order->exchange_rate;

		unset( $order->exchange_rate );
		unset( $order->currency_id );
		unset( $order->oc_code );
		unset( $order->oc_symbol );
		unset( $order->oc_precision );
		unset( $order->base_currency_id );
		unset( $order->bc_code );
		unset( $order->bc_symbol );
		unset( $order->bc_precision );

		// --------------------------------------------------------------------------

		//	Vouchers
		if ( $order->voucher_id ) :

			$order->voucher							= new stdClass();
			$order->voucher->id						= (int) $order->voucher_id;
			$order->voucher->code					= $order->v_code;
			$order->voucher->label					= $order->v_label;
			$order->voucher->type					= $order->v_type;
			$order->voucher->discount_type			= $order->v_discount_type;
			$order->voucher->discount_value			= (float) $order->v_discount_value;
			$order->voucher->discount_application	= $order->v_discount_application;
			$order->voucher->product_type_id		= (int) $order->v_product_type_id;
			$order->voucher->valid_from				= $order->v_valid_from;
			$order->voucher->valid_to				= $order->v_valid_to;
			$order->voucher->is_active				= (bool) $order->v_is_active;
			$order->voucher->is_deleted				= (bool) $order->v_is_deleted;

		else :

			$order->voucher							= FALSE;

		endif;

		unset( $order->voucher_id );
		unset( $order->v_code );
		unset( $order->v_label );
		unset( $order->v_type );
		unset( $order->v_discount_type );
		unset( $order->v_discount_value );
		unset( $order->v_discount_application );
		unset( $order->v_product_type_id );
		unset( $order->v_valid_from );
		unset( $order->v_valid_to );
		unset( $order->v_is_active );
		unset( $order->v_is_deleted );
	}


	// --------------------------------------------------------------------------


	protected function _format_item( &$item )
	{
		$item->id					= (int) $item->id;
		$item->quantity				= (int) $item->quantity;
		$item->price				= (float) $item->price;
		$item->price_render			= (float) $item->price_render;
		$item->sale_price			= (float) $item->sale_price;
		$item->sale_price_render	= (float) $item->sale_price_render;
		$item->tax					= (float) $item->tax;
		$item->tax_render			= (float) $item->tax_render;
		$item->shipping				= (float) $item->shipping;
		$item->shipping_render		= (float) $item->shipping_render;
		$item->total				= (float) $item->total;
		$item->total_render			= (float) $item->total_render;
		$item->was_on_sale			= (bool) $item->was_on_sale;
		$item->processed			= (bool) $item->processed;
		$item->refunded				= (bool) $item->refunded;

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

		//	Tax rate
		$item->tax_rate				= new stdClass();
		$item->tax_rate->id			= (int) $item->tax_rate_id;
		$item->tax_rate->label		= $item->tax_rate_label;
		$item->tax_rate->rate		= (float) $item->tax_rate_rate;

		unset( $item->tax_rate_id );
		unset( $item->tax_rate_label );
		unset( $item->tax_rate_rate );

		// --------------------------------------------------------------------------

		//	Meta
		unset( $item->meta->id );
		unset( $item->meta->product_id );

		// --------------------------------------------------------------------------

		//	Extra data
		$item->extra_data = $item->extra_data ? unserialize( $item->extra_data ) : NULL;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core shop
 * models. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_ORDER_MODEL' ) ) :

	class Shop_order_model extends NAILS_Shop_order_model
	{
	}

endif;

/* End of file shop_order_model.php */
/* Location: ./modules/shop/models/shop_order_model.php */