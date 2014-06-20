<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop_product_type_model
 *
 * Description:	The user group model handles user's passwords
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_product_type_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		$this->_table				= NAILS_DB_PREFIX . 'shop_product_type';
		$this->_table_prefix		= 'spt';
		$this->_destructive_delete	= FALSE;
	}


	// --------------------------------------------------------------------------


	protected function _getcount_common( $data = array(), $_caller = NULL )
	{
		if ( empty( $data['sort'] ) ) :

			$data['sort'] = 'label';

		else :

			$data = array( 'sort' => 'label' );

		endif;

		// --------------------------------------------------------------------------

		if ( ! empty( $data['include_count'] ) ) :

			if ( empty( $this->db->ar_select ) ) :

				//	No selects have been called, call this so that we don't *just* get the product count
				$_prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
				$this->db->select( $_prefix . '*' );

			endif;

			$this->db->select( '(SELECT COUNT(*) FROM ' . NAILS_DB_PREFIX .  'shop_product WHERE type_id = ' . $this->_table_prefix . '.id) product_count' );

		endif;

		// --------------------------------------------------------------------------

		return parent::_getcount_common( $data, $_caller );
	}


	// --------------------------------------------------------------------------

	//	Meta fields for various product types

	// --------------------------------------------------------------------------

	public function meta_fields_download()
	{
		$_out = array();

		// --------------------------------------------------------------------------

		//	TODO: This array should be a form builder config array - when that library is complete.

		//	Download ID
		$_out[0]				= new stdClass();
		$_out[0]->type			= 'cdn_object';
		$_out[0]->key			= 'download_id';
		$_out[0]->label			= 'Download';
		$_out[0]->bucket		= 'shop-download';
		$_out[0]->tip			= '';
		$_out[0]->validation	= 'xss_clean|required';

		// --------------------------------------------------------------------------

		return $_out;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_PRODUCT_TYPE_MODEL' ) ) :

	class Shop_product_type_model extends NAILS_Shop_product_type_model
	{
	}

endif;

/* End of file shop_product_type_model.php */
/* Location: ./modules/shop/models/shop_product_type_model.php */