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
	/**
	 * Creates a new object
	 * 
	 * @access public
	 * @param array $data The data to create the object with
	 * @param bool $return_obj Whether to return just the new ID or the full object
	 * @return mixed
	 **/
	public function create( $data = array(), $return_obj = FALSE )
	{
		if ( $data )
			$this->db->set( $data );
		
		// --------------------------------------------------------------------------
		
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->set( 'created_by', active_user( 'id' ) );
		
		$this->db->insert( 'shop_currency' );
		
		if ( $return_obj ) :
		
			if ( $this->db->affected_rows() ) :
			
				$_id = $this->db->insert_id();
				
				return $this->get_by_id( $_id );
			
			else :
			
				return FALSE;
			
			endif;
		
		else :
		
			return $this->db->affected_rows() ? $this->db->insert_id() : FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Updates an existing object
	 * 
	 * @access public
	 * @param int $id The ID of the object to update
	 * @param array $data The data to update the object with
	 * @return bool
	 **/
	public function update( $id, $data = array() )
	{
		if ( ! $data )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		if ( is_numeric( $id ) ) :

			$this->db->where( 'id', $id );

		else :

			$this->db->where( 'code', $id );

		endif;

		// --------------------------------------------------------------------------

		$this->db->set( $data );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->update( 'shop_currency' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Deletes an existing object
	 * 
	 * @access public
	 * @param int $id The ID of the object to delete
	 * @return bool
	 **/
	public function delete( $id )
	{
		$this->db->where( 'id', $id );
		$this->db->delete( 'shop_currency' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches all objects
	 * 
	 * @access public
	 * @param none
	 * @return array
	 **/
	public function get_all( $only_active = TRUE )
	{
		if ( $only_active ) :

			$this->db->where( 'c.is_active', TRUE );

		endif;

		$this->db->order_by( 'c.code' );

		// --------------------------------------------------------------------------

		$_results = $this->db->get( 'shop_currency c' )->result();

		foreach ( $_results aS $result ) :

			$this->_format_currency_object( $result );

		endforeach;

		return $_results;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches all objects
	 * 
	 * @access public
	 * @param none
	 * @return array
	 **/
	public function get_all_flat( $only_active = TRUE )
	{
		$_currencies = $this->get_all( $only_active );
		$_out = array();

		foreach ( $_currencies AS $currency ) :

			$_out[$currency->id] = $currency->code . ' - ' . $currency->label;

		endforeach;

		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's ID
	 * 
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @return	stdClass
	 **/
	public function get_by_id( $id )
	{
		$this->db->where( 'c.id', $id );
		$_result = $this->get_all( FALSE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's code
	 * 
	 * @access public
	 * @param string $code The code of the object to fetch
	 * @return	stdClass
	 **/
	public function get_by_code( $code )
	{
		$this->db->where( 'c.code', $code );
		$_result = $this->get_all( FALSE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}


	// --------------------------------------------------------------------------


	public function set_active_currencies( $ids )
	{
		if ( ! is_array( $ids ) || ! $ids ) :

			$this->_set_error( 'At least one currency is required to be active.' );
			return FALSE;

		endif;

		$this->db->set( 'is_active', FALSE );
		$this->db->update( 'shop_currency' );

		if ( $this->db->affected_rows() ) :

			$this->db->set( 'is_active', TRUE );
			$this->db->where_in( 'id', $ids );
			$this->db->update( 'shop_currency' );

			if ( $this->db->affected_rows() ) :

				return TRUE;
			
			else :

				$this->_set_error( 'Unable to enable currencies' );
				return FALSE;

			endif;

		else :

			$this->_set_error( 'Unable to disabled all currencies' );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _format_currency_object( &$currency )
	{
		$currency->id				= (int) $currency->id;
		$currency->created_by		= is_null( $currency->created_by ) ? NULL : (int) $currency->created_by;
		$currency->modified_by		= is_null( $currency->modified_by ) ? NULL : (int) $currency->modified_by;
		$currency->is_active		= (bool) $currency->is_active;
		$currency->base_exchange	= (float) $currency->base_exchange;

		// --------------------------------------------------------------------------

		//	If this currency lacks a symbol, use it's CODE as a symbol
		if ( ! $currency->symbol ) :

			$currency->symbol			= $currency->code . ' ';
			$currency->symbol_position	= 'BEFORE';

		endif;
	}


	// --------------------------------------------------------------------------


	public function sync( &$logger = NULL )
	{
		//	Check to see if a logger object has been passed, if not create
		//	a dummy method so we don't get errors
		
		if ( ! method_exists( $logger, 'line' ) ) :
		
			//	It hasn't, define a dummy
			$_logger = function( $line ) {};
			
		else :
		
			$_logger = function( $line ) use ( &$logger) { $logger->line( $line ); };
		
		endif;

		// --------------------------------------------------------------------------

		if ( defined( 'NAILS_SHOP_OPENEXCHANGERATES_APP_ID' ) ) :

			//	Make sure we know what the base currency is
			if ( defined( 'SHOP_BASE_CURRENCY_CODE' ) ) :

				$this->load->model( 'shop/shop_model', 'shop' );

			endif;

			$_logger( '... Base Currency is ' . SHOP_BASE_CURRENCY_CODE );

			//	Set up the cURL request
			// $this->load->library( 'curl' );

			// $_url		= 'http://openexchangerates.org/api/latest.json?app_id=' . NAILS_SHOP_OPENEXCHANGERATES_APP_ID;
			// $_params	= array( 'app_id' => NAILS_SHOP_OPENEXCHANGERATES_APP_ID );

			// $this->curl->create( $_url );
			// $this->curl->option( CURLOPT_FAILONERROR, FALSE );
			// $_result = json_decode( $this->curl->execute() );

			$_result = json_decode( '{
  "disclaimer": "Exchange rates are provided for informational purposes only, and do not constitute financial advice of any kind. Although every attempt is made to ensure quality, NO guarantees are given whatsoever of accuracy, validity, availability, or fitness for any purpose - please use at your own risk. All usage is subject to your acceptance of the Terms and Conditions of Service, available at: https://openexchangerates.org/terms/",
  "license": "Data sourced from various providers with public-facing APIs; copyright may apply; resale is prohibited; no warranties given of any kind. All usage is subject to your acceptance of the License Agreement available at: https://openexchangerates.org/license/",
  "timestamp": 1374069660,
  "base": "USD",
  "rates": {
    "AED": 3.673099,
    "AFN": 56.052233,
    "ALL": 106.956625,
    "AMD": 414.199997,
    "ANG": 1.78104,
    "AOA": 96.4541,
    "ARS": 5.434452,
    "AUD": 1.080661,
    "AWG": 1.789967,
    "AZN": 0.7844,
    "BAM": 1.490408,
    "BBD": 2,
    "BDT": 77.871849,
    "BGN": 1.490727,
    "BHD": 0.377013,
    "BIF": 1545.63,
    "BMD": 1,
    "BND": 1.261493,
    "BOB": 6.906245,
    "BRL": 2.235328,
    "BSD": 1,
    "BTC": 0.010329,
    "BTN": 59.490951,
    "BWP": 8.544125,
    "BYR": 8876.6225,
    "BZD": 2.018656,
    "CAD": 1.040109,
    "CDF": 921.914503,
    "CHF": 0.940784,
    "CLF": 0.021735,
    "CLP": 499.094002,
    "CNY": 6.143293,
    "COP": 1877.55978,
    "CRC": 500.462301,
    "CUP": 22.687419,
    "CVE": 84.067984,
    "CZK": 19.74686,
    "DJF": 177.93,
    "DKK": 5.678469,
    "DOP": 41.745413,
    "DZD": 79.011297,
    "EEK": 11.771245,
    "EGP": 7.004703,
    "ETB": 18.759138,
    "EUR": 0.760908,
    "FJD": 1.890247,
    "FKP": 0.658346,
    "GBP": 0.658346,
    "GEL": 1.65405,
    "GHS": 2.060906,
    "GIP": 0.658346,
    "GMD": 32.630175,
    "GNF": 6979.726667,
    "GTQ": 7.811891,
    "GYD": 202.978336,
    "HKD": 7.757758,
    "HNL": 20.222467,
    "HRK": 5.725186,
    "HTG": 43.321175,
    "HUF": 222.724983,
    "IDR": 10028.925,
    "ILS": 3.569881,
    "INR": 59.270988,
    "IQD": 1162.955008,
    "IRR": 12260.3,
    "ISK": 121.0725,
    "JEP": 0.658346,
    "JMD": 101.195834,
    "JOD": 0.708736,
    "JPY": 99.372561,
    "KES": 86.863639,
    "KGS": 48.930733,
    "KHR": 4016.365,
    "KMF": 374.408547,
    "KPW": 900,
    "KRW": 1117.853367,
    "KWD": 0.285352,
    "KYD": 0.822562,
    "KZT": 152.678978,
    "LAK": 7791.976667,
    "LBP": 1510.707255,
    "LKR": 131.49911,
    "LRD": 74.6404,
    "LSL": 9.890674,
    "LTL": 2.629044,
    "LVL": 0.53485,
    "LYD": 1.280714,
    "MAD": 8.477992,
    "MDL": 12.417687,
    "MGA": 2210.36,
    "MKD": 47.090754,
    "MMK": 983.783125,
    "MNT": 1453.604981,
    "MOP": 7.984993,
    "MRO": 293.25925,
    "MTL": 0.683602,
    "MUR": 31.103925,
    "MVR": 15.386488,
    "MWK": 330.406125,
    "MXN": 12.581004,
    "MYR": 3.190212,
    "MZN": 29.9211,
    "NAD": 9.870124,
    "NGN": 161.233085,
    "NIO": 24.746619,
    "NOK": 5.998988,
    "NPR": 95.078696,
    "NZD": 1.265271,
    "OMR": 0.385044,
    "PAB": 1,
    "PEN": 2.76164,
    "PGK": 2.280567,
    "PHP": 43.282852,
    "PKR": 100.345473,
    "PLN": 3.235922,
    "PYG": 4472.611125,
    "QAR": 3.641301,
    "RON": 3.377505,
    "RSD": 86.640149,
    "RUB": 32.407462,
    "RWF": 650.18025,
    "SAR": 3.750662,
    "SBD": 7.10045,
    "SCR": 11.967206,
    "SDG": 4.408288,
    "SEK": 6.592443,
    "SGD": 1.259843,
    "SHP": 0.658346,
    "SLL": 4323.448277,
    "SOS": 1384.285,
    "SRD": 3.275,
    "STD": 18697.783333,
    "SVC": 8.743516,
    "SYP": 70.150014,
    "SZL": 9.891954,
    "THB": 31.047765,
    "TJS": 4.767133,
    "TMT": 2.84125,
    "TND": 1.656294,
    "TOP": 1.850332,
    "TRY": 1.918024,
    "TTD": 6.408708,
    "TWD": 29.802202,
    "TZS": 1618.850405,
    "UAH": 8.150647,
    "UGX": 2587.8594,
    "USD": 1,
    "UYU": 21.047995,
    "UZS": 2103.44258,
    "VEF": 6.291518,
    "VND": 21195.923925,
    "VUV": 96.300001,
    "WST": 2.353554,
    "XAF": 499.430903,
    "XAG": 0.050198,
    "XAU": 0.000773,
    "XCD": 2.7023,
    "XDR": 0.66355,
    "XOF": 499.531003,
    "XPF": 90.833738,
    "YER": 214.914222,
    "ZAR": 9.834652,
    "ZMK": 5227.108333,
    "ZMW": 5.476318,
    "ZWL": 322.387247
  }
}' );

			if ( ! isset( $_result->error ) ) :

				//	Ok, now we know the rates we need to work out what the base_exchange rate is.
				//	If the store's base rate is the same as the API's base rate then we're golden,
				//	if it's not then we'll need to do some calculations.

				if ( SHOP_BASE_CURRENCY_CODE == $_result->base ) :

					foreach ( $_result->rates AS $code => $rate ) :

						$_data = array( 'base_exchange' => $rate );
						$this->update( $code, $_data );
						$_logger( '... ' . $code . ' > ' . $rate );

					endforeach;

				else :

					$_logger( '... API base is ' . $_result->base . '; calculating differences...' );

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
						$_logger( '... Calculating and saving new exchange rate for ' . SHOP_BASE_CURRENCY_CODE . ' > ' . $code . ' (' . $_new_rate . ')' );

					endforeach;


				endif;

				// --------------------------------------------------------------------------
				
				return TRUE;

			else :

				$_logger( '... An error occurred when querying the API:' );
				$_logger( '... ' . $_result->status . ' ' . $_result->message . ' - ' . $_result->description );
				return FALSE;

			endif;


		else :

			$_logger( '... NAILS_SHOP_OPENEXCHANGERATES_APP_ID is not defined. Sync aborted.' );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function convert_to_user( $value )
	{
		return $value * SHOP_USER_CURRENCY_BASE_EXCHANGE;
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_CURRENCY_MODEL' ) ) :

	class Shop_currency_model extends NAILS_Shop_currency_model
	{
	}

endif;

/* End of file  */
/* Location: ./application/models/ */