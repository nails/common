<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_model.php
 *
 * Description:		This model primarily handles shop settings
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_model extends NAILS_Model
{
	protected $_settings;
	protected $_base_currency;


	// --------------------------------------------------------------------------


	public function __construct( $config = array() )
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$_config_set_session = isset( $config['set_session'] ) ? (bool) $config['set_session'] : TRUE;

		// --------------------------------------------------------------------------

		$_base = $this->get_base_currency();

		// --------------------------------------------------------------------------

		//	Shop's base currency (i.e what the products are listed in etc)
		if ( ! defined( 'SHOP_BASE_CURRENCY_SYMBOL' ) )		define( 'SHOP_BASE_CURRENCY_SYMBOL',		$_base->symbol );
		if ( ! defined( 'SHOP_BASE_CURRENCY_SYMBOL_POS' ) )	define( 'SHOP_BASE_CURRENCY_SYMBOL_POS',	$_base->symbol_position );
		if ( ! defined( 'SHOP_BASE_CURRENCY_PRECISION' ) )	define( 'SHOP_BASE_CURRENCY_PRECISION',		$_base->decimal_precision );
		if ( ! defined( 'SHOP_BASE_CURRENCY_CODE' ) )		define( 'SHOP_BASE_CURRENCY_CODE',			$_base->code );

		//	Formatting constants
		if ( ! defined( 'SHOP_BASE_CURRENCY_THOUSANDS' ) )	define( 'SHOP_BASE_CURRENCY_THOUSANDS',		$_base->thousands_seperator );
		if ( ! defined( 'SHOP_BASE_CURRENCY_DECIMALS' ) )	define( 'SHOP_BASE_CURRENCY_DECIMALS',		$_base->decimal_symbol );

		//	User's preferred currency
		if ( $this->session->userdata( 'shop_currency' ) ) :

			//	Use the currency defined in the session
			$_currency_code = $this->session->userdata( 'shop_currency' );

		elseif( active_user( 'shop_currency' ) ) :

			//	Use the currency defined in the user object
			$_currency_code = active_user( 'shop_currency' );

			if ( ! headers_sent() ) :

				$this->session->set_userdata( 'shop_currency', $_currency_code );

			endif;

		else :

			//	Can we determine the user's location and set a currency based on that?
			//	If not, fall back to base currency

			$this->load->library('geo_ip');

			$_lookup = $this->geo_ip->country();

			if ( ! empty( $_lookup->status ) && $_lookup->status == 200 ) :

				//	We know the code, does it have a known currency?
				$_country_currency = $this->shop_currency_model->get_by_country( $_lookup->country->iso );

				if ( $_country_currency ) :

					$_currency_code = $_country_currency->code;

				else :

					//	Fall back to default
					$_currency_code = $_base->code;

				endif;

			else :

				$_currency_code = $_base->code;

			endif;

			//	Save to session
			if ( ! headers_sent() ) :

				$this->session->set_userdata( 'shop_currency', $_currency_code );

			endif;

		endif;

		//	Fetch the user's render currency
		$_user_currency = $this->shop_currency_model->get_by_code( $_currency_code );

		if ( ! $_user_currency  ) :

			//	Bad currency code
			$_user_currency = $_base;

			if ( ! headers_sent() ) :

				$this->session->unset_userdata( 'shop_currency', $_currency_code );

			endif;

			if ( $this->user_model->is_logged_in() ) :

				$this->user_model->update( active_user( 'id' ), array( 'shop_currency' => NULL ) );

			endif;

		endif;

		//	Set the user constants
		if ( ! defined( 'SHOP_USER_CURRENCY_SYMBOL' ) )			define( 'SHOP_USER_CURRENCY_SYMBOL',		$_user_currency->symbol );
		if ( ! defined( 'SHOP_USER_CURRENCY_SYMBOL_POS' ) )		define( 'SHOP_USER_CURRENCY_SYMBOL_POS',	$_user_currency->symbol_position );
		if ( ! defined( 'SHOP_USER_CURRENCY_PRECISION' ) )		define( 'SHOP_USER_CURRENCY_PRECISION',		$_user_currency->decimal_precision );
		if ( ! defined( 'SHOP_USER_CURRENCY_CODE' ) )			define( 'SHOP_USER_CURRENCY_CODE',			$_user_currency->code );

		//	Formatting constants
		if ( ! defined( 'SHOP_USER_CURRENCY_THOUSANDS' ) )		define( 'SHOP_USER_CURRENCY_THOUSANDS',		$_user_currency->thousands_seperator );
		if ( ! defined( 'SHOP_USER_CURRENCY_DECIMALS' ) )		define( 'SHOP_USER_CURRENCY_DECIMALS',		$_user_currency->decimal_symbol );
	}


	// --------------------------------------------------------------------------


	public function get_base_currency()
	{
		$_cache = $this->_get_cache( 'base_currency' );

		if ( $_cache ) :

			return $_cache;

		endif;

		// --------------------------------------------------------------------------

		//	Load the currency model, if not already loaded
		if ( ! $this->load->model_is_loaded( 'shop_currency_model' ) ) :

			$this->load->model( 'shop/shop_currency_model' );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch base currency
		$_base = $this->shop_currency_model->get_by_code( app_setting( 'base_currency', 'shop' ) );

		//	Cache
		$this->_set_cache( 'base_currency', $_base );

		return $_base;
	}


	// --------------------------------------------------------------------------


	public function format_price( $price, $include_symbol = FALSE, $include_thousands = FALSE, $for_currency = NULL, $decode_symbol = FALSE )
	{
		//	Formatting for which currency? If null or emptyt, assume user currency
		if ( NULL === $for_currency || ! $for_currency ) :

			$_code		= SHOP_USER_CURRENCY_CODE;
			$_symbol	= SHOP_USER_CURRENCY_SYMBOL;
			$_thousands	= $include_thousands ? SHOP_USER_CURRENCY_THOUSANDS : '';
			$_precision	= SHOP_USER_CURRENCY_PRECISION;
			$_decimals	= SHOP_USER_CURRENCY_DECIMALS;
			$_position	= SHOP_USER_CURRENCY_SYMBOL_POS;

		else :

			//	Fetch the currency in question - check cache first
			$_currency = $this->_get_cache( 'format_price-' . $for_currency );

			if ( $_currency ) :

				$_code		= $_currency->code;
				$_symbol	= $_currency->symbol;
				$_thousands	= $include_thousands ? $_currency->thousands : '';
				$_precision	= $_currency->precision;
				$_decimals	= $_currency->decimals;
				$_position	= $_currency->position;

			else :

				//	Fetch currency

				//	Load the currency model, if not already loaded
				if ( ! $this->load->model_is_loaded( 'shop_currency_model' ) ) :

					$this->load->model( 'shop/shop_currency_model' );

				endif;

				if ( is_numeric( $for_currency ) ) :

					$_currency = $this->shop_currency_model->get_by_id( $for_currency );

				else :

					$_currency = $this->shop_currency_model->get_by_code( $for_currency );

				endif;

				if ( $_currency ) :

					$_code		= $_currency->code;
					$_symbol	= $_currency->symbol;
					$_thousands	= $include_thousands ? $_currency->thousands_seperator : '';
					$_precision	= $_currency->decimal_precision;
					$_decimals	= $_currency->decimal_symbol;
					$_position	= $_currency->symbol_position;

					//	Cache it
					$_cache				= new stdClass();
					$_cache->code		= $_code;
					$_cache->symbol		= $_symbol;
					$_cache->thousands	= $_thousands;
					$_cache->precision	= $_precision;
					$_cache->decimals	= $_decimals;
					$_cache->position	= $_position;

					$this->_set_cache( 'format_price-' . $for_currency, $_cache );

				else :

					return FALSE;

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		$_value = number_format( $price, $_precision, $_decimals, $_thousands );

		if ( $include_symbol ) :

			if ( $decode_symbol ) :

				//	ENT_HTML5 added in PHP 5.4.0, use that if you can, if not replace certain strings manually
				if ( version_compare( phpversion(), '5.4.0', '>=' ) ) :

					$_symbol = html_entity_decode( $_symbol, ENT_COMPAT | ENT_HTML5, 'UTF-8' );

				else :

					$_symbol = html_entity_decode( $_symbol, ENT_COMPAT, 'UTF-8' );

					$_replace				= array();
					$_replace['&dollar;']	= '$';

					$_symbol = str_replace( array_keys( $_replace ), $_replace, $_symbol );

				endif;

			endif;

			// --------------------------------------------------------------------------

			if ( $_position == 'BEFORE' ) :

				$_return =  $_symbol . $_value;

			else :

				$_return =   $_value . $_symbol;

			endif;

			if ( ! $_symbol || $_symbol == '&curren;' ) :

				$_return .= ' ' . $_code;

			endif;

			// --------------------------------------------------------------------------

			return $_return;

		else :

			return $_value;

		endif;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_MODEL' ) ) :

	class Shop_model extends NAILS_Shop_model
	{
	}

endif;

/* End of file shop_model.php */
/* Location: ./modules/shop/models/shop_model.php */