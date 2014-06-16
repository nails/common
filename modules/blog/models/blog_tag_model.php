<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		blog_tag_model
 *
 * Description:	This model handles all things tag related
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Blog_tag_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table			= NAILS_DB_PREFIX . 'blog_tag';
		$this->_table_prefix	= 'bt';
	}


	// --------------------------------------------------------------------------


	public function get_all( $include_count = FALSE )
	{
		$_select	= array();
		$_select[]	= $this->_table_prefix . '.id';
		$_select[]	= $this->_table_prefix . '.slug';
		$_select[]	= $this->_table_prefix . '.label';

		$this->db->select( $_select );

		if ( $include_count ) :

			$this->db->select( '(SELECT COUNT(DISTINCT post_id) FROM ' . NAILS_DB_PREFIX . 'blog_post_tag WHERE tag_id = ' . $this->_table_prefix . '.id) post_count' );

		endif;

		$this->db->order_by( $this->_table_prefix . '.label' );
		$_tags = $this->db->get( $this->_table . ' ' . $this->_table_prefix )->result();

		foreach ( $_tags AS $tag ) :

			$this->_format_tag( $tag );

		endforeach;

		return $_tags;
	}


	// --------------------------------------------------------------------------


	public function get_by_id( $id, $include_count = FALSE  )
	{
		$this->db->where( 'id', $id );
		$_tag = $this->get_all( $include_count );

		if ( ! $_tag )
			return FALSE;

		return $_tag[0];
	}


	// --------------------------------------------------------------------------


	public function get_by_slug( $slug, $include_count = FALSE  )
	{
		$this->db->where( 'slug', $slug );
		$_tag = $this->get_all( $include_count );

		if ( ! $_tag )
			return FALSE;

		return $_tag[0];
	}


	// --------------------------------------------------------------------------


	public function create( $label )
	{
		$_data			= array();
		$_data['slug']	= $this->_generate_slug( $label );
		$_data['label']	= $label;

		return parent::create( $_data );
	}

	// --------------------------------------------------------------------------


	public function update( $id_slug, $label )
	{
		$_slug = $this->_generate_slug( $label );

		$this->db->set( 'slug', $_slug );
		$this->db->set( 'label', $_slug );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( $this->user_model->is_logged_in() ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		if ( is_numeric( $id_slug ) ) :

			$this->db->where( 'id', $id_slug );

		else :

			$this->db->where( 'slug', $id_slug );

		endif;

		$this->db->update( $this->_table );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function destroy( $id_slug )
	{
		if ( ! $id_slug ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		if ( is_numeric( $id_slug ) ) :

			$this->db->where( 'id', $id_slug );

		else :

			$this->db->where( 'slug', $id_slug );

		endif;

		$this->db->delete( $this->_table );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function delete( $id_slug )
	{
		return $this->destroy( $id_slug );
	}


	// --------------------------------------------------------------------------


	public function format_url( $slug )
	{
		return site_url( app_setting( 'url', 'blog' ) . 'tag/' . $slug );
	}


	// --------------------------------------------------------------------------


	protected function _format_tag( &$tag )
	{
		$tag->id	= (int) $tag->id;
		$tag->url	= $this->format_url( $tag->slug );

		if ( isset( $tag->post_count ) ) :

			$tag->post_count = (int) $tag->post_count;

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG_TAG_MODEL' ) ) :

	class Blog_tag_model extends NAILS_Blog_tag_model
	{
	}

endif;

/* End of file blog_tag_model.php */
/* Location: ./modules/blog/models/blog_tag_model.php */