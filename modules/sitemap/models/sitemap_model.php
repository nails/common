<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NAILS_Sitemap_model
 *
 * Description:	This model handles the generation of sitemaps
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Sitemap_model extends NAILS_Model
{
	protected $_writers;
	protected $_filename_json;
	protected $_filename_xml;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Set Defaults
		$this->_writers				= array();
		$this->_filename_json		= 'sitemap.json';
		$this->_filename_xml		= 'sitemap.xml';

		//	Default writers
		$this->_writers['static']	= array( $this, '_generator_static' );
		$this->_writers['cms']		= array( $this, '_generator_cms' );
		$this->_writers['blog']		= array( $this, '_generator_blog' );
		$this->_writers['shop']		= array( $this, '_generator_shop' );
	}


	// --------------------------------------------------------------------------


	public function get_filename_json()
	{
		return $this->_filename_json;
	}


	// --------------------------------------------------------------------------


	public function get_filename_xml()
	{
		return $this->_filename_xml;
	}


	// --------------------------------------------------------------------------


	public function generate()
	{
		//	Will we be able to write to the cache?
		if ( ! is_writable( DEPLOY_CACHE_DIR ) ) :

			$this->_set_error( 'Cache is not writeable.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_map					= new stdClass();
		$_map->meta				= new stdClass();
		$_map->meta->generated	= date( DATE_ATOM );
		$_map->pages			= array();

		foreach ( $this->_writers AS $slug => $method ) :

			if ( is_callable( array( $method[0], $method[1] ) ) ) :

				$_result = call_user_func( array( $method[0], $method[1] ) );

				if ( is_array( $_result ) ) :

					$_map->pages = array_merge( $_map->pages, $_result );

				endif;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Sort the array into a vaguely sensible order
		$this->load->helper( 'array' );
		array_sort_multi( $_map->pages, 'location' );

		// --------------------------------------------------------------------------

		//	Save this data as JSON and XML files
		$this->load->helper( 'file' );

		//	JSON, easy
		if ( ! write_file( DEPLOY_CACHE_DIR . $this->_filename_json, json_encode( $_map ) ) ) :

			$this->_set_error( 'Failed to write ' . $this->_filename_json . '.' );
			return FALSE;

		endif;

		//	XML file is a little more complex
		$_fh = fopen( DEPLOY_CACHE_DIR . $this->_filename_xml, 'w' );

		if (  ! $_fh ) :

			$this->_set_error( 'Failed to write ' . $this->_filename_xml . ': Could not open file for writing.' );
			return FALSE;

		endif;

		$_xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$_xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'. "\n";

		if ( fwrite( $_fh, $_xml ) ) :

			for ( $i = 0; $i < count( $_map->pages ); $i++ ) :

				$_xml  = '<url>' . "\n";
				$_xml .= '<loc>' . $_map->pages[$i]->location . '</loc>'. "\n";
				$_xml .= ! empty( $_map->pages[$i]->lastmod )		? '<lastmod>' . $_map->pages[$i]->lastmod . '</lastmod>' . "\n"			: '';
				$_xml .= ! empty( $_map->pages[$i]->changefreq )	? '<changefreq>' . $_map->pages[$i]->changefreq. '</changefreq>' . "\n"	: '';
				$_xml .= ! empty( $_map->pages[$i]->priority )		? '<priority>' . $_map->pages[$i]->priority. '</priority>' . "\n"		: '';
				$_xml .= '</url>'. "\n";

				if ( ! fwrite( $_fh, $_xml ) ) :

					@unlink( DEPLOY_CACHE_DIR . $this->_filename_xml );
					$this->_set_error( 'Failed to write ' . $this->_filename_xml . ': Could write to file - #2.' );
					return FALSE;

				endif;

			endfor;

			//	finally, close <urlset>
			$_xml = '</urlset>' . "\n";

			if ( ! fwrite( $_fh, $_xml ) ) :

				@unlink( DEPLOY_CACHE_DIR . $this->_filename_xml );
				$this->_set_error( 'Failed to write ' . $this->_filename_xml . ': Could write to file - #3.' );
				return FALSE;

			endif;

			return TRUE;

		else :

			@unlink( DEPLOY_CACHE_DIR . $this->_filename_xml );
			$this->_set_error( 'Failed to write ' . $this->_filename_xml . ': Could write to file - #1.' );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _generator_static()
	{
		$_map = array();

		// --------------------------------------------------------------------------

		$_map[0]				= new stdClass();
		$_map[0]->title			= 'Homepage';
		$_map[0]->location		= site_url();
		$_map[0]->breadcrumbs	= '';
		$_map[0]->changefreq	= 'daily';
		$_map[0]->priority		= 1;

		// --------------------------------------------------------------------------

		return $_map;
	}


	// --------------------------------------------------------------------------


	protected function _generator_cms()
	{
		if ( module_is_enabled( 'cms' ) ) :

			$_map = array();

			// --------------------------------------------------------------------------

			$this->load->model( 'cms/cms_page_model' );

			$_pages		= $this->cms_page_model->get_all();
			$_counter	= 0;

			foreach ( $_pages AS $page ) :

				if ( $page->is_published && ! $page->is_homepage ) :

					$_map[$_counter]				= new stdClass();
					$_map[$_counter]->title			= htmlentities( $page->published->title );
					$_map[$_counter]->breadcrumbs	= $page->published->breadcrumbs;
					$_map[$_counter]->location		= site_url( $page->published->slug );
					$_map[$_counter]->lastmod		= date( DATE_ATOM, strtotime( $page->modified ) );
					$_map[$_counter]->changefreq	= 'monthly';
					$_map[$_counter]->priority		= 0.5;

				endif;

				$_counter++;

			endforeach;

			// --------------------------------------------------------------------------

			return $_map;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _generator_blog()
	{
		if ( module_is_enabled( 'blog' ) ) :

			$_map = array();

			// --------------------------------------------------------------------------

			$this->load->model( 'blog/blog_post_model' );

			$_posts		= $this->blog_post_model->get_all();
			$_url		= app_setting( 'url', 'blog' );
			$_counter	= 0;

			// --------------------------------------------------------------------------

			//	Blog front page route
			$_map[$_counter]				= new stdClass();
			$_map[$_counter]->title			= htmlentities( 'Blog' );	//	TODO: this is silly, should blog "name" be configurable?
			$_map[$_counter]->location		= site_url( $_url );
			$_map[$_counter]->changefreq	= 'daily';
			$_map[$_counter]->priority		= 0.5;

			$_counter++;

			// --------------------------------------------------------------------------

			foreach ( $_posts AS $post ) :

				if ( $post->is_published ) :

					$_map[$_counter]				= new stdClass();
					$_map[$_counter]->title			= htmlentities( $post->title );
					$_map[$_counter]->location		= site_url( $_url . $post->slug );
					$_map[$_counter]->lastmod		= date( DATE_ATOM, strtotime( $post->modified ) );
					$_map[$_counter]->changefreq	= 'monthly';
					$_map[$_counter]->priority		= 0.5;

				endif;

				$_counter++;

			endforeach;

			// --------------------------------------------------------------------------

			return $_map;

		endif;
	}


	// --------------------------------------------------------------------------



	protected function _generator_shop()
	{
		if ( module_is_enabled( 'shop' ) ) :

			//	TODO: all shop product/category/tag/sale routes etc

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_routes()
	{
		$_routes						= array();
		$_routes['sitemap']				= 'sitemap/sitemap';
		$_routes[$this->_filename_xml]	= 'sitemap/sitemap';
		$_routes[$this->_filename_json]	= 'sitemap/sitemap';

		return $_routes;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SITEMAP_MODEL' ) ) :

	class Sitemap_model extends NAILS_Sitemap_model
	{
	}

endif;


/* End of file sitemap_model.php */
/* Location: ./modules/sitemap/models/sitemap_model.php */