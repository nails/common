<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_tax_model.php
 *
 * Description:		This model handles everything to do with currencies
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_tax_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table = NAILS_DB_PREFIX . 'shop_tax_rate';
	}


	// --------------------------------------------------------------------------


	public function get_all()
	{
		$this->db->where( 'is_deleted', FALSE );
		$this->db->order_by( 'label' );
		return parent::get_all();
	}


	// --------------------------------------------------------------------------


	public function get_all_flat()
	{
		$_rates = $this->get_all();
		$_out	= array();

		foreach ( $_rates AS $rate ) :

			$_out[$rate->id] = $rate->label . ' - ' . $rate->rate*100 . '%';

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$obj )
	{
		$obj->id				= (int) $obj->id;
		$obj->product_count		= isset( $obj->product_count ) ? (int) $obj->product_count : NULL;
	}


	// --------------------------------------------------------------------------


	public function create( $data )
	{
		$_data = new stdClass();

		if ( isset( $data->label ) ) :

			$_data->label = strip_tags( $data->label );

		else :

			$this->_set_error( 'Label is required.' );
			return FALSE;

		endif;

		if ( isset( $data->rate ) ) :

			$_data->rate = (float) $data->rate;

		else :

			$this->_set_error( 'Rate is required.' );
			return FALSE;

		endif;

		if ( ! empty( $_data ) ) :

			//	Generate a slug
			$this->db->set( $_data );
			$this->db->set( 'created', 'NOW()', FALSE );
			$this->db->set( 'modified', 'NOW()', FALSE );

			if ( active_user( 'id' ) ) :

				$this->db->set( 'created_by', active_user( 'id' ) );
				$this->db->set( 'modified_by', active_user( 'id' ) );

			endif;

			$this->db->insert( $this->_table );

			if ( $this->db->affected_rows() ) :

				return $this->db->insert_id();

			else :

				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function update( $id, $data )
	{
		$_data = new stdClass();

		if ( isset( $data->label ) ) :

			$_data->label = strip_tags( $data->label );

		else :

			$this->_set_error( 'Label is required.' );
			return FALSE;

		endif;

		if ( isset( $data->rate ) ) :

			$_data->rate = (float) $data->rate;

		else :

			$this->_set_error( 'Rate is required.' );
			return FALSE;

		endif;

		if ( ! empty( $_data ) ) :

			//	Generate a slug
			$this->db->set( $_data );
			$this->db->set( 'modified', 'NOW()', FALSE );
			$this->db->where( 'id', $id );

			if ( active_user( 'id' ) ) :

				$this->db->set( 'modified_by', active_user( 'id' ) );

			endif;

			if ( $this->db->update( $this->_table ) ) :

				return TRUE;

			else :

				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function delete( $id )
	{
		$this->db->set( 'is_deleted', TRUE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->where( 'id', $id );
		$this->db->update( $this->_table );
		return (bool) $this->db->affected_rows();
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_TAX_MODEL' ) ) :

	class Shop_tax_model extends NAILS_Shop_tax_model
	{
	}

endif;

/* End of file  */
/* Location: ./application/models/ */