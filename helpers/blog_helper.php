<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Get latest blog posts
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'blog_latest_posts' ) )
{
	function blog_latest_posts( $limit = 9 )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->isModelLoaded( 'blog_post_model' ) ) :

			get_instance()->load->model( 'blog/blog_post_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->blog_post_model->get_latest( $limit );
	}
}


// --------------------------------------------------------------------------


/**
 * Get all posts for a certain tag
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'blog_posts_with_tag' ) )
{
	function blog_posts_with_tag( $id_slug, $only_published = TRUE, $include_body = FALSE, $exclude_deleted = TRUE )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->isModelLoaded( 'blog_post_model' ) ) :

			get_instance()->load->model( 'blog/blog_post_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->blog_post_model->get_with_tag( $id_slug, $only_published, $include_body, $exclude_deleted );
	}
}


// --------------------------------------------------------------------------


/**
 * Get all posts for a certain category
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'blog_posts_with_category' ) )
{
	function blog_posts_with_category( $id_slug, $only_published = TRUE, $include_body = FALSE, $exclude_deleted = TRUE )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->isModelLoaded( 'blog_post_model' ) ) :

			get_instance()->load->model( 'blog/blog_post_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->blog_post_model->get_with_category( $id_slug, $only_published, $include_body, $exclude_deleted );
	}
}


// --------------------------------------------------------------------------


/**
 * Get all posts which contain a certain association
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'blog_posts_with_association' ) )
{
	function blog_posts_with_association( $association_index, $associated_id )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->isModelLoaded( 'blog_post_model' ) ) :

			get_instance()->load->model( 'blog/blog_post_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->blog_post_model->get_with_association( $association_index, $associated_id );
	}
}

/* End of file blog_helper.php */
/* Location: ./modules/blog/helpers/blog_helper.php */