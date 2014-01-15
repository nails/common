<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_product_model.php
 *
 * Description:		This model handles everything to do with products
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
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

		$this->_table_product			= NAILS_DB_PREFIX . 'shop_product';
		$this->_table_attribute			= NAILS_DB_PREFIX . 'shop_product_attribute';
		$this->_table_brand				= NAILS_DB_PREFIX . 'shop_product_brand';
		$this->_table_category			= NAILS_DB_PREFIX . 'shop_product_category';
		$this->_table_collection		= NAILS_DB_PREFIX . 'shop_product_collection';
		$this->_table_gallery			= NAILS_DB_PREFIX . 'shop_product_gallery';
		$this->_table_range				= NAILS_DB_PREFIX . 'shop_product_range';
		$this->_table_tag				= NAILS_DB_PREFIX . 'shop_product_tag';
		$this->_table_variation			= NAILS_DB_PREFIX . 'shop_product_variation';
		$this->_table_variation_gallery	= NAILS_DB_PREFIX . 'shop_product_variation_gallery';
		$this->_table_variation_meta	= NAILS_DB_PREFIX . 'shop_product_variation_meta';
		$this->_table_variation_price	= NAILS_DB_PREFIX . 'shop_product_variation_price';
		$this->_table_type				= NAILS_DB_PREFIX . 'shop_product_type';
		$this->_table_tax_rate			= NAILS_DB_PREFIX . 'shop_tax_rate';
	}


	// --------------------------------------------------------------------------


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
		//	Quick check of incoming data
		$_data = new stdClass();

		//	Product Info
		//	============
		$_data->type_id		= isset( $data['type_id'] )			? (int) $data['type_id']	: NULL;

		if ( ! $_data->type_id ) :

			$this->_set_error( 'Product type must be defined.' );
			return FALSE;

		endif;

		$_data->title		= isset( $data['title'] )			? $data['title']			: NULL;
		$_data->is_active	= isset( $data['is_active'] )		? (bool) $data['is_active']	: FALSE;
		$_data->brands		= isset( $data['brands'] )			? $data['brands']			: array();
		$_data->categories	= isset( $data['categories'] )		? $data['categories']		: array();
		$_data->tags		= isset( $data['tags'] )			? $data['tags']				: array();

		$_data->tax_rate_id	= isset( $data['tax_rate_id'] ) &&	(int) $data['tax_rate_id']	? (int) $data['tax_rate_id']	: NULL;

		// --------------------------------------------------------------------------

		//	Description
		//	===========
		$_data->description	= isset( $data['description'] ) ? $data['description']	: NULL;

		// --------------------------------------------------------------------------

		//	Variants - Loop variants
		//	========================

		if ( ! isset( $data['variation'] ) || ! $data['variation'] ) :

			$this->_set_error( 'At least one variation is required.' );
			return FALSE;

		endif;

		$_data->variation	= array();
		$_product_type		= $this->get_product_type_by_id( $_data->type_id );

		if ( ! $_product_type ) :

			$this->_set_error( 'Invalid Product Type' );
			return FALSE;

		endif;

		$_product_type_meta	= array();

		if ( is_callable( array( $this, 'product_type_meta_fields_' . $_product_type->slug ) ) ) :

			$_product_type_meta = $this->{'product_type_meta_fields_' . $_product_type->slug}();

		endif;

		foreach ( $data['variation'] AS $index => $v ) :

			//	Details
			//	-------

			$_data->variation[$index]						= new stdClass();
			$_data->variation[$index]->label				= isset( $v['label'] )				? $v['label']				: NULL;
			$_data->variation[$index]->sku					= isset( $v['sku'] )				? $v['sku']					: NULL;
			$_data->variation[$index]->quantity_available	= isset( $v['quantity_available'] )	? $v['quantity_available']	: NULL;
			$_data->variation[$index]->quantity_sold		= isset( $v['quantity_sold'] )		? $v['quantity_sold']		: NULL;

			//	Meta
			//	----

			//	If this product type is_physical then ensure that the dimensions are specified
			$_data->variation[$index]->meta = new stdClass();

			if ( $_product_type->is_physical ) :

				$_data->variation[$index]->meta->length				= isset( $v['meta']['length'] )				? $v['meta']['length']				: NULL;
				$_data->variation[$index]->meta->width				= isset( $v['meta']['width'] )				? $v['meta']['width']				: NULL;
				$_data->variation[$index]->meta->height				= isset( $v['meta']['height'] )				? $v['meta']['height']				: NULL;
				$_data->variation[$index]->meta->measurement_unit	= isset( $v['meta']['measurement_unit'] )	? $v['meta']['measurement_unit']	: NULL;
				$_data->variation[$index]->meta->weight				= isset( $v['meta']['weight'] )				? $v['meta']['weight']				: NULL;
				$_data->variation[$index]->meta->weight_unit		= isset( $v['meta']['weight_unit'] )		? $v['meta']['weight_unit']			: NULL;

				foreach( $_data->variation[$index]->meta AS $key => $field ) :

					if ( ! $field ) :

						$this->_set_error( 'Physical dimensions must be supplied for all variants.' );
						return FALSE;

					endif;

				endforeach;

			endif;

			//	Any custom checks for the extra meta fields

			//	Process each field
			foreach( $_product_type_meta AS $field ) :

				$_data->variation[$index]->meta->{$field->key}	= isset( $v['meta'][$field->key] )	? $v['meta'][$field->key]	: NULL;

			endforeach;

			//	Pricing
			//	-------
			$_data->variation[$index]->pricing = array();

			if ( isset( $v['pricing'] ) ) :

				//	At the very least the base price must be defined
				$_base_price_set = FALSE;
				foreach( $v['pricing'] AS $price_index => $price ) :

					$_data->variation[$index]->pricing[$price_index]				= new stdClass();
					$_data->variation[$index]->pricing[$price_index]->currency_id	= isset( $price['currency_id'] )	? $price['currency_id']	: NULL;
					$_data->variation[$index]->pricing[$price_index]->price			= isset( $price['price'] )			? $price['price']		: NULL;
					$_data->variation[$index]->pricing[$price_index]->sale_price	= isset( $price['sale_price'] )		? $price['sale_price']	: NULL;

					if ( $price['currency_id'] == SHOP_BASE_CURRENCY_ID ) :

						$_base_price_set = TRUE;

					endif;

				endforeach;

				if ( ! $_base_price_set ) :

					$this->_set_error( 'The ' . SHOP_BASE_CURRENCY_CODE . ' price must be set for all variants.' );
					return FALSE;

				endif;

			endif;

			//	Gallery Associations
			//	--------------------
			$_data->variation[$index]->gallery = array();

			if ( isset( $v['gallery'] ) ) :

				foreach( $v['gallery'] AS $gallery_index => $image ) :

					$this->form_validation->set_rules( 'variation[' . $index . '][gallery][' . $gallery_index . ']',	'',	'xss_clean' );

					if( $image ) :

						$_data->variation[$index]->gallery[] = $image;

					endif;

				endforeach;

			endif;

			//	Shipping
			//	--------

			$_data->variation[$index]->shipping						= new stdClass();
			$_data->variation[$index]->shipping->collection_only	= isset( $v['shipping']['collection_only'] )	? (bool) $v['shipping']['collection_only']	: FALSE;

		endforeach;

		// --------------------------------------------------------------------------

		//	Gallery
		$_data->gallery			= isset( $data['gallery'] )			? $data['gallery']			: array();

		// --------------------------------------------------------------------------

		//	Attributes
		$_data->attributes		= isset( $data['attributes'] )		? $data['attributes']		: array();

		// --------------------------------------------------------------------------

		//	Ranges & Collections
		$_data->ranges			= isset( $data['ranges'] )			? $data['ranges']			: array();
		$_data->collections		= isset( $data['collections'] )		? $data['collections']		: array();

		// --------------------------------------------------------------------------

		//	SEO
		$_data->seo_description	= isset( $data['seo_description'] )	? $data['seo_description']	: NULL;
		$_data->seo_keywords	= isset( $data['seo_keywords'] )	? $data['seo_keywords']		: NULL;

		// --------------------------------------------------------------------------
		// ==========================================================================
		// --------------------------------------------------------------------------

		//	Now we shove all this lvoely data into the database. Yummy!

		//	Start the transaction, safety first!
		$this->db->trans_begin();
		$_rollback = FALSE;

		//	Add the product
		$this->db->set( 'type_id',			$_data->type_id );
		$this->db->set( 'title',			$_data->title );
		$this->db->set( 'description',		$_data->description );
		$this->db->set( 'seo_description',	$_data->seo_description );
		$this->db->set( 'seo_keywords',		$_data->seo_keywords );
		$this->db->set( 'tax_rate_id',		$_data->tax_rate_id );
		$this->db->set( 'is_active',		$_data->is_active );
		$this->db->set( 'is_deleted',		FALSE);
		$this->db->set( 'created',			'NOW()', FALSE );
		$this->db->set( 'modified',			'NOW()', FALSE );

		if ( $this->user->is_logged_in() ) :

			$this->db->set( 'created_by',		active_user( 'id' ) );
			$this->db->set( 'modified_by',		active_user( 'id' ) );

		endif;

		if ( $this->db->insert( $this->_table_product ) ) :

			$_id = $this->db->insert_id();

			//	Product Attributes
			//	==================
			foreach( $_data->attributes AS &$attr ) :

				$attr['product_id'] = $_id;

			endforeach;

			if ( $_data->attributes ) :

				$this->db->insert_batch( $this->_table_attribute, $_data->attributes );

			endif;

			//	Product Brands
			//	==============
			$_temp = array();
			foreach( $_data->brands AS $id ) :

				$_temp[] = array( 'product_id' => $_id, 'brand_id' => $id );

			endforeach;

			if ( $_temp ) :

				$this->db->insert_batch( $this->_table_brand, $_temp );

			endif;

			//	Product Categories
			//	==================
			$_temp = array();
			foreach( $_data->categories AS $id ) :

				$_temp[] = array( 'product_id' => $_id, 'category_id' => $id );

			endforeach;

			if ( $_temp ) :

				$this->db->insert_batch( $this->_table_category, $_temp );

			endif;

			//	Product Collections
			//	===================
			$_temp = array();
			foreach( $_data->collections AS $id ) :

				$_temp[] = array( 'product_id' => $_id, 'collection_id' => $id );

			endforeach;

			if ( $_temp ) :

				$this->db->insert_batch( $this->_table_collection, $_temp );

			endif;

			//	Product Gallery
			//	===============
			$_temp		= array();
			$_counter	= 0;
			foreach( $_data->gallery AS $id ) :

				$_temp[] = array( 'product_id' => $_id, 'object_id' => $id, 'order' => $_counter );
				$_counter++;

			endforeach;

			if ( $_temp ) :

				$this->db->insert_batch( $this->_table_gallery, $_temp );

			endif;

			//	Product Ranges
			//	==============
			$_temp = array();
			foreach( $_data->ranges AS $id ) :

				$_temp[] = array( 'product_id' => $_id, 'range_id' => $id );

			endforeach;

			if ( $_temp ) :

				$this->db->insert_batch( $this->_table_range, $_temp );

			endif;

			//	Product Tags
			//	============
			$_temp = array();
			foreach( $_data->tags AS $id ) :

				$_temp[] = array( 'product_id' => $_id, 'tag_id' => $id );

			endforeach;

			if ( $_temp ) :

				$this->db->insert_batch( $this->_table_tag, $_temp );

			endif;

			//	Product Variations
			//	==================
			$_counter = 0;
			foreach( $_data->variation AS $index => $v ) :

				$this->db->set( 'product_id',			$_id );
				$this->db->set( 'label',				$v->label );
				$this->db->set( 'sku',					$v->sku );
				$this->db->set( 'quantity_available',	$v->quantity_available );
				$this->db->set( 'quantity_sold',		$v->quantity_sold );
				$this->db->set( 'order',				$_counter );

				if ( $this->db->insert( $this->_table_variation ) ) :

					$_variation_id = $this->db->insert_id();

					//	Product Variation: Gallery
					//	==========================
					$_temp = array();
					foreach( $v->gallery AS $id ) :

						$_temp[] = array(
							'product_id' => $_id,
							'variation_id' => $_variation_id,
							'object_id' => $id
							);

					endforeach;

					if ( $_temp ) :

						$this->db->insert_batch( $this->_table_variation_gallery, $_temp );

					endif;

					//	Product Variation: Meta
					//	=======================

					$this->db->set( 'product_id',	$_id );
					$this->db->set( 'variation_id',	$_variation_id );
					$this->db->set( (array) $v->meta );
					$this->db->set( 'shipping_collection_only',	$v->shipping->collection_only );

					$this->db->insert( $this->_table_variation_meta );


					//	Product Variation: Price
					//	========================

					foreach( $v->pricing AS &$price ) :

						$price->product_id		= $_id;
						$price->variation_id	= $_variation_id;

						$price = (array) $price;

					endforeach;

					if ( $v->pricing ) :

						$this->db->insert_batch( $this->_table_variation_price, $v->pricing );

					endif;

				else :

					$this->_set_error( 'Unable to create variation with label "' . $v->label . '".' );
					$_rollback = TRUE;
					break;

				endif;

				$_counter++;

			endforeach;

		else :

			$this->_set_error( 'Unable to create base product.' );
			$_rollback = TRUE;

		endif;


		// --------------------------------------------------------------------------

		//	Wrap it all up
		if ( $this->db->trans_status() === FALSE || $_rollback ) :

			$this->db->trans_rollback();
			return FALSE;

		else :

			$this->db->trans_commit();

			if ( $return_obj ) :

				return $this->get_by_id( $_id );

			else :

				return $_id;

			endif;

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
		$_current = $this->get_by_id( $id );

		if ( ! $_current ) :

			$this->_set_error( 'Invalid product ID' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		dumpanddie( 'TODO' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Marks a product as deleted
	 *
	 * @access public
	 * @param int $id The ID of the object to delete
	 * @return bool
	 **/
	public function delete( $id )
	{
		return $this->update( $id, array( 'is_deleted' => TRUE ) );
	}


	// --------------------------------------------------------------------------


	/**
	 * Restores a deleted object
	 *
	 * @access public
	 * @param int $id The ID of the object to delete
	 * @return bool
	 **/
	public function restore( $id )
	{
		return $this->update( $id, array( 'is_deleted' => FALSE ) );
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
		$this->db->delete( $this->_table );

		if ( $this->db->affected_rows() ) :

			return TRUE;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches all objects
	 *
	 * @access public
	 * @param none
	 * @return array
	 **/
	public function get_all( $only_active = TRUE, $order = NULL, $limit = NULL )
	{
		//	TODO: Caching
		//	Maybe use CI database caching?

		// --------------------------------------------------------------------------

		//	Selects
		$this->db->select( 'p.*' );
		$this->db->select( 'pt.slug type_slug, pt.label type_label, pt.is_physical type_is_physical' );
		$this->db->select( 'tr.label tax_rate_label, tr.rate tax_rate_rate' );

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

		//	Join all the tables!
		$this->db->join( $this->_table_type . ' pt', 'p.type_id = pt.id' );
		$this->db->join( $this->_table_tax_rate . ' tr', 'p.tax_rate_id = tr.id', 'LEFT' );

		//	GrumpyCat says no
		$this->db->where( 'p.is_deleted', FALSE );

		if ( $only_active ) :

			$this->db->where( 'p.is_active', TRUE );

		endif;

		// --------------------------------------------------------------------------

		$_products = $this->db->get( $this->_table_product . ' p' )->result();

		// --------------------------------------------------------------------------

		foreach ( $_products AS $product ) :

			$this->_format_product_object( $product );

			// --------------------------------------------------------------------------

			//	Fetch associated content

			//	Attributes
			//	==========
			$this->db->select( 'pa.attribute_id id, a.label, pa.value' );
			$this->db->where( 'pa.product_id', $product->id );
			$this->db->join( NAILS_DB_PREFIX . 'shop_attribute a', 'a.id = pa.attribute_id' );
			$product->attributes = $this->db->get( $this->_table_attribute . ' pa' )->result();

			//	Brands
			//	======
			$this->db->select( 'b.id, b.slug, b.label, b.logo_id, b.is_hidden' );
			$this->db->where( 'pb.product_id', $product->id );
			$this->db->join( NAILS_DB_PREFIX . 'shop_brand b', 'b.id = pb.brand_id' );
			$product->brands = $this->db->get( $this->_table_brand . ' pb' )->result();

			//	Categories
			//	==========
			$this->db->select( 'c.id, c.slug, c.label, c.label_nested' );
			$this->db->where( 'pc.product_id', $product->id );
			$this->db->join( NAILS_DB_PREFIX . 'shop_category c', 'c.id = pc.category_id' );
			$product->categories = $this->db->get( $this->_table_category . ' pc' )->result();

			//	Collections
			//	===========
			$this->db->select( 'c.id, c.slug, c.label' );
			$this->db->where( 'pc.product_id', $product->id );
			$this->db->join( NAILS_DB_PREFIX . 'shop_collection c', 'c.id = pc.collection_id' );
			$product->collections = $this->db->get( $this->_table_collection . ' pc' )->result();

			//	Gallery
			//	=======
			$this->db->select( 'object_id' );
			$this->db->where( 'product_id', $product->id );
			$this->db->order_by( 'order' );
			$_temp = $this->db->get( $this->_table_gallery )->result();

			$product->gallery = array();
			foreach( $_temp AS $image ) :

				$product->gallery[] = (int) $image->object_id;

			endforeach;

			//	Range
			//	=====
			$this->db->select( 'r.id, r.slug, r.label' );
			$this->db->where( 'pr.product_id', $product->id );
			$this->db->join( NAILS_DB_PREFIX . 'shop_range r', 'r.id = pr.range_id' );
			$product->ranges = $this->db->get( $this->_table_range . ' pr' )->result();

			//	Tags
			//	====
			$this->db->select( 't.id, t.slug, t.label' );
			$this->db->where( 'pt.product_id', $product->id );
			$this->db->join( NAILS_DB_PREFIX . 'shop_tag t', 't.id = pt.tag_id' );
			$product->tags = $this->db->get( $this->_table_tag . ' pt' )->result();

			//	Variations
			//	==========
			$this->db->select( 'pv.*' );
			$this->db->where( 'pv.product_id', $product->id );
			$this->db->order_by( 'pv.order' );
			$product->variations = $this->db->get( $this->_table_variation . ' pv' )->result();

			foreach( $product->variations AS &$v ) :

				//	Meta
				//	====
				$this->db->where( 'variation_id', $v->id );
				$v->meta = $this->db->get( $this->_table_variation_meta )->row();

				unset( $v->meta->id );
				unset( $v->meta->product_id );
				unset( $v->meta->variation_id );

				//	Meta
				//	====
				$this->db->where( 'variation_id', $v->id );
				$_temp = $this->db->get( $this->_table_variation_gallery )->result();
				$v->gallery = array();

				foreach( $_temp AS $image ) :

					$v->gallery[] = $image->object_id;

				endforeach;

				//	Price
				//	====
				$this->db->select( 'c.id,c.code,c.symbol,c.symbol_position,c.label,c.decimal_precision,c.decimal_symbol,c.thousands_seperator,pvp.price,pvp.sale_price' );
				$this->db->where( 'pvp.variation_id', $v->id );
				$this->db->join( NAILS_DB_PREFIX . 'shop_currency c', 'c.id = pvp.currency_id' );
				$v->price = $this->db->get( $this->_table_variation_price . ' pvp' )->result();

			endforeach;

			// --------------------------------------------------------------------------

			//	Do prices need converted?
			// if ( SHOP_BASE_CURRENCY_ID != SHOP_USER_CURRENCY_ID ) :

			// 	//	Has a set price been defined for this currency?
			// 	if ( ! is_null( $product->render_price ) ) :

			// 		$product->price_render = $product->render_price;

			// 	else :

			// 		$product->price_render = shop_convert_to_user( $product->price );

			// 	endif;

			// 	//	What about a set sale price?
			// 	if ( ! is_null( $product->render_sale_price ) ) :

			// 		$product->sale_price_render = $product->render_sale_price;

			// 	else :

			// 		$product->sale_price_render = shop_convert_to_user( $product->sale_price );

			// 	endif;

			// else :

			// 	$product->price_render		= $product->price;
			// 	$product->sale_price_render	= $product->price;

			// endif;


		endforeach;

		// --------------------------------------------------------------------------

		return $_products;
	}


	// --------------------------------------------------------------------------


	protected function search( $only_active = TRUE, $order = NULL, $limit = NULL )
	{
		dumpanddie( 'TODO: Search products' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts the total amount of products for a partricular query/search key. Essentially performs
	 * the same query as $this->get_all() but without limiting.
	 *
	 * @access	public
	 * @param	string	$where	An array of where conditions
	 * @param	mixed	$search	A string containing the search terms
	 * @return	int
	 * @author	Pablo
	 *
	 **/
	public function count_all( $only_active = FALSE, $where = NULL, $search = NULL )
	{
		//$this->_getcount_common( $only_active, $where, $search );

		// --------------------------------------------------------------------------

		//	Execute Query
		return $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_product p' );
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


	public function get_product_types( $include_count = FALSE )
	{
		$this->db->select( 'pt.id,pt.slug,pt.label,pt.description,pt.is_physical,pt.ipn_method,pt.max_per_order,pt.max_variations,pt.created,pt.modified' );

		if ( $include_count ) :

			$this->db->select( '(SELECT COUNT(*) FROM ' . NAILS_DB_PREFIX .  'shop_product WHERE type_id = pt.id) product_count' );

		endif;

		$this->db->order_by( 'label' );
		$_result = $this->db->get( $this->_table_type . ' pt' )->result();

		foreach( $_result AS &$r ) :

			$this->_format_product_type_object( $r );

		endforeach;

		return $_result;
	}


	// --------------------------------------------------------------------------


	public function get_product_types_flat( $include_count = FALSE )
	{
		$_types	= $this->get_product_types( $include_count );
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


	public function get_product_type_by_id( $id, $include_count = FALSE  )
	{
		$this->db->where( 'id', $id );
		$_types = $this->get_product_types( $include_count );

		if ( ! $_types ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return $_types[0];
	}


	// --------------------------------------------------------------------------


	public function create_product_type( $data )
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
			$_data->slug = $this->_generate_slug( $data->label, NAILS_DB_PREFIX . 'shop_product_type', 'slug' );
			$this->db->set( $_data );
			$this->db->set( 'created', 'NOW()', FALSE );
			$this->db->set( 'modified', 'NOW()', FALSE );

			if ( active_user( 'id' ) ) :

				$this->db->set( 'created_by', active_user( 'id' ) );
				$this->db->set( 'modified_by', active_user( 'id' ) );

			endif;

			$this->db->insert( NAILS_DB_PREFIX . 'shop_product_type' );

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


	public function delete_product_type( $id )
	{
		//	Turn off DB Errors
		$_previous = $this->db->db_debug;
		$this->db->db_debug = FALSE;

		$this->db->trans_begin();
		$this->db->where( 'id', $id );
		$this->db->delete( NAILS_DB_PREFIX . 'shop_product_type' );
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


	public function update_product_type( $id, $data )
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

			if ( $this->db->update( NAILS_DB_PREFIX . 'shop_product_type' ) ) :

				return TRUE;

			else :

				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_for_category( $category, $only_active = TRUE, $order = NULL, $limit = NULL )
	{
		$this->load->model( 'shop/shop_category_model', 'category' );

		if ( is_numeric( $category ) ) :

			$_category = $this->category->get_by_id( $category );

		else :

			$_category = $this->category->get_by_slug( $category );

		endif;

		if ( ! $_category ) :

			return array();

		endif;

		// --------------------------------------------------------------------------

		//	Fetch all the ID's we want to search across
		$_ids 		= array( $_category->id );
		$_ids		= array_merge( $_ids, $this->category->get_ids_of_all_children( $_category->id ) );

		$this->db->where_in( 'pc.category_id', $_ids );
		$this->db->join( $this->_table_category . ' pc', 'pc.product_id = p.id' );
		$this->db->group_by( 'p.id' );
		return $this->get_all();
	}


	// --------------------------------------------------------------------------


	public function product_type_meta_fields_download()
	{
		$_out = array();

		// --------------------------------------------------------------------------

		//	TODO: This array should be a form builder config array - when that library is complete.

		//	Download ID
		$_out[0]				= new stdClass();
		$_out[0]->type			= 'cdn_object';
		$_out[0]->key			= 'download_id';
		$_out[0]->label			= 'Download';
		$_out[0]->bucket		= 'shop-download';
		$_out[0]->tip			= '';
		$_out[0]->validation	= 'xss_clean|required';

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _format_product_object( &$product )
	{
		//	Type casting
		$product->id			= (int) $product->id;
		$product->is_active		= (bool) $product->is_active;
		$product->is_deleted	= (bool) $product->is_deleted;

		//	Product type
		$product->type				= new stdClass();
		$product->type->id			= (int) $product->type_id;
		$product->type->slug		= $product->type_slug;
		$product->type->label		= $product->type_label;
		$product->type->is_physical	= $product->type_is_physical;

		unset( $product->type_id );
		unset( $product->type_slug );
		unset( $product->type_label );
		unset( $product->type_is_physical );

		//	Tax Rate
		$product->tax_rate			= new stdClass();
		$product->tax_rate->id		= (int) $product->tax_rate_id;
		$product->tax_rate->label	= $product->tax_rate_label;
		$product->tax_rate->rate	= $product->tax_rate_rate;

		unset( $product->tax_rate_id );
		unset( $product->tax_rate_label );
		unset( $product->tax_rate_rate );
	}


	// --------------------------------------------------------------------------


	protected function _format_variation_object( &$variation )
	{
		//	Type casting
		$variation->id			= (int) $variation->id;
	}


	// --------------------------------------------------------------------------


	protected function _format_product_type_object( &$obj )
	{
		//	Type casting
		$obj->id				= (int) $obj->id;
		$obj->max_per_order		= is_numeric( $obj->max_per_order ) ? (int) $obj->max_per_order : $obj->max_per_order;
		$obj->max_variations	= (int) $obj->max_variations;
		$obj->product_count		= isset( $obj->product_count ) ? (int) $obj->product_count : NULL;
		$obj->is_physical		= (bool) $obj->is_physical;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_PRODUCT_MODEL' ) ) :

	class Shop_product_model extends NAILS_Shop_product_model
	{
	}

endif;

/* End of file shop_product_model.php */
/* Location: ./application/models/shop_product_model.php */