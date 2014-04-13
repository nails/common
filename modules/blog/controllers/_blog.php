<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NALS_BLOG_Controller
 *
 * Description:	This controller executes various bits of common Blog functionality
 *
 **/


class NAILS_Blog_Controller extends NAILS_Controller
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Check this module is enabled in settings
		if ( ! module_is_enabled( 'blog' ) ) :

			//	Cancel execution, module isn't enabled
			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Load language file
		$this->lang->load( 'blog', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------

		//	Load the models
		$this->load->model( 'blog_model',			'blog' );
		$this->load->model( 'blog_post_model',		'post' );
		$this->load->model( 'blog_widget_model',	'widget' );

		// --------------------------------------------------------------------------

		if ( blog_setting( 'categories_enabled' ) ) :

			$this->load->model( 'blog_category_model',	'category' );

		endif;


		if ( blog_setting( 'tags_enabled' ) ) :

			$this->load->model( 'blog_tag_model',	'tag' );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch the Blog URL
		$this->data['blog_url'] = blog_setting( 'blog_url' );

		// --------------------------------------------------------------------------

		//	Load the styles
		//$this->asset->load( 'nails.blog.css', TRUE );

		if ( file_exists( FCPATH . 'assets/css/blog.css' ) ) :

			$this->asset->load( 'blog.css' );

		endif;
	}
}

/* End of file _blog.php */
/* Location: ./application/modules/blog/controllers/_blog.php */