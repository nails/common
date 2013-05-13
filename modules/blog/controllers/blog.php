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
		$this->data['widget'] = new stdClass();
		$this->data['widget']->latest_posts	= $this->widget->latest_posts();
		$this->data['widget']->categories	= $this->widget->categories();
		$this->data['widget']->tags			= $this->widget->tags();
		
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
		$this->data['post'] = $this->post->get_by_slug( $this->uri->segment( 2 ), TRUE );
		
		// --------------------------------------------------------------------------
		
		//	Check we have something to show, otherwise, bail out
		if ( !$this->data['post'] )
			show_404();
			
		// --------------------------------------------------------------------------
		
		//	Widgets
		$this->data['widget'] = new stdClass();
		$this->data['widget']->latest_posts	= $this->widget->latest_posts();
		$this->data['widget']->categories	= $this->widget->categories();
		$this->data['widget']->tags			= $this->widget->tags();

		// --------------------------------------------------------------------------
		
		//	Meta & Breadcrumbs
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
	
	
	/**
	 * Map slugs to the single() method
	 * 
	 * @access public
	 * @return void
	 **/
	public function _remap( $method )
	{
		if ( method_exists( $this, $method ) ) :
		
			//	Method exists, execute it
			$this->{$method}();
		
		else :
		
			//	Doesn't exist, consider segment( 2 ) a slug
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