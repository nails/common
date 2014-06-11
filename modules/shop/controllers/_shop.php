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

		// --------------------------------------------------------------------------

		//	Load up the shop's skin
		$_skin			= app_setting( 'skin', 'shop' ) ? app_setting( 'skin', 'shop' ) : 'getting-started';
		$_app_path		= FCPATH . APPPATH . 'modules/shop/views/' . $_skin . '/' . $_skin . '.json';
		$_nails_path	= NAILS_PATH . 'modules/shop/views/' . $_skin . '/' . $_skin . '.json';

		//	Load the skin's configs
		if ( file_exists( $_app_path ) ) :

			$this->_skin	= @json_decode( file_get_contents( $_app_path ) );
			$_using			= $_app_path;

		elseif ( file_exists( $_nails_path ) ) :

			$this->_skin	= @json_decode( file_get_contents( $_nails_path ) );
			$_using			= $_app_path;

		else :

			show_fatal_error( 'Shop skin "' . $_skin . '" is not configured correctly.', 'I was unable to load a valid skin configuration file for skin "' . $_skin . '"' );

		endif;

		//	Check skin config is sane
		if ( empty( $this->_skin ) ) :

			show_fatal_error( 'Shop skin "' . $_skin . '" is not configured correctly.', 'I was unable to load a valid skin configuration file. I looked for ' . $_using );

		elseif ( ! is_object( $this->_skin ) ) :

			show_fatal_error( 'Shop skin "' . $_skin . '" is not configured correctly.', 'The configuration file was found (at ' . $_using . ') but it did not contain a valid config object.' );

		else :

			$this->_skin->dir = $_skin;

		endif;

		//	Check skin is compatible with this version of Nails
		if ( ! empty( $this->_skin->require->nails ) ) :

			preg_match( '/^(.*)?(\d.\d.\d)$/', $this->_skin->require->nails, $_matches );

			$_modifier		= $_matches[1];
			$_version		= $_matches[2];
			$_error_title	= 'Shop skin "' . $_skin . ' is not compatible with the version of Nails running on ' . APP_NAME;
			$_error_message	= '"' . $_skin . '" requires Nails ' . $_modifier . $_version . ', version ' . NAILS_VERSION . ' is installed.';

			if ( ! empty( $_version ) ) :

				$_version_compare = version_compare( NAILS_VERSION, $_version );

				if ( $_matches[1] == '>' ) :

					if ( $_version_compare <= 0 ) :

						show_fatal_error( $_error_title, $_error_message );

					endif;

				elseif ( $_matches[1] == '<' ) :

					if ( $_version_compare >= 0 ) :

						show_fatal_error( $_error_title, $_error_message );

					endif;

				elseif ( $_matches[1] == '>=' ) :

					if ( $_version_compare < 0 ) :

						show_fatal_error( $_error_title, $_error_message );

					endif;

				elseif ( $_matches[1] == '<=' ) :

					if ( $_version_compare >= 0 ) :

						show_fatal_error( $_error_title, $_error_message );

					endif;

				else :

					//	This skin is only compatible with a specific version of Nails
					if ( $_version_compare != 0 ) :

						show_fatal_error( $_error_title, $_error_message );

					endif;

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Pass to $this->data, for the views
		$this->data['skin'] = $this->_skin;
	}
}

/* End of file _shop.php */
/* Location: ./modules/shop/controllers/_shop.php */