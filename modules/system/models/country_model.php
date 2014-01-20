<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NAILS_Country_model
 *
 * Description:	This model contains all methods for handling countries
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Country_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table	= 'country';
		$this->_prefix	= 'c';
	}


	// --------------------------------------------------------------------------


	public function get_all()
	{
		$this->db->select( 'c.*, l.slug lang_slug,l.name lang_label' );
		$this->db->join( NAILS_DB_PREFIX . 'language l', 'l.id = c.language_id', 'left' );
		$this->db->order_by( 'c.iso_name' );
		$_result = $this->db->get( NAILS_DB_PREFIX . 'country c' );

		if ( ! $_result ) :

			return array();

		endif;

		// --------------------------------------------------------------------------

		$_results = $_result->result();

		//	Format
		foreach ( $_results AS $country ) :

			$this->_format_country_object( $country );

		endforeach;

		// --------------------------------------------------------------------------

		return $_results;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches all objects as a flat array
	 *
	 * @access public
	 * @param none
	 * @return array
	 **/
	public function get_all_flat()
	{
		$_countrys	= $this->get_all();
		$_out		= array();

		foreach ( $_countrys AS $country ) :

			$_out[$country->id] = $country->iso_name;

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _format_country_object( &$country )
	{
		$country->id	= (int) $country->id;

		// --------------------------------------------------------------------------

		//	Langauge
		$country->language			= new stdClass();
		$country->language->id		= (int) $country->language_id;
		$country->language->slug	= (int) $country->lang_slug;
		$country->language->label	= (int) $country->lang_label;

		unset( $country->language_id );
		unset( $country->lang_slug );
		unset( $country->lang_label );
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_COUNTRY_MODEL' ) ) :

	class Country_model extends NAILS_Country_model
	{
	}

endif;


/* End of file country_model.php */
/* Location: ./system/models/country_model.php */