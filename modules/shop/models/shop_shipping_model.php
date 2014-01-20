<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_shipping_model.php
 *
 * Description:		This model handles shipping methods
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_shipping_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table			= NAILS_DB_PREFIX . 'shop_shipping_method';
		$this->_table_prefix	= 'sm';
	}


	// --------------------------------------------------------------------------


	public function get_all( $only_active = TRUE, $include_deleted = FALSE )
	{
		$this->db->select( 'sm.*, tr.id tr_id, tr.label tr_label, tr.rate tr_rate' );

		$this->db->join( NAILS_DB_PREFIX . 'shop_tax_rate tr', 'tr.id = sm.tax_rate_id', 'LEFT' );

		if ( $only_active ) :

			$this->db->where( 'sm.is_active', TRUE );

		endif;

		if ( ! $include_deleted ) :

			$this->db->where( 'sm.is_deleted', FALSE );

		endif;

		$this->db->order_by( 'sm.order' );

		$_methods = $this->db->get( NAILS_DB_PREFIX . 'shop_shipping_method sm' )->result();

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
		$_override = $this->db->get( NAILS_DB_PREFIX . 'shop_product_shipping_method' )->row();

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
		$object->order						= (int) $object->order;

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_SHIPPING_MODEL' ) ) :

	class Shop_shipping_model extends NAILS_Shop_shipping_model
	{
	}

endif;

/* End of file shop_shipping_model.php */
/* Location: ./application/models/shop_shipping_model.php */