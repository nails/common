<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_App_setting_model extends NAILS_Model
{
	protected $_settings;


	// --------------------------------------------------------------------------


	/**
	 * Construct the setting model, set defaults
	 */
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table			= NAILS_DB_PREFIX . 'app_setting';
		$this->_table_prefix	= 'n';
		$this->_settings	= array();
	}


	// --------------------------------------------------------------------------


	/**
	 * Get's emails associated with a particular group/key
	 * @param  string  $key           The key to retrieve
	 * @param  string  $grouping      The group the key belongs to
	 * @param  boolean $force_refresh Whether to force a group refresh
	 * @return array
	 */
	public function get( $key = NULL, $grouping = 'app', $force_refresh = FALSE )
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

		if ( empty( $key ) ) :

			return $this->_settings[$grouping];

		else :

			return isset( $this->_settings[$grouping][$key] ) ? $this->_settings[$grouping][$key] : NULL;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Set a group/key either by passing an array of key=>value pairs as the $key
	 * or by passing a string to $key and setting $value
	 * @param mixed  $key      The key to set, or an array of key => value pairs
	 * @param string $grouping The grouping to store the keys under
	 * @param mixed  $value    The data to store, only used if $key is a string
	 * @return boolean
	 */
	public function set( $key, $grouping = 'app', $value = NULL )
	{
		$this->db->trans_begin();

		if ( is_array( $key ) ) :

			foreach ( $key AS $key => $value ) :

				$this->_set( $key, $grouping, $value );

			endforeach;

		else :

			$this->_set( $key, $grouping, $value );

		endif;

		if ( $this->db->trans_status() === FALSE ) :

			$this->db->trans_rollback();
			return FALSE;

		else :

			$this->db->trans_commit();
			return TRUE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Inserts/Updates a group/key value
	 * @param string $key      The key to set
	 * @param string $grouping The key's grouping
	 * @param mixed  $value    The value of the group/key
	 * @return void
	 */
	protected function _set( $key, $grouping, $value )
	{
		$this->db->where( 'key', $key );
		$this->db->where( 'grouping', $grouping );
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_APP_SETTING_MODEL' ) ) :

	class App_setting_model extends NAILS_App_setting_model
	{
	}

endif;

/* End of file app_setting_model.php */
/* Location: ./modules/system/models/app_setting_model.php */