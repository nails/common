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
		$this->load->model( 'blog_skin_model',		'skin' );

		// --------------------------------------------------------------------------

		if ( app_setting( 'categories_enabled', 'blog' ) ) :

			$this->load->model( 'blog_category_model',	'category' );

		endif;


		if ( app_setting( 'tags_enabled', 'blog' ) ) :

			$this->load->model( 'blog_tag_model',	'tag' );

		endif;

		// --------------------------------------------------------------------------

		//	Load up the blog's skin
		$_skin = app_setting( 'skin', 'blog' ) ? app_setting( 'skin', 'blog' ) : 'getting-started';

		$this->_skin = $this->skin->get( $_skin );

		if ( ! $this->_skin ) :

			show_fatal_error( 'Failed to load blog skin "' . $_skin . '"', 'Blog skin "' . $_skin . '" failed to load at ' . APP_NAME . ', the following reason was given: ' . $this->skin->last_error() );

		endif;

		// --------------------------------------------------------------------------

		//	Pass to $this->data, for the views
		$this->data['skin'] = $this->_skin;

		// --------------------------------------------------------------------------

		//	Blog name
		$this->_blog_name = app_setting( 'name', 'blog' ) ? app_setting( 'name', 'blog' ) : 'Blog';
	}
}

/* End of file _blog.php */
/* Location: ./application/modules/blog/controllers/_blog.php */