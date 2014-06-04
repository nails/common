<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop_product_type_model
 *
 * Description:	The user group model handles user's passwords
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_product_type_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table			= NAILS_DB_PREFIX . 'shop_product_type';
		$this->_table_prefix	= 'spt';
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

		if ( isset( $data->description ) ) :

			$_data->description = strip_tags( $data->description, '<a><strong><em><img>' );

		endif;

		if ( isset( $data->is_physical ) ) :

			$_data->is_physical = $data->is_physical;

		endif;

		if ( isset( $data->ipn_method ) ) :

			$_data->ipn_method = $data->ipn_method;

		endif;

		if ( isset( $data->max_per_order ) ) :

			$_data->max_per_order = $data->max_per_order;

		endif;

		if ( isset( $data->max_variations ) ) :

			$_data->max_variations =  $data->max_variations;

		endif;

		if ( ! empty( $_data ) ) :

			//	Generate a slug
			$_data->slug = $this->_generate_slug( $data->label, '', '', $this->_table );
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


	public function delete( $id )
	{
		//	Turn off DB Errors
		$_previous = $this->db->db_debug;
		$this->db->db_debug = FALSE;

		$this->db->trans_begin();
		$this->db->where( 'id', $id );
		$this->db->delete( $this->_table );
		$_affected_rows = $this->db->affected_rows();

		if ($this->db->trans_status() === FALSE) :

		    $this->db->trans_rollback();

			$_return = FALSE;

		else :

		    $this->db->trans_commit();
			$_return = (bool) $_affected_rows;

		endif;

		//	Put DB errors back as they were
		$this->db->db_debug = $_previous;

		return $_return;
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

		if ( isset( $data->description ) ) :

			$_data->description = strip_tags( $data->description, '<a><strong><em><img>' );

		endif;

		if ( isset( $data->is_physical ) ) :

			$_data->is_physical = $data->is_physical;

		endif;

		if ( isset( $data->ipn_method ) ) :

			$_data->ipn_method = $data->ipn_method;

		endif;

		if ( isset( $data->max_per_order ) ) :

			$_data->max_per_order = $data->max_per_order;

		endif;

		if ( isset( $data->max_variations ) ) :

			$_data->max_variations =  $data->max_variations;

		endif;

		if ( ! empty( $_data ) ) :

			$this->db->set( $_data );
			$this->db->set( 'modified', 'NOW()', FALSE );
			$this->db->where( 'id', $id );

			if ( $this->db->update( $this->_table) ) :

				return TRUE;

			else :

				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_by_id( $id, $include_count = FALSE  )
	{
		$this->db->where( 'id', $id );
		$_types = $this->get_all( $include_count );

		if ( ! $_types ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return $_types[0];
	}


	// --------------------------------------------------------------------------


	public function get_all_flat( $include_count = FALSE )
	{
		$_types	= $this->get_all( $include_count );
		$_out	= array();

		foreach ( $_types AS $type ) :

			$_out[$type->id] = $type->label;

			if ( $include_count ) :

				$_out[$type->id] .= ' (' . $type->product_count . ')';

			endif;

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	public function get_all( $include_count = FALSE )
	{
		$this->db->select( 'pt.id,pt.slug,pt.label,pt.description,pt.is_physical,pt.ipn_method,pt.max_per_order,pt.max_variations,pt.created,pt.modified' );

		if ( $include_count ) :

			$this->db->select( '(SELECT COUNT(*) FROM ' . NAILS_DB_PREFIX .  'shop_product WHERE type_id = pt.id) product_count' );

		endif;

		$this->db->order_by( 'label' );
		$_result = $this->db->get( $this->_table . ' pt' )->result();

		foreach( $_result AS &$r ) :

			$this->_format_object( $r );

		endforeach;

		return $_result;
	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$obj )
	{
		//	Type casting
		$obj->id				= (int) $obj->id;
		$obj->max_per_order		= (int) $obj->max_per_order;
		$obj->max_variations	= (int) $obj->max_variations;
		$obj->product_count		= isset( $obj->product_count ) ? (int) $obj->product_count : NULL;
		$obj->is_physical		= (bool) $obj->is_physical;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_PRODUCT_TYPE_MODEL' ) ) :

	class Shop_product_type_model extends NAILS_Shop_product_type_model
	{
	}

endif;

/* End of file shop_product_type_model.php */
/* Location: ./system/application/models/shop_product_type_model.php */