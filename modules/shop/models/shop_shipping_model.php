<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_shipping_model.php
 *
 * Description:		This model finds and loads blog modules
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_shipping_model extends NAILS_Model
{
	protected $_available;
	protected $_modules;

	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_available			= NULL;
		$this->_module_extension	= '-shop-shipping';

		//	Module locations
		//	This must be an array with 2 indexes:
		//	`path`		=> The absolute path to the directory containing the modules (required)
		//	`url`		=> The URL to access the modules (required)

		$this->_module_locations = array();

		//	Nails modules
		$this->_module_locations[]	= array(
										'path'	=> NAILS_PATH . 'modules/shop/shipping',
										'url'	=> NAILS_URL . 'modules/shop/shipping'
									);

		//	'Official' modules
		$this->_module_locations[]	= array(
										'path' => FCPATH . 'vendor/nailsapp',
										'url' => site_url( 'vendor/nailsapp', page_is_secure() )
									);

		//	App Modules
		$this->_module_locations[]	= array(
										'path' => FCPATH . APPPATH . 'modules/shop/shipping',
										'url' => site_url( APPPATH . 'modules/shop/shipping', page_is_secure() )
									);
	}

	// --------------------------------------------------------------------------

	public function get_available( $refresh = FALSE )
	{
		if ( ! is_null( $this->_available ) && ! $refresh ) :

			return $this->_available;

		endif;

		//	Reset
		$this->_available = array();

		// --------------------------------------------------------------------------

		//	Look for modules, where a module has the same name, the last one found is the
		//	one which is used

		$this->load->helper( 'directory' );

		//	Take a fresh copy
		$_module_locations = $this->_module_locations;

		//	Sanitise
		for ( $i = 0; $i < count( $_module_locations ); $i++ ) :

			//	Ensure path is present and has a trailing slash
			if ( isset( $_module_locations[$i]['path'] ) ) :

				$_module_locations[$i]['path'] = substr( $_module_locations[$i]['path'], -1, 1 ) == '/' ? $_module_locations[$i]['path'] : $_module_locations[$i]['path'] . '/';

			else :

				unset( $_module_locations[$i] );

			endif;

			//	Ensure URL is present and has a trailing slash
			if ( isset( $_module_locations[$i]['url'] ) ) :

				$_module_locations[$i]['url'] = substr( $_module_locations[$i]['url'], -1, 1 ) == '/' ? $_module_locations[$i]['url'] : $_module_locations[$i]['url'] . '/';

			else :

				unset( $_module_locations[$i] );

			endif;

		endfor;

		//	Reset array keys, possible that some may have been removed
		$_module_locations = array_values( $_module_locations );

		foreach( $_module_locations AS $module_location ) :

			$_path	= $module_location['path'];
			$_modules	= directory_map( $_path, 1 );

			if ( is_array( $_modules ) ) :

				foreach( $_modules AS $module ) :

					//	Filter out non-modules
					$_pattern = '/^(.*)' . preg_quote( $this->_module_extension, '/' ) . '$/';

					if ( ! preg_match( $_pattern, $module ) ) :

						log_message( 'debug', '"' . $module . '" is not a blog module.' );
						continue;

					endif;

					// --------------------------------------------------------------------------

					//	Exists?
					if ( file_exists( $_path . $module . '/config.json' ) ) :

						$_config = @json_decode( file_get_contents( $_path . $module . '/config.json' ) );

					else :

						log_message( 'error', 'Could not find configuration file for module "' . $_path . $module. '".' );
						continue;

					endif;

					//	Valid?
					if ( empty( $_config ) ) :

						log_message( 'error', 'Configuration file for module "' . $_path . $module. '" contains invalid JSON.' );
						continue;

					elseif ( ! is_object( $_config ) ) :

						log_message( 'error', 'Configuration file for module "' . $_path . $module. '" contains invalid data.' );
						continue;

					endif;

					//	Version OK?
					if ( ! empty( $_config->require->nails ) ) :

						preg_match( '/^(.*)?(\d.\d.\d)$/', $_config->require->nails, $_matches );

						$_modifier	= $_matches[1];
						$_version	= $_matches[2];
						$_error		= '"' . $_path . $module . '" requires Nails ' . $_modifier . $_version . ', version ' . NAILS_VERSION . ' is installed.';

						if ( ! empty( $_version ) ) :

							$_version_compare = version_compare( NAILS_VERSION, $_version );

							if ( $_matches[1] == '>' ) :

								if ( $_version_compare <= 0 ) :

									log_message( 'error', $_error );
									continue;

								endif;

							elseif ( $_matches[1] == '<' ) :

								if ( $_version_compare >= 0 ) :

									log_message( 'error', $_error );
									continue;

								endif;

							elseif ( $_matches[1] == '>=' ) :

								if ( $_version_compare < 0 ) :

									log_message( 'error', $_error );
									continue;

								endif;

							elseif ( $_matches[1] == '<=' ) :

								if ( $_version_compare >= 0 ) :

									log_message( 'error', $_error );
									continue;

								endif;

							else :

								//	This module is only compatible with a specific version of Nails
								if ( $_version_compare != 0 ) :

									log_message( 'error', $_error );
									continue;

								endif;

							endif;

						endif;

					endif;

					// --------------------------------------------------------------------------

					//	All good!

					//	Set the slug
					$_config->slug	= preg_replace( '/^(.*?)' . preg_quote( $this->_module_extension ) . '$/', '$1', $module );

					//	Set the path
					$_config->path	= $_path . $module . '/';

					//	Set the URL
					$_config->url	= $module_location['url'] . $module . '/';

					$this->_available[$_config->slug] = $_config;

				endforeach;

			endif;

		endforeach;

		return $this->_available;
	}


	// --------------------------------------------------------------------------


	public function get( $slug, $refresh = FALSE )
	{
		$_modules = $this->get_available( $refresh );

		foreach( $_modules AS $module ) :

			if ( $module->slug == $slug ) :

				return $module;

			endif;

		endforeach;

		$this->_set_error( '"' . $slug . '" was not found.' );
		return FALSE;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core blog
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_SHIPPING_MODEL' ) ) :

	class Shop_shipping_model extends NAILS_Shop_shipping_model
	{
	}

endif;

/* End of file shop_shipping_model.php */
/* Location: ./modules/shop/models/shop_shipping_model.php */