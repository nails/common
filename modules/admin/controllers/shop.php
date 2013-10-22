<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin : Shop
*
* Description:	Shop Manager
*
*/

require_once NAILS_PATH . 'modules/admin/controllers/_admin.php';

/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop extends NAILS_Admin_Controller
{

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access static
	 * @param none
	 * @return void
	 **/
	static function announce()
	{
		if ( ! module_is_enabled( 'shop' ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$d = new stdClass();

		// --------------------------------------------------------------------------

		//	Configurations
		$d->name				= 'Shop';					//	Display name.

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs				= array();
		$d->funcs['inventory']		= 'Manage Inventory';				//	Sub-nav function.
		$d->funcs['orders']		= 'Manage Orders';					//	Sub-nav function.
		$d->funcs['vouchers']	= 'Manage Vouchers';				//	Sub-nav function.
		$d->funcs['sales']		= 'Manage Sales';				//	Sub-nav function.
		$d->funcs['reports']	= 'Generate Reports';				//	Sub-nav function.

		// --------------------------------------------------------------------------

		//	Only announce the controller if the user has permission to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns an array of notifications for various methods
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function notifications()
	{
		$_ci =& get_instance();
		$_notifications = array();

		// --------------------------------------------------------------------------

		get_instance()->load->model( 'shop/shop_order_model', 'order' );

		$_notifications['orders']			= array();
		$_notifications['orders']['type']	= 'alert';
		$_notifications['orders']['title']	= 'Unfulfilled orders';
		$_notifications['orders']['value']	= get_instance()->order->count_unfulfilled_orders();

		// --------------------------------------------------------------------------

		return $_notifications;
	}


	// --------------------------------------------------------------------------


	static function permissions()
	{
		$_permissions = array();

		// --------------------------------------------------------------------------

		//	Inventory
		$_permissions['inventory_create']	= 'Inventory: Create';
		$_permissions['inventory_edit']		= 'Inventory: Edit';
		$_permissions['inventory_delete']	= 'Inventory: Delete';
		$_permissions['inventory_restore']	= 'Inventory: Restore';

		//	Orders
		$_permissions['orders_view']		= 'Orders: View';
		$_permissions['orders_reprocess']	= 'Orders: Reprocess';
		$_permissions['orders_process']		= 'Orders: Process';

		//	Vouchers
		$_permissions['vouchers_create']		= 'Vouchers: Create';
		$_permissions['vouchers_activate']		= 'Vouchers: Activate';
		$_permissions['vouchers_deactivate']	= 'Vouchers: Deactivate';

		// --------------------------------------------------------------------------

		return $_permissions;
	}


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Defaults defaults
		$this->shop_inventory_group			= FALSE;
		$this->shop_inventory_where			= array();
		$this->shop_inventory_actions		= array();
		$this->shop_inventory_sortfields	= array();

		$this->shop_orders_group			= FALSE;
		$this->shop_orders_where			= array();
		$this->shop_orders_actions			= array();
		$this->shop_orders_sortfields		= array();

		$this->shop_vouchers_group			= FALSE;
		$this->shop_vouchers_where			= array();
		$this->shop_vouchers_actions		= array();
		$this->shop_vouchers_sortfields		= array();

		// --------------------------------------------------------------------------

		$this->shop_inventory_sortfields[] = array( 'label' => 'ID',				'col' => 'p.id' );
		$this->shop_inventory_sortfields[] = array( 'label' => 'Title',				'col' => 'p.title' );
		$this->shop_inventory_sortfields[] = array( 'label' => 'Type',				'col' => 'pt.label' );
		$this->shop_inventory_sortfields[] = array( 'label' => 'Price',				'col' => 'p.price' );
		$this->shop_inventory_sortfields[] = array( 'label' => 'Modified',			'col' => 'p.modified' );

		$this->shop_orders_sortfields[] = array( 'label' => 'ID',				'col' => 'o.id' );
		$this->shop_orders_sortfields[] = array( 'label' => 'Date Placed',		'col' => 'o.created' );
		$this->shop_orders_sortfields[] = array( 'label' => 'Last Modified',	'col' => 'o.modified' );
		$this->shop_orders_sortfields[] = array( 'label' => 'Value',			'col' => 'o.grand_total' );

		$this->shop_vouchers_sortfields[] = array( 'label' => 'ID',				'col' => 'v.id' );
		$this->shop_vouchers_sortfields[] = array( 'label' => 'Code',			'col' => 'v.code' );

		// --------------------------------------------------------------------------

		//	Load models which this model depends on
		$this->load->model( 'shop/shop_model', 'shop' );
		$this->load->model( 'shop/shop_currency_model', 'currency' );
		$this->load->model( 'shop/shop_product_model', 'product' );
		$this->load->model( 'shop/shop_tax_model', 'tax' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Manage the inventory
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function inventory()
	{
		switch( $this->uri->segment( '4' ) ) :

			case 'create' :		$this->_inventory_create();		break;
			case 'edit' :		$this->_inventory_edit();		break;
			case 'delete' :		$this->_inventory_delete();		break;
			case 'restore' :	$this->_inventory_restore();		break;
			case 'index' :
			default :			$this->_inventory_index();		break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _inventory_index()
	{
		//	Set method info
		$this->data['page']->title = 'Manage Inventory';

		// --------------------------------------------------------------------------

		//	Searching, sorting, ordering and paginating.
		$_hash = 'search_' . md5( uri_string() ) . '_';

		if ( $this->input->get( 'reset' ) ) :

			$this->session->unset_userdata( $_hash . 'per_page' );
			$this->session->unset_userdata( $_hash . 'sort' );
			$this->session->unset_userdata( $_hash . 'order' );

		endif;

		$_default_per_page	= $this->session->userdata( $_hash . 'per_page' ) ? $this->session->userdata( $_hash . 'per_page' ) : 50;
		$_default_sort		= $this->session->userdata( $_hash . 'sort' ) ? 	$this->session->userdata( $_hash . 'sort' ) : 'p.id';
		$_default_order		= $this->session->userdata( $_hash . 'order' ) ? 	$this->session->userdata( $_hash . 'order' ) : 'desc';

		//	Define vars
		$_search = array( 'keywords' => $this->input->get( 'search' ), 'columns' => array() );

		foreach ( $this->shop_inventory_sortfields AS $field ) :

			$_search['columns'][strtolower( $field['label'] )] = $field['col'];

		endforeach;

		$_limit		= array(
						$this->input->get( 'per_page' ) ? $this->input->get( 'per_page' ) : $_default_per_page,
						$this->input->get( 'offset' ) ? $this->input->get( 'offset' ) : 0
					);
		$_order		= array(
						$this->input->get( 'sort' ) ? $this->input->get( 'sort' ) : $_default_sort,
						$this->input->get( 'order' ) ? $this->input->get( 'order' ) : $_default_order
					);

		//	Set sorting and ordering info in session data so it's remembered for when user returns
		$this->session->set_userdata( $_hash . 'per_page', $_limit[0] );
		$this->session->set_userdata( $_hash . 'sort', $_order[0] );
		$this->session->set_userdata( $_hash . 'order', $_order[1] );

		//	Set values for the page
		$this->data['search']				= new stdClass();
		$this->data['search']->per_page		= $_limit[0];
		$this->data['search']->sort			= $_order[0];
		$this->data['search']->order		= $_order[1];

		// --------------------------------------------------------------------------

		//	Prepare the $_where
		$_where = NULL;

		// --------------------------------------------------------------------------

		//	Pass any extra data to the view
		$this->data['actions']		= $this->shop_inventory_actions;
		$this->data['sortfields']	= $this->shop_inventory_sortfields;

		// --------------------------------------------------------------------------

		//	Fetch orders
		$this->load->model( 'shop/shop_product_model', 'product' );

		$this->data['items']		= new stdClass();
		$this->data['items']->data	= $this->product->get_all( FALSE, $_order, $_limit, $_where, $_search );

		//	Work out pagination
		$this->data['items']->pagination				= new stdClass();
		$this->data['items']->pagination->total_results	= $this->product->count_all( FALSE, $_where, $_search );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/inventory/index',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _inventory_create()
	{
		$this->data['page']->title = 'Add new Inventory Item';

		// --------------------------------------------------------------------------

		//	Fetch data, this data is used in both the view and the form submission
		$this->data['product_types']	= $this->product->get_product_types();
		$this->data['currencies']		= $this->currency->get_all();

		//	Fetch product meta fields
		$_product_types						= $this->product->get_product_types();
		$this->data['product_types_meta']	= array();

		foreach ( $_product_types AS $type ) :

			if ( is_callable( array( $this->product, 'product_type_meta_fields_' . $type->slug ) ) ) :

				$this->data['product_types_meta'][$type->id] = $this->product->{'product_type_meta_fields_' . $type->slug}();

			else :

				$this->data['product_types_meta'][$type->id] = array();

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Process POST
		//$_POST = unserialize( 'a:16:{s:7:"type_id";s:1:"1";s:5:"title";s:26:"This is my product\'s title";s:9:"is_active";s:1:"1";s:6:"brands";a:2:{i:0;s:1:"1";i:1;s:1:"2";}s:10:"categories";a:2:{i:0;s:1:"0";i:1;s:1:"4";}s:4:"tags";a:1:{i:0;s:1:"1";}s:11:"tax_rate_id";s:1:"1";s:11:"description";s:280:"<p>This is my product&#39;s description, isn&#39;t it <em><strong>freaking awesome?!</strong></em></p><p>And here&#39;s a hottie:</p><p><img alt="" src="http://dev.nails.web/cdn/serve/user-1/1380278000_081d7f848ff0722517aa042ea64d1ddd.jpg" style="height:667px; width:500px" /></p>";s:9:"variation";a:2:{i:0;a:7:{s:5:"label";s:21:"First variation title";s:3:"sku";s:14:"SKU-THING-1234";s:18:"quantity_available";s:3:"100";s:13:"quantity_sold";s:1:"0";s:4:"meta";a:7:{s:6:"length";s:3:"100";s:5:"width";s:3:"100";s:6:"height";s:3:"100";s:16:"measurement_unit";s:1:"m";s:6:"weight";s:3:"100";s:11:"weight_unit";s:2:"kg";s:11:"download_id";s:0:"";}s:7:"pricing";a:3:{i:0;a:3:{s:11:"currency_id";s:2:"52";s:5:"price";s:3:"100";s:10:"sale_price";s:2:"50";}i:1;a:3:{s:11:"currency_id";s:2:"47";s:5:"price";s:3:"100";s:10:"sale_price";s:2:"50";}i:2;a:3:{s:11:"currency_id";s:3:"144";s:5:"price";s:0:"";s:10:"sale_price";s:0:"";}}s:7:"gallery";a:1:{i:0;s:1:"8";}}i:1;a:8:{s:5:"label";s:41:"Variation 2, very special collection only";s:3:"sku";s:14:"SKU-THING-4567";s:18:"quantity_available";s:1:"1";s:13:"quantity_sold";s:1:"0";s:4:"meta";a:7:{s:6:"length";s:4:"1000";s:5:"width";s:4:"1000";s:6:"height";s:4:"1000";s:16:"measurement_unit";s:2:"mm";s:6:"weight";s:4:"1000";s:11:"weight_unit";s:1:"g";s:11:"download_id";s:0:"";}s:7:"pricing";a:3:{i:0;a:3:{s:11:"currency_id";s:2:"52";s:5:"price";s:4:"1000";s:10:"sale_price";s:3:"500";}i:1;a:3:{s:11:"currency_id";s:2:"47";s:5:"price";s:4:"1000";s:10:"sale_price";s:3:"500";}i:2;a:3:{s:11:"currency_id";s:3:"144";s:5:"price";s:0:"";s:10:"sale_price";s:0:"";}}s:7:"gallery";a:1:{i:0;s:1:"7";}s:8:"shipping";a:1:{s:15:"collection_only";s:1:"1";}}}s:7:"gallery";a:2:{i:0;s:1:"7";i:1;s:1:"8";}s:10:"attributes";a:2:{i:0;a:2:{s:12:"attribute_id";s:1:"2";s:5:"value";s:9:"the value";}i:1;a:2:{s:12:"attribute_id";s:1:"1";s:5:"value";s:13:"another value";}}s:6:"ranges";a:2:{i:0;s:1:"1";i:1;s:1:"2";}s:11:"collections";a:1:{i:0;s:1:"2";}s:15:"seo_description";s:30:"SEO Description; this is nice.";s:12:"seo_keywords";s:12:"seo,keywords";s:6:"submit";s:6:"Create";}' );
		if ( $this->input->post() ) :

			//	Form validation, this'll be fun...
			$this->load->library( 'form_validation' );


			//	Product Info
			//	============
			$this->form_validation->set_rules( 'type_id',			'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'title',				'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'is_active',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'brands',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'categories',		'',	'xss_clean' );
			$this->form_validation->set_rules( 'tags',				'',	'xss_clean' );
			$this->form_validation->set_rules( 'tax_rate_id',		'',	'xss_clean|required' );

			// --------------------------------------------------------------------------

			//	Description
			//	===========
			$this->form_validation->set_rules( 'description',		'',	'required' );

			// --------------------------------------------------------------------------

			//	Variants - Loop variants
			//	========================
			foreach ( $this->input->post( 'variation' ) AS $index => $v ) :

				//	Details
				//	-------

				$this->form_validation->set_rules( 'variation[' . $index . '][label]',				'',	'xss_clean|required' );
				$this->form_validation->set_rules( 'variation[' . $index . '][sku]',				'',	'xss_clean' );
				$this->form_validation->set_rules( 'variation[' . $index . '][quantity_available]',	'',	'xss_clean|callback__callback_inventory_valid_quantity' );
				$this->form_validation->set_rules( 'variation[' . $index . '][quantity_sold]',		'',	'xss_clean|callback__callback_inventory_valid_quantity' );

				//	Meta
				//	----

				//	If this product type is_physical then ensure that the dimensions are specified
				$_rules			= 'xss_clean';
				$_rules_unit	= 'xss_clean';

				foreach( $this->data['product_types'] AS $type ) :

					if ( $type->id == $this->input->post( 'type_id' ) && $type->is_physical ) :

						$_rules			.= '|required|numeric';
						$_rules_unit	.= '|required';
						break;

					endif;

				endforeach;

				$this->form_validation->set_rules( 'variation[' . $index . '][meta][length]',			'',	$_rules );
				$this->form_validation->set_rules( 'variation[' . $index . '][meta][width]',			'',	$_rules );
				$this->form_validation->set_rules( 'variation[' . $index . '][meta][height]',			'',	$_rules );
				$this->form_validation->set_rules( 'variation[' . $index . '][meta][measurement_unit]',	'',	$_rules_unit );
				$this->form_validation->set_rules( 'variation[' . $index . '][meta][weight]',			'',	$_rules );
				$this->form_validation->set_rules( 'variation[' . $index . '][meta][weight_unit]',		'',	$_rules_unit );

				//	Any custom checks for the extra meta fields
				if ( isset( $this->data['product_types_meta'][$this->input->post( 'type_id' )] ) && $this->data['product_types_meta'][$this->input->post( 'type_id' )] ) :

					//	Process each rule
					foreach( $this->data['product_types_meta'][$this->input->post( 'type_id' )] AS $field ) :

						$this->form_validation->set_rules( 'variation[' . $index . '][meta][' . $field->key . ']',	'',	$field->validation );

					endforeach;

				endif;

				//	Pricing
				//	-------
				if ( isset( $v['pricing'] ) ) :

					foreach( $v['pricing'] AS $price_index => $price ) :

						$_required	= $price['currency_id'] == SHOP_BASE_CURRENCY_ID ? '|required' : '';

						$this->form_validation->set_rules( 'variation[' . $index . '][pricing][' . $price_index . '][price]',		'',	'xss_clean|callback__callback_inventory_valid_price' . $_required );
						$this->form_validation->set_rules( 'variation[' . $index . '][pricing][' . $price_index . '][sale_price]',	'',	'xss_clean|callback__callback_inventory_valid_price' . $_required );

					endforeach;

				endif;

				//	Gallery Associations
				//	--------------------
				if ( isset( $v['gallery'] ) ) :

					foreach( $v['gallery'] AS $gallery_index => $image ) :

						$this->form_validation->set_rules( 'variation[' . $index . '][gallery][' . $gallery_index . ']',	'',	'xss_clean' );

					endforeach;

				endif;

				//	Shipping
				//	--------

				$this->form_validation->set_rules( 'variation[' . $index . '][shipping][collection_only]',	'',	'xss_clean' );

			endforeach;


			// --------------------------------------------------------------------------

			//	Gallery
			$this->form_validation->set_rules( 'gallery',			'',	'xss_clean' );

			// --------------------------------------------------------------------------

			//	Attributes
			$this->form_validation->set_rules( 'attributes',		'',	'xss_clean' );

			// --------------------------------------------------------------------------

			//	Ranges & Collections
			$this->form_validation->set_rules( 'ranges',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'collections',		'',	'xss_clean' );

			// --------------------------------------------------------------------------

			//	SEO
			$this->form_validation->set_rules( 'seo_description',	'',	'xss_clean' );
			$this->form_validation->set_rules( 'seo_keywords',		'',	'xss_clean' );

			// --------------------------------------------------------------------------

			//	Set messages
			$this->form_validation->set_message( 'required',	lang( 'fv_required' ) );
			$this->form_validation->set_message( 'numeric',		lang( 'fv_numeric' ) );
			$this->form_validation->set_message( 'is_natural',	lang( 'fv_is_natural' ) );

			// --------------------------------------------------------------------------

			if ( $this->form_validation->run( $this ) ) :

				//	Validated! Create the product
				$_product = $this->product->create( $this->input->post() );

				if ( $_product ) :

					$this->session->set_flashdata( 'success', '<strong>Success!</strong> Product was created successfully.' );
					redirect( 'admin/shop/inventory' );
					return;

				else :

					$this->data['error'] = '<strong>Sorry,</strong> there was a problem creating the Product. ' . $this->product->last_error();

				endif;

			else :

				$this->data['error'] = '<strong>Whoops!</strong> There are some problems with the form.';

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load additional models
		$this->load->model( 'shop/shop_attribute_model',	'attribute' );
		$this->load->model( 'shop/shop_brand_model',		'brand' );
		$this->load->model( 'shop/shop_category_model',		'category' );
		$this->load->model( 'shop/shop_collection_model',	'collection' );
		$this->load->model( 'shop/shop_range_model',		'range' );
		$this->load->model( 'shop/shop_tag_model',			'tag' );

		// --------------------------------------------------------------------------

		//	Fetch additional data
		$this->data['product_types_flat']	= $this->product->get_product_types_flat();
		$this->data['tax_rates']			= $this->tax->get_all_flat();
		$this->data['attributes']			= $this->attribute->get_all_flat();
		$this->data['brands']				= $this->brand->get_all_flat();
		$this->data['categories']			= $this->category->get_all_nested_flat();
		$this->data['collections']			= $this->collection->get_all();
		$this->data['ranges']				= $this->range->get_all();
		$this->data['tags']					= $this->tag->get_all_flat();

		array_unshift( $this->data['tax_rates'], 'No Tax');

		// --------------------------------------------------------------------------

		//	Assets
		$this->asset->library( 'ckeditor' );
		$this->asset->library( 'nails_api' );
		$this->asset->load( 'jquery.serializeobject.min.js', TRUE );
		$this->asset->load( 'nails.admin.shop.inventory.create_edit.min.js', TRUE );
		$this->asset->load( 'jquery.uploadify.min.js', TRUE );
		$this->asset->load( 'mustache.min.js', TRUE );

		// --------------------------------------------------------------------------

		//	Libraries
		$this->load->library( 'mustache' );

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/inventory/create',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	public function _callback_inventory_valid_price( $str )
	{
		$str = trim( $str );

		if ( $str && ! is_numeric( $str ) ) :

			$this->form_validation->set_message( '_callback_inventory_valid_price', 'This is not a valid price' );
			return FALSE;

		else :

			return TRUE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function _callback_inventory_valid_quantity( $str )
	{
		$str = trim( $str );

		if ( $str && ! is_numeric( $str ) ) :

			$this->form_validation->set_message( '_callback_inventory_valid_quantity', 'This is not a valid quantity' );
			return FALSE;

		elseif ( $str && is_numeric( $str ) && $str < 0 ) :

			$this->form_validation->set_message( '_callback_inventory_valid_quantity', lang( 'fv_is_natural' ) );
			return FALSE;

		else :

			return TRUE;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _inventory_edit()
	{
		dump( 'edit inventory' );
	}


	// --------------------------------------------------------------------------


	protected function _inventory_delete()
	{
		dump( 'delete inventory' );
	}


	// --------------------------------------------------------------------------


	protected function _inventory_restore()
	{
		dump( 'restore inventory' );
	}


	// --------------------------------------------------------------------------


	public function orders()
	{
		switch( $this->uri->segment( '4' ) ) :

			case 'view' :		$this->_orders_view();		break;
			case 'reprocess' :	$this->_orders_reprocess();	break;
			case 'process' :	$this->_orders_process();	break;
			case 'index' :
			default :			$this->_orders_index();		break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Manage orders
	 *
	 * @access protected
	 * @param none
	 * @return void
	 **/
	protected function _orders_index()
	{
		//	Set method info
		$this->data['page']->title = 'Manage Orders';

		// --------------------------------------------------------------------------

		//	Searching, sorting, ordering and paginating.
		$_hash = 'search_' . md5( uri_string() ) . '_';

		if ( $this->input->get( 'reset' ) ) :

			$this->session->unset_userdata( $_hash . 'per_page' );
			$this->session->unset_userdata( $_hash . 'sort' );
			$this->session->unset_userdata( $_hash . 'order' );

		endif;

		$_default_per_page	= $this->session->userdata( $_hash . 'per_page' ) ? $this->session->userdata( $_hash . 'per_page' ) : 50;
		$_default_sort		= $this->session->userdata( $_hash . 'sort' ) ? 	$this->session->userdata( $_hash . 'sort' ) : 'o.id';
		$_default_order		= $this->session->userdata( $_hash . 'order' ) ? 	$this->session->userdata( $_hash . 'order' ) : 'desc';

		//	Define vars
		$_search = array( 'keywords' => $this->input->get( 'search' ), 'columns' => array() );

		foreach ( $this->shop_orders_sortfields AS $field ) :

			$_search['columns'][strtolower( $field['label'] )] = $field['col'];

		endforeach;

		$_limit		= array(
						$this->input->get( 'per_page' ) ? $this->input->get( 'per_page' ) : $_default_per_page,
						$this->input->get( 'offset' ) ? $this->input->get( 'offset' ) : 0
					);
		$_order		= array(
						$this->input->get( 'sort' ) ? $this->input->get( 'sort' ) : $_default_sort,
						$this->input->get( 'order' ) ? $this->input->get( 'order' ) : $_default_order
					);

		//	Set sorting and ordering info in session data so it's remembered for when user returns
		$this->session->set_userdata( $_hash . 'per_page', $_limit[0] );
		$this->session->set_userdata( $_hash . 'sort', $_order[0] );
		$this->session->set_userdata( $_hash . 'order', $_order[1] );

		//	Set values for the page
		$this->data['search']				= new stdClass();
		$this->data['search']->per_page		= $_limit[0];
		$this->data['search']->sort			= $_order[0];
		$this->data['search']->order		= $_order[1];
		$this->data['search']->show			= $this->input->get( 'show' );
		$this->data['search']->fulfilled	= $this->input->get( 'fulfilled' );

		// --------------------------------------------------------------------------

		//	Prepare the where
		if ( $this->data['search']->show || $this->data['search']->fulfilled ) :

			$_where = '( ';

			if ( $this->data['search']->show ) :

				$_where .= '`o`.`status` IN (';

					$_statuses = array_keys( $this->data['search']->show );
					foreach ( $_statuses AS &$stat ) :

						$stat = strtoupper( $stat );

					endforeach;
					$_where .= "'" . implode( "','", $_statuses ) . "'";

				$_where .= ')';

			endif;

			// --------------------------------------------------------------------------

			if ( $this->data['search']->show && $this->data['search']->fulfilled ) :

				$_where .= ' AND ';

			endif;

			// --------------------------------------------------------------------------

			if ( $this->data['search']->fulfilled ) :

				$_where .= '`o`.`fulfilment_status` IN (';

					$_statuses = array_keys( $this->data['search']->fulfilled );
					foreach ( $_statuses AS &$stat ) :

						$stat = strtoupper( $stat );

					endforeach;
					$_where .= "'" . implode( "','", $_statuses ) . "'";

				$_where .= ')';

			endif;

			$_where .= ')';

		else :

			$_where = NULL;

		endif;

		// --------------------------------------------------------------------------

		//	Pass any extra data to the view
		$this->data['actions']		= $this->shop_orders_actions;
		$this->data['sortfields']	= $this->shop_orders_sortfields;

		// --------------------------------------------------------------------------

		//	Fetch orders
		$this->load->model( 'shop/shop_order_model', 'order' );

		$this->data['orders']		= new stdClass();
		$this->data['orders']->data = $this->order->get_all( $_order, $_limit, $_where, $_search );

		//	Work out pagination
		$this->data['orders']->pagination					= new stdClass();
		$this->data['orders']->pagination->total_results	= $this->order->count_orders( $_where, $_search );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/orders/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * View order
	 *
	 * @access protected
	 * @param none
	 * @return void
	 **/
	protected function _orders_view()
	{
		if ( ! $this->user->has_permission( 'admin.shop.orders_view' ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to view order details.' );
			redirect( 'admin/shop/orders' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch and check order
		$this->load->model( 'shop/shop_order_model', 'order' );

		$this->data['order'] = $this->order->get_by_id( $this->uri->segment( 5 ) );

		if ( ! $this->data['order'] ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> no order exists by that ID.' );
			redirect( 'admin/shop/orders' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Fulfilled?
		$this->load->helper( 'date' );
		if ( $this->data['order']->status == 'PAID' ) :

			if ( $this->data['order']->fulfilment_status == 'UNFULFILLED' ) :

				$this->data['message'] = '<strong>This order has not been fulfilled; order was placed ' . nice_time( strtotime( $this->data['order']->created ) ) . '</strong><br />Once all purchased items are marked as processed the order will be automatically marked as fulfilled.';

			elseif ( ! $this->data['success'] ):

				$this->data['success'] = '<strong>This order was fulfilled ' . nice_time( strtotime( $this->data['order']->fulfilled ) ) . '</strong>';

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Set method info
		$this->data['page']->title = 'View Order &rsaquo; ' . $this->data['order']->ref;

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/orders/view',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}

	// --------------------------------------------------------------------------

	protected function _orders_reprocess()
	{
		if ( ! $this->user->has_permission( 'admin.shop.orders_reprocess' ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to reprocess orders.' );
			redirect( 'admin/shop/orders' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Check order exists
		$this->load->model( 'shop/shop_order_model', 'order' );
		$_order = $this->order->get_by_id( $this->uri->segment( 5 ) );

		if ( ! $_order ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I couldn\'t find an order by that ID.' );
			redirect( 'admin/shop/orders' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	PROCESSSSSS...
		$this->order->process( $_order );

		// --------------------------------------------------------------------------

		//	Send a receipt to the customer
		$this->order->send_receipt( $_order );

		// --------------------------------------------------------------------------

		//	Send a notification to the store owner(s)
		$this->order->send_order_notification( $_order );

		// --------------------------------------------------------------------------

		if ( $_order->voucher ) :

			//	Redeem the voucher, if it's there
			$this->load->model( 'shop/shop_voucher_model', 'voucher' );
			$this->voucher->redeem( $_order->voucher->id, $_order );

		endif;

		// --------------------------------------------------------------------------

		$this->session->set_flashdata( 'success', '<strong>Success!</strong> Order was processed succesfully. The user has been sent a receipt.' );
		redirect( 'admin/shop/orders' );
	}


	// --------------------------------------------------------------------------


	protected function _orders_process()
	{
		if ( ! $this->user->has_permission( 'admin.shop.orders_process' ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to process order items.' );
			redirect( 'admin/shop/orders' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$_order_id		= $this->uri->segment( 5 );
		$_product_id	= $this->uri->segment( 6 );
		$_is_fancybox	= $this->input->get( 'is_fancybox' ) ? '?is_fancybox=true' : '';

		// --------------------------------------------------------------------------

		//	Update item
		if ( $this->uri->segment( 7 ) == 'processed' ) :

			$this->db->set( 'processed', TRUE );

		else :

			$this->db->set( 'processed', FALSE );

		endif;

		$this->db->where( 'order_id',	$_order_id );
		$this->db->where( 'id',			$_product_id );

		$this->db->update( NAILS_DB_PREFIX . 'shop_order_product' );

		if ( $this->db->affected_rows() ) :

			//	Product updated, check if order has been fulfilled
			$this->db->where( 'order_id', $_order_id );
			$this->db->where( 'processed', FALSE );

			if ( ! $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_order_product' ) ) :

				//	No unprocessed items, consider order FULFILLED
				$this->load->model( 'shop/shop_order_model', 'order' );
				$this->order->fulfil( $_order_id );

			else :

				//	Still some unprocessed items, mark as unfulfilled (in case it was already fulfilled)
				$this->load->model( 'shop/shop_order_model', 'order' );
				$this->order->unfulfil( $_order_id );

			endif;

			// --------------------------------------------------------------------------

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Product\'s status was updated successfully.' );
			redirect( 'admin/shop/orders/view/' . $_order_id . $_is_fancybox );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I was not able to update the status of that product.' );
			redirect( 'admin/shop/orders/view/' . $_order_id . $_is_fancybox );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Manage vouchers
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function vouchers()
	{
		//	Load voucher model
		$this->load->model( 'shop/shop_voucher_model', 'voucher' );

		// --------------------------------------------------------------------------

		switch( $this->uri->segment( '4' ) ) :

			case 'create' :		$this->_vouchers_create();		break;
			case 'activate' :	$this->_vouchers_activate();	break;
			case 'deactivate' :	$this->_vouchers_deactivate();	break;
			case 'index' :
			default :			$this->_vouchers_index();		break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _vouchers_index()
	{
		//	Set method info
		$this->data['page']->title = 'Manage Vouchers';

		// --------------------------------------------------------------------------

		//	Searching, sorting, ordering and paginating.
		$_hash = 'search_' . md5( uri_string() ) . '_';

		if ( $this->input->get( 'reset' ) ) :

			$this->session->unset_userdata( $_hash . 'per_page' );
			$this->session->unset_userdata( $_hash . 'sort' );
			$this->session->unset_userdata( $_hash . 'order' );

		endif;

		$_default_per_page	= $this->session->userdata( $_hash . 'per_page' ) ? $this->session->userdata( $_hash . 'per_page' ) : 50;
		$_default_sort		= $this->session->userdata( $_hash . 'sort' ) ? 	$this->session->userdata( $_hash . 'sort' ) : 'v.id';
		$_default_order		= $this->session->userdata( $_hash . 'order' ) ? 	$this->session->userdata( $_hash . 'order' ) : 'desc';

		//	Define vars
		$_search = array( 'keywords' => $this->input->get( 'search' ), 'columns' => array() );

		foreach ( $this->shop_vouchers_sortfields AS $field ) :

			$_search['columns'][strtolower( $field['label'] )] = $field['col'];

		endforeach;

		$_limit		= array(
						$this->input->get( 'per_page' ) ? $this->input->get( 'per_page' ) : $_default_per_page,
						$this->input->get( 'offset' ) ? $this->input->get( 'offset' ) : 0
					);
		$_order		= array(
						$this->input->get( 'sort' ) ? $this->input->get( 'sort' ) : $_default_sort,
						$this->input->get( 'order' ) ? $this->input->get( 'order' ) : $_default_order
					);

		//	Set sorting and ordering info in session data so it's remembered for when user returns
		$this->session->set_userdata( $_hash . 'per_page', $_limit[0] );
		$this->session->set_userdata( $_hash . 'sort', $_order[0] );
		$this->session->set_userdata( $_hash . 'order', $_order[1] );

		//	Set values for the page
		$this->data['search']				= new stdClass();
		$this->data['search']->per_page		= $_limit[0];
		$this->data['search']->sort			= $_order[0];
		$this->data['search']->order		= $_order[1];
		$this->data['search']->show			= $this->input->get( 'show' );

		// --------------------------------------------------------------------------

		//	Prepare the where
		if ( $this->data['search']->show ) :

			$_where = '( ';

			if ( $this->data['search']->show ) :

				$_where .= '`v`.`type` IN (';

					$_statuses = array_keys( $this->data['search']->show );
					foreach ( $_statuses AS &$stat ) :

						$stat = strtoupper( $stat );

					endforeach;
					$_where .= "'" . implode( "','", $_statuses ) . "'";

				$_where .= ')';

			endif;

			$_where .= ')';

		else :

			$_where = NULL;

		endif;

		// --------------------------------------------------------------------------

		//	Pass any extra data to the view
		$this->data['actions']		= $this->shop_vouchers_actions;
		$this->data['sortfields']	= $this->shop_vouchers_sortfields;

		// --------------------------------------------------------------------------

		//	Fetch vouchers
		$this->data['vouchers']		= new stdClass();
		$this->data['vouchers']->data = $this->voucher->get_all( FALSE, $_order, $_limit, $_where, $_search );

		//	Work out pagination
		$this->data['vouchers']->pagination					= new stdClass();
		$this->data['vouchers']->pagination->total_results	= $this->voucher->count_vouchers( FALSE, $_where, $_search );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/vouchers/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _vouchers_create()
	{
		if ( ! $this->user->has_permission( 'admin.shop.vouchers_create' ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to create vouchers.' );
			redirect( 'admin/shop/vouchers' );
			return;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->input->post() ) :

			$this->load->library( 'form_validation' );

			//	Common
			$this->form_validation->set_rules( 'type',					'', 'required|callback__callback_voucher_valid_type' );
			$this->form_validation->set_rules( 'code',					'', 'required|is_unique[' . NAILS_DB_PREFIX . 'shop_voucher.code]|callback__callback_voucher_valid_code' );
			$this->form_validation->set_rules( 'label',					'', 'required' );
			$this->form_validation->set_rules( 'valid_from',			'', 'required|callback__callback_voucher_valid_from' );
			$this->form_validation->set_rules( 'valid_to',				'', 'callback__callback_voucher_valid_to' );

			//	Voucher Type specific rules
			switch ( $this->input->post( 'type' ) ) :

				case 'LIMITED_USE' :

					$this->form_validation->set_rules( 'limited_use_limit',	'', 'required|is_natural_no_zero' );

					$this->form_validation->set_message( 'is_natural_no_zero',	'Only positive integers are valid.' );

					$this->form_validation->set_rules( 'discount_type',			'', 'required|callback__callback_voucher_valid_discount_type' );
					$this->form_validation->set_rules( 'discount_application',	'', 'required|callback__callback_voucher_valid_discount_application' );

				break;

				case 'NORMAL' :
				default :

					$this->form_validation->set_rules( 'discount_type',			'', 'required|callback__callback_voucher_valid_discount_type' );
					$this->form_validation->set_rules( 'discount_application',	'', 'required|callback__callback_voucher_valid_discount_application' );

				break;

				case 'GIFT_CARD' :

					//	Quick hack
					$_POST['discount_type']			= 'AMOUNT';
					$_POST['discount_application']	= 'ALL';

				break;

			endswitch;

			//	Discount Type specific rules
			switch ( $this->input->post( 'discount_type' ) ) :

				case 'PERCENTAGE' :

					$this->form_validation->set_rules( 'discount_value',	'', 'required|is_natural_no_zero|greater_than[0]|less_than[101]' );

					$this->form_validation->set_message( 'greater_than',		'Must be in the range 1-100' );
					$this->form_validation->set_message( 'less_than',			'Must be in the range 1-100' );

				break;

				case 'AMOUNT' :

					$this->form_validation->set_rules( 'discount_value',	'', 'required|numeric|greater_than[0]' );

					$this->form_validation->set_message( 'greater_than',		'Must be greater than 0' );

				break;

				default:

					//	No specific rules

				break;

			endswitch;

			//	Discount application specific rules
			switch ( $this->input->post( 'discount_application' ) ) :

				case 'PRODUCT_TYPES' :

					$this->form_validation->set_rules( 'product_type_id',	'', 'required|callback__callback_voucher_valid_product_type' );

					$this->form_validation->set_message( 'greater_than',		'Must be greater than 0' );

				break;


				case 'PRODUCTS' :
				case 'SHIPPING' :
				case 'ALL' :
				default :

					//	No specific rules

				break;

			endswitch;

			$this->form_validation->set_message( 'required',			lang( 'fv_required' ) );
			$this->form_validation->set_message( 'is_unique',			'Code already in use.' );


			if ( $this->form_validation->run( $this ) ) :

				//	Prepare the $_data variable
				$_data	= array();

				$_data['type']					= $this->input->post( 'type' );
				$_data['code']					= strtoupper( $this->input->post( 'code' ) );
				$_data['discount_type']			= $this->input->post( 'discount_type' );
				$_data['discount_value']		= $this->input->post( 'discount_value' );
				$_data['discount_application']	= $this->input->post( 'discount_application' );
				$_data['label']					= $this->input->post( 'label' );
				$_data['valid_from']			= $this->input->post( 'valid_from' );
				$_data['is_active']				= TRUE;

				if ( $this->input->post( 'valid_to' ) ) :

					$_data['valid_to']			= $this->input->post( 'valid_to' );

				endif;

				//	Define specifics
				if ( $this->input->post( 'type' ) == 'GIFT_CARD' ) :

					$_data['gift_card_balance']		= $this->input->post( 'discount_value' );
					$_data['discount_type']			= 'AMOUNT';
					$_data['discount_application']	= 'ALL';

				endif;

				if ( $this->input->post( 'type' ) == 'LIMITED_USE' ) :

					$_data['limited_use_limit']	= $this->input->post( 'limited_use_limit' );

				endif;

				if ( $this->input->post( 'discount_application' ) == 'PRODUCT_TYPES' ) :

					$_data['product_type_id']	= $this->input->post( 'product_type_id' );

				endif;

				// --------------------------------------------------------------------------

				//	Attempt to create
				if ( $this->voucher->create( $_data ) ) :

					$this->session->set_flashdata( 'success', '<strong>Success!</strong> Voucher "' . $_data['code'] . '" was created successfully.' );
					redirect( 'admin/shop/vouchers' );

				else :

					$this->data['error'] = '<strong>Sorry,</strong> there was a problem creating the voucher. '  . $this->voucher->last_error();

				endif;

			else :

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		$this->data['page']->title = 'Create Voucher';

		// --------------------------------------------------------------------------

		//	Fetch data
		$this->data['product_types'] = $this->product->get_product_types_flat();

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->load( 'nails.admin.shop.vouchers.min.js', TRUE );

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/vouchers/create',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	public function _callback_voucher_valid_code( &$str )
	{
		$str = strtoupper( $str );

		if  ( preg_match( '/[^a-zA-Z0-9]/', $str ) ) :

			$this->form_validation->set_message( '_callback_voucher_valid_code', 'Invalid characters.' );
			return FALSE;

		else :

			return TRUE;

		endif;

	}

	public function _callback_voucher_valid_type( $str )
	{
		$_valid_types = array('NORMAL','LIMITED_USE','GIFT_CARD');
		$this->form_validation->set_message( '_callback_voucher_valid_type', 'Invalid voucher type.' );
		return array_search( $str, $_valid_types ) !== FALSE;
	}

	public function _callback_voucher_valid_discount_type( $str )
	{
		$_valid_types = array('PERCENTAGE','AMOUNT');
		$this->form_validation->set_message( '_callback_voucher_valid_discount_type', 'Invalid discount type.' );
		return array_search( $str, $_valid_types ) !== FALSE;
	}

	public function _callback_voucher_valid_product_type( $str )
	{
		$this->form_validation->set_message( '_callback_voucher_valid_product_type', 'Invalid product type.' );
		return (bool) $this->product->get_product_type_by_id( $str );
	}

	public function _callback_voucher_valid_from( &$str )
	{
		//	Check $str is a valid date
		$_date = date( 'Y-m-d H:i:s', strtotime( $str ) );

		//	Check format of str
		if ( preg_match( '/^\d\d\d\d\-\d\d-\d\d$/', trim( $str ) ) ) :

			//in YYYY-MM-DD format, add the time
			$str = trim( $str ) . ' 00:00:00';

		endif;

		if ( $_date != $str ) :

			$this->form_validation->set_message( '_callback_voucher_valid_from', 'Invalid date.' );
			return FALSE;

		endif;

		//	If valid_to is defined make sure valid_from isn't before it
		if ( $this->input->post( 'valid_to' ) ) :

			$_date = strtotime( $this->input->post( 'valid_to' ) );

			if ( strtotime( $str ) >= $_date ) :

				$this->form_validation->set_message( '_callback_voucher_valid_from', 'Valid From date cannot be after Valid To date.' );
				return FALSE;

			endif;

		endif;

		return TRUE;
	}

	public function _callback_voucher_valid_to( &$str )
	{
		//	If empty ignore
		if ( ! $str )
			return TRUE;

		// --------------------------------------------------------------------------

		//	Check $str is a valid date
		$_date = date( 'Y-m-d H:i:s', strtotime( $str ) );

		//	Check format of str
		if ( preg_match( '/^\d\d\d\d\-\d\d\-\d\d$/', trim( $str ) ) ) :

			//in YYYY-MM-DD format, add the time
			$str = trim( $str ) . ' 00:00:00';

		endif;

		if ( $_date != $str ) :

			$this->form_validation->set_message( '_callback_voucher_valid_to', 'Invalid date.' );
			return FALSE;

		endif;

		//	Make sure valid_from isn't before it
		$_date = strtotime( $this->input->post( 'valid_from' ) );

		if ( strtotime( $str ) <= $_date ) :

			$this->form_validation->set_message( '_callback_voucher_valid_to', 'Valid To date cannot be before Valid To date.' );
			return FALSE;

		endif;

		return TRUE;
	}


	// --------------------------------------------------------------------------


	protected function _vouchers_activate()
	{
		if ( ! $this->user->has_permission( 'admin.shop.vouchers_activate' ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to activate vouchers.' );
			redirect( 'admin/shop/vouchers' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$_id = $this->uri->segment( 5 );

		if ( $this->voucher->update( $_id, array( 'is_active' => TRUE ) ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Voucher was activated successfully.' );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> There was a problem activating the voucher. ' . $this->voucher->last_error() );

		endif;

		redirect( 'admin/shop/vouchers' );
	}


	// --------------------------------------------------------------------------


	protected function _vouchers_deactivate()
	{
		if ( ! $this->user->has_permission( 'admin.shop.vouchers_deactivate' ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to suspend vouchers.' );
			redirect( 'admin/shop/vouchers' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$_id = $this->uri->segment( 5 );

		if ( $this->voucher->update( $_id, array( 'is_active' => FALSE ) ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Voucher was suspended successfully.' );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> There was a problem suspending the voucher. ' . $this->voucher->last_error() );

		endif;

		redirect( 'admin/shop/vouchers' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Other managers
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function sales()
	{
		switch( $this->uri->segment( '4' ) ) :

			case 'add' :		$this->_sales_add();	break;
			case 'edit' :		$this->_sales_edit();	break;
			case 'delete' :		$this->_sales_delete();	break;
			case 'index' :
			default :			$this->_sales_index();	break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _sales_index()
	{
		dump( 'List Sales' );
	}


	// --------------------------------------------------------------------------


	protected function _sales_add()
	{
		dump( 'Add Sale' );
	}


	// --------------------------------------------------------------------------


	protected function _sales_edit()
	{
		dump( 'Edit Sale' );
	}


	// --------------------------------------------------------------------------


	protected function _sales_delete()
	{
		dump( 'Delete Sale' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Other managers
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function manage()
	{
		//	Load voucher model
		$this->load->model( 'shop/shop_voucher_model', 'voucher' );

		// --------------------------------------------------------------------------

		switch( $this->uri->segment( '4' ) ) :

			case 'attributes' :			$this->_manage_attributes();			break;
			case 'brands' :				$this->_manage_brands();				break;
			case 'categories' :			$this->_manage_categories();			break;
			case 'collections' :		$this->_manage_collections();				break;
			case 'ranges' :				$this->_manage_ranges();				break;
			case 'shipping_methods' :	$this->_manage_shipping_methods();		break;
			case 'tags' :				$this->_manage_tags();					break;
			case 'tax_rates' :			$this->_manage_tax_rates();				break;
			case 'types' :				$this->_manage_types();					break;

			// --------------------------------------------------------------------------

			case 'index' :
			default :					show_404();								break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _manage_attributes()
	{
		if ( $this->input->post() ) :

			dumpanddie( $_POST );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch data
		//	TODO

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/manage/attributes',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _manage_brands()
	{
		//	Load model
		$this->load->model( 'shop/shop_brand_model', 'brand' );

		// --------------------------------------------------------------------------

		if ( $this->input->post() ) :

			switch( $this->input->post( 'action' ) ) :

				case 'create' :

					$this->load->library( 'form_validation' );

					$this->form_validation->set_rules( 'label',				'',	'xss_clean|required' );
					$this->form_validation->set_rules( 'logo_id',			'',	'xss_clean' );
					$this->form_validation->set_rules( 'description',		'',	'xss_clean' );
					$this->form_validation->set_rules( 'seo_description',	'',	'xss_clean' );
					$this->form_validation->set_rules( 'seo_keywords',		'',	'xss_clean' );

					$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

					if ( $this->form_validation->run() ) :

						$_data					= new stdClass();
						$_data->label			= $this->input->post( 'label' );
						$_data->logo_id			= $this->input->post( 'logo_id' );
						$_data->description		= $this->input->post( 'description' );
						$_data->seo_description	= $this->input->post( 'seo_description' );
						$_data->seo_keywords	= $this->input->post( 'seo_keywords' );

						if ( $this->brand->create( $_data ) ) :

							//	Redirect to clear form
							$this->session->Set_flashdata( 'success', '<strong>Success!</strong> Brand created successfully.' );
							redirect( 'admin/shop/manage/brands?' . $_SERVER['QUERY_STRING'] );
							return;

						else :

							$this->data['error']	= '<strong>Sorry,</strong> there was a problem creating the Brand. ' . $this->brand->last_error();
							$this->data['show_tab']	= 'create';

						endif;

					else :

						$this->data['error']	= '<strong>Sorry,</strong> there was a problem creating the Brand.';
						$this->data['show_tab']	= 'create';

					endif;

				break;

				case 'edit' :

					$this->load->library( 'form_validation' );

					$_id = $this->input->post( 'id' );
					$this->form_validation->set_rules( $_id . '[label]',			'',	'xss_clean|required' );
					$this->form_validation->set_rules( $_id . '[logo_id]',			'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[description]',		'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[seo_description]',	'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[seo_keywords]',		'',	'xss_clean' );

					$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

					if ( $this->form_validation->run() ) :

						$_data					= new stdClass();
						$_data->label			= isset( $_POST[$_id]['label'] )			? $_POST[$_id]['label']				: NULL;
						$_data->logo_id			= isset( $_POST[$_id]['logo_id'] )			? $_POST[$_id]['logo_id']			: NULL;
						$_data->description		= isset( $_POST[$_id]['description'] )		? $_POST[$_id]['description']		: NULL;
						$_data->seo_description	= isset( $_POST[$_id]['seo_description'] )	? $_POST[$_id]['seo_description']	: NULL;
						$_data->seo_keywords	= isset( $_POST[$_id]['seo_keywords'] )		? $_POST[$_id]['seo_keywords']		: NULL;

						if ( $this->brand->update( $_id, $_data ) ) :

							$this->data['success']	= '<strong>Success!</strong> Brand saved successfully.';

						else :

							$this->data['error']	= '<strong>Sorry,</strong> there was a problem saving the Brand. ' . $this->brand->last_error();
							$this->data['show_tab']	= 'edit';

						endif;

					else :

						$this->data['error']	= '<strong>Sorry,</strong> there was a problem saving the Brand.';
						$this->data['show_tab']	= 'edit';

					endif;

				break;

				case 'delete' :

					$_id = $this->input->post( 'id' );
					if ( $this->brand->delete( $_id ) ) :

						$this->data['success'] = '<strong>Success!</strong> Brand was deleted successfully.';

					else :

						$this->data['error'] = '<strong>Sorry,</strong> there was a problem deleting the Brand; it may be in use.';

					endif;

				break;

			endswitch;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch data
		$this->data['brands'] = $this->brand->get_all( TRUE );

		// --------------------------------------------------------------------------

		//	Page data
		$this->data['page']->title = 'Manage &rsaquo; Brands';

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/manage/brands',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}



	// --------------------------------------------------------------------------


	protected function _manage_categories()
	{
		//	Load model
		$this->load->model( 'shop/shop_category_model', 'category' );

		// --------------------------------------------------------------------------

		if ( $this->input->post() ) :

			switch( $this->input->post( 'action' ) ) :

				case 'create' :

					$this->load->library( 'form_validation' );

					$this->form_validation->set_rules( 'label',				'',	'xss_clean|required' );
					$this->form_validation->set_rules( 'parent_id',			'',	'xss_clean' );
					$this->form_validation->set_rules( 'description',		'',	'xss_clean' );
					$this->form_validation->set_rules( 'seo_description',	'',	'xss_clean' );
					$this->form_validation->set_rules( 'seo_keywords',		'',	'xss_clean' );

					$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

					if ( $this->form_validation->run() ) :

						$_data					= new stdClass();
						$_data->label			= $this->input->post( 'label' );
						$_data->parent_id		= $this->input->post( 'parent_id' );
						$_data->description		= $this->input->post( 'description' );
						$_data->seo_description	= $this->input->post( 'seo_description' );
						$_data->seo_keywords	= $this->input->post( 'seo_keywords' );

						if ( $this->category->create( $_data ) ) :

							//	Redirect to clear form
							$this->session->Set_flashdata( 'success', '<strong>Success!</strong> Category created successfully.' );
							redirect( 'admin/shop/manage/categories?' . $_SERVER['QUERY_STRING'] );
							return;

						else :

							$this->data['error']	= '<strong>Sorry,</strong> there was a problem creating the Category. ' . $this->category->last_error();
							$this->data['show_tab']	= 'create';

						endif;

					else :

						$this->data['error']	= '<strong>Sorry,</strong> there was a problem creating the Category.';
						$this->data['show_tab']	= 'create';

					endif;

				break;

				case 'edit' :

					$this->load->library( 'form_validation' );

					$_id = $this->input->post( 'id' );
					$this->form_validation->set_rules( $_id . '[label]',			'',	'xss_clean|required' );
					$this->form_validation->set_rules( $_id . '[logo_id]',			'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[description]',		'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[seo_description]',	'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[seo_keywords]',		'',	'xss_clean' );

					$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

					if ( $this->form_validation->run() ) :

						$_data					= new stdClass();
						$_data->label			= isset( $_POST[$_id]['label'] )			? $_POST[$_id]['label']				: NULL;
						$_data->logo_id			= isset( $_POST[$_id]['logo_id'] )			? $_POST[$_id]['logo_id']			: NULL;
						$_data->description		= isset( $_POST[$_id]['description'] )		? $_POST[$_id]['description']		: NULL;
						$_data->seo_description	= isset( $_POST[$_id]['seo_description'] )	? $_POST[$_id]['seo_description']	: NULL;
						$_data->seo_keywords	= isset( $_POST[$_id]['seo_keywords'] )		? $_POST[$_id]['seo_keywords']		: NULL;

						if ( $this->category->update( $_id, $_data ) ) :

							$this->data['success']	= '<strong>Success!</strong> Category saved successfully.';

						else :

							$this->data['error']	= '<strong>Sorry,</strong> there was a problem saving the Category. ' . $this->category->last_error();
							$this->data['show_tab']	= 'edit';

						endif;

					else :

						$this->data['error']	= '<strong>Sorry,</strong> there was a problem saving the Category.';
						$this->data['show_tab']	= 'edit';

					endif;

				break;

				case 'delete' :

					$_id = $this->input->post( 'id' );
					if ( $this->category->delete( $_id ) ) :

						$this->data['success'] = '<strong>Success!</strong> Category was deleted successfully.';

					else :

						$this->data['error'] = '<strong>Sorry,</strong> there was a problem deleting the Category; it may be in use.';

					endif;

				break;

			endswitch;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch data
		$this->data['categories'] = $this->category->get_all( FALSE, TRUE );

		// --------------------------------------------------------------------------

		//	Page data
		$this->data['page']->title = 'Manage &rsaquo; Categories';

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/manage/categories',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _manage_collections()
	{
		if ( $this->input->post() ) :

			dumpanddie( $_POST );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch data
		//	TODO

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/manage/collections',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _manage_ranges()
	{
		if ( $this->input->post() ) :

			dumpanddie( $_POST );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch data
		//	TODO

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/manage/ranges',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _manage_shipping_methods()
	{
		if ( $this->input->post() ) :

			dumpanddie( $_POST );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch data
		//	TODO

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',						$this->data );
		$this->load->view( 'admin/shop/manage/shipping_methods',	$this->data );
		$this->load->view( 'structure/footer',						$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _manage_tags()
	{
		if ( $this->input->post() ) :

			dumpanddie( $_POST );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch data
		//	TODO

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/manage/tags',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _manage_tax_rates()
	{
		if ( $this->input->post() ) :

			dumpanddie( $_POST );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch data
		//	TODO

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/manage/tax_rates',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _manage_types()
	{
		if ( $this->input->post() ) :

			switch( $this->input->post( 'action' ) ) :

				case 'create' :

					$this->load->library( 'form_validation' );

					$this->form_validation->set_rules( 'label',				'',	'xss_clean|required' );
					$this->form_validation->set_rules( 'description',		'',	'xss_clean' );
					$this->form_validation->set_rules( 'is_physical',		'',	'xss_clean' );
					$this->form_validation->set_rules( 'ipn_method',		'',	'xss_clean' );
					$this->form_validation->set_rules( 'max_per_order',		'',	'xss_clean' );
					$this->form_validation->set_rules( 'max_variations',	'',	'xss_clean' );

					$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

					if ( $this->form_validation->run() ) :

						$_data					= new stdClass();
						$_data->label			= $this->input->post( 'label' );
						$_data->description		= $this->input->post( 'description' );
						$_data->is_physical		= $this->input->post( 'is_physical' );
						$_data->ipn_method		= $this->input->post( 'ipn_method' );
						$_data->max_per_order	= $this->input->post( 'max_per_order' );
						$_data->max_variations	= $this->input->post( 'max_variations' );

						if ( $this->product->create_product_type( $_data ) ) :

							//	Redirect to clear form
							$this->session->Set_flashdata( 'success', '<strong>Success!</strong> Product Type created successfully.' );
							redirect( 'admin/shop/manage/types?' . $_SERVER['QUERY_STRING'] );
							return;

						else :

							$this->data['error']	= '<strong>Sorry,</strong> there was a problem creating the Product Type. ' . $this->product->last_error();
							$this->data['show_tab']	= 'create';

						endif;

					else :

						$this->data['error']	= '<strong>Sorry,</strong> there was a problem creating the Product Type.';
						$this->data['show_tab']	= 'create';

					endif;

				break;

				case 'edit' :

					$this->load->library( 'form_validation' );

					$_id = $this->input->post( 'id' );
					$this->form_validation->set_rules( $_id . '[label]',			'',	'xss_clean|required' );
					$this->form_validation->set_rules( $_id . '[description]',		'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[is_physical]',		'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[ipn_method]',		'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[max_per_order]',	'',	'xss_clean' );
					$this->form_validation->set_rules( $_id . '[max_variations]',	'',	'xss_clean' );

					$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

					if ( $this->form_validation->run() ) :

						$_data					= new stdClass();
						$_data->label			= isset( $_POST[$_id]['label'] )			? $_POST[$_id]['label']				: NULL;
						$_data->description		= isset( $_POST[$_id]['description'] )		? $_POST[$_id]['description']		: NULL;
						$_data->is_physical		= isset( $_POST[$_id]['is_physical'] )		? $_POST[$_id]['is_physical']		: NULL;
						$_data->ipn_method		= isset( $_POST[$_id]['ipn_method'] )		? $_POST[$_id]['ipn_method']		: NULL;
						$_data->max_per_order	= isset( $_POST[$_id]['max_per_order'] )	? $_POST[$_id]['max_per_order']		: NULL;
						$_data->max_variations	= isset( $_POST[$_id]['max_variations'] )	? $_POST[$_id]['max_variations']	: NULL;

						if ( $this->product->update_product_type( $_id, $_data ) ) :

							$this->data['success']	= '<strong>Success!</strong> Product Type saved successfully.';

						else :

							$this->data['error']	= '<strong>Sorry,</strong> there was a problem saving the Product Type. ' . $this->product->last_error();
							$this->data['show_tab']	= 'edit';

						endif;

					else :

						$this->data['error']	= '<strong>Sorry,</strong> there was a problem saving the Product Type.';
						$this->data['show_tab']	= 'edit';

					endif;

				break;

				case 'delete' :

					$_id = $this->input->post( 'id' );
					if ( $this->product->delete_product_type( $_id ) ) :

						$this->data['success'] = '<strong>Success!</strong> Product Type was deleted successfully.';

					else :

						$this->data['error'] = '<strong>Sorry,</strong> there was a problem deleting the Product Type; it may be in use.';

					endif;

				break;

			endswitch;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch data
		$this->data['types'] = $this->product->get_product_types( TRUE );

		// --------------------------------------------------------------------------

		//	Page data
		$this->data['page']->title = 'Manage &rsaquo; Product Types';

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/manage/types',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	public function reports()
	{
		if ( ! $this->user->has_permission( 'admin.shop.reports' ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to generate reports.' );
			redirect( 'admin/shop/vouchers' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Set method info
		$this->data['page']->title = 'Generate Reports';

		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :

			//	TODO

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/reports/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * The following block of code makes it simple to extend one of the core admin
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP' ) ) :

	class Shop extends NAILS_Shop
	{
	}

endif;

/* End of file universities.php */
/* Location: ./application/modules/admin/controllers/universities.php */