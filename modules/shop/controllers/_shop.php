<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NALS_SHOP_Controller
 *
 * Description:	This controller executes various bits of common Shop functionality
 *
 **/


class NAILS_Shop_Controller extends NAILS_Controller
{
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
		$this->load->model( 'shop_model',			'shop' );
		$this->load->model( 'shop_voucher_model',	'voucher' );
		$this->load->model( 'shop_basket_model',	'basket' );
		$this->load->model( 'shop_currency_model',	'currency' );
		$this->load->model( 'shop_order_model',		'order' );
		$this->load->model( 'shop_product_model',	'product' );
		$this->load->model( 'shop_shipping_model',	'shipping' );

		// --------------------------------------------------------------------------

		//	Load the styles
		$this->asset->load( 'nails.shop.css', TRUE );

		if ( file_exists( FCPATH . 'assets/css/shop.css' ) ) :

			$this->asset->load( 'shop.css' );

		endif;
	}
}

/* End of file _shop.php */
/* Location: ./modules/shop/controllers/_shop.php */