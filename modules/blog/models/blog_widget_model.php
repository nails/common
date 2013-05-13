<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		blog_widget_model
 *
 * Description:	This model handles all interactions with blog widgets on site.
 * 
 **/

class Blog_widget_model extends NAILS_Model
{
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

		$this->db->limit( $_config->limit );
		$_posts = $this->db->get( 'blog_post' )->result();

		// --------------------------------------------------------------------------

		//	Render HTML?
		if ( $return_html ) :

			$_out = '<h' . $_config->h_tag . ' class="' . $_config->h_class . '">' . $_config->title . '</h' . $_config->h_tag . '>';
			$_out .= '<ul>';

			foreach ( $_posts AS $post ) :

				$_out .= '<li class="' . $_config->li_class . '">';
				$_out .= anchor( 'blog/' . $post->slug, $post->title );

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
		return '';
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
		return '';
	}
}

/* End of file blog_widget_model.php */
/* Location: ./application/models/blog_widget_model.php */