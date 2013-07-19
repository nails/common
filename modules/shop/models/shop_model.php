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


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$_base = $this->get_base_currency();

		// --------------------------------------------------------------------------

		//	Shop's base currency (i.e what the products are listed in etc)
		if ( ! defined( 'SHOP_BASE_CURRENCY_SYMBOL' ) )		define( 'SHOP_BASE_CURRENCY_SYMBOL',		$_base->symbol );
		if ( ! defined( 'SHOP_BASE_CURRENCY_SYMBOL_POS' ) )	define( 'SHOP_BASE_CURRENCY_SYMBOL_POS',	$_base->symbol_position );
		if ( ! defined( 'SHOP_BASE_CURRENCY_PRECISION' ) )	define( 'SHOP_BASE_CURRENCY_PRECISION',		$_base->decimal_precision );
		if ( ! defined( 'SHOP_BASE_CURRENCY_CODE' ) )		define( 'SHOP_BASE_CURRENCY_CODE',			$_base->code );
		if ( ! defined( 'SHOP_BASE_CURRENCY_ID' ) )			define( 'SHOP_BASE_CURRENCY_ID',			$_base->id );

		//	Formatting constants
		if ( ! defined( 'SHOP_BASE_CURRENCY_THOUSANDS' ) )	define( 'SHOP_BASE_CURRENCY_THOUSANDS',		$_base->thousands_seperator );
		if ( ! defined( 'SHOP_BASE_CURRENCY_DECIMALS' ) )	define( 'SHOP_BASE_CURRENCY_DECIMALS',		$_base->decimal_symbol );

		//	User's preferred currency
		if ( $this->session->userdata( 'shop_currency' ) ) :

			//	Use the currency defined in the session
			$_currency_id = $this->session->userdata( 'shop_currency' );

		elseif( active_user( 'shop_currency' ) ) :

			//	Use the currency defined in the user object
			$_currency_id = active_user( 'shop_currency' );
			$this->session->set_userdata( 'shop_currency', $_currency_id );

		else :

			//	Can we determine the user's location and set a currency based on that?
			//	If not, fall back to base currency

			$this->load->library('geoip');
			$this->geoip->InfoIP('90.221.187.105');
			$_country_code = $this->geoip->result_country_code();

			if ( $_country_code ) :

				//	We know the code, does it have a known currency?
				$_country_currency = $this->currency->get_by_country( $_country_code );

				if ( $_country_currency ) :

					$_currency_id = $_country_currency->id;

				else :

					//	Fall back to default
					$_currency_id = $_base->id;

				endif;

			else :

				$_currency_id = $_base->id;

			endif;

			//	Save to session
			$this->session->set_userdata( 'shop_currency', $_currency_id );

		endif;

		//	Fetch the user's render currency
		$_user_currency = $this->currency->get_by_id( $_currency_id );

		if ( ! $_user_currency || ! $_user_currency->is_active ) :

			//	Bad currency ID or not active, use base
			$_user_currency = $_base;
			$this->session->unset_userdata( 'shop_currency', $_currency_id );

			if ( $thus->user->is_logged_in() ) :

				$this->user->update( active_user( 'id' ), array( 'shop_currency' => NULL ) );

			endif;

		endif;

		//	Set the user constants
		if ( ! defined( 'SHOP_USER_CURRENCY_SYMBOL' ) )			define( 'SHOP_USER_CURRENCY_SYMBOL',		$_user_currency->symbol );
		if ( ! defined( 'SHOP_USER_CURRENCY_SYMBOL_POS' ) )		define( 'SHOP_USER_CURRENCY_SYMBOL_POS',	$_user_currency->symbol_position );
		if ( ! defined( 'SHOP_USER_CURRENCY_PRECISION' ) )		define( 'SHOP_USER_CURRENCY_PRECISION',		$_user_currency->decimal_precision );
		if ( ! defined( 'SHOP_USER_CURRENCY_CODE' ) )			define( 'SHOP_USER_CURRENCY_CODE',			$_user_currency->code );
		if ( ! defined( 'SHOP_USER_CURRENCY_ID' ) )				define( 'SHOP_USER_CURRENCY_ID',			$_user_currency->id );

		//	Formatting constants
		if ( ! defined( 'SHOP_USER_CURRENCY_THOUSANDS' ) )		define( 'SHOP_USER_CURRENCY_THOUSANDS',		$_user_currency->thousands_seperator );
		if ( ! defined( 'SHOP_USER_CURRENCY_DECIMALS' ) )		define( 'SHOP_USER_CURRENCY_DECIMALS',		$_user_currency->decimal_symbol );

		//	Exchange rate betweent the two currencies
		if ( ! defined( 'SHOP_USER_CURRENCY_BASE_EXCHANGE' ) )	define( 'SHOP_USER_CURRENCY_BASE_EXCHANGE',	$_user_currency->base_exchange );
	}


	// --------------------------------------------------------------------------


	public function settings( $key = NULL, $force_refresh = FALSE )
	{
		if ( ! $this->_settings || $force_refresh ) :

			$_settings = $this->db->get( 'shop_settings' )->result();

			foreach ( $_settings AS $setting ) :

				$this->_settings[ $setting->key ] = unserialize( $setting->value );

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $key ) :

			return $this->_settings;

		else :

			return isset( $this->_settings[$key] ) ? $this->_settings[$key] : NULL;

		endif;
	}


	// --------------------------------------------------------------------------


	public function set_settings( $key_values )
	{
		foreach ( $key_values AS $key => $value ) :

			$this->db->where( 'key', $key );
			$this->db->set( 'value', serialize( $value ) );
			$this->db->update( 'shop_settings' );

			// --------------------------------------------------------------------------

			//	Unset the cache if the base_currency is being updated
			if ( $key == 'base_currency' ) :

				$this->_unset_cache( 'base_currency' );

			endif;

		endforeach;

		return TRUE;
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
		if ( ! $this->load->model_is_loaded( 'currency' ) ) :

			$this->load->model( 'shop/shop_currency_model', 'currency' );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch base currency
		$_base = $this->currency->get_by_id( $this->settings( 'base_currency' ) );

		//	Cache
		$this->_set_cache( 'base_currency', $_base );

		return $_base;
	}


	// --------------------------------------------------------------------------


	public function format_price( $price, $include_symbol = FALSE, $include_thousands = FALSE, $for_currency = NULL, $decode_symbol = FALSE )
	{
		//	Foratting for which currency? If null or emptyt, assume user currency
		if ( is_null( $for_currency ) || ! $for_currency ) :

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
				if ( ! $this->load->model_is_loaded( 'currency' ) ) :

					$this->load->model( 'shop/shop_currency_model', 'currency' );

				endif;

				if ( is_numeric( $for_currency ) ) :

					$_currency = $this->currency->get_by_id( $for_currency );

				else :

					$_currency = $this->currency->get_by_code( $for_currency );

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_MODEL' ) ) :

	class Shop_model extends NAILS_Shop_model
	{
	}

endif;

/* End of file shop_model.php */
/* Location: ./application/models/shop_model.php */