<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		blog_category_model
 *
 * Description:	This model handles all things category related
 * 
 **/

class Blog_category_model extends NAILS_Model
{
	public function get_all( $include_count = FALSE )
	{
		$this->db->select( 'c.id,c.slug,c.label' );

		if ( $include_count ) :

			$this->db->select( '(SELECT COUNT(DISTINCT post_id) FROM blog_post_category WHERE category_id = c.id) post_count' );

		endif;

		$this->db->order_by( 'c.label' );
		$_categories = $this->db->get( 'blog_category c' )->result();
		
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
		$_slug = $this->_generate_slug( $label );
		$this->db->set( 'slug', $_slug );
		$this->db->set( 'label', $label );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( $this->user->is_logged_in() ) :

			$this->db->set( 'created_by', active_user( 'id' ) );
			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		$this->db->insert( 'blog_category' );

		return (bool) $this->db->affected_rows();
	}

	// --------------------------------------------------------------------------


	public function update( $id_slug, $label )
	{
		$_slug = $this->_generate_slug( $label );
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

		$this->db->update( 'blog_category' );

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

		$this->db->delete( 'blog_category' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function delete( $id_slug )
	{
		return $this->destroy( $id_slug );
	}


	// --------------------------------------------------------------------------


	private function _format_category( &$category )
	{
		$category->id	= (int) $category->id;

		if ( isset( $category->post_count ) ) :

			$category->post_count	= (int) $category->post_count;

		endif;
	}


	// --------------------------------------------------------------------------

	private function _generate_slug( $label )
	{
		$_counter = 0;
		
		do
		{
			$_slug = url_title( $label, 'dash', TRUE );

			if ( $_counter ) :

				$_slug_test = $_slug . '-' . $_counter;

			else :

				$_slug_test = $_slug;

			endif;

			$this->db->where( 'slug', $_slug_test );
			$_counter++;

		} while( $this->db->count_all_results( 'blog_category' ) );

		return $_slug_test;
	}
}

/* End of file blog_category_model.php */
/* Location: ./application/models/blog_category_model.php */