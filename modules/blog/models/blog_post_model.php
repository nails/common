<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		blog_post_model
 *
 * Description:	This model handles all interactions with blog posts on site.
 * 
 **/

class Blog_post_model extends NAILS_Model
{
	/**
	 * Creates a new object
	 * 
	 * @access public
	 * @param array $data The data to create the object with
	 * @return mixed
	 **/
	public function create( $data = array() )
	{
		//	Prepare slug
		$_counter = 0;
		do
		{
			$_slug  = url_title( $data['title'], 'dash', TRUE );
			$_slug .= $_counter > 0 ? '-' . $_counter : '';
			
			$_counter++;
			
			$this->db->where( 'slug', $_slug );
			
		} while( $this->db->count_all_results( 'blog_post' ) );
		
		// --------------------------------------------------------------------------
		
		//	Set data
		$this->db->set( 'user_id',			active_user( 'id' ) );
		$this->db->set( 'slug',				$_slug );
		$this->db->set( 'title',			$data['title'] );
		$this->db->set( 'excerpt',			trim( strip_tags( $data['excerpt'] ) ) );
		$this->db->set( 'image',			$data['image'] );
		$this->db->set( 'body',				$data['body'] );
		$this->db->set( 'seo_title',		$data['title'] );
		$this->db->set( 'seo_description',	$data['seo_description'] );
		$this->db->set( 'seo_keywords',		$data['seo_keywords'] );
		$this->db->set( 'is_published',		$data['is_published'] );
		$this->db->set( 'created',			'NOW()', FALSE );
		$this->db->set( 'modified',			'NOW()', FALSE );
		$this->db->set( 'created_by',		active_user( 'id' ) );
		
		if ( $data['is_published'] ) :
		
			$this->db->set( 'published',	'NOW()', FALSE );
		
		endif;
		
		$this->db->insert( 'blog_post' );
		
		if ( $this->db->affected_rows() ) :
		
			return $this->db->insert_id();
		
		else :
		
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Updates an existing object
	 * 
	 * @access public
	 * @param int $id The ID of the object to update
	 * @param array $data The data to update the object with
	 * @return bool
	 **/
	public function update( $id, $data = array() )
	{
		//	Prepare slug; the slug is regenrated if the blog post is in a transitioning state
		//	to published. We don't want to be changing slugs of published posts but while
		//	it's a draft we can do whatever (even if it was previously published, made a draft
		//	then republished).
		
		$this->db->select( 'is_published' );
		$this->db->where( 'id', $id );
		$_current = $this->db->get( 'blog_post' )->row();
		
		if ( ! $_current->is_published && $data['is_published'] ) :
		
			$_counter = 0;
			do
			{
				$_slug  = url_title( $data['title'], 'dash', TRUE );
				$_slug .= $_counter > 0 ? '-' . $_counter : '';
				
				$_counter++;
				
				$this->db->where( 'id !=', $id );
				$this->db->where( 'slug', $_slug );
				
			} while( $this->db->count_all_results( 'blog_post' ) );
			
			// --------------------------------------------------------------------------
			
			//	Also update the published datetime
			$this->db->set( 'published',	'NOW()', FALSE );
		
		else :
		
			$_slug = FALSE;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set data
		$this->db->set( 'user_id',			active_user( 'id' ) );
		
		if ( isset( $data['title'] ) ) :			$this->db->set( 'title',			$data['title'] );			endif;
		if ( isset( $data['excerpt'] ) ) :			$this->db->set( 'excerpt',			trim( strip_tags( $data['excerpt'] ) ) );	endif;
		if ( isset( $data['image'] ) ) :			$this->db->set( 'image',			$data['image'] );			endif;
		if ( isset( $data['body'] ) ) :				$this->db->set( 'body',				$data['body'] );			endif;
		if ( isset( $data['seo_title'] ) ) :		$this->db->set( 'seo_title',		$data['title'] );			endif;
		if ( isset( $data['seo_description'] ) ) :	$this->db->set( 'seo_description',	$data['seo_description'] );	endif;
		if ( isset( $data['seo_keywords'] ) ) :		$this->db->set( 'seo_keywords',		$data['seo_keywords'] );	endif;
		if ( isset( $data['is_published'] ) ) :		$this->db->set( 'is_published',		$data['is_published'] );	endif;
		if ( isset( $data['modified'] ) ) :			$this->db->set( 'modified',			'NOW()', FALSE );			endif;
		
		$this->db->set( 'modified', 'NOW()', FALSE );
		
		if ( active_user( 'id' ) ) :
		
			$this->db->set( 'modified_by', active_user( 'id' ) );
		
		endif;
		
		if ( $_slug ) :
		
			$this->db->set( 'slug',			$_slug );
		
		endif;
		
		$this->db->where( 'id', $id );
		
		$this->db->update( 'blog_post' );
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Deletes an existing object
	 * 
	 * @access public
	 * @param int $id The ID of the object to delete
	 * @return bool
	 **/
	public function delete( $id )
	{
		$this->db->set( 'is_deleted', TRUE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->where( 'id', $id );
		$this->db->update( 'blog_post' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Undeleted an existing object
	 * 
	 * @access public
	 * @param int $id The ID of the object to recover
	 * @return bool
	 **/
	public function recover( $id )
	{
		$this->db->set( 'is_deleted', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->where( 'id', $id );
		$this->db->update( 'blog_post' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches all objects
	 * 
	 * @access public
	 * @param bool $include_related Whether to include related content in the results or not
	 * @param bool $only_published Whether to include drafts in the results or not
	 * @return array
	 **/
	public function get_all( $only_published = TRUE, $include_body = FALSE, $exclude_deleted = TRUE )
	{
		$this->db->select( 'bp.id, bp.user_id, bp.slug, bp.title, bp.image, bp.excerpt, bp.seo_title' );
		$this->db->select( 'bp.seo_description, bp.seo_keywords, bp.is_published, bp.is_deleted, bp.created, bp.created_by, bp.modified, bp.published' );
		
		if ( $include_body ) :
		
			$this->db->select( 'bp.body' );
		
		endif;
		
		$this->db->select( 'um.first_name, um.last_name, u.email, um.profile_img, um.gender' );
		
		$this->db->join( 'user u', 'bp.user_id = u.id', 'LEFT' );
		$this->db->join( 'user_meta um', 'bp.user_id = um.user_id', 'LEFT' );
		
		if ( $only_published ) :
		
			$this->db->where( 'bp.is_published', TRUE );
		
		endif;
		
		if ( $exclude_deleted ) :
		
			$this->db->where( 'bp.is_deleted', FALSE );
			
		endif;
		
		$this->db->order_by( 'modified', 'DESC' );
		
		$_posts = $this->db->get( 'blog_post bp' )->result();
		
		foreach ( $_posts AS $post ) :
		
			$this->_format_post_object( $post );
		
		endforeach;
		
		return $_posts;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch all objects as a flat array
	 * 
	 * @access public
	 * @param bool $only_published Whether to include drafts in the results or not
	 * @return array
	 **/
	public function get_all_flat( $only_published = TRUE )
	{
		$this->db->select( 'bp.id, bp.title' );
		
		if ( $only_published ) :
		
			$this->db->where( 'bp.is_published', TRUE );
		
		endif;
		
		$_posts	= $this->db->get( 'blog_post bp' )->result();
		$_out		= array();
		
		foreach ( $_posts AS $post ) :
		
			$_out[$post->id] = $post->title;
		
		endforeach;
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's ID
	 * 
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @return stdClass
	 **/
	public function get_by_id( $id )
	{
		$this->db->where( 'bp.id', $id );
		$_result = $this->get_all( FALSE, TRUE, FALSE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's slug
	 * 
	 * @access public
	 * @param string $slug The slug of the object to fetch
	 * @return stdClass
	 **/
	public function get_by_slug( $slug )
	{
		$this->db->where( 'bp.slug', $slug );
		$_result = $this->get_all( FALSE, TRUE, FALSE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}


	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches latest
	 * 
	 * @access public
	 * @return array
	 **/
	public function get_latest( $limit = 9 )
	{
		$this->db->limit( $limit );
		$this->db->order_by( 'bp.created', 'DESC' );		
		return $this->get_all();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _format_post_object( &$post )
	{
		//	Type casting
		$post->id					= (int) $post->id;
		$post->is_published			= (bool) $post->is_published;
		$post->is_deleted			= (bool) $post->is_deleted;
		
		//	Generate URL
		$post->url					= site_url( 'blog/' . $post->slug );
		
		//	Author
		$post->author				= new stdClass();
		$post->author->id			= (int) $post->user_id;
		$post->author->first_name	= $post->first_name;
		$post->author->last_name	= $post->last_name;
		$post->author->email		= $post->email;
		$post->author->profile_img	= $post->profile_img;
		$post->author->gender		= $post->gender;
		
		unset( $post->user_id );
		unset( $post->first_name );
		unset( $post->last_name );
		unset( $post->email );
		unset( $post->profile_img );
		unset( $post->gender );
	}
}

/* End of file blog_post_model.php */
/* Location: ./application/models/blog_post_model.php */