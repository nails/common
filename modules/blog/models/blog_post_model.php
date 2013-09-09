<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		blog_post_model
 *
 * Description:	This model handles all interactions with blog posts on site.
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Blog_post_model extends NAILS_Model
{

	protected $_reserved;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Define reserved words (for slugs, basically just controller methods)
		$this->_reserved = array( 'index', 'single', 'category','tag', 'archive' );
	}


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

		if ( ! isset( $data['title'] ) || ! $data['title'] ) :

			$this->_set_error( 'Title missing' );
			return FALSE;

		endif;

		$_slug_prefix = array_search( $data['title'], $this->_reserved ) !== FALSE ? 'post-' : '';

		do
		{
			$_slug  = $_slug_prefix . url_title( $data['title'], 'dash', TRUE );
			$_slug .= $_counter > 0 ? '-' . $_counter : '';

			$_counter++;

			$this->db->where( 'slug', $_slug );

		} while( $this->db->count_all_results( 'blog_post' ) );

		// --------------------------------------------------------------------------

		//	Set data
		$this->db->set( 'slug',				$_slug );

		if ( array_key_exists( 'title', $data ) ) :				$this->db->set( 'title',			$data['title'] );			endif;
		if ( array_key_exists( 'excerpt', $data ) ) :			$this->db->set( 'excerpt',			trim( strip_tags( $data['excerpt'] ) ) );	endif;
		if ( array_key_exists( 'body', $data ) ) :				$this->db->set( 'body',				$data['body'] );			endif;
		if ( array_key_exists( 'seo_title', $data ) ) :			$this->db->set( 'seo_title',		$data['title'] );			endif;
		if ( array_key_exists( 'seo_description', $data ) ) :	$this->db->set( 'seo_description',	$data['seo_description'] );	endif;
		if ( array_key_exists( 'seo_keywords', $data ) ) :		$this->db->set( 'seo_keywords',		$data['seo_keywords'] );	endif;
		if ( array_key_exists( 'is_published', $data ) ) :		$this->db->set( 'is_published',		$data['is_published'] );	endif;

		//	Safety first!
		if ( array_key_exists( 'image_id', $data ) ) :

			$_image_id = (int) $data['image_id'];
			$_image_id = ! $_image_id ? NULL : $_image_id;

			$this->db->set( 'image_id', $_image_id );

		endif;

		$this->db->set( 'created',			'NOW()', FALSE );
		$this->db->set( 'modified',			'NOW()', FALSE );
		$this->db->set( 'created_by',		active_user( 'id' ) );
		$this->db->set( 'modified_by',		active_user( 'id' ) );

		if ( $data['is_published'] ) :

			$this->db->set( 'published',	'NOW()', FALSE );

		endif;

		$this->db->insert( 'blog_post' );

		if ( $this->db->affected_rows() ) :

			$_id = $this->db->insert_id();

			//	Add Categories and tags, if any
			if ( isset( $data['categories'] ) && $data['categories'] ) :

				$_data = array();

				foreach ( $data['categories'] AS $cat_id ) :

					$_data[] = array( 'post_id' => $_id, 'category_id' => $cat_id );

				endforeach;

				$this->db->insert_batch( 'blog_post_category', $_data );

			endif;

			if ( isset( $data['tags'] ) && $data['tags'] != FALSE ) :

				$_data = array();

				foreach ( $data['tags'] AS $tag_id ) :

					$_data[] = array( 'post_id' => $_id, 'tag_id' => $tag_id );

				endforeach;

				$this->db->insert_batch( 'blog_post_tag', $_data );

			endif;

			// --------------------------------------------------------------------------

			//	Add associations, if any
			if ( isset( $data['associations'] ) && $data['associations'] ) :

				//	Fetch association config
				$_association = $this->config->item( 'blog_post_associations' );

				foreach ( $data['associations'] AS $index => $assoc ) :

					if ( ! isset( $_association[$index] ) ) :

						continue;

					endif;

					$_data = array();

					foreach( $assoc AS $id ) :

						$_data[] = array( 'post_id' => $_id, 'associated_id' => $id );

					endforeach;

					if ( $_data ) :

						$this->db->insert_batch( $_association[$index]->target, $_data );

					endif;

				endforeach;

			endif;

			// --------------------------------------------------------------------------

			return $_id;

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

			if ( ! isset( $data['title'] ) || ! $data['title'] ) :

				$this->_set_error( 'Title missing' );
				return FALSE;

			endif;

			$_slug_prefix = array_search( $data['title'], $this->_reserved ) !== FALSE ? 'post-' : '';

			do
			{
				$_slug  = $_slug_prefix . url_title( $data['title'], 'dash', TRUE );
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
		if ( array_key_exists( 'title', $data ) ) :				$this->db->set( 'title',			$data['title'] );			endif;
		if ( array_key_exists( 'excerpt', $data ) ) :			$this->db->set( 'excerpt',			trim( strip_tags( $data['excerpt'] ) ) );	endif;
		if ( array_key_exists( 'body', $data ) ) :				$this->db->set( 'body',				$data['body'] );			endif;
		if ( array_key_exists( 'seo_title', $data ) ) :			$this->db->set( 'seo_title',		$data['title'] );			endif;
		if ( array_key_exists( 'seo_description', $data ) ) :	$this->db->set( 'seo_description',	$data['seo_description'] );	endif;
		if ( array_key_exists( 'seo_keywords', $data ) ) :		$this->db->set( 'seo_keywords',		$data['seo_keywords'] );	endif;
		if ( array_key_exists( 'is_published', $data ) ) :		$this->db->set( 'is_published',		$data['is_published'] );	endif;
		if ( array_key_exists( 'modified', $data ) ) :			$this->db->set( 'modified',			'NOW()', FALSE );			endif;

		//	Safety first!
		if ( array_key_exists( 'image_id', $data ) ) :

			$_image_id = (int) $data['image_id'];
			$_image_id = ! $_image_id ? NULL : $_image_id;

			$this->db->set( 'image_id', $_image_id );

		endif;

		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( active_user( 'id' ) ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		if ( $_slug ) :

			$this->db->set( 'slug', $_slug );

		endif;

		$this->db->where( 'id', $id );

		$this->db->update( 'blog_post' );

		// --------------------------------------------------------------------------

		//	Update/reset any categories/tags if any have been defined
		if ( isset( $data['categories'] ) ) :

			//	Delete all categories
			$this->db->where( 'post_id', $id );
			$this->db->delete( 'blog_post_category' );

			//	Recreate new ones
			if ( $data['categories'] ) :

				$_data = array();

				foreach ( $data['categories'] AS $cat_id ) :

					$_data[] = array( 'post_id' => $id, 'category_id' => $cat_id );

				endforeach;

				$this->db->insert_batch( 'blog_post_category', $_data );

			endif;

		endif;

		if ( isset( $data['tags'] ) ) :

			//	Delete all tags
			$this->db->where( 'post_id', $id );
			$this->db->delete( 'blog_post_tag' );

			//	Recreate new ones
			if ( $data['tags'] ) :

				$_data = array();

				foreach ( $data['tags'] AS $tag_id ) :

					$_data[] = array( 'post_id' => $id, 'tag_id' => $tag_id );

				endforeach;

				$this->db->insert_batch( 'blog_post_tag', $_data );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Add associations, if any
		if ( isset( $data['associations'] ) && $data['associations'] ) :

			//	Fetch association config
			$_association = $this->config->item( 'blog_post_associations' );


			foreach ( $data['associations'] AS $index => $assoc ) :

				if ( ! isset( $_association[$index] ) ) :

					continue;

				endif;

				//	Clear old associations
				$this->db->where( 'post_id', $id );
				$this->db->delete( $_association[$index]->target );

				//	Add new ones
				$_data = array();

				foreach( $assoc AS $assoc_id ) :

					$_data[] = array( 'post_id' => $id, 'associated_id' => $assoc_id );

				endforeach;

				if ( $_data ) :

					$this->db->insert_batch( $_association[$index]->target, $_data );

				endif;

			endforeach;

		endif;

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

		if ( $this->db->affected_rows() ) :

			return TRUE;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Recover an existing object
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
	public function get_all( $only_published = TRUE, $include_body = FALSE, $include_gallery = FALSE, $exclude_deleted = TRUE )
	{
		$this->db->select( 'bp.id, bp.slug, bp.title, bp.image_id, bp.gallery_type, bp.gallery_position, bp.excerpt, bp.seo_title' );
		$this->db->select( 'bp.seo_description, bp.seo_keywords, bp.is_published, bp.is_deleted, bp.created, bp.created_by, bp.modified, bp.modified_by, bp.published' );

		if ( $include_body ) :

			$this->db->select( 'bp.body' );

		endif;

		$this->db->select( 'u.first_name, u.last_name, u.email, u.profile_img, u.gender' );

		$this->db->join( 'user u', 'bp.modified_by = u.id', 'LEFT' );

		if ( $only_published ) :

			$this->db->where( 'bp.is_published', TRUE );

		endif;

		if ( $exclude_deleted ) :

			$this->db->where( 'bp.is_deleted', FALSE );

		endif;

		$this->db->order_by( 'published', 'DESC' );

		$_posts = $this->db->get( 'blog_post bp' )->result();

		foreach ( $_posts AS $post ) :

			$this->_format_post_object( $post );

			// --------------------------------------------------------------------------

			//	Fetch associated categories
			$this->db->select( 'c.id,c.slug,c.label' );
			$this->db->join( 'blog_category c', 'c.id = pc.category_id' );
			$this->db->where( 'pc.post_id', $post->id );
			$this->db->group_by( 'c.id' );
			$this->db->order_by( 'c.label' );
			$post->categories = $this->db->get( 'blog_post_category pc' )->result();

			//	Fetch associated tags
			$this->db->select( 't.id,t.slug,t.label' );
			$this->db->join( 'blog_tag t', 't.id = pt.tag_id' );
			$this->db->where( 'pt.post_id', $post->id );
			$this->db->group_by( 't.id' );
			$this->db->order_by( 't.label' );
			$post->tags = $this->db->get( 'blog_post_tag pt' )->result();

			// --------------------------------------------------------------------------

			//	Fetch associated images
			if ( $include_gallery ) :

				$this->db->where( 'post_id', $post->id );
				$this->db->order_by( 'order' );
				$post->gallery = $this->db->get( 'blog_post_image' )->result();

			else :

				$post->gallery = array();

			endif;

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
		$_result = $this->get_all( FALSE, TRUE, TRUE, FALSE );

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
		$_result = $this->get_all( FALSE, TRUE, TRUE, FALSE );

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


	public function get_archive( $year = NULL, $month = NULL )
	{
		if ( $year ) :

			$this->db->where( 'YEAR( bp.published ) = ', (int) $year );

		endif;

		// --------------------------------------------------------------------------

		if ( $month ) :

			$this->db->where( 'MONTH( bp.published ) = ', (int) $month );

		endif;

		// --------------------------------------------------------------------------

		return $this->get_all();
	}


	// --------------------------------------------------------------------------


	public function get_with_category( $id_slug, $only_published = TRUE, $include_body = FALSE, $exclude_deleted = TRUE )
	{
		$this->db->select( 'bp.id, bp.slug, bp.title, bp.image_id, bp.excerpt, bp.seo_title' );
		$this->db->select( 'bp.seo_description, bp.seo_keywords, bp.is_published, bp.is_deleted, bp.created, bp.created_by, bp.modified, bp.modified_by, bp.published' );

		if ( $include_body ) :

			$this->db->select( 'bp.body' );

		endif;

		$this->db->select( 'u.first_name, u.last_name, u.email, u.profile_img, u.gender' );

		$this->db->join( 'blog_post bp', 'bp.id = bc.post_id' );
		$this->db->join( 'user u', 'bp.modified_by = u.id', 'LEFT' );

		if ( $only_published ) :

			$this->db->where( 'bp.is_published', TRUE );

		endif;

		if ( $exclude_deleted ) :

			$this->db->where( 'bp.is_deleted', FALSE );

		endif;

		$this->db->order_by( 'published', 'DESC' );

		if ( is_numeric( $id_slug ) ) :

			$this->db->where( 'bc.category_id = ', $id_slug );

		else :

			$this->db->where( 'c.slug = ', $id_slug );
			$this->db->join( 'blog_category c', 'c.id = bc.category_id' );

		endif;

		$_posts = $this->db->get( 'blog_post_category bc' )->result();

		foreach ( $_posts AS $post ) :

			$this->_format_post_object( $post );

		endforeach;

		return $_posts;
	}


	// --------------------------------------------------------------------------


	public function get_with_tag( $id_slug, $only_published = TRUE, $include_body = FALSE, $exclude_deleted = TRUE )
	{
		$this->db->select( 'bp.id, bp.slug, bp.title, bp.image_id, bp.excerpt, bp.seo_title' );
		$this->db->select( 'bp.seo_description, bp.seo_keywords, bp.is_published, bp.is_deleted, bp.created, bp.created_by, bp.modified, bp.modified_by, bp.published' );

		if ( $include_body ) :

			$this->db->select( 'bp.body' );

		endif;

		$this->db->select( 'u.first_name, u.last_name, u.email, u.profile_img, u.gender' );

		$this->db->join( 'blog_post bp', 'bp.id = bt.post_id' );
		$this->db->join( 'user u', 'bp.modified_by = u.id', 'LEFT' );

		if ( $only_published ) :

			$this->db->where( 'bp.is_published', TRUE );

		endif;

		if ( $exclude_deleted ) :

			$this->db->where( 'bp.is_deleted', FALSE );

		endif;

		$this->db->order_by( 'published', 'DESC' );

		if ( is_numeric( $id_slug ) ) :

			$this->db->where( 'bt.tag_id = ', $id_slug );

		else :

			$this->db->where( 't.slug = ', $id_slug );
			$this->db->join( 'blog_tag t', 't.id = bt.tag_id' );

		endif;

		$_posts = $this->db->get( 'blog_post_tag bt' )->result();

		foreach ( $_posts AS $post ) :

			$this->_format_post_object( $post );

		endforeach;

		return $_posts;
	}


	// --------------------------------------------------------------------------


	protected function _format_post_object( &$post )
	{
		//	Type casting
		$post->id					= (int) $post->id;
		$post->is_published			= (bool) $post->is_published;
		$post->is_deleted			= (bool) $post->is_deleted;

		//	Generate URL
		$post->url					= site_url( blog_setting( 'blog_url' ) . $post->slug );

		//	Author
		$post->author				= new stdClass();
		$post->author->id			= (int) $post->modified_by;
		$post->author->first_name	= $post->first_name;
		$post->author->last_name	= $post->last_name;
		$post->author->email		= $post->email;
		$post->author->profile_img	= $post->profile_img;
		$post->author->gender		= $post->gender;

		unset( $post->modified_by );
		unset( $post->first_name );
		unset( $post->last_name );
		unset( $post->email );
		unset( $post->profile_img );
		unset( $post->gender );
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG_POST_MODEL' ) ) :

	class Blog_post_model extends NAILS_Blog_post_model
	{
	}

endif;

/* End of file blog_post_model.php */
/* Location: ./application/models/blog_post_model.php */