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
		$this->lang->load( 'shop', RENDER_LANG );
		
		// --------------------------------------------------------------------------
		
		//	Load the models
		$this->load->model( 'shop_basket_model',	'basket' );
		$this->load->model( 'shop_currency_model',	'currency' );
		$this->load->model( 'shop_order_model',		'order' );
		$this->load->model( 'shop_product_model',	'product' );
		$this->load->model( 'shop_voucher_model',	'voucher' );
		
		// --------------------------------------------------------------------------
		
		//	Load the helper
		$this->load->helper( 'shop' );
		
		// --------------------------------------------------------------------------
		
		//	Load the styles
		$this->asset->load( 'nails.shop.css', TRUE );
	}
}

/* End of file _shop.php */
/* Location: ./application/modules/shop/controllers/_shop.php */