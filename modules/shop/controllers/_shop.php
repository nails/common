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
		$this->lang->load( 'shop' );

		// --------------------------------------------------------------------------

		//	Load the models
		$this->load->model( 'shop/shop_model' );
		$this->load->model( 'shop/shop_basket_model' );
		$this->load->model( 'shop/shop_brand_model' );
		$this->load->model( 'shop/shop_category_model' );
		$this->load->model( 'shop/shop_collection_model' );
		$this->load->model( 'shop/shop_currency_model' );
		$this->load->model( 'shop/shop_order_model' );
		$this->load->model( 'shop/shop_product_model' );
		$this->load->model( 'shop/shop_product_type_model' );
		$this->load->model( 'shop/shop_range_model' );
		$this->load->model( 'shop/shop_shipping_model' );
		$this->load->model( 'shop/shop_sale_model' );
		$this->load->model( 'shop/shop_tag_model' );
		$this->load->model( 'shop/shop_voucher_model' );
		$this->load->model( 'shop/shop_skin_model' );

		// --------------------------------------------------------------------------

		//	Load up the shop's skin
		$_skin = app_setting( 'skin', 'shop' ) ? app_setting( 'skin', 'shop' ) : 'getting-started';

		$this->_skin = $this->shop_skin_model->get( $_skin );

		if ( ! $this->_skin ) :

			show_fatal_error( 'Failed to load shop skin "' . $_skin . '"', 'Shop skin "' . $_skin . '" failed to load at ' . APP_NAME . ', the following reason was given: ' . $this->shop_skin_model->last_error() );

		endif;

		// --------------------------------------------------------------------------

		//	Pass to $this->data, for the views
		$this->data['skin'] = $this->_skin;

		// --------------------------------------------------------------------------

		//	Shop's name
		$this->_shop_name = app_setting( 'name', 'shop' ) ? app_setting( 'name', 'shop' ) : 'Shop';
	}
}

/* End of file _shop.php */
/* Location: ./modules/shop/controllers/_shop.php */