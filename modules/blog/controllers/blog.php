<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Blog
 *
 * Description:	This controller handles the front page of the blog
 *
 **/

/**
 * OVERLOADING NAILS' BLOG MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

//	Include _blog.php; executes common functionality
require_once '_blog.php';

class NAILS_Blog extends NAILS_Blog_Controller
{
	/**
	 * Browse all articles
	 *
	 * @access public
	 * @return void
	 **/
	public function index()
	{
		//	Meta & Breadcrumbs
		$this->data['page']->title 				= APP_NAME . ' Blog';
		$this->data['page']->seo->description 		= '';
		$this->data['page']->seo->keywords 			= '';

		// --------------------------------------------------------------------------

		//	Handle pagination
		$_page		= $this->uri->rsegment( 2 );
		$_per_page	= app_setting( 'home_per_page', 'blog' );
		$_per_page	= $_per_page ? $_per_page : 10;

		$this->data['pagination']			= new stdClass();
		$this->data['pagination']->page		= $_page;
		$this->data['pagination']->per_page	= $_per_page;

		// --------------------------------------------------------------------------

		//	Send any additional data
		$_data						= array();
		$_data['include_body']		= ! app_setting( 'use_excerpts', 'blog' );
		$_data['include_gallery']	= app_setting( 'home_show_gallery', 'blog' );
		$_data['sort']				= array( 'bp.published', 'desc' );

		//	Only published items which are not schduled for the future
		$_data['where']		= array();
		$_data['where'][]	= array( 'column' => 'is_published',	'value' => TRUE );
		$_data['where'][]	= array( 'column' => 'published <=',	'value' => 'NOW()', 'escape' => FALSE );

		// --------------------------------------------------------------------------

		//	Load posts and count
		$this->data['posts'] = $this->blog_post_model->get_all( $_page, $_per_page, $_data );
		$this->data['pagination']->total = $this->blog_post_model->count_all( $_data );

		// --------------------------------------------------------------------------

		//	Widgets
		$this->_fetch_sidebar_widgets();

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',					$this->data );
		$this->load->view( $this->_skin->path . 'views/browse',	$this->data );
		$this->load->view( 'structure/footer',					$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * View a single article
	 *
	 * @access public
	 * @return void
	 **/
	public function single( $id = NULL )
	{
		//	Get the single post by its slug
		if ( $id ) :

			$this->data['post'] = $this->blog_post_model->get_by_id( $id );

			if ( $this->data['post']->url != $this->input->server( 'REQUEST_URI' ) ) :

				redirect( $this->data['post']->url, 'location', 301 );

			endif;

		else :

			$this->data['post'] = $this->blog_post_model->get_by_slug( $this->uri->rsegment( 2 ) );

		endif;

		// --------------------------------------------------------------------------

		//	Check we have something to show, otherwise, bail out
		if ( ! $this->data['post'] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	If this post's status is not published then 404, unless logged in as an admin
		if ( ! $this->data['post']->is_published && ! $this->user_model->is_admin() ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Widgets
		$this->_fetch_sidebar_widgets();

		// --------------------------------------------------------------------------

		//	Meta
		$this->data['page']->title				= $this->_blog_name . ': ';
		$this->data['page']->title				.= $this->data['post']->seo_title ? $this->data['post']->seo_title : $this->data['post']->title;
		$this->data['page']->seo->description	= $this->data['post']->seo_description;
		$this->data['page']->seo->keywords		= $this->data['post']->seo_keywords;

		// --------------------------------------------------------------------------

		//	Assets
		if ( app_setting( 'social_enabled', 'blog' ) ) :

			$this->asset->load( 'social-likes/social-likes.min.js', 'BOWER' );

			switch ( app_setting( 'social_skin', 'blog' ) )  :

				case 'FLAT' :

					$this->asset->load( 'social-likes/social-likes_flat.css', 'BOWER' );

				break;

				case 'BIRMAN' :

					$this->asset->load( 'social-likes/social-likes_birman.css', 'BOWER' );

				break;

				case 'CLASSIC' :
				default:

					$this->asset->load( 'social-likes/social-likes_classic.css', 'BOWER' );

				break;

			endswitch;

		endif;

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',					$this->data );
		$this->load->view( $this->_skin->path . 'views/single',	$this->data );
		$this->load->view( 'structure/footer',					$this->data );

		// --------------------------------------------------------------------------

		//	Register a hit
		$_data				= array();
		$_data['user_id']	= active_user( 'id' );
		$_data['referrer']	= $this->input->server( 'HTTP_REFERER' );

		$this->blog_post_model->add_hit( $this->data['post']->id, $_data );
	}


	// --------------------------------------------------------------------------


	public function category()
	{
		if ( ! app_setting( 'categories_enabled', 'blog' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		if ( ! $this->uri->rsegment( 3 ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Get category
		$this->data['category'] = $this->blog_category_model->get_by_slug( $this->uri->rsegment( 3 ) );

		if ( ! $this->data['category'] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Widgets
		$this->_fetch_sidebar_widgets();

		// --------------------------------------------------------------------------

		//	Meta
		$this->data['page']->title 				= $this->_blog_name . ': Posts in category "' . $this->data['category']->label . '"';
		$this->data['page']->seo->description 	= 'All posts on ' . APP_NAME . ' posted in the  ' . $this->data['category']->label . ' category ';
		$this->data['page']->seo->keywords 		= '';

		// --------------------------------------------------------------------------

		//	Handle pagination
		$_page		= $this->uri->rsegment( 2 );
		$_per_page	= app_setting( 'home_per_page', 'blog' );
		$_per_page	= $_per_page ? $_per_page : 10;

		$this->data['pagination']			= new stdClass();
		$this->data['pagination']->page		= $_page;
		$this->data['pagination']->per_page	= $_per_page;

		// --------------------------------------------------------------------------

		//	Send any additional data
		$_data						= array();
		$_data['include_body']		= ! app_setting( 'use_excerpts', 'blog' );
		$_data['include_gallery']	= app_setting( 'home_show_gallery', 'blog' );
		$_data['sort']				= array( 'bp.published', 'desc' );

		//	Only published items which are not schduled for the future
		$_data['where']		= array();
		$_data['where'][]	= array( 'column' => 'is_published',	'value' => TRUE );
		$_data['where'][]	= array( 'column' => 'published <=',	'value' => 'NOW()', 'escape' => FALSE );

		// --------------------------------------------------------------------------

		//	Load posts and count
		$this->data['posts'] = $this->blog_post_model->get_with_category( $this->data['category']->id, $_page, $_per_page, $_data );
		$this->data['pagination']->total = $this->blog_post_model->count_all( $_data );

		// --------------------------------------------------------------------------

		//	Finally, let the views know this is an 'archive' type page
		$this->data['archive_title'] = 'Posts in category "' . $this->data['category']->label . '"';

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',					$this->data );
		$this->load->view( $this->_skin->path . 'views/browse',	$this->data );
		$this->load->view( 'structure/footer',					$this->data );
	}


	// --------------------------------------------------------------------------


	public function tag()
	{
		if ( ! app_setting( 'tags_enabled', 'blog' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		if ( ! $this->uri->rsegment( 3 ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Get category
		$this->data['tag'] = $this->blog_tag_model->get_by_slug( $this->uri->rsegment( 3 ) );

		if ( ! $this->data['tag'] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Widgets
		$this->_fetch_sidebar_widgets();

		// --------------------------------------------------------------------------

		//	Meta
		$this->data['page']->title 				= $this->_blog_name . ': Posts tagged with "' . $this->data['tag']->label . '"';
		$this->data['page']->seo->description	= 'All posts on ' . APP_NAME . ' tagged with  ' . $this->data['tag']->label . ' ';
		$this->data['page']->seo->keywords 		= '';

		// --------------------------------------------------------------------------

		//	Handle pagination
		$_page		= $this->uri->rsegment( 2 );
		$_per_page	= app_setting( 'home_per_page', 'blog' );
		$_per_page	= $_per_page ? $_per_page : 10;

		$this->data['pagination']			= new stdClass();
		$this->data['pagination']->page		= $_page;
		$this->data['pagination']->per_page	= $_per_page;

		// --------------------------------------------------------------------------

		//	Send any additional data
		$_data						= array();
		$_data['include_body']		= ! app_setting( 'use_excerpts', 'blog' );
		$_data['include_gallery']	= app_setting( 'home_show_gallery', 'blog' );
		$_data['sort']				= array( 'bp.published', 'desc' );

		//	Only published items which are not schduled for the future
		$_data['where']		= array();
		$_data['where'][]	= array( 'column' => 'is_published',	'value' => TRUE );
		$_data['where'][]	= array( 'column' => 'published <=',	'value' => 'NOW()', 'escape' => FALSE );

		// --------------------------------------------------------------------------

		//	Load posts and count
		$this->data['posts'] = $this->blog_post_model->get_with_tag( $this->data['tag']->id, $_page, $_per_page, $_data );
		$this->data['pagination']->total = $this->blog_post_model->count_all( $_data );

		// --------------------------------------------------------------------------

		//	Finally, let the views know this is an 'archive' type page
		$this->data['archive_title'] = 'Posts in tag "' . $this->data['tag']->label . '"';

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',					$this->data );
		$this->load->view( $this->_skin->path . 'views/browse',	$this->data );
		$this->load->view( 'structure/footer',					$this->data );
	}


	// --------------------------------------------------------------------------


	public function rss()
	{
		if ( ! app_setting( 'rss_enabled', 'blog' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Get posts
		$_data						= array();
		$_data['include_body']		= TRUE;
		$_data['include_gallery']	= app_setting( 'home_show_gallery', 'blog' );
		$_data['sort']				= array( 'bp.published', 'desc' );

		//	Only published items which are not schduled for the future
		$_data['where']		= array();
		$_data['where'][]	= array( 'column' => 'is_published',	'value' => TRUE );
		$_data['where'][]	= array( 'column' => 'published <=',	'value' => 'NOW()', 'escape' => FALSE );

		$this->data['posts'] = $this->blog_post_model->get_all( NULL, NULL, $_data );

		// --------------------------------------------------------------------------

		//	Set Output
		$this->output->set_content_type( 'text/xml; charset=UTF-8' );
		$this->load->view( $this->_skin->path . 'views/rss', $this->data );
	}


	// --------------------------------------------------------------------------

	/**
	 * Loads all the enabled sidebar widgets
	 * @return void
	 */
	protected function _fetch_sidebar_widgets()
	{
		//	Widgets
		if ( app_setting( 'sidebar_enabled', 'blog' ) ) :

			$this->data['widget'] = new stdClass();

			if ( app_setting( 'sidebar_latest_posts', 'blog' ) ) :

				$this->data['widget']->latest_posts = $this->blog_widget_model->latest_posts();

			endif;

			if ( app_setting( 'sidebar_categories', 'blog' ) ) :

				$this->data['widget']->categories = $this->blog_widget_model->categories();

			endif;

			if ( app_setting( 'sidebar_tags', 'blog' ) ) :

				$this->data['widget']->tags = $this->blog_widget_model->tags();

			endif;

			if ( app_setting( 'sidebar_popular_posts', 'blog' ) ) :

				$this->data['widget']->popular_posts = $this->blog_widget_model->popular_posts();

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Map slugs to the single() method
	 *
	 * @access public
	 * @return void
	 **/
	public function _remap( $method )
	{
		$method = $method ? $method : 'index';

		if ( method_exists( $this, $method ) && $this->input->get( 'id' ) ) :

			$this->single( $this->input->get( 'id' ) );

		elseif ( method_exists( $this, $method ) ) :

			//	Method exists, execute it
			$this->{$method}();

		elseif( is_numeric( $method ) ) :

			//	Paginating the main blog page
			$this->index();

		else :

			//	Doesn't exist, consider rsegment( 2 ) a slug
			$this->single();

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' BLOG MODULE
 *
 * The following block of code makes it simple to extend one of the core blog
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG' ) ) :

	class Blog extends NAILS_Blog
	{
	}

endif;

/* End of file blog.php */
/* Location: ./modules/blog/controllers/blog.php */