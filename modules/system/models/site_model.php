<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NAILS_Site_model
 *
 * Description:	This model contains all methods for handling system settings
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Site_model extends NAILS_Model
{
	protected $_settings;


	// --------------------------------------------------------------------------


	public function get_settings( $key = NULL, $force_refresh = FALSE )
	{
		if ( ! $this->_settings || $force_refresh ) :

			$_settings = $this->db->get( NAILS_DB_PREFIX . 'site_settings' )->result();

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
			$this->db->update( NAILS_DB_PREFIX . 'site_settings' );

		endforeach;

		return TRUE;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SITE_MODEL' ) ) :

	class Site_model extends NAILS_Site_model
	{
	}

endif;


/* End of file site_model.php */
/* Location: ./system/models/site_model.php */