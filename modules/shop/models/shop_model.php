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
		
		//	User's preferred currency
		//	TODO: Same as default just now
		if ( ! defined( 'SHOP_USER_CURRENCY_SYMBOL' ) )		define( 'SHOP_USER_CURRENCY_SYMBOL',		$_base->symbol );
		if ( ! defined( 'SHOP_USER_CURRENCY_SYMBOL_POS' ) )	define( 'SHOP_USER_CURRENCY_SYMBOL_POS',	$_base->symbol_position );
		if ( ! defined( 'SHOP_USER_CURRENCY_PRECISION' ) )	define( 'SHOP_USER_CURRENCY_PRECISION',		$_base->decimal_precision );
		if ( ! defined( 'SHOP_USER_CURRENCY_CODE' ) )		define( 'SHOP_USER_CURRENCY_CODE',			$_base->code );
		if ( ! defined( 'SHOP_USER_CURRENCY_ID' ) )			define( 'SHOP_USER_CURRENCY_ID',			$_base->id );

		//	Exchange rate betweent the two currencies
		//	TODO: Hardcoded GBP just now
		if ( ! defined( 'SHOP_USER_CURRENCY_EXCHANGE' ) )	define( 'SHOP_USER_CURRENCY_EXCHANGE',	1 );
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

				$this->_unet_cache( 'base_currency' );

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


	public function format_price( $price, $include_symbol = FALSE )
	{
		if ( $include_symbol ) :

			if ( SHOP_USER_CURRENCY_SYMBOL_POS == 'BEFORE' ) :

				return SHOP_USER_CURRENCY_SYMBOL . number_format( $price, SHOP_USER_CURRENCY_PRECISION );

			else :

				return number_format( $price, SHOP_USER_CURRENCY_PRECISION ) . SHOP_USER_CURRENCY_SYMBOL;

			endif;

		else :

			return number_format( $price, SHOP_USER_CURRENCY_PRECISION );

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