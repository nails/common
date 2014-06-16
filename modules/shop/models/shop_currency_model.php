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

		$this->_table			= NAILS_DB_PREFIX . 'shop_currency';
		$this->_table_prefix	= 'c';
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

			$this->db->where( $this->_table_prefix . '.is_active', TRUE );

		endif;

		$this->db->order_by( $this->_table_prefix . '.code' );

		return parent::get_all();
	}


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
		$this->db->where( $this->_table_prefix . '.id', $id );
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
		$this->db->where( $this->_table_prefix . '.code', $code );
		$_result = $this->get_all( FALSE );

		// --------------------------------------------------------------------------

		if ( ! $_result )
			return FALSE;

		// --------------------------------------------------------------------------

		return $_result[0];
	}


	// --------------------------------------------------------------------------


	public function get_by_country( $country )
	{
		//	What are we dealing with?
		if ( is_numeric( $country ) ) :

			//	An ID
			$this->db->where( 'scc.country_id', $country );

		elseif( is_string( $country ) ) :

			if ( strlen( $country ) == 2 ) :

				$this->db->where( 'c.iso_code', $country );
				$this->db->join( NAILS_DB_PREFIX . 'country c', 'c.id = scc.country_id' );

			elseif ( strlen( $country ) == 3 ) :

				$this->db->where( 'c.iso_code_3', $country );
				$this->db->join( NAILS_DB_PREFIX . 'country c', 'c.id = scc.country_id' );

			else :

				//	Unknown
				return NULL;

			endif;

		else :

			//	Unknown
			return NULL;

		endif;

		// --------------------------------------------------------------------------

		$this->db->select( 'sc.*' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_currency sc', 'sc.id = scc.currency_id' );
		$_result = $this->db->get( NAILS_DB_PREFIX . 'shop_currency_country scc' );

		if ( $_result && $_result->row() ) :

			$_result = $_result->row();

			//	Format
			$this->_format_object( $_result );

			return $_result;

		else :

			return NULL;

		endif;
	}


	// --------------------------------------------------------------------------


	public function set_active_currencies( $ids )
	{
		if ( ! is_array( $ids ) || ! $ids ) :

			$this->_set_error( 'At least one currency is required to be active.' );
			return FALSE;

		endif;

		$this->db->set( 'is_active', FALSE );
		$this->db->update( NAILS_DB_PREFIX . 'shop_currency' );

		if ( $this->db->affected_rows() ) :

			$this->db->set( 'is_active', TRUE );
			$this->db->where_in( 'id', $ids );
			$this->db->update( NAILS_DB_PREFIX . 'shop_currency' );

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


	protected function _format_object( &$object )
	{
		$object->id				= (int) $object->id;
		$object->created_by		= NULL === $object->created_by	? NULL : (int) $object->created_by;
		$object->modified_by	= NULL === $object->modified_by	? NULL : (int) $object->modified_by;
		$object->is_active		= (bool) $object->is_active;
		$object->base_exchange	= (float) $object->base_exchange;

		// --------------------------------------------------------------------------

		//	If this currency lacks a symbol, use it's CODE as a symbol
		if ( ! $object->symbol ) :

			$object->symbol				= $object->code . ' ';
			$object->symbol_position	= 'BEFORE';

		endif;
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