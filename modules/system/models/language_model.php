<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		CORE_NAILS_Language_Model
 *
 * Description:	This model contains all methods for handling languages
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Language_model extends NAILS_Model
{
	protected $_default_lang;

	// --------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table						= NAILS_DB_PREFIX . 'language';
		$this->_table_prefix				= 'l';
		$this->_table_label_column			= 'name';
		$this->_table_auto_set_timestamps	= FALSE;
	}

	// --------------------------------------------------------------------------

	public function set_usr_obj( &$usr )
	{
		$this->user =& $usr;
	}


	// --------------------------------------------------------------------------


	public function get_all_supported()
	{
		$this->db->where( $this->_table_prefix . '.supported', TRUE );
		return $this->get_all();
	}


	// --------------------------------------------------------------------------


	public function get_all_supported_flat()
	{
		$_out	= array();
		$_langs	= $this->get_all_supported();

		for( $i=0; $i<count( $_langs ); $i++ ) :

			$_out[$_langs[$i]->id] = $_langs[$i]->name;

		endfor;

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	public function get_default_id()
	{
		$_cache_key	= 'lang-default-' . APP_DEFAULT_LANG_SLUG;
		$_cache		= $this->_get_cache( $_cache_key );

		if ( $_cache ) :

			return $_cache->id;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch and cache
		$_default = $this->get_by_slug( APP_DEFAULT_LANG_SLUG );

		if ( $_default ) :

			//	Save to cache
			$this->_set_cache( $_cache_key, $_default );

			return $_default->id;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_default_name()
	{
		$_cache_key	= 'lang-default-' . APP_DEFAULT_LANG_SLUG;
		$_cache		= $this->_get_cache( $_cache_key );

		if ( $_cache ) :

			return $_cache->name;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch and cache
		$_default = $this->get_by_slug( APP_DEFAULT_LANG_SLUG );

		if ( $_default ) :

			//	Save to cache
			$this->_set_cache( $_cache_key, $_default );

			return $_default->name;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function mark_supported( $id )
	{
		$_data				= array();
		$_data['supported']	= TRUE;
		return $this->update( $id, $_data );
	}


	// --------------------------------------------------------------------------


	public function mark_unsupported( $id )
	{
		$_data				= array();
		$_data['supported']	= FALSE;
		return $this->update( $id, $_data );
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_LANGUAGE_MODEL' ) ) :

	class Language_model extends NAILS_Language_model
	{
	}

endif;


/* End of file language_model.php */
/* Location: ./system/application/models/language_model.php */