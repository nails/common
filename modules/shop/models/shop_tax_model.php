<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_tax_model.php
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

class NAILS_Shop_tax_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table = 'shop_tax_rate';
	}


	// --------------------------------------------------------------------------


	public function get_all()
	{
		$this->db->where( 'is_deleted', FALSE );
		return parent::get_all();
	}


	// --------------------------------------------------------------------------


	public function get_all_flat()
	{
		$_rates = $this->get_all();
		$_out	= array();

		foreach ( $_rates AS $rate ) :

			$_out[$rate->id] = $rate->label . ' - ' . $rate->rate*100 . '%';

		endforeach;

		return $_out;
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_TAX_MODEL' ) ) :

	class Shop_tax_model extends NAILS_Shop_tax_model
	{
	}

endif;

/* End of file  */
/* Location: ./application/models/ */