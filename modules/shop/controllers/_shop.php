<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NALS_SHOP_Controller
 *
 * Description:	This controller executes various bits of common Shop functionality
 *
 **/


class NAILS_Shop_Controller extends NAILS_Controller
{
	protected $_skin;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Check this module is enabled in settings
		if ( ! module_is_enabled( 'shop' ) ) :

			//	Cancel execution, module isn't enabled
			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Load language file
		$this->lang->load( 'shop', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------

		//	Load the models
		$this->load->model( 'shop_model',				'shop' );
		$this->load->model( 'shop_basket_model',		'basket' );
		$this->load->model( 'shop_brand_model',			'brand' );
		$this->load->model( 'shop_category_model',		'category' );
		$this->load->model( 'shop_collection_model',	'collection' );
		$this->load->model( 'shop_currency_model',		'currency' );
		$this->load->model( 'shop_order_model',			'order' );
		$this->load->model( 'shop_product_model',		'product' );
		$this->load->model( 'shop_product_type_model',	'product_type' );
		$this->load->model( 'shop_range_model',			'range' );
		$this->load->model( 'shop_shipping_model',		'shipping' );
		$this->load->model( 'shop_sale_model',			'sale' );
		$this->load->model( 'shop_tag_model',			'tag' );
		$this->load->model( 'shop_voucher_model',		'voucher' );
		$this->load->model( 'shop_skin_model',			'skin' );

		// --------------------------------------------------------------------------

		//	Load up the shop's skin
		$_skin = app_setting( 'skin', 'shop' ) ? app_setting( 'skin', 'shop' ) : 'getting-started';

		$this->_skin = $this->skin->get( $_skin );

		if ( ! $this->_skin ) :

			show_fatal_error( 'Failed to shop skin "' . $_skin . '"', 'Shop skin "' . $_skin . '" failed to load at ' . APP_NAME . ', the following reason was given: ' . $this->skin->last_error() );

		endif;

		// --------------------------------------------------------------------------

		//	Pass to $this->data, for the views
		$this->data['skin'] = $this->_skin;
	}
}

/* End of file _shop.php */
/* Location: ./modules/shop/controllers/_shop.php */