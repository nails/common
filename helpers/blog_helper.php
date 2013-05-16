<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Helper for quickly accessing blog settings
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'blog_setting' ) )
{
	function blog_setting( $key = NULL )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'blog' ) ) :
		
			get_instance()->load->model( 'blog/blog_model', 'blog' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return get_instance()->blog->settings( $key );
	}
}

/* End of file blog_helper.php */
/* Location: ./modules/blog/helpers/blog_helper.php */