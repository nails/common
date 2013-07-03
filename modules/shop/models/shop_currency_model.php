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

			$_result = json_decode( '{"disclaimer": "Exchange rates are provided for informational purposes only, and do not constitute financial advice of any kind. Although every attempt is made to ensure quality, NO guarantees are given whatsoever of accuracy, validity, availability, or fitness for any purpose - please use at your own risk. All usage is subject to your acceptance of the Terms and Conditions ofService, available at: https://openexchangerates.org/terms/","license": "Data sourced from various providers with public-facing APIs; copyright may apply; resale is prohibited; no warranties given of any kind. All usage is subject to your acceptance of the License Agreement available at: https://openexchangeratesorg/license/","timestamp": 1372863647,"base": "USD","rates": {"AED": 3.672986,"AFN": 55.6191,"ALL": 108.219125,"AMD": 414.932497,"ANG": 1.7888,"AOA": 96.341732,"ARS": 5.395642,"AUD": 1.102143,"AWG": 1.789967,"AZN": 0.78445,"BAM": 1.505925,"BBD": 2,"BDT": 77.729301,"BGN": 1.507697,"BHD": 0.37699,"BIF": 1544.166667,"BMD": 1,"BND": 1.270524,"BOB": 6.905234,"BRL": 2.253742,"BSD": 1,"BTC": 0.011432,"BTN": 60.008534,"BWP": 8.650527,"BYR": 8813.625,"BZD": 2.009066,"CAD": 1.053212,"CDF": 922.605233,"CHF": 0.94872,"CLF": 0.02202,"CLP": 503.185837,"CNY": 6.141948,"COP": 1918.439715,"CRC": 500.224502,"CUP": 22.682881,"CVE": 84.917783,"CZK": 20.058742,"DJF": 177.869999,"DKK": 5.74617,"DOP": 41.771752,"DZD": 79.997145,"EEK": 11.771245,"EGP": 7.025365,"ETB": 18.72745,"EUR": 0.771015,"FJD": 1.898055,"FKP": 0.655143,"GBP": 0.655143,"GEL": 1.654975,"GHS": 2.028458,"GIP": 0.655143,"GMD": 35.71755,"GNF": 6945.863333,"GTQ": 7.828679,"GYD": 203.004999,"HKD": 7.754444,"HNL": 20.284677,"HRK": 5.749914,"HTG": 43.148125,"HUF": 226.762105,"IDR": 9926.141275,"ILS": 3.639967,"INR": 60.088595,"IQD": 1162.453333,"IRR": 12273.65,"ISK": 124.35,"JEP": 0.655143,"JMD": 100.717355,"JOD": 0.708219,"JPY": 99.673174,"KES": 86.111254,"KGS": 48.62,"KHR": 4021.556667,"KMF": 379.409165,"KPW": 900,"KRW": 1138.786202,"KWD": 0.285783,"KYD": 0.821797,"KZT": 151.875756,"LAK": 7755.335,"LBP": 1510.998505,"LKR": 130.633202,"LRD": 74.622433,"LSL": 10.015328,"LTL": 2.659366,"LVL": 0.54066,"LYD": 1.278331,"MAD": 8.559106,"MDL": 12.45118,"MGA": 2200.111667,"MKD": 46.668519,"MMK": 976.64925,"MNT": 1431.5,"MOP": 7.98069,"MRO": 297.317,"MTL": 0.683738,"MUR": 31.160006,"MVR": 15.339738,"MWK": 329.483667,"MXN": 13.053335,"MYR": 3.178401,"MZN": 29.716167,"NAD": 10.023772,"NGN": 160.489081,"NIO": 24.670815,"NOK": 6.10683,"NPR": 96.057992,"NZD": 1.289687,"OMR": 0.384997,"PAB": 1,"PEN": 2.782701,"PGK": 2.2085,"PHP": 43.396135,"PKR": 99.79965,"PLN": 3.332225,"PYG": 4518.80297,"QAR": 3.641402,"RON": 3.426333,"RSD": 87.958634,"RUB": 33.181581,"RWF": 648.611375,"SAR": 3.750503,"SBD": 7.188693,"SCR": 11.907389,"SDG": 4.406727,"SEK": 6.705515,"SGD": 1.271214,"SHP": 0.655143,"SLL": 4322.78161,"SOS": 1397.401667,"SRD": 3.275,"STD": 18933.95,"SVC": 8.742069,"SYP": 70.135986,"SZL": 10.0159,"THB": 31.056816,"TJS": 4.767133,"TMT": 2.855,"TND": 1.664162,"TOP": 1.847532,"TRY": 1.943178,"TTD": 6.408425,"TWD": 30.03619,"TZS": 1629.801593,"UAH": 8.149833,"UGX": 2580.876083,"USD": 1,"UYU": 20.455546,"UZS": 2088.273005,"VEF": 6.291277,"VND": 21211.036425,"VUV": 96.2,"WST": 2.334946,"XAF": 504.828149,"XAG": 0.051814,"XAU": 0.000795,"XCD": 2.70185,"XDR": 0.66675,"XOF": 504.8785,"XPF": 91.838538,"YER": 215.017752,"ZAR": 10.055047,"ZMK": 5227.108333,"ZMW": 5.47633,"ZWL": 322.322775}}' );

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