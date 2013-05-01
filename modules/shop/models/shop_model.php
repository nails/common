<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_model.php
 *
 * Description:		This model primarily handles shop settings
 * 
 **/

class Shop_model extends NAILS_Model
{
	private $_settings;
	
	
	// --------------------------------------------------------------------------
	
	
	public function settings( $key = NULL )
	{
		if ( ! $this->_settings ) :
		
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
		dump( 'TODO' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_base_currency()
	{
		return $this->currency->get_by_id( shop_setting( 'base_currency' ) );
	}
}

/* End of file shop_model.php */
/* Location: ./application/models/shop_model.php */