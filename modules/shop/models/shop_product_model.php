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

		$this->_table					= NAILS_DB_PREFIX . 'shop_product';
		$this->_table_prefix			= 'p';

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
		//	Do all we need to do with the incoming data
		$data = $this->_create_update_prep_data( $data );

		if ( ! $data ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Execute
		$_id = $this->_create_update_execute( $data );

		//	Wrap it all up
		if ( $_id ) :

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

		//	Do all we need to do with the incoming data
		$_data = $this->_create_update_prep_data( $data );

		if ( ! $_data ) :

			return FALSE;

		endif;

		$_data->id = $id;

		// --------------------------------------------------------------------------

		//	Execute
		$_id = $this->_create_update_execute( $_data );

		//	Wrap it all up
		if ( $_id ) :

			return TRUE;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _create_update_prep_data( $data )
	{
		//	Quick check of incoming data
		$_data = new stdClass();

		if ( empty( $data['label'] ) ) :

			$this->_set_error( 'Label is a required field.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Slug
		//	====

		$_data->slug = $this->_generate_slug( $data['label'], '', '', $this->_table );

		//	Product Info
		//	============

		$_data->type_id = isset( $data['type_id'] ) ? (int) $data['type_id']	: NULL;

		if ( ! $_data->type_id ) :

			$this->_set_error( 'Product type must be defined.' );
			return FALSE;

		endif;

		$_data->label		= isset( $data['label'] )		? trim( $data['label'] )		: NULL;
		$_data->is_active	= isset( $data['is_active'] )	? (bool) $data['is_active']		: FALSE;
		$_data->is_deleted	= isset( $data['is_deleted'] )	? (bool) $data['is_deleted']	: FALSE;
		$_data->brands		= isset( $data['brands'] )		? $data['brands']				: array();
		$_data->categories	= isset( $data['categories'] )	? $data['categories']			: array();
		$_data->tags		= isset( $data['tags'] )		? $data['tags']					: array();

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
		$_product_type		= $this->shop_product_type_model->get_by_id( $_data->type_id );

		if ( ! $_product_type ) :

			$this->_set_error( 'Invalid Product Type' );
			return FALSE;

		else :

			$_data->is_physical = $_product_type->is_physical;

		endif;

		$_product_type_meta	= array();

		if ( is_callable( array( $this->product_type, 'meta_fields_' . $_product_type->slug ) ) ) :

			$_product_type_meta = $this->shop_product_type_model->{'meta_fields_' . $_product_type->slug}();

		endif;

		$_sku_tracker = array();

		foreach ( $data['variation'] AS $index => $v ) :

			//	Details
			//	-------

			$_data->variation[$index] = new stdClass();

			//	If there's an ID note it down, we'll be using it later as a switch between INSERT and UPDATE
			if ( ! empty( $v['id'] ) ) :

				$_data->variation[$index]->id = $v['id'];

			endif;

			$_data->variation[$index]->label	= isset( $v['label'] )	? $v['label']	: NULL;
			$_data->variation[$index]->sku		= isset( $v['sku'] )	? $v['sku']		: NULL;

			$_sku_tracker[] = $_data->variation[$index]->sku;

			//	Stock
			//	-----
			$_data->variation[$index]->stock_status = isset( $v['stock_status'] ) ? $v['stock_status'] : 'OUT_OF_STOCK';


			switch ( $_data->variation[$index]->stock_status ) :

				case 'IN_STOCK' :

					$_data->variation[$index]->quantity_available	= isset( $v['quantity_available'] ) ? $v['quantity_available'] : NULL;
					$_data->variation[$index]->lead_time			= NULL;

				break;

				case 'TO_ORDER' :

					$_data->variation[$index]->quantity_available	= NULL;
					$_data->variation[$index]->lead_time			= isset( $v['lead_time'] ) ? $v['lead_time'] : NULL;

				break;

				case 'OUT_OF_STOCK' :

					//	Shhh, be vewy qwiet, we're huntin' wabbits.

					$_data->variation[$index]->quantity_available	= NULL;
					$_data->variation[$index]->lead_time			= NULL;

				break;

			endswitch;

			//	Meta
			//	----

			//	If this product type is_physical then ensure that the dimensions are specified
			$_data->variation[$index]->meta = new stdClass();

			//	Any custom checks for the extra meta fields

			//	Process each field
			foreach( $_product_type_meta AS $field ) :

				$_data->variation[$index]->meta->{$field->key} = isset( $v['meta'][$field->key] ) ? $v['meta'][$field->key] : NULL;

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

			$_data->variation[$index]->shipping = new stdClass();

			if ( $_product_type->is_physical ) :

				$_data->variation[$index]->shipping->length				= isset( $v['shipping']['length'] )				? $v['shipping']['length']					: NULL;
				$_data->variation[$index]->shipping->width				= isset( $v['shipping']['width'] )				? $v['shipping']['width']					: NULL;
				$_data->variation[$index]->shipping->height				= isset( $v['shipping']['height'] )				? $v['shipping']['height']					: NULL;
				$_data->variation[$index]->shipping->measurement_unit	= isset( $v['shipping']['measurement_unit'] )	? $v['shipping']['measurement_unit']		: 'MM';
				$_data->variation[$index]->shipping->weight				= isset( $v['shipping']['weight'] )				? $v['shipping']['weight']					: NULL;
				$_data->variation[$index]->shipping->weight_unit		= isset( $v['shipping']['weight_unit'] )		? $v['shipping']['weight_unit']				: 'G';
				$_data->variation[$index]->shipping->collection_only	= isset( $v['shipping']['collection_only'] )	? (bool) $v['shipping']['collection_only']	: FALSE;

			else :

				$_data->variation[$index]->shipping->length				= NULL;
				$_data->variation[$index]->shipping->width				= NULL;
				$_data->variation[$index]->shipping->height				= NULL;
				$_data->variation[$index]->shipping->measurement_unit	= NULL;
				$_data->variation[$index]->shipping->weight				= NULL;
				$_data->variation[$index]->shipping->weight_unit		= NULL;
				$_data->variation[$index]->shipping->collection_only	= FALSE;

			endif;

		endforeach;

		//	Duplicate SKUs?
		$_sku_tracker	= array_filter( $_sku_tracker );
		$_count			= array_count_values( $_sku_tracker );

		if ( count( $_count ) != count( $_sku_tracker ) ) :

			//	If only one occurance of everything then the count on both
			//	should be the same, if not then it'll vary.

			$this->_set_error( 'All variations which have defined SKUs must be unique.' );
			return FALSE;

		endif;

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
		$_data->seo_title		= isset( $data['seo_title'] )		? $data['seo_title']		: NULL;
		$_data->seo_description	= isset( $data['seo_description'] )	? $data['seo_description']	: NULL;
		$_data->seo_keywords	= isset( $data['seo_keywords'] )	? $data['seo_keywords']		: NULL;

		// --------------------------------------------------------------------------

		return $_data;
	}


	// --------------------------------------------------------------------------


	protected function _create_update_execute( $data )
	{
		//	Start the transaction, safety first!
		$this->db->trans_begin();
		$_rollback = FALSE;

		//	Add the product
		$this->db->set( 'type_id',			$data->type_id );
		$this->db->set( 'label',			$data->label );
		$this->db->set( 'description',		$data->description );
		$this->db->set( 'seo_title',		$data->seo_title );
		$this->db->set( 'seo_description',	$data->seo_description );
		$this->db->set( 'seo_keywords',		$data->seo_keywords );
		$this->db->set( 'tax_rate_id',		$data->tax_rate_id );
		$this->db->set( 'is_active',		$data->is_active );
		$this->db->set( 'is_deleted',		$data->is_deleted );
		$this->db->set( 'modified',			'NOW()', FALSE );

		if ( $this->user_model->is_logged_in() ) :

			$this->db->set( 'modified_by',	active_user( 'id' ) );

		endif;

		if ( ! empty( $data->id ) ) :

			$this->db->where( 'id', $data->id );
			$_result = $this->db->update( $this->_table );
			$_action = 'update';

		else :

			$_result = $this->db->insert( $this->_table );
			$_action = 'create';
			$data->id = $this->db->insert_id();

		endif;

		if ( $_result ) :

			//	The following items are all handled, and error, in the [mostly] same way
			//	loopy loop for clarity and consistency.

			$_types = array();

			//					//Items to loop			//Field name		//Plural human		//Table name
			$_types[]	= array( $data->attributes,		'attribute_id',		'attributes',		$this->_table_attribute );
			$_types[]	= array( $data->brands,			'brand_id',			'brands',			$this->_table_brand );
			$_types[]	= array( $data->categories,		'category_id',		'categories',		$this->_table_category );
			$_types[]	= array( $data->collections,	'collection_id',	'collections',		$this->_table_collection );
			$_types[]	= array( $data->gallery,		'object_id',		'gallery items',	$this->_table_gallery );
			$_types[]	= array( $data->ranges,			'range_id',			'ranges',			$this->_table_range );
			$_types[]	= array( $data->tags,			'tag_id',			'tags',				$this->_table_tag );

			foreach ( $_types AS $type ) :

				list( $_items, $_field, $_type, $_table ) = $type;

				//	Clear old items
				$this->db->where( 'product_id', $data->id );
				if ( ! $this->db->delete( $_table ) ) :

					$this->_set_error( 'Failed to clear old product ' . $_type . '.' );
					$_rollback = TRUE;
					break;

				endif;

				$_temp = array();
				switch( $_field ) :

					case 'attribute_id' :

						foreach( $_items AS $item ) :

							$_temp[] = array( 'product_id' => $data->id, 'attribute_id' => $item['attribute_id'], 'value' => $item['value'] );

						endforeach;

					break;

					case 'object_id' :

						$_counter = 0;
						foreach( $_items AS $item_id ) :

							$_temp[] = array( 'product_id' => $data->id, $_field => $item_id, 'order' => $_counter );
							$_counter++;

						endforeach;

					break;

					default :

						foreach( $_items AS $item_id ) :

							$_temp[] = array( 'product_id' => $data->id, $_field => $item_id );

						endforeach;

					break;

				endswitch;

				if ( $_temp ) :

					if ( ! $this->db->insert_batch( $_table, $_temp ) ) :

						$this->_set_error( 'Failed to add product ' . $_type . '.' );
						$_rollback = TRUE;

					endif;

				endif;

			endforeach;


			//	Product Variations
			//	==================

			if ( ! $_rollback ) :

				$_counter = 0;

				//	Keep a note of the variants we deal with, we'll
				//	want to mark any we don't deal with as deleted

				$_variant_id_tracker = array();

				foreach( $data->variation AS $index => $v ) :

					//	Product Variation: Details
					//	==========================

					$this->db->set( 'label',	$v->label );
					$this->db->set( 'sku',		$v->sku );
					$this->db->set( 'order',	$_counter );


					//	Product Variation: Stock Status
					//	===============================

					$this->db->set( 'stock_status',			$v->stock_status );
					$this->db->set( 'quantity_available',	$v->quantity_available );
					$this->db->set( 'lead_time',			$v->lead_time );


					//	Product Variation: Shipping
					//	===========================

					$this->db->set( 'ship_collection_only',		$v->shipping->collection_only );
					$this->db->set( 'ship_length',				$v->shipping->length );
					$this->db->set( 'ship_width',				$v->shipping->width );
					$this->db->set( 'ship_height',				$v->shipping->height );
					$this->db->set( 'ship_measurement_unit',	$v->shipping->measurement_unit );
					$this->db->set( 'ship_weight',				$v->shipping->weight );
					$this->db->set( 'ship_weight_unit',			$v->shipping->weight_unit );

					if ( ! empty( $v->id ) ) :

						//	Existing variation, update what's there
						$this->db->where( 'id', $v->id );
						$_result = $this->db->update( $this->_table_variation );
						$_action = 'update';

						$_variant_id_tracker[] = $v->id;

					else :

						//	New variation, add it.
						$this->db->set( 'product_id', $data->id );
						$_result = $this->db->insert( $this->_table_variation );
						$_action = 'create';

						$_variant_id_tracker[] = $this->db->insert_id();

						$v->id = $this->db->insert_id();

					endif;

					if ( $_result ) :

						//	Product Variation: Gallery
						//	==========================

						$this->db->where( 'variation_id', $v->id );
						if ( ! $this->db->delete( $this->_table_variation_gallery ) ) :

							$this->_set_error( 'Failed to clear gallery items for variant with label "' . $v->label . '"' );
							$_rollback = TRUE;

						endif;

						if  (! $_rollback ) :

							$_temp = array();
							foreach( $v->gallery AS $object_id ) :

								$_temp[] = array(
									'variation_id'	=> $v->id,
									'object_id'		=> $object_id
								);

							endforeach;

							if ( $_temp ) :

								if ( ! $this->db->insert_batch( $this->_table_variation_gallery, $_temp ) ) :

									$this->_set_error( 'Failed to update gallery items variant with label "' . $v->label . '"' );
									$_rollback = TRUE;

								endif;

							endif;

						endif;


						//	Product Variation: Meta
						//	=======================

						if ( ! $_rollback ) :

							$this->db->where( 'variation_id', $v->id );
							if ( ! $this->db->delete( $this->_table_variation_meta ) ) :

								$this->_set_error( 'Failed to clear meta data for variant with label "' . $v->label . '"' );
								$_rollback = TRUE;

							endif;

							if ( ! $_rollback ) :

								$this->db->set( 'variation_id',	$v->id );
								$this->db->set( (array) $v->meta );

								if ( ! $this->db->insert( $this->_table_variation_meta ) ) :

									$this->_set_error( 'Failed to update meta data for variant with label "' . $v->label . '"' );
									$_rollback = TRUE;

								endif;

							endif;

						endif;


						//	Product Variation: Price
						//	========================

						if ( ! $_rollback ) :

							$this->db->where( 'variation_id', $v->id );
							if ( ! $this->db->delete( $this->_table_variation_price ) ) :

								$this->_set_error( 'Failed to clear price data for variant with label "' . $v->label . '"' );
								$_rollback = TRUE;

							endif;

							if ( ! $_rollback ) :

								foreach( $v->pricing AS &$price ) :

									$price->variation_id = $v->id;

									$price = (array) $price;

								endforeach;

								if ( $v->pricing ) :

									if ( ! $this->db->insert_batch( $this->_table_variation_price, $v->pricing ) ) :

										$this->_set_error( 'Failed to update price data for variant with label "' . $v->label . '"' );
										$_rollback = TRUE;

									endif;

								endif;

							endif;

						endif;

					else :

						$this->_set_error( 'Unable to ' . $_action . ' variation with label "' . $v->label . '".' );
						$_rollback = TRUE;
						break;

					endif;

					$_counter++;

				endforeach;

				//	Mark all untouched variants as deleted
				if ( ! $_rollback ) :

					$this->db->set( 'is_deleted', TRUE );
					$this->db->where( 'product_id', $data->id );
					$this->db->where_not_in( 'id', $_variant_id_tracker );

					if ( ! $this->db->update( $this->_table_variation ) ) :

						$this->_set_error( 'Unable to delete old variations.' );
						$_rollback = TRUE;

					endif;

				endif;

			endif;

		else :

			$this->_set_error( 'Failed to ' . $_action . ' base product.' );
			$_rollback = TRUE;

		endif;


		// --------------------------------------------------------------------------

		//	Wrap it all up
		if ( $this->db->trans_status() === FALSE || $_rollback ) :

			$this->db->trans_rollback();
			return FALSE;

		else :

			$this->db->trans_commit();

			// --------------------------------------------------------------------------

			//	Inform any persons who may have subscribed to a 'keep me informed' notification
			$_variants_available = array();

			$this->db->select( 'id' );
			$this->db->where( 'product_id', $data->id );
			$this->db->where( 'is_active', TRUE );
			$this->db->where( 'is_deleted', FALSE );
			$this->db->where( 'stock_status', 'IN_STOCK' );
			$this->db->where( '(quantity_available = 0 OR quantity_available - quantity_sold > 0)' );
			$_variants_available_raw = $this->db->get( $this->_table_variation	 )->result();
			$_variants_available = array();

			foreach( $_variants_available_raw AS $v ) :

				$_variants_available[] = $v->id;

			endforeach;

			if ( $_variants_available ) :

				if ( ! $this->load->model_is_loaded( 'shop_inform_product_available_model' ) ) :

					$this->load->model( 'shop/shop_inform_product_available_model' );

				endif;

				$this->shop_inform_product_available_model->inform( $data->id, $_variants_available );

			endif;



			// --------------------------------------------------------------------------

			return $data->id;

		endif;
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
		return parent::update( $id, array( 'is_deleted' => TRUE ) );
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
		return parent::update( $id, array( 'is_deleted' => FALSE ) );
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches all objects
	 *
	 * @access public
	 * @param none
	 * @return array
	 **/
	public function get_all( $only_active = TRUE, $order = NULL, $limit = NULL, $include_deleted = FALSE, $include_deleted_variants = FALSE )
	{
		//	TODO: Caching
		//	TODO: Adopt the generic Nails Model format

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
		if ( ! $include_deleted ) :

			$this->db->where( 'p.is_deleted', FALSE );

		endif;

		if ( $only_active ) :

			$this->db->where( 'p.is_active', TRUE );

		endif;

		// --------------------------------------------------------------------------

		$_products = $this->db->get( $this->_table . ' p' )->result();

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
			$this->db->select( 'b.id, b.slug, b.label, b.logo_id, b.is_active' );
			$this->db->where( 'pb.product_id', $product->id );
			$this->db->join( NAILS_DB_PREFIX . 'shop_brand b', 'b.id = pb.brand_id' );
			$product->brands = $this->db->get( $this->_table_brand . ' pb' )->result();

			//	Categories
			//	==========
			$this->db->select( 'c.id, c.slug, c.label, c.breadcrumbs' );
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

			//	Featured image
			//	==============
			if ( ! empty( $product->gallery[0] ) ) :

				$product->featured_img = $product->gallery[0];

			else :

				$product->featured_img = NULL;

			endif;

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
			if ( ! $include_deleted_variants ) :

				$this->db->where( 'pv.is_deleted', FALSE );

			endif;
			$this->db->order_by( 'pv.order' );
			$product->variations = $this->db->get( $this->_table_variation . ' pv' )->result();

			foreach( $product->variations AS &$v ) :

				//	Meta
				//	====
				$this->db->where( 'variation_id', $v->id );
				$v->meta = $this->db->get( $this->_table_variation_meta )->row();

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

				$this->_format_variation_object( $v );

			endforeach;

			//	Work out price/price_from
			//	=========================

			$product->price = array();

			foreach( $product->variations AS &$v ) :

				foreach( $v->price AS $price ) :

					if ( empty( $product->price[$price->code] ) ) :

						$product->price[$price->code]					= new stdClass();
						$product->price[$price->code]->max_price		= NULL;
						$product->price[$price->code]->min_price		= NULL;
						$product->price[$price->code]->max_sale_price	= NULL;
						$product->price[$price->code]->min_sale_price	= NULL;

					endif;

					if ( is_null( $product->price[$price->code]->max_price ) || $price->price > $product->price[$price->code]->max_price ) :

						$product->price[$price->code]->max_price = $price->price;

					endif;

					if ( is_null( $product->price[$price->code]->min_price ) || $price->price < $product->price[$price->code]->min_price ) :

						$product->price[$price->code]->min_price = $price->price;

					endif;

					if ( is_null( $product->price[$price->code]->max_sale_price ) || $price->sale_price > $product->price[$price->code]->max_sale_price ) :

						$product->price[$price->code]->max_sale_price = $price->sale_price;

					endif;

					if ( is_null( $product->price[$price->code]->min_sale_price ) || $price->sale_price < $product->price[$price->code]->min_sale_price ) :

						$product->price[$price->code]->min_sale_price = $price->sale_price;

					endif;

				endforeach;

			endforeach;

		endforeach;

		// --------------------------------------------------------------------------

		return $_products;
	}


	// --------------------------------------------------------------------------

	/**
	 * Fetch a product by it's ID
	 * @param  int $id The ID of the product
	 * @return mixed     FALSE on failure, stdClass on success
	 */
	public function get_by_id( $id )
	{
		$this->db->where( $this->_table_prefix . '.id', $id );
		$_item = $this->get_all( TRUE, NULL, NULL, TRUE, FALSE );

		if ( ! $_item ) :

			return FALSE;

		endif;

		return $_item[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch a product by it's slug
	 * @param  string $slug The slug of the product
	 * @return mixed     FALSE on failure, stdClass on success
	 */
	public function get_by_slug( $slug )
	{
		$this->db->where( $this->_table_prefix . '.slug', $slug );
		$_item = $this->get_all( TRUE, NULL, NULL, TRUE, FALSE );

		if ( ! $_item ) :

			return FALSE;

		endif;

		return $_item[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Count all the total number of products
	 * @param  boolean $only_active     Include active items only
	 * @param  array  $where            TODO
	 * @param  string  $search          TODO
	 * @param  boolean $include_deleted Include deleted items
	 * @return int                   The number of products
	 */
	public function count_all( $only_active = FALSE, $where = NULL, $search = NULL, $include_deleted = FALSE )
	{
		if ( ! $include_deleted ) :

			$this->db->where( 'p.is_deleted', FALSE );

		endif;

		if ( $only_active ) :

			$this->db->where( 'p.is_active', TRUE );

		endif;

		return $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_product p' );
	}

	// --------------------------------------------------------------------------


	/**
	 * Fetch products from a particular category
	 * @param  mixed  $category        The ID or slug of the category
	 * @param  boolean $only_active     Include active items only
	 * @param  [type]  $order           [description]
	 * @param  [type]  $limit           [description]
	 * @param  boolean $include_deleted Include deleted items
	 * @return array                   An array of products
	 */
	public function get_for_category( $category, $only_active = TRUE, $order = NULL, $limit = NULL, $include_deleted = FALSE )
	{
		$this->load->model( 'shop/shop_category_model' );

		if ( is_numeric( $category ) ) :

			$_category = $this->shop_category_model->get_by_id( $category );

		else :

			$_category = $this->shop_category_model->get_by_slug( $category );

		endif;

		if ( ! $_category ) :

			return array();

		endif;

		// --------------------------------------------------------------------------

		//	Fetch all the ID's we want to search across
		$_ids = array( $_category->id );
		$_ids = array_merge( $_ids, $this->shop_category_model->get_ids_of_all_children( $_category->id ) );

		$this->db->where_in( 'pc.category_id', $_ids );
		$this->db->join( $this->_table_category . ' pc', 'pc.product_id = p.id' );
		$this->db->group_by( 'p.id' );
		return $this->get_all( $only_active, $order, $limit, $include_deleted );
	}


	// --------------------------------------------------------------------------


	public function format_url( $slug )
	{
		return site_url( app_setting( 'url', 'shop' ) . 'product/' . $slug );
	}


	// --------------------------------------------------------------------------


	/**
	 * Formats a product object
	 * @param  stdClass $product The product object to format
	 * @return void
	 */
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

		//	URL
		$product->url = $this->format_url( $product->slug );
	}


	// --------------------------------------------------------------------------


	/**
	 * If the seo_description or seo_keywords fields are empty this method will
	 * generate some content for them.
	 * @param  object $product A product object
	 * @return void
	 */
	public function generate_seo_content( &$product )
	{
		//	Autogenerate some SEO content if it's not been set
		if ( empty( $product->seo_description ) ) :

			$product->seo_description = 'Buy ' . $product->label . ' at ' . APP_NAME;

			if ( ! empty( $product->categories ) ) :

				$_categories_arr = array();
				foreach( $product->categories AS $category ) :

					$_categories_arr[] = $category->label;

				endforeach;
				$product->seo_description .= ' (' . implode( ', ', $_categories_arr ) . ')';

			endif;

		endif;

		if ( empty( $product->seo_keywords ) ) :

			//	Extract common keywords
			$this->lang->load( 'shop/shop' );
			$_common = explode( ',', lang( 'shop_common_words' ) );
			$_common = array_unique( $_common );
			$_common = array_filter( $_common );

			//	Remove them and return the most popular words
			$_description = strtolower( $product->description );
			$_description = str_replace( "\n", ' ', strip_tags( $_description ) );
			$_description = str_word_count( $_description, 1 );
			$_description = array_count_values( $_description	);
			arsort( $_description );
			$_description = array_keys( $_description );
			$_description = array_diff( $_description, $_common );
			$_description = array_slice( $_description, 0, 10 );

			$product->seo_keywords = implode( ',', $_description );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Formats a variation object
	 * @param  stdClass $variation The variation object to format
	 * @return void
	 */
	protected function _format_variation_object( &$variation )
	{
		//	Type casting
		$variation->id					= (int) $variation->id;
		$variation->product_id			= (int) $variation->product_id;
		$variation->quantity_sold		= (int) $variation->quantity_sold;
		$variation->order				= (int) $variation->order;
		$variation->is_deleted			= (bool) $variation->is_deleted;
		$variation->quantity_available	= is_numeric( $variation->quantity_available ) ? (int) $variation->quantity_available : $variation->quantity_available;

		//	Gallery
		if ( ! empty( $variation->gallery ) && is_array( $variation->gallery ) ) :

			foreach ( $variation->gallery AS &$object_id ) :

				$object_id	= (int) $object_id;

			endforeach;

		endif;

		//	Price
		if ( ! empty( $variation->price ) && is_array( $variation->price ) ) :

			foreach ( $variation->price AS $price ) :

				$price->id					= (int) $price->id;
				$price->decimal_precision	= (int) $price->decimal_precision;
				$price->price				= (float) $price->price;
				$price->sale_price			= (float) $price->sale_price;

			endforeach;

		endif;

		//	Shipping data
		$variation->shipping					= new stdClass();
		$variation->shipping->length			= (float) $variation->ship_length;
		$variation->shipping->width				= (float) $variation->ship_width;
		$variation->shipping->height			= (float) $variation->ship_height;
		$variation->shipping->measurement_unit	= $variation->ship_measurement_unit;
		$variation->shipping->weight			= (float) $variation->ship_weight;
		$variation->shipping->weight_unit		= $variation->ship_weight_unit;
		$variation->shipping->collection_only	= (bool) $variation->ship_collection_only;

		unset( $variation->ship_length );
		unset( $variation->ship_width );
		unset( $variation->ship_height );
		unset( $variation->ship_measurement_unit );
		unset( $variation->ship_weight );
		unset( $variation->ship_weight_unit );
		unset( $variation->ship_collection_only );
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_PRODUCT_MODEL' ) ) :

	class Shop_product_model extends NAILS_Shop_product_model
	{
	}

endif;

/* End of file shop_product_model.php */
/* Location: ./modules/shop/models/shop_product_model.php */