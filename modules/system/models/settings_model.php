<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NAILS_Settings_model
 *
 * Description:	This model contains all methods for handling app settings
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Settings_model extends NAILS_Model
{
	protected $_settings;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table		= NAILS_DB_PREFIX . 'app_settings';
		$this->_settings	= array();
	}


	// --------------------------------------------------------------------------


	public function get_settings( $key = NULL, $grouping = 'app', $force_refresh = FALSE )
	{
		if ( empty( $this->_settings[$grouping] ) || $force_refresh ) :

			$this->db->where( 'grouping', $grouping );
			$_settings = $this->db->get( $this->_table )->result();
			$this->_settings[$grouping] = array();

			foreach ( $_settings AS $setting ) :

				$this->_settings[$grouping][ $setting->key ] = unserialize( $setting->value );

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $key ) :

			return $this->_settings[$grouping];

		else :

			return isset( $this->_settings[$grouping][$key] ) ? $this->_settings[$grouping][$key] : NULL;

		endif;
	}


	// --------------------------------------------------------------------------


	public function set_settings( $key_values, $grouping = 'app' )
	{
		foreach ( $key_values AS $key => $value ) :

			$this->db->where( 'key', $key );
			if ( $this->db->count_all_results( $this->_table ) ) :

				$this->db->where( 'grouping', $grouping );
				$this->db->where( 'key', $key );
				$this->db->set( 'value', serialize( $value ) );
				$this->db->update( $this->_table);

			else :

				$this->db->set( 'grouping', $grouping );
				$this->db->set( 'key', $key );
				$this->db->set( 'value', serialize( $value ) );
				$this->db->insert( $this->_table );

			endif;

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SETTINGS_MODEL' ) ) :

	class Settings_model extends NAILS_Settings_model
	{
	}

endif;


/* End of file settings_model.php */
/* Location: ./system/models/settings_model.php */