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

class NAILS_Shop_skin_model extends NAILS_Model
{
	protected $_available;
	protected $_skins;

	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_available	= NULL;
		$this->_skins		= array();

	}

	// --------------------------------------------------------------------------

	public function  get_available( $refresh = FALSE )
	{
		if ( ! is_null( $this->_available ) && ! $refresh ) :

			return $this->_available;

		endif;

		// --------------------------------------------------------------------------

		//	Have a look in the Nails and App directories, app skins trump nails skins
		$this->load->helper( 'directory' );

		$_available = array();
		$_available = array_merge( $_available, (array) directory_map( NAILS_PATH . 'modules/shop/views/', 1 ) );
		$_available = array_merge( $_available, (array) directory_map( FCPATH . APPPATH . 'modules/shop/views/', 1 ) );
		$_available = array_filter( $_available );
		$_available = array_unique( $_available );

		// --------------------------------------------------------------------------

		foreach( $_available AS $skin ) :

			$_skin = $this->get( $skin, $refresh );
			if ( $_skin ) :

				$this->_available[] = $_skin;

			endif;

		endforeach;

		return $this->_available;
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


	// --------------------------------------------------------------------------


	public function get( $skin, $refresh = FALSE )
	{
		if ( ! empty( $this->_skins[$skin] ) && ! $refresh ) :

			return $this->_skins[$skin];

		endif;

		// --------------------------------------------------------------------------

		$_app_path		= FCPATH . APPPATH . 'modules/shop/views/';
		$_nails_path	= NAILS_PATH . 'modules/shop/views/';

		//	Load the skin's configs
		if ( file_exists( $_app_path . $skin . '/' . $skin . '.json' ) ) :

			$_skin		= @json_decode( file_get_contents( $_app_path . $skin . '/' . $skin . '.json' ) );
			$_is_nails	= FALSE;

		elseif ( file_exists( $_nails_path . $skin . '/' . $skin . '.json' ) ) :

			$_skin		= @json_decode( file_get_contents( $_nails_path . $skin . '/' . $skin . '.json' ) );
			$_is_nails	= TRUE;

		else :

			$this->_set_error( 'Could not find valid configuration file.' );
			return FALSE;

		endif;

		//	Check skin config is sane
		if ( empty( $_skin ) ) :

			$this->_set_error( 'Could not find valid configuration file.' );
			return FALSE;

		elseif ( ! is_object( $_skin ) ) :

			$this->_set_error( 'Corrupt configuration file.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Check skin is compatible with this version of Nails
		if ( ! empty( $_skin->require->nails ) ) :

			preg_match( '/^(.*)?(\d.\d.\d)$/', $_skin->require->nails, $_matches );

			$_modifier	= $_matches[1];
			$_version	= $_matches[2];
			$_error		= '"' . $_skin . '" requires Nails ' . $_modifier . $_version . ', version ' . NAILS_VERSION . ' is installed.';

			if ( ! empty( $_version ) ) :

				$_version_compare = version_compare( NAILS_VERSION, $_version );

				if ( $_matches[1] == '>' ) :

					if ( $_version_compare <= 0 ) :

						$this->_set_error( $_error );
						return FALSE;

					endif;

				elseif ( $_matches[1] == '<' ) :

					if ( $_version_compare >= 0 ) :

						$this->_set_error( $_error );
						return FALSE;

					endif;

				elseif ( $_matches[1] == '>=' ) :

					if ( $_version_compare < 0 ) :

						$this->_set_error( $_error );
						return FALSE;

					endif;

				elseif ( $_matches[1] == '<=' ) :

					if ( $_version_compare >= 0 ) :

						$this->_set_error( $_error );
						return FALSE;

					endif;

				else :

					//	This skin is only compatible with a specific version of Nails
					if ( $_version_compare != 0 ) :

						$this->_set_error( $_error );
						return FALSE;

					endif;

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Set the dir, URL and Path

		//	Dir
		$_skin->dir = $skin;


		//	URL & Path
		if ( $_is_nails ) :

			$_skin->url		= NAILS_URL . 'modules/shop/views/' . $skin . '/';
			$_skin->path	= $_nails_path . $skin . '/';

		else :

			$_skin->url		= site_url( 'modules/shop/views/' . $skin . '/' );
			$_skin->path	= $_app_path . $skin . '/';

		endif;

		// --------------------------------------------------------------------------

		$this->_skins[$skin] = $_skin;

		return $this->_skins[$skin];
	}


	// --------------------------------------------------------------------------


	public function is_compatible( $skin )
	{

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