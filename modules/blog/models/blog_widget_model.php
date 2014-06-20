<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		blog_widget_model
 *
 * Description:	This model handles all interactions with blog widgets on site.
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Blog_widget_model extends NAILS_Model
{
	/**
	 * Fetches the latest blog posts
	 * @param  integer $limit The maximum number of posts to return
	 * @return array
	 */
	public function latest_posts( $limit = 5 )
	{
		$this->db->select( 'id,slug,title,published' );
		$this->db->where( 'is_published', TRUE );
		$this->db->where( 'published <=', 'NOW()', FALSE );
		$this->db->where( 'is_deleted', FALSE );
		$this->db->limit( $limit );
		$this->db->order_by( 'published', 'DESC' );
		$_posts = $this->db->get( NAILS_DB_PREFIX . 'blog_post' )->result();

		if ( ! $this->load->model_is_loaded( 'blog_post_model' ) ) :

			$this->load->model( 'blog/blog_post_model' );

		endif;

		foreach ( $_posts AS $post ) :

			$post->url = $this->blog_post_model->format_url( $post->slug );

		endforeach;

		return $_posts;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches the latest blog posts
	 *
	 * @access public
	 * @param array $config Changes to the default configs
	 * @return array
	 **/
	public function popular_posts( $limit = 5 )
	{
		$this->db->select( 'bp.id,bp.slug,bp.title,bp.published,COUNT(bph.id) hits' );
		$this->db->join( NAILS_DB_PREFIX . 'blog_post bp', 'bp.id = bph.post_id' );
		$this->db->where( 'bp.is_published', TRUE );
		$this->db->where( 'bp.published <=', 'NOW()', FALSE );
		$this->db->where( 'bp.is_deleted', FALSE );
		$this->db->group_by( 'bp.id' );
		$this->db->order_by( 'hits', 'DESC' );
		$this->db->order_by( 'bp.published', 'DESC' );
		$this->db->limit( $limit );

		$_posts = $this->db->get( NAILS_DB_PREFIX . 'blog_post_hit bph' )->result();

		if ( ! $this->load->model_is_loaded( 'blog_post_model' ) ) :

			$this->load->model( 'blog/blog_post_model' );

		endif;

		foreach ( $_posts AS $post ) :

			$post->url = $this->blog_post_model->format_url( $post->slug );

		endforeach;

		return $_posts;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches the blog categories
	 *
	 * @access public
	 * @param array $config Changes to the default configs
	 * @param boolean $return_html Whether to return HTML or just the data
	 * @return array
	 **/
	public function categories( $include_count = TRUE, $only_populated = TRUE )
	{
		$this->db->select( 'c.id,c.slug,c.label' );

		if ( $include_count ) :

			$this->db->select( '(SELECT COUNT(DISTINCT bpc.post_id) FROM ' . NAILS_DB_PREFIX . 'blog_post_category bpc JOIN ' . NAILS_DB_PREFIX . 'blog_post bp ON bpc.post_id = bp.id WHERE bpc.category_id = c.id AND bp.is_published = 1 AND bp.is_deleted = 0 AND bp.published <= NOW()) post_count' );

		endif;

		if ( $only_populated ) :

			$this->db->having( 'post_count > ', 0 );

		endif;

		$this->db->order_by( 'c.label' );

		$_categories = $this->db->get( NAILS_DB_PREFIX . 'blog_category c' )->result();

		if ( ! $this->load->model_is_loaded( 'blog_category_model' ) ) :

			$this->load->model( 'blog/blog_category_model' );

		endif;

		foreach ( $_categories AS $cat ) :

			$cat->url = $this->blog_category_model->format_url( $cat->slug );

		endforeach;

		return $_categories;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches the blog tags
	 *
	 * @access public
	 * @param array $config Changes to the default configs
	 * @param boolean $return_html Whether to return HTML or just the data
	 * @return mixed
	 **/
	public function tags( $include_count = TRUE, $only_populated = TRUE )
	{
		$this->db->select( 't.id,t.slug,t.label' );

		if ( $include_count ) :

			$this->db->select( '(SELECT COUNT(DISTINCT bpt.post_id) FROM ' . NAILS_DB_PREFIX . 'blog_post_tag bpt JOIN ' . NAILS_DB_PREFIX . 'blog_post bp ON bpt.post_id = bp.id WHERE bpt.tag_id = t.id AND bp.is_published = 1 AND bp.is_deleted = 0 AND bp.published <= NOW()) post_count' );

		endif;

		if ( $only_populated ) :

			$this->db->having( 'post_count > ', 0 );

		endif;

		$this->db->order_by( 't.label' );

		$_tags = $this->db->get( NAILS_DB_PREFIX . 'blog_tag t' )->result();

		if ( ! $this->load->model_is_loaded( 'blog_tag_model' ) ) :

			$this->load->model( 'blog/blog_tag_model' );

		endif;

		foreach ( $_tags AS $tag ) :

			$tag->url = $this->blog_tag_model->format_url( $tag->slug );

		endforeach;

		return $_tags;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core Nails
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG_WIDGET_MODEL' ) ) :

	class Blog_widget_model extends NAILS_Blog_widget_model
	{
	}

endif;

/* End of file blog_widget_model.php */
/* Location: ./application/models/blog_widget_model.php */