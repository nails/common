<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_product_model.php
 *
 * Description:		This model handles everything to do with products
 * 
 **/

/**
 * OVERLOADING NAILS'S MODELS
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

class NAILS_Shop_product_model extends NAILS_Model
{
	protected $_table;
	
	
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
		
		// --------------------------------------------------------------------------
		
		$this->_table			= 'shop_product';
		$this->_table_gallery	= 'shop_product_gallery';
		$this->_table_meta		= 'shop_product_meta';
		$this->_table_type		= 'shop_product_type';
		$this->_table_tax		= 'shop_tax_rate';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Creates a new object
	 * 
	 * @access public
	 * @param array $data The data to create the object with
	 * @param array $meta The meta data for the object
	 * @param bool $return_obj Whether to return just the new ID or the full object
	 * @return mixed
	 **/
	public function create( $data = array(), $meta = array(), $return_obj = FALSE )
	{
		//	Minimum requirements are title and type
		if ( !isset( $data['type'] ) || ! isset( $data['title'] ) ) :
		
			$this->_set_error( 'Missing Product Type or Title.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Check type is valid
		$this->db->where( 'slug', $data['type'] );
		$_type = $this->db->get( $this->_table_type )->row();
		
		if ( ! $_type ) :
		
			$this->_set_error( 'Invalid product type' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Define minimum
		$this->db->set( 'type_id', $_type->id );
		$this->db->set( 'title', $data['title'] );
		
		unset( $data['type'] );
		unset( $data['title'] );
		
		// --------------------------------------------------------------------------
		
		if ( $data ) :
		
			$this->db->set( $data );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->set( 'created_by', active_user( 'id' ) );
		
		$this->db->insert( $this->_table );
		
		$_id = $this->db->insert_id();
		
		if ( $_id ) :
		
			//	Prefix all meta fields with the type slug
			$_meta = array();
			foreach ( $meta AS $key => $value ) :
			
				$_meta[$_type->slug . '_' . $key ] = $value;
			
			endforeach;
			
			if ( $_meta ) :
			
				$this->db->set( $_meta );
			
			endif;
			
			$this->db->set( 'product_id', $_id );
			$this->db->insert( $this->_table_meta );
			
			return $_id;
		
		else :
		
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
	public function update( $id, $data = array(), $meta = array() )
	{
		$_current = $this->get_by_id( $id );
		
		if ( ! $_current ) :
		
			$this->_set_error( 'Invalid product ID' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Minimum requirements are title and type
		if ( isset( $data['title'] ) && ! $data['title'] ) :
		
			$this->_set_error( 'Missing Product Title.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Can't change product type, or ID
		unset( $data['type_id'] );
		unset( $data['id'] );
		
		// --------------------------------------------------------------------------
		
		if ( $data ) :
		
			$this->db->set( $data );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->set( 'modified_by', active_user( 'id' ) );
		$this->db->where( 'id', $id );
		
		if ($this->db->update( $this->_table ) ) :
		
			
			//	Prefix all meta fields with the type slug
			$_meta = array();
			foreach ( $meta AS $key => $value ) :
			
				$_meta[$_current->type->slug . '_' . $key ] = $value;
			
			endforeach;
			
			if ( $_meta ) :
			
				$this->db->set( $_meta );
				$this->db->where( 'product_id', $id );
				$this->db->update( $this->_table_meta );
			
			endif;
			
			return TRUE;
		
		else :
		
			$this->_set_error( 'Unable to save product.' );
			return FALSE;
		
		endif;
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
		$this->db->delete( $this->_table );
		
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
	public function get_all( $only_active = TRUE )
	{
		$this->db->select( 'p.*' );
		$this->db->select( 'tr.id tax_id, tr.label tax_label, tr.rate tax_rate' );
		$this->db->select( 'pm.download_bucket,pm.download_filename' );
		$this->db->select( 'pt.slug type_slug, pt.label type_label, pt.requires_shipping type_requires_shipping,pt.max_per_order type_max_per_order' );
		
		$this->db->join( $this->_table_meta . ' pm', 'p.id = pm.product_id' );
		$this->db->join( $this->_table_type . ' pt', 'p.type_id = pt.id' );
		$this->db->join( $this->_table_tax . ' tr', 'p.tax_rate_id = tr.id', 'LEFT' );

		$this->db->where( 'p.is_deleted', FALSE );

		if ( $only_active ) :
		
			$this->db->where( 'p.is_active', TRUE );

		endif;
		
		$_products = $this->db->get( $this->_table . ' p' )->result();
		
		// --------------------------------------------------------------------------
		
		foreach ( $_products AS $product ) :
		
			$this->_format_product_object( $product );
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_products;
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
		$this->db->where( 'p.id', $id );
		$_result = $this->get_all( FALSE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}

	// --------------------------------------------------------------------------


	public function get_product_types()
	{
		$this->db->order_by( 'label' );
		return $this->db->get( $this->_table_type )->result();
	}


	// --------------------------------------------------------------------------


	public function get_product_types_flat()
	{
		$_types	= $this->get_product_types();
		$_out	= array();

		foreach ( $_types AS $type ) :

			$_out[$type->id] = $type->label;

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	public function get_product_type_by_id( $id )
	{
		$this->db->where( 'id', $id );
		$_types = $this->get_product_types();

		if ( ! $_types )
			return FALSE;

		return $_types[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _format_product_object( &$product )
	{
		//	Type casting
		$product->id			= (int) $product->id;
		$product->price			= (float) $product->price;
		$product->sale_price	= (float) $product->sale_price;
		$product->tax_rate		= (float) $product->tax_rate;
		$product->is_active		= (bool) $product->is_active;
		$product->quantity_sold	= (int) $product->quantity_sold;
		
		if ( ! is_null( $product->quantity_available ) ) :
		
			$product->quantity_available = (int) $product->quantity_available;
		
		endif;
		
		if ( $product->is_on_sale && time() > strtotime( $product->sale_start ) && time() < strtotime( $product->sale_end ) ) :
		
			$product->is_on_sale	= TRUE;
		
		else :
		
			$product->is_on_sale	= FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Tax Rate
		$product->tax			= new stdClass();
		$product->tax->id		= (int) $product->tax_id;
		$product->tax->label	= $product->tax_label;
		$product->tax->rate		= (float) $product->tax_rate;
		
		unset( $product->tax_id );
		unset( $product->tax_label );
		unset( $product->tax_rate );
		
		// --------------------------------------------------------------------------
		
		//	Type
		$product->type						= new stdClass();
		$product->type->id					= (int) $product->type_id;
		$product->type->slug				= $product->type_slug;
		$product->type->label				= $product->type_label;
		$product->type->requires_shipping	= (bool) $product->type_requires_shipping;
		
		if ( ! is_null( $product->type_max_per_order ) ) :
		
			$product->type->max_per_order	= (int) $product->type_max_per_order;
		
		endif;
		
		unset( $product->type_id );
		unset( $product->type_slug );
		unset( $product->type_label );
		unset( $product->type_requires_shipping );
		unset( $product->type_max_per_order );
		
		// --------------------------------------------------------------------------
		
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S MODELS
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_PRODUCT_MODEL' ) ) :

	class Shop_product_model extends NAILS_Shop_product_model
	{
	}

endif;

/* End of file shop_product_model.php */
/* Location: ./application/models/shop_product_model.php */