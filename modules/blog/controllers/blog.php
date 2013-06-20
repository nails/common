<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Blog
 *
 * Description:	This controller handles the front page of the blog
 * 
 **/

/**
 * OVERLOADING NAILS'S SHOP MODULE
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
		$this->data['page']->description 		= '';
		$this->data['page']->keywords 			= '';

		// --------------------------------------------------------------------------
		
		//	Load posts		
		$this->data['posts'] = $this->post->get_all();

		// --------------------------------------------------------------------------

		//	Widgets
		if ( blog_setting( 'sidebar_enabled' ) ) :

			$this->data['widget'] = new stdClass();
			$this->data['widget']->latest_posts	= $this->widget->latest_posts();
			$this->data['widget']->categories	= $this->widget->categories();
			$this->data['widget']->tags			= $this->widget->tags();

		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/browse',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * View a single article
	 * 
	 * @access public
	 * @return void
	 **/
	public function single()
	{
		//	Get the single post by its slug
		$this->data['post'] = $this->post->get_by_slug( $this->uri->rsegment( 2 ), TRUE );
		
		// --------------------------------------------------------------------------
		
		//	Check we have something to show, otherwise, bail out
		if ( ! $this->data['post'] )
			show_404();

		// --------------------------------------------------------------------------

		//	If this post's status is not published then 404, unless logged in as an admin
		if ( ! $this->data['post']->is_published && ! $this->user->is_admin() )
			show_404();
			
		// --------------------------------------------------------------------------
		
		//	Widgets
		if ( blog_setting( 'sidebar_enabled' ) ) :

			$this->data['widget'] = new stdClass();
			$this->data['widget']->latest_posts	= $this->widget->latest_posts();
			$this->data['widget']->categories	= $this->widget->categories();
			$this->data['widget']->tags			= $this->widget->tags();

		endif;

		// --------------------------------------------------------------------------
		
		//	Meta
		$this->data['page']->title 				= $this->data['post']->title;
		$this->data['page']->description 		= $this->data['post']->seo_description;
		$this->data['page']->keywords 			= $this->data['post']->seo_keywords;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/single',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	public function archive()
	{
		//	Widgets
		if ( blog_setting( 'sidebar_enabled' ) ) :

			$this->data['widget'] = new stdClass();
			$this->data['widget']->latest_posts	= $this->widget->latest_posts();
			$this->data['widget']->categories	= $this->widget->categories();
			$this->data['widget']->tags			= $this->widget->tags();

		endif;

		// --------------------------------------------------------------------------

		$_year	= $this->uri->rsegment( 3 );
		$_month	= $this->uri->rsegment( 4 );

		if ( $_year && $_month ) :

			$this->_archive_month( $_year, $_month );

		elseif ( $_year ) :

			$this->_archive_year( $_year );

		else :

			$this->_archive();

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _archive()
	{
		//	Meta
		$this->data['page']->title 				= 'Archive';
		$this->data['page']->description 		= 'Archive of all posts on ' . APP_NAME;
		$this->data['page']->keywords 			= '';
		
		// --------------------------------------------------------------------------

		$this->data['posts'] = $this->post->get_archive();

		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/archive',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _archive_year( $year )
	{
		//	Meta
		$this->data['page']->title 				= 'Archive (' . $year . ')';
		$this->data['page']->description 		= 'Archive of all posts on ' . APP_NAME . ' posted during ' . $year;
		$this->data['page']->keywords 			= '';
		
		// --------------------------------------------------------------------------

		$this->data['posts'] = $this->post->get_archive( $year );

		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/archive',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _archive_month( $year, $month )
	{
		switch ( (int) $month ) :

			case 1 :	$_month = lang( 'month_jan' ); break;
			case 2 :	$_month = lang( 'month_feb' ); break;
			case 3 :	$_month = lang( 'month_mar' ); break;
			case 4 :	$_month = lang( 'month_apr' ); break;
			case 5 :	$_month = lang( 'month_may' ); break;
			case 6 :	$_month = lang( 'month_jun' ); break;
			case 7 :	$_month = lang( 'month_jul' ); break;
			case 8 :	$_month = lang( 'month_aug' ); break;
			case 8 :	$_month = lang( 'month_sep' ); break;
			case 10 :	$_month = lang( 'month_oct' ); break;
			case 11 :	$_month = lang( 'month_nov' ); break;
			case 12 :	$_month = lang( 'month_dec' ); break;

		endswitch;

		// --------------------------------------------------------------------------

		//	Meta
		$this->data['page']->title 				= 'Archive (' . $_month . ', ' . $year . ')';
		$this->data['page']->description 		= 'Archive of all posts on ' . APP_NAME . ' posted during ' . $_month . ', ' . $year;
		$this->data['page']->keywords 			= '';
		
		// --------------------------------------------------------------------------

		$this->data['posts'] = $this->post->get_archive( $year, $month );

		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/archive',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	public function category()
	{
		if ( ! blog_setting( 'categories_enabled' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		if ( ! $this->uri->rsegment( 3 ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Get category
		$this->data['category'] = $this->category->get_by_slug( $this->uri->rsegment( 3 ) );
		
		if ( ! $this->data['category'] ) :

			show_404();
		
		endif;

		// --------------------------------------------------------------------------

		if ( blog_setting( 'sidebar_enabled' ) ) :

			$this->data['widget'] = new stdClass();
			$this->data['widget']->latest_posts	= $this->widget->latest_posts();
			$this->data['widget']->categories	= $this->widget->categories();
			$this->data['widget']->tags			= $this->widget->tags();

		endif;

		// --------------------------------------------------------------------------

		//	Meta
		$this->data['page']->title 				= 'Posts in category "' . $this->data['category']->label . '"';
		$this->data['page']->description 		= 'Archive of all posts on ' . APP_NAME . ' posted in the  ' . $this->data['category']->label . ' category ';
		$this->data['page']->keywords 			= '';
		
		// --------------------------------------------------------------------------

		$this->data['posts'] = $this->post->get_with_category( $this->data['category']->id );

		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/archive',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	public function tag()
	{
		if ( ! blog_setting( 'tags_enabled' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		if ( ! $this->uri->rsegment( 3 ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Get category
		$this->data['tag'] = $this->tag->get_by_slug( $this->uri->rsegment( 3 ) );
		
		if ( ! $this->data['tag'] ) :

			show_404();
		
		endif;

		// --------------------------------------------------------------------------

		if ( blog_setting( 'sidebar_enabled' ) ) :

			$this->data['widget'] = new stdClass();
			$this->data['widget']->latest_posts	= $this->widget->latest_posts();
			$this->data['widget']->categories	= $this->widget->categories();
			$this->data['widget']->tags			= $this->widget->tags();

		endif;

		// --------------------------------------------------------------------------

		//	Meta
		$this->data['page']->title 				= 'Posts tagged with "' . $this->data['tag']->label . '"';
		$this->data['page']->description 		= 'Archive of all posts on ' . APP_NAME . ' tagged with  ' . $this->data['tag']->label . ' ';
		$this->data['page']->keywords 			= '';
		
		// --------------------------------------------------------------------------

		$this->data['posts'] = $this->post->get_with_tag( $this->data['tag']->id );

		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/archive',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
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

		if ( method_exists( $this, $method ) ) :
		
			//	Method exists, execute it
			$this->{$method}();
		
		else :
		
			//	Doesn't exist, consider rsegment( 2 ) a slug
			$this->single();
		
		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S BLOG MODULE
 * 
 * The following block of code makes it simple to extend one of the core blog
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG' ) ) :

	class Blog extends NAILS_Blog
	{
	}

endif;

/* End of file blog.php */
/* Location: ./modules/blog/controllers/blog.php */