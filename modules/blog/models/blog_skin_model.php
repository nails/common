<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			blog_skin_model.php
 *
 * Description:		This model finds and loads blog skins
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Blog_skin_model extends NAILS_Model
{
	protected $_available;
	protected $_skins;

	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_available	= NULL;
		$this->_skin_prefix	= 'skin-blog-';

		//	Skin locations
		//	This must be an array with 2 indexes:
		//	`path`		=> The absolute path to the directory containing the skins (required)
		//	`url`		=> The URL to access the skins (required)

		$this->_skin_locations		= array();

		//	Nails skins
		$this->_skin_locations[]	= array(
										'path'	=> NAILS_PATH . 'modules/blog/skins',
										'url'	=> NAILS_URL . 'modules/blog/skins'
									);

		//	'Official' skins
		$this->_skin_locations[]	= array(
										'path' => FCPATH . 'vendor/nailsapp',
										'url' => site_url( 'vendor/nailsapp', page_is_secure() )
									);

		//	App Skins
		$this->_skin_locations[]	= array(
										'path' => FCPATH . APPPATH . 'modules/blog/skins',
										'url' => site_url( APPPATH . 'modules/blog/skins', page_is_secure() )
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

		//	Look for skins, where a skin has the same name, the last one found is the
		//	one which is used

		$this->load->helper( 'directory' );

		//	Take a fresh copy
		$_skin_locations = $this->_skin_locations;

		//	Sanitise
		for ( $i = 0; $i < count( $_skin_locations ); $i++ ) :

			//	Ensure path is present and has a trailing slash
			if ( isset( $_skin_locations[$i]['path'] ) ) :

				$_skin_locations[$i]['path'] = substr( $_skin_locations[$i]['path'], -1, 1 ) == '/' ? $_skin_locations[$i]['path'] : $_skin_locations[$i]['path'] . '/';

			else :

				unset( $_skin_locations[$i] );

			endif;

			//	Ensure URL is present and has a trailing slash
			if ( isset( $_skin_locations[$i]['url'] ) ) :

				$_skin_locations[$i]['url'] = substr( $_skin_locations[$i]['url'], -1, 1 ) == '/' ? $_skin_locations[$i]['url'] : $_skin_locations[$i]['url'] . '/';

			else :

				unset( $_skin_locations[$i] );

			endif;

		endfor;

		//	Reset array keys, possible that some may have been removed
		$_skin_locations = array_values( $_skin_locations );

		foreach( $_skin_locations AS $skin_location ) :

			$_path	= $skin_location['path'];
			$_skins	= directory_map( $_path, 1 );

			if ( is_array( $_skins ) ) :

				foreach( $_skins AS $skin ) :

					//	Filter out non-skins
					$_pattern = '/^' . preg_quote( $this->_skin_prefix, '/' ) . '(.*)$/';

					if ( ! preg_match( $_pattern, $skin ) ) :

						log_message( 'debug', '"' . $skin . '" is not a blog skin.' );
						continue;

					endif;

					// --------------------------------------------------------------------------

					//	Exists?
					if ( file_exists( $_path . $skin . '/config.json' ) ) :

						$_config = @json_decode( file_get_contents( $_path . $skin . '/config.json' ) );

					else :

						log_message( 'error', 'Could not find configuration file for skin "' . $_path . $skin. '".' );
						continue;

					endif;

					//	Valid?
					if ( empty( $_config ) ) :

						log_message( 'error', 'Configuration file for skin "' . $_path . $skin. '" contains invalid JSON.' );
						continue;

					elseif ( ! is_object( $_config ) ) :

						log_message( 'error', 'Configuration file for skin "' . $_path . $skin. '" contains invalid data.' );
						continue;

					endif;

					//	Version OK?
					if ( ! empty( $_config->require->nails ) ) :

						preg_match( '/^(.*)?(\d.\d.\d)$/', $_config->require->nails, $_matches );

						$_modifier	= $_matches[1];
						$_version	= $_matches[2];
						$_error		= '"' . $_path . $skin . '" requires Nails ' . $_modifier . $_version . ', version ' . NAILS_VERSION . ' is installed.';

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

								//	This skin is only compatible with a specific version of Nails
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
					$_config->slug	= preg_replace( '/^' . preg_quote( $this->_skin_prefix, '/' ) . '(.*?)$/', '$1', $skin );

					//	Set the path
					$_config->path	= $_path . $skin . '/';

					//	Set the URL
					$_config->url	= $skin_location['url'] . $skin . '/';

					$this->_available[$skin] = $_config;

				endforeach;

			endif;

		endforeach;

		$this->_available = array_values( $this->_available );

		return $this->_available;
	}


	// --------------------------------------------------------------------------


	public function get( $slug, $refresh = FALSE )
	{
		$_skins = $this->get_available( $refresh );

		foreach( $_skins AS $skin ) :

			if ( $skin->slug == $slug ) :

				return $skin;

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG_SKIN_MODEL' ) ) :

	class Blog_skin_model extends NAILS_Blog_skin_model
	{
	}

endif;

/* End of file blog_skin_model.php */
/* Location: ./modules/blog/models/blog_skin_model.php */