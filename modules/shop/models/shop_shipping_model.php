<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_shipping_model.php
 *
 * Description:		This model handles shipping methods
 * 
 **/

class Shop_shipping_model extends NAILS_Model
{
	public function get_all( $only_active = TRUE, $include_deleted = FALSE )
	{
		$this->db->select( 'sm.*, tr.id tr_id, tr.label tr_label, tr.rate tr_rate' );

		$this->db->join( 'shop_tax_rate tr', 'tr.id = sm.tax_rate_id', 'LEFT' );

		if ( $only_active ) :

			$this->db->where( 'sm.is_active', TRUE );

		endif;

		if ( ! $include_deleted ) :

			$this->db->where( 'sm.is_deleted', FALSE );

		endif;

		$this->db->order_by( 'sm.courier, sm.order' );

		$_methods = $this->db->get( 'shop_shipping_method sm' )->result();

		foreach ( $_methods AS $method ) :

			$this->_format_method( $method );

		endforeach;

		return $_methods;
	}


	// --------------------------------------------------------------------------


	public function get_by_id( $id )
	{
		$this->db->where( 'sm.id', $id );
		$_method = $this->get_all( FALSE );

		if ( ! $_method ) :

			return FALSE;

		endif;

		return $_method[0];
	}


	// --------------------------------------------------------------------------


	public function get_default_id()
	{
		$this->db->where( 'sm.is_default', TRUE );
		$_method = $this->get_all();

		if ( ! $_method ) :

			return FALSE;

		endif;

		return $_method[0]->id;
	}


	// --------------------------------------------------------------------------


	public function get_price_for_product( $product_id, $method_id )
	{
		//	Look-up the shipping emthod
		$_method = $this->validate( $method_id );

		if ( ! $_method ) :

			return FALSE;

		endif;

		//	Look in the product_shipping_method table for a price override
		$this->db->where( 'product_id', $product_id );
		$this->db->where( 'shipping_method_id', $_method->id );
		$_override = $this->db->get( 'shop_product_shipping_method' )->row();

		if ( $_override ) :

			$_out					= new stdClass();
			$_out->price			= $_override->price;
			$_out->price_additional	= $_override->price_additional;
			$_out->tax_rate			= $_method->tax_rate->rate;

		else :

			$_out					= new stdClass();
			$_out->price			= $_method->default_price;
			$_out->price_additional	= $_method->default_price_additional;
			$_out->tax_rate			= $_method->tax_rate->rate;

		endif;

		return $_out;
	}


	// --------------------------------------------------------------------------


	public function validate( $shipping_method )
	{
		$_method = $this->get_by_id( $shipping_method );
		
		if ( ! $_method ) :

			$this->_set_error( 'Invalid Shipping Method.' );
			return FALSE;

		endif;

		if ( ! $_method->is_active ) :

			$this->_set_error( 'Invalid Shipping Method.' );
			return FALSE;

		endif;

		return $_method;
	}


	// --------------------------------------------------------------------------


	protected function _format_method( &$object )
	{
		$object->id							= (int) $object->id;
		$object->order						= (int) $object->id;

		$object->default_price				= (float) $object->default_price;
		$object->default_price_additional	= (float) $object->default_price_additional;

		$object->is_active					= (bool) $object->is_active;
		$object->is_deleted					= (bool) $object->is_deleted;
		$object->is_default					= (bool) $object->is_default;

		$object->tax_rate					= new stdClass();
		$object->tax_rate->id				= (int) $object->tr_id;
		$object->tax_rate->label			= $object->tr_label;
		$object->tax_rate->rate				= $object->tr_rate;

		unset( $object->tr_id );
		unset( $object->tax_rate_id );
		unset( $object->tr_label );
		unset( $object->tr_rate );

	}
}

/* End of file shop_shipping_model.php */
/* Location: ./application/models/shop_shipping_model.php */