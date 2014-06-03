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
	public function __construct()
	{
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
		$this->db->where( 'published <=', 'NOW()', FALSE );
		$this->db->where( 'is_deleted', FALSE );
		$this->db->limit( $_config->limit );
		$this->db->order_by( 'published', 'DESC' );
		$_posts = $this->db->get( NAILS_DB_PREFIX . 'blog_post' )->result();

		// --------------------------------------------------------------------------

		//	Any data?
		if ( ! $_posts ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Render HTML?
		if ( $return_html ) :

			$_out  = '<h' . $_config->h_tag . ' class="' . $_config->h_class . '">' . $_config->title . '</h' . $_config->h_tag . '>';
			$_out .= '<ul>';

			foreach ( $_posts AS $post ) :

				$_out .= '<li class="' . $_config->li_class . '">';
				$_out .= anchor( $this->data['blog_url'] . $post->slug, $post->title );

				if ( $_config->meta_show ) :

					$_out .= '<br />';
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
	 * Fetches the latest blog posts
	 *
	 * @access public
	 * @param array $config Changes to the default configs
	 * @param boolean $return_html Whether to return HTML or just the data
	 * @return mixed``
	 **/
	public function popular_posts( $config = array(), $return_html = TRUE )
	{
		//	Define defaults
		$_config				= new stdClass();
		$_config->limit			= isset( $config['limit'] ) ? (int) $config['limit'] : 5;
		$_config->h_tag			= isset( $config['h_tag'] ) ? $config['h_tag'] : '5';
		$_config->h_class		= isset( $config['h_class'] ) ? $config['h_class'] : '';
		$_config->li_class		= isset( $config['li_class'] ) ? $config['li_class'] : '';
		$_config->title			= isset( $config['title'] ) ? $config['title'] : 'Popular Posts';
		$_config->meta_show		= isset( $config['meta_show'] ) ? $config['meta_show'] : TRUE;
		$_config->meta_class	= isset( $config['meta_class'] ) ? $config['meta_class'] : '';

		// --------------------------------------------------------------------------

		$this->db->select( 'bp.id,bp.slug,bp.title,bp.published,COUNT(bph.id) hits' );

		$this->db->join( NAILS_DB_PREFIX . 'blog_post bp', 'bp.id = bph.post_id' );

		$this->db->where( 'bp.is_published', TRUE );
		$this->db->where( 'bp.published <=', 'NOW()', FALSE );
		$this->db->where( 'bp.is_deleted', FALSE );

		$this->db->group_by( 'bp.id' );
		$this->db->order_by( 'hits', 'DESC' );
		$this->db->order_by( 'bp.published', 'DESC' );
		$this->db->limit( $_config->limit );

		$_posts = $this->db->get( NAILS_DB_PREFIX . 'blog_post_hit bph' )->result();

		// --------------------------------------------------------------------------

		//	Any data?
		if ( ! $_posts ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Render HTML?
		if ( $return_html ) :

			$_out  = '<h' . $_config->h_tag . ' class="' . $_config->h_class . '">' . $_config->title . '</h' . $_config->h_tag . '>';
			$_out .= '<ul>';

			foreach ( $_posts AS $post ) :

				$_out .= '<li class="' . $_config->li_class . '">';
				$_out .= anchor( $this->data['blog_url'] . $post->slug, $post->title );

				if ( $_config->meta_show ) :

					$_out .= '<br />';
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

			$this->db->select( '(SELECT COUNT(DISTINCT bpc.post_id) FROM ' . NAILS_DB_PREFIX . 'blog_post_category bpc JOIN ' . NAILS_DB_PREFIX . 'blog_post bp ON bpc.post_id = bp.id WHERE bpc.category_id = c.id AND bp.is_published = 1 AND bp.is_deleted = 0 AND bp.published <= NOW()) post_count' );

		endif;

		if ( NULL !== $_config->limit && is_numeric( $_config->limit ) ) :

			$this->db->limit( $_config->limit );

		endif;

		$this->db->order_by( 'c.label' );
		$this->db->having( 'post_count > ', 0 );
		$_cats = $this->db->get( NAILS_DB_PREFIX . 'blog_category c' )->result();

		// --------------------------------------------------------------------------

		//	Any data?
		if ( ! $_cats ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Render HTML?
		if ( $return_html ) :

			$_out  = '<h' . $_config->h_tag . ' class="' . $_config->h_class . '">' . $_config->title . '</h' . $_config->h_tag . '>';
			$_out .= '<ul>';

			foreach ( $_cats AS $cat ) :

				$_out .= '<li class="' . $_config->li_class . '">';

				$_count = $_config->show_count ? ' (' . $cat->post_count . ')' : '';
				$_out .= anchor( $this->data['blog_url'] . 'category/' . $cat->slug, $cat->label ) . $_count;
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

			$this->db->select( '(SELECT COUNT(DISTINCT bpt.post_id) FROM ' . NAILS_DB_PREFIX . 'blog_post_tag bpt JOIN ' . NAILS_DB_PREFIX . 'blog_post bp ON bpt.post_id = bp.id WHERE tag_id = t.id AND bp.is_published = 1 AND bp.is_deleted = 0 AND bp.published <= NOW()) post_count' );

		endif;

		if ( NULL !== $_config->limit && is_numeric( $_config->limit ) ) :

			$this->db->limit( $_config->limit );

		endif;

		$this->db->order_by( 't.label' );
		$this->db->having( 'post_count > ', 0 );
		$_tags = $this->db->get( NAILS_DB_PREFIX . 'blog_tag t' )->result();

		// --------------------------------------------------------------------------

		//	Any data?
		if ( ! $_tags ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Render HTML?
		if ( $return_html ) :

			$_out  = '<h' . $_config->h_tag . ' class="' . $_config->h_class . '">' . $_config->title . '</h' . $_config->h_tag . '>';
			$_out .= '<ul>';

			foreach ( $_tags AS $tag ) :

				$_out .= '<li class="' . $_config->li_class . '">';

				$_count = $_config->show_count ? ' (' . $tag->post_count . ')' : '';
				$_out .= anchor( $this->data['blog_url'] . 'tag/' . $tag->slug, $tag->label . $_count );
				$_out .= '</li>';

			endforeach;

			$_out .= '</ul>';

			return $_out;

		else :

			return $_cats;

		endif;
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