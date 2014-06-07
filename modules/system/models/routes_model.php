<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NAILS_routes_model
 *
 * Description:	This model should be used to write to the app routes file.
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Routes_model extends NAILS_Model
{
	protected $_can_write_routes;
	protected $_routes_file;
	protected $_writers;
	protected $_routes;

	public $cant_write_reason;

	// --------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Set Defaults
		$this->_routes_dir			= DEPLOY_CACHE_DIR;
		$this->_routes_file			= 'routes_app.php';
		$this->_writers				= array();
		$this->_can_write_routes	= $this->_can_write_routes();
		$this->_routes				= array();

		if ( ! $this->_can_write_routes ) :

			$this->cant_write_reason = $this->last_error();
			$this->clear_errors();

		endif;

		//	Default writers
		$this->_writers['sitemap']	= array( $this, '_routes_sitemap' );
		$this->_writers['cms']		= array( $this, '_routes_cms' );
		$this->_writers['blog']		= array( $this, '_routes_blog' );
		$this->_writers['shop']		= array( $this, '_routes_shop' );
	}


	// --------------------------------------------------------------------------


	public function can_write_routes()
	{
		return $this->_can_write_routes;
	}


	// --------------------------------------------------------------------------

	public function update( $which = NULL )
	{
		if ( ! $this->_can_write_routes ) :

			$this->_set_error( $this->cant_write_reason );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->_data = '<?php  if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . "\n\n";
		$this->_data .= '//	THIS FILE IS CREATED/MODIFIED AUTOMATICALLY, ANY MANUAL EDITS WILL BE OVERWRITTEN'."\n\n";

		foreach ( $this->_writers AS $slug => $method ) :

			//	TODO: Give the ability to selectively update a part of the routes file.
			//	Perhaps restricting edits to be between two known comments...?

			//if ( NULL == $which || $which == $slug ) :

				if ( is_callable( array( $method[0], $method[1] ) ) ) :

					$_result = call_user_func( array( $method[0], $method[1] ) );

					if ( is_array( $_result ) ) :

						$this->_routes = array_merge( $this->_routes, $_result );

					endif;

				endif;

			//endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Start writing the file
		return $this->_write_file();
	}


	// --------------------------------------------------------------------------


	protected function _routes_sitemap()
	{
		$_routes = array();

		if ( module_is_enabled( 'sitemap' ) ) :

			$this->load->model( 'sitemap/sitemap_model' );

			$_routes['//BEGIN SITEMAP'] = '';
			$_routes = $_routes + $this->sitemap_model->get_routes();
			$_routes['//END SITEMAP'] = '';

		endif;

		return $_routes;
	}


	// --------------------------------------------------------------------------


	protected function _routes_cms()
	{
		$_routes = array();

		if ( module_is_enabled( 'cms' ) ) :

			$_routes['//BEGIN CMS'] = '';

			// --------------------------------------------------------------------------

			$this->load->model( 'cms/cms_page_model' );
			$_pages = $this->cms_page_model->get_all();

			foreach ( $_pages AS $page ) :

				if ( $page->is_published ) :

					$_routes[$page->published->slug] = 'cms/render/page/' . $page->id;

				endif;

			endforeach;

			// --------------------------------------------------------------------------

			/**
			 *	Make a route for each slug history item, don't overwrite any existing route
			 *	Doing them second and checking (instead of letting the real pages overwrite
			 *	the key) in an attempt to optimise, the router takes the first route it comes
			 *	across so, the logic is that the "current" slug is the one which is getting
			 *	hit most often, so place it first, if a legacy slug is used (in theory less
			 *	often) then the router can work a little harder.
			 **/

			$this->db->select( 'sh.slug,sh.page_id' );
			$this->db->join( NAILS_DB_PREFIX . 'cms_page p', 'p.id = sh.page_id' );
			$this->db->where( 'p.is_deleted', FALSE );
			$this->db->where( 'p.is_published', TRUE );
			$_slugs = $this->db->get( NAILS_DB_PREFIX . 'cms_page_slug_history sh')->result();

			foreach ( $_slugs AS $route ) :

				if ( ! isset( $_routes[$route->slug] ) ) :

					$_routes[$route->slug] = 'cms/render/legacy_slug/' . $route->page_id;

				endif;

			endforeach;

			// --------------------------------------------------------------------------

			$_routes['//END CMS'] = '';

		endif;

		return $_routes;
	}


	// --------------------------------------------------------------------------


	protected function _routes_shop()
	{
		$_routes = array();

		if ( module_is_enabled( 'shop' ) ) :

			$_settings = app_setting( NULL, 'shop' );

			$_routes['//BEGIN SHOP'] = '';

			//	Shop front page route
			$_routes[substr( $_settings['url'], 0, -1 ) . '(/(:any)?/?)?'] = 'shop/$2';

			//	TODO: all shop product/category/tag/sale routes etc

			$_routes['//END SHOP'] = '';

		endif;

		return $_routes;
	}


	// --------------------------------------------------------------------------


	protected function _routes_blog()
	{
		$_routes = array();

		if ( module_is_enabled( 'blog' ) ) :

			$_settings = app_setting( NULL, 'blog' );

			$_routes['//BEGIN BLOG'] = '';

			//	Blog front page route
			$_routes[substr( $_settings['url'], 0, -1 ) . '(/(:any)?/?)?'] = 'blog/$2';

			$_routes['//END BLOG'] = '';

		endif;

		return $_routes;
	}


	// --------------------------------------------------------------------------


	protected function _write_file()
	{
		//	Routes are writeable, apparently, give it a bash
		$_data = '<?php  if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . "\n\n";
		$_data .= '/**' . "\n";
		$_data .= ' * THIS FILE IS CREATED/MODIFIED AUTOMATICALLY'."\n";
		$_data .= ' * ==========================================='."\n";
		$_data .= ' *'."\n";
		$_data .= ' * Any changes you make in this file will be overwritten the' . "\n";
		$_data .= ' * next time the app routes are generated.'."\n";
		$_data .= ' *'."\n";
		$_data .= ' * See Nails docs for instructions on how to utilise the' . "\n";
		$_data .= ' * routes_app.php file'."\n";
		$_data .= ' *'."\n";
		$_data .= ' **/' . "\n\n";

		// --------------------------------------------------------------------------

		foreach ( $this->_routes AS $key => $value ) :

			if ( preg_match( '#^//.*$#', $key ) ) :

				//	This is a comment
				$_data .= $key . "\n";

			else :

				//	This is a route
				$_data .= '$route[\'' . $key . '\']=\'' . $value . '\';' . "\n";

			endif;

		endforeach;

		$_data .= "\n" . '//LAST GENERATED: ' . date( 'Y-m-d H:i:s' );

		// --------------------------------------------------------------------------

		$_fh = @fopen( $this->_routes_dir . $this->_routes_file, 'w' );

		if ( ! $_fh ) :

			$this->_set_error( 'Unable to open routes file for writing.<small>Located at: ' . $this->_routes_dir . $this->_routes_file . '</small>' );
			return FALSE;

		endif;

		if ( ! fwrite( $_fh, $_data ) ) :

			fclose( $_fh );
			$this->_set_error( 'Unable to write data to routes file.<small>Located at: ' . $this->_routes_dir . $this->_routes_file . '</small>' );
			return FALSE;

		endif;

		fclose( $_fh );

		return TRUE;
	}



	// --------------------------------------------------------------------------


	protected function _can_write_routes()
	{
		//	First, test if file exists, if it does is it writable?
		if ( file_exists( $this->_routes_dir . $this->_routes_file ) ) :

			if ( is_really_writable( $this->_routes_dir . $this->_routes_file ) ) :

				return TRUE;

			else :

				//	Attempt to chmod the file
				if ( @chmod( $this->_routes_dir . $this->_routes_file, FILE_WRITE_MODE ) ) :

					return TRUE;

				else :

					$this->_set_error( 'The route config exists, but is not writeable. <small>Located at: ' . $this->_routes_dir . $this->_routes_file . '</small>' );
					return FALSE;

				endif;

			endif;

		elseif ( is_really_writable( $this->_routes_dir ) ) :

			return TRUE;

		else :

			//	Attempt to chmod the directory
			if ( @chmod( $this->_routes_dir, DIR_WRITE_MODE ) ) :

				return TRUE;

			else :

				$this->_set_error( 'The route directory is not writeable. <small>' . $this->_routes_dir . '</small>' );
				return FALSE;

			endif;

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_ROUTES_MODEL' ) ) :

	class Routes_model extends NAILS_Routes_model
	{
	}

endif;


/* End of file routes_model.php */
/* Location: ./modules/system/models/routes_model.php */