<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_currency_model.php
 *
 * Description:		This model handles everything to do with currencies
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_currency_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		$this->config->load( 'currency' );
	}


	// --------------------------------------------------------------------------


	public function get_all()
	{
		return $this->config->item( 'currency' );
	}


	// --------------------------------------------------------------------------


	public function get_all_flat()
	{
		$_out		= array();
		$_currency	= $this->get_all();

		foreach( $_currency AS $c ) :

			$_out[$c->code] = $c->label;

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	public function get_by_code( $code )
	{
		$_currency = $this->get_all();

		return ! empty( $_currency[$code] ) ? $_currency[$code] : FALSE;
	}

	// --------------------------------------------------------------------------


	public function sync()
	{
		if ( defined( 'NAILS_SHOP_OPENEXCHANGERATES_APP_ID' ) && NAILS_SHOP_OPENEXCHANGERATES_APP_ID ) :

			//	Make sure we know what the base currency is
			if ( defined( 'SHOP_BASE_CURRENCY_CODE' ) ) :

				$this->load->model( 'shop/shop_model' );

			endif;

			_LOG( '... Base Currency is ' . SHOP_BASE_CURRENCY_CODE );

			//	Set up the cURL request
			$this->load->library( 'curl' );

			$_url		= 'http://openexchangerates.org/api/latest.json?app_id=' . NAILS_SHOP_OPENEXCHANGERATES_APP_ID;
			$_params	= array( 'app_id' => NAILS_SHOP_OPENEXCHANGERATES_APP_ID );

			$this->curl->create( $_url );
			$this->curl->option( CURLOPT_FAILONERROR, FALSE );
			$_result = json_decode( $this->curl->execute() );

			if ( ! isset( $_result->error ) ) :

				//	Ok, now we know the rates we need to work out what the base_exchange rate is.
				//	If the store's base rate is the same as the API's base rate then we're golden,
				//	if it's not then we'll need to do some calculations.

				if ( SHOP_BASE_CURRENCY_CODE == $_result->base ) :

					foreach ( $_result->rates AS $code => $rate ) :

						$_data = array( 'base_exchange' => $rate );
						$this->update( $code, $_data );
						_LOG( '... ' . $code . ' > ' . $rate );

					endforeach;

				else :

					_LOG( '... API base is ' . $_result->base . '; calculating differences...' );

					$_base = 1;
					foreach ( $_result->rates AS $code => $rate ) :

						if ( $code == SHOP_BASE_CURRENCY_CODE ) :

							$_base = $rate;
							break;

						endif;

					endforeach;

					foreach ( $_result->rates AS $code => $rate ) :

						//	We calculate the new exchange rate as so: $rate / $_base
						//	See here: http://stackoverflow.com/a/17452753/789224
						//	PS. Haters gonna hate.

						$_new_rate = $rate / $_base;
						$_data = array( 'base_exchange' => $_new_rate );
						$this->update( $code, $_data );
						_LOG( '... Calculating and saving new exchange rate for ' . SHOP_BASE_CURRENCY_CODE . ' > ' . $code . ' (' . $_new_rate . ')' );

					endforeach;


				endif;

				// --------------------------------------------------------------------------

				return TRUE;

			else :

				_LOG( '... An error occurred when querying the API:' );
				_LOG( '... ' . $_result->status . ' ' . $_result->message . ' - ' . $_result->description );
				return FALSE;

			endif;


		else :

			_LOG( '... NAILS_SHOP_OPENEXCHANGERATES_APP_ID is not defined. Sync aborted.' );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function convert_to_user( $value )
	{
		return $this->convert_using_rate( $value, SHOP_USER_CURRENCY_BASE_EXCHANGE );
	}


	// --------------------------------------------------------------------------


	public function convert_using_rate( $value, $rate )
	{
		return $value * $rate;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_CURRENCY_MODEL' ) ) :

	class Shop_currency_model extends NAILS_Shop_currency_model
	{
	}

endif;

/* End of file shop_currency_model.php */
/* Location: ./modules/shop/models/shop_currency_model.php */