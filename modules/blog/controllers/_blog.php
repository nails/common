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
		$this->lang->load( 'blog', RENDER_LANG );
		
		// --------------------------------------------------------------------------
		
		//	Load the models
		$this->load->model( 'blog_post_model',		'post' );
		
		// --------------------------------------------------------------------------
		
		//	Load the helper
		$this->load->helper( 'blog' );
		
		// --------------------------------------------------------------------------
		
		//	Load the styles
		$this->asset->load( 'nails.blog.css', TRUE );
	}
}

/* End of file _blog.php */
/* Location: ./application/modules/blog/controllers/_blog.php */