<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		blog_category_model
 *
 * Description:	This model handles all things category related
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Blog_category_model extends NAILS_Model
{
	public function get_all( $include_count = FALSE )
	{
		$this->db->select( 'c.id,c.slug,c.label' );

		if ( $include_count ) :

			$this->db->select( '(SELECT COUNT(DISTINCT post_id) FROM blog_post_category WHERE category_id = c.id) post_count' );

		endif;

		$this->db->order_by( 'c.label' );
		$_categories = $this->db->get( NAILS_DB_PREFIX . 'blog_category c' )->result();

		foreach ( $_categories AS $category ) :

			$this->_format_category( $category );

		endforeach;

		return $_categories;
	}


	// --------------------------------------------------------------------------


	public function get_by_id( $id, $include_count = FALSE )
	{
		$this->db->where( 'c.id', $id );
		$_category = $this->get_all( $include_count );

		if ( ! $_category )
			return FALSE;

		return $_category[0];
	}


	// --------------------------------------------------------------------------


	public function get_by_slug( $slug, $include_count = FALSE )
	{
		$this->db->where( 'c.slug', $slug );
		$_category = $this->get_all( $include_count );

		if ( ! $_category )
			return FALSE;

		return $_category[0];
	}


	// --------------------------------------------------------------------------


	public function create( $label )
	{
		$_slug = $this->_generate_slug( $label, 'blog_category' );
		$this->db->set( 'slug', $_slug );
		$this->db->set( 'label', $label );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( $this->user->is_logged_in() ) :

			$this->db->set( 'created_by', active_user( 'id' ) );
			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		$this->db->insert( NAILS_DB_PREFIX . 'blog_category' );

		return (bool) $this->db->affected_rows();
	}

	// --------------------------------------------------------------------------


	public function update( $id_slug, $label )
	{
		$_slug = $this->_generate_slug( $label, 'blog_category' );
		$this->db->set( 'slug', $_slug );
		$this->db->set( 'label', $_slug );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( $this->user->is_logged_in() ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		if ( is_numeric( $id_slug ) ) :

			$this->db->where( 'id', $id_slug );

		else :

			$this->db->where( 'slug', $id_slug );

		endif;

		$this->db->update( NAILS_DB_PREFIX . 'blog_category' );

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

		$this->db->delete( NAILS_DB_PREFIX . 'blog_category' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function delete( $id_slug )
	{
		return $this->destroy( $id_slug );
	}


	// --------------------------------------------------------------------------


	protected function _format_category( &$category )
	{
		$category->id	= (int) $category->id;

		if ( isset( $category->post_count ) ) :

			$category->post_count	= (int) $category->post_count;

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
 * CodeIgniter  instanciate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG_CATEGORY_MODEL' ) ) :

	class Blog_category_model extends NAILS_Blog_category_model
	{
	}

endif;


/* End of file blog_category_model.php */
/* Location: ./application/models/blog_category_model.php */