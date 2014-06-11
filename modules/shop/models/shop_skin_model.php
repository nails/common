<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_skin_model.php
 *
 * Description:		This model finds and loads shop skins
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_skin_model extends CI_Model
{
	protected $_available;

	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_available = NULL;
	}

	// --------------------------------------------------------------------------

	public function  get_available( $refresh = FALSE )
	{
		if ( ! is_null( $this->_available ) && ! $refresh ) :

			return $this->_available;

		endif;

		// --------------------------------------------------------------------------

		//	Have a look in the Nails and App directories, app skins trum nails skins
		$this->_available = array();
		$this->_available = array_merge( $this->_available, $this->_find_skins( TRUE ) );
		$this->_available = array_merge( $this->_available, $this->_find_skins( FALSE ) );

		// --------------------------------------------------------------------------

		//	Tidy up
		foreach( $this->_available AS $dir => $skin ) :

			$skin->dir = $dir;

		endforeach;

		return array_values( $this->_available );
	}


	// --------------------------------------------------------------------------


	protected function _find_skins( $is_nails )
	{
		$this->load->helper( 'directory' );

		// --------------------------------------------------------------------------

		if ( $is_nails ) :

			$_path = NAILS_PATH . 'modules/shop/views/';

		else :

			$_path = FCPATH . APPPATH . 'modules/shop/views/';

		endif;

		$_dir_map	= directory_map( $_path, 1 );
		$_found		= array();

		if ( is_array( $_dir_map ) ) :

			foreach( $_dir_map AS $skin ) :

				$_config = $_path . $skin . '/' . $skin . '.json';

				if ( file_exists( $_config ) ) :

					$_skin_config = @json_decode( file_get_contents( $_config ) );

					if ( $_skin_config && ! empty( $_skin_config->name ) ) :

						if ( $is_nails ) :

							$_skin_config->url	= NAILS_URL . 'modules/shop/views/' . $skin . '/';

						else :

							$_skin_config->url	= site_url( 'modules/shop/views/' . $skin . '/' );

						endif;

						$_skin_config->path	= $_path . $skin . '/';

						$_found[$skin]	= $_skin_config;

					endif;

				endif;

			endforeach;

		endif;

		return $_found;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_SKIN_MODEL' ) ) :

	class Shop_skin_model extends NAILS_Shop_skin_model
	{
	}

endif;

/* End of file shop_skin_model.php */
/* Location: ./modules/shop/models/shop_skin_model.php */