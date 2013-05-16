<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			blog_model.php
 *
 * Description:		This model primarily handles blog settings
 * 
 **/

class Blog_model extends NAILS_Model
{
	private $_settings;
	
	
	// --------------------------------------------------------------------------
	
	
	public function settings( $key = NULL, $force_refresh = FALSE )
	{
		if ( ! $this->_settings || $force_refresh ) :
		
			$_settings = $this->db->get( 'blog_settings' )->result();
			
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
			$this->db->update( 'blog_settings' );

		endforeach;

		return TRUE;
	}
}

/* End of file blog_model.php */
/* Location: ./application/models/blog_model.php */