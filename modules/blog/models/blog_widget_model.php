<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		blog_widget_model
 *
 * Description:	This model handles all interactions with blog widgets on site.
 * 
 **/

class Blog_widget_model extends NAILS_Model
{
	public function __construct()
	{
		//	Load the helper
		$this->load->helper( 'blog' );

		// --------------------------------------------------------------------------

		//	Fetch the Blog URL
		$this->data['blog_url'] = blog_setting( 'blog_url' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches the latest blog posts
	 * 
	 * @access public
	 * @param array $config Changes to the default configs
	 * @param boolean $return_html Whether to return HTML or just the data
	 * @return mixed``
	 **/
	public function latest_posts( $config = array(), $return_html = TRUE )
	{
		//	Define defaults
		$_config				= new stdClass();
		$_config->limit			= isset( $config['limit'] ) ? (int) $config['limit'] : 5;
		$_config->h_tag			= isset( $config['h_tag'] ) ? $config['h_tag'] : '5';
		$_config->h_class		= isset( $config['h_class'] ) ? $config['h_class'] : '';
		$_config->li_class		= isset( $config['li_class'] ) ? $config['li_class'] : '';
		$_config->title			= isset( $config['title'] ) ? $config['title'] : 'Latest Posts';
		$_config->meta_show		= isset( $config['meta_show'] ) ? $config['meta_show'] : TRUE;
		$_config->meta_class	= isset( $config['meta_class'] ) ? $config['meta_class'] : '';

		// --------------------------------------------------------------------------

		$this->db->select( 'id,slug,title,published' );
		$this->db->where( 'is_published', TRUE );
		$this->db->limit( $_config->limit );
		$this->db->order_by( 'published', 'DESC' );
		$_posts = $this->db->get( 'blog_post' )->result();

		// --------------------------------------------------------------------------

		//	Render HTML?
		if ( $return_html ) :

			$_out = '<h' . $_config->h_tag . ' class="' . $_config->h_class . '">' . $_config->title . '</h' . $_config->h_tag . '>';
			$_out .= '<ul>';

			foreach ( $_posts AS $post ) :

				$_out .= '<li class="' . $_config->li_class . '">';
				$_out .= anchor( $this->data['blog_url'] . $post->slug, $post->title );

				if ( $_config->meta_show ) :

					$_out .= '<small class="' . $_config->meta_class . '">';
					$_out .= 'Published ' . date( 'jS F Y, H:i', strtotime( $post->published ) );
					$_out .= '</small>';

				endif;
				$_out .= '</li>';

			endforeach;

			$_out .= '</ul>';

			return $_out;

		else :

			return $_posts;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches the blog categories
	 * 
	 * @access public
	 * @param array $config Changes to the default configs
	 * @param boolean $return_html Whether to return HTML or just the data
	 * @return mixed
	 **/
	public function categories( $config = array(), $return_html = TRUE )
	{
		//	Define defaults
		$_config				= new stdClass();
		$_config->limit			= isset( $config['limit'] ) ? (int) $config['limit'] : NULL;
		$_config->h_tag			= isset( $config['h_tag'] ) ? $config['h_tag'] : '5';
		$_config->h_class		= isset( $config['h_class'] ) ? $config['h_class'] : '';
		$_config->li_class		= isset( $config['li_class'] ) ? $config['li_class'] : '';
		$_config->title			= isset( $config['title'] ) ? $config['title'] : 'Categories';
		$_config->show_count	= isset( $config['show_count'] ) ? $config['show_count'] : TRUE;

		// --------------------------------------------------------------------------

		$this->db->select( 'c.id,c.slug,c.label' );

		if ( $_config->show_count ) :

			$this->db->select( '(SELECT COUNT(DISTINCT post_id) FROM blog_post_category WHERE category_id = c.id) post_count' );

		endif;

		if ( ! is_null( $_config->limit ) && is_numeric( $_config->limit ) ) :

			$this->db->limit( $_config->limit );

		endif;

		$this->db->order_by( 'c.label' );
		$_cats = $this->db->get( 'blog_category c' )->result();

		// --------------------------------------------------------------------------

		//	Render HTML?
		if ( $return_html ) :

			$_out = '<h' . $_config->h_tag . ' class="' . $_config->h_class . '">' . $_config->title . '</h' . $_config->h_tag . '>';
			$_out .= '<ul>';

			foreach ( $_cats AS $cat ) :

				$_out .= '<li class="' . $_config->li_class . '">';

				$_count = $_config->show_count ? ' (' . $cat->post_count . ')' : '';
				$_out .= '&rsaquo; ' . anchor( $this->data['blog_url'] . 'category/' . $cat->slug, $cat->label ) . $_count;
				$_out .= '</li>';

			endforeach;

			$_out .= '</ul>';

			return $_out;

		else :

			return $_cats;

		endif;
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
	public function tags( $config = array(), $return_html = TRUE )
	{
		//	Define defaults
		$_config				= new stdClass();
		$_config->limit			= isset( $config['limit'] ) ? (int) $config['limit'] : NULL;
		$_config->h_tag			= isset( $config['h_tag'] ) ? $config['h_tag'] : '5';
		$_config->h_class		= isset( $config['h_class'] ) ? $config['h_class'] : '';
		$_config->li_class		= isset( $config['li_class'] ) ? $config['li_class'] : '';
		$_config->title			= isset( $config['title'] ) ? $config['title'] : 'Tags';
		$_config->show_count	= isset( $config['show_count'] ) ? $config['show_count'] : TRUE;

		// --------------------------------------------------------------------------

		$this->db->select( 't.id,t.slug,t.label' );

		if ( $_config->show_count ) :

			$this->db->select( '(SELECT COUNT(DISTINCT post_id) FROM blog_post_tag WHERE tag_id = t.id) post_count' );

		endif;

		if ( ! is_null( $_config->limit ) && is_numeric( $_config->limit ) ) :

			$this->db->limit( $_config->limit );

		endif;

		$this->db->order_by( 't.label' );
		$_tags = $this->db->get( 'blog_tag t' )->result();

		// --------------------------------------------------------------------------

		//	Render HTML?
		if ( $return_html ) :

			$_out = '<h' . $_config->h_tag . ' class="' . $_config->h_class . '">' . $_config->title . '</h' . $_config->h_tag . '>';
			$_out .= '<ul>';

			foreach ( $_tags AS $tag ) :

				$_out .= '<li class="' . $_config->li_class . '">';

				$_count = $_config->show_count ? ' (' . $tag->post_count . ')' : '';
				$_out .= '&rsaquo; ' . anchor( $this->data['blog_url'] . 'tag/' . $tag->slug, $tag->label ) . $_count;
				$_out .= '</li>';

			endforeach;

			$_out .= '</ul>';

			return $_out;

		else :

			return $_cats;

		endif;
	}
}

/* End of file blog_widget_model.php */
/* Location: ./application/models/blog_widget_model.php */