<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_voucher_model.php
 *
 * Description:		This model handles everything to do with vouchers
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_voucher_model extends NAILS_Model
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

		if ( $this->user_model->is_logged_in() ) :

			$this->db->set( 'created_by', active_user( 'id' ) );
			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		$this->db->insert( NAILS_DB_PREFIX . 'shop_voucher' );

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
		if ( $this->user_model->is_logged_in() ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;
		$this->db->where( 'id', $id );
		$this->db->update( NAILS_DB_PREFIX . 'shop_voucher' );

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
		$this->db->delete( NAILS_DB_PREFIX . 'shop_voucher' );
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

			$this->load->model( 'shop/shop_order_model' );
			$order = $this->shop_order_model->get_by_id( $order );

		endif;

		// --------------------------------------------------------------------------

		switch( $voucher->type ) :

			case 'GIFT_CARD' :	$this->_redeem_gift_card( $voucher, $order );	break;
			default:

				//	Bump the use count
				$this->db->set( 'last_used', 'NOW()', FALSE );
				$this->db->set( 'modified', 'NOW()', FALSE );
				$this->db->set( 'use_count', 'use_count+1', FALSE );

				$this->db->where( 'id', $voucher->id );
				$this->db->update( NAILS_DB_PREFIX . 'shop_voucher' );

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _redeem_gift_card( $voucher, $order )
	{
		if ( $order->requires_shipping ) :

			if ( app_setting( 'free_shipping_threshold', 'shop' ) <= $order->totals->sub ) :

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
		$this->db->update( NAILS_DB_PREFIX . 'shop_voucher' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches all objects
	 *
	 * @access public
	 * @param none
	 * @return array
	 **/
	public function get_all( $only_active = TRUE, $order = NULL, $limit = NULL, $where = NULL, $search = NULL )
	{
		$this->db->select( 'v.*, ue.email,u.first_name,u.last_name,u.profile_img,u.gender' );

		// --------------------------------------------------------------------------

		//	Only active vouchers?
		if ( $only_active ) :

			$this->db->where( 'v.is_active', TRUE );

		endif;

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
		$this->_getcount_vouchers_common( $where, $search );

		// --------------------------------------------------------------------------

		//	Ignore deleted vouchers
		$this->db->where( 'v.is_deleted', FALSE );

		$_vouchers = $this->db->get( NAILS_DB_PREFIX . 'shop_voucher v' )->result();

		// --------------------------------------------------------------------------

		$this->load->model( 'shop/shop_product_type_model' );

		foreach ( $_vouchers AS $voucher ) :

			//	Fetch extra data
			switch ( $voucher->discount_application ) :

				case 'PRODUCT_TYPES' :

					$_cache = $this->_get_cache(  'voucher-product-type-' . $voucher->product_type_id );
					if ( $_cache ) :

						//	Exists in cache
						$voucher->product = $_cache;

					else :

						//	Doesn't exist, fetch and save
						$voucher->product = $this->shop_product_type_model->get_by_id( $voucher->product_type_id );
						$this->_set_cache( 'voucher-product-type-' . $voucher->product_type_id, $voucher->product );

					endif;

				break;

			endswitch;

			// --------------------------------------------------------------------------

			$this->_format_voucher( $voucher );

		endforeach;

		// --------------------------------------------------------------------------

		return $_vouchers;
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts the total amount of vouchers for a partricular query/search key. Essentially performs
	 * the same query as $this->get_all() but without limiting.
	 *
	 * @access	public
	 * @param	string	$where	An array of where conditions
	 * @param	mixed	$search	A string containing the search terms
	 * @return	int
	 *
	 **/
	public function count_vouchers( $only_active = TRUE, $where = NULL, $search = NULL )
	{
		//	Only active vouchers?
		if ( $only_active ) :

			$this->db->where( 'v.is_active', TRUE );

		endif;

		// --------------------------------------------------------------------------

		$this->_getcount_vouchers_common( $where, $search );

		// --------------------------------------------------------------------------

		//	Ignore deleted vouchers
		$this->db->where( 'v.is_deleted', FALSE );

		// --------------------------------------------------------------------------

		//	Execute Query
		return $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_voucher v' );
	}


	// --------------------------------------------------------------------------


	protected function _getcount_vouchers_common( $where = NULL, $search = NULL )
	{
		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = v.created_by', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );

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
			$search['columns']['id']			= 'v.id';
			$search['columns']['code']			= 'v.code';

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
		if ( NULL !== $_voucher->valid_to && $_voucher->valid_to != '0000-00-00 00:00:00' && strtotime( $_voucher->valid_to ) < time() ) :

			$this->_set_error( 'Voucher has expired.' );
			return FALSE;

		endif;

		//	Is this a shipping voucher being applied to an order with no shippable items?
		if ( NULL !== $basket && $_voucher->discount_application == 'SHIPPING' && ! $basket->requires_shipping ) :

			$this->_set_error( 'Your order does not contian any items which require shipping, voucher not needed!' );
			return FALSE;

		endif;

		//	Is there a shipping threshold? If so, and the voucher is type SHIPPING
		//	and the threshold has been reached then prevent it being added as it
		//	doesn't make sense.

		if ( NULL !== $basket && app_setting( 'free_shipping_threshold', 'shop' ) && $_voucher->discount_application == 'SHIPPING' ) :

			if ( $basket->totals->sub >= app_setting( 'free_shipping_threshold', 'shop' ) ) :

				$this->_set_error( 'Your order qualifies for free shipping, voucher not needed!' );
				return FALSE;

			endif;

		endif;


		//	If the voucher applies to a particular product type, check the basket contains
		//	that product, otherwise it doesn't make sense to add it

		if ( NULL !== $basket && $_voucher->discount_application == 'PRODUCT_TYPES' ) :

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

		//	Creator
		$voucher->creator				= new stdClass();
		$voucher->creator->id			= (int) $voucher->created_by;
		$voucher->creator->email		= $voucher->email;
		$voucher->creator->first_name	= $voucher->first_name;
		$voucher->creator->last_name	= $voucher->last_name;
		$voucher->creator->profile_img	= $voucher->profile_img;
		$voucher->creator->gender		= $voucher->gender;

		unset( $voucher->created_by );
		unset( $voucher->email );
		unset( $voucher->first_name );
		unset( $voucher->last_name );
		unset( $voucher->profile_img );
		unset( $voucher->gender );
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_VOUCHER_MODEL' ) ) :

	class Shop_voucher_model extends NAILS_Shop_voucher_model
	{
	}

endif;

/* End of file shop_voucher_model.php */
/* Location: ./modules/shop/models/shop_voucher_model.php */