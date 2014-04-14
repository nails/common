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

		$this->_table				= NAILS_DB_PREFIX . 'blog_post';
		$this->_table_prefix		= 'bp';	//	Hard-coded throughout model; take care when changing this
		$this->_table_label_column	= 'title';
		$this->_destructive_delete	= FALSE;

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

		// --------------------------------------------------------------------------

		//	Generate a slug
		$_prefix = array_search( $data['title'], $this->_reserved ) !== FALSE ? 'post-' : '';
		$this->db->set( 'slug', $this->_generate_slug( $data['title'], $_prefix ) );

		// --------------------------------------------------------------------------

		//	Set data
		if ( isset( $data['title'] ) ) :			$this->db->set( 'title',			$data['title'] );			endif;
		if ( isset( $data['body'] ) ) :				$this->db->set( 'body',				$data['body'] );			endif;
		if ( isset( $data['seo_title'] ) ) :		$this->db->set( 'seo_title',		$data['title'] );			endif;
		if ( isset( $data['seo_description'] ) ) :	$this->db->set( 'seo_description',	$data['seo_description'] );	endif;
		if ( isset( $data['seo_keywords'] ) ) :		$this->db->set( 'seo_keywords',		$data['seo_keywords'] );	endif;
		if ( isset( $data['is_published'] ) ) :		$this->db->set( 'is_published',		$data['is_published'] );	endif;

		//	Safety first!
		if ( array_key_exists( 'image_id', $data ) ) :

			$_image_id = (int) $data['image_id'];
			$_image_id = ! $_image_id ? NULL : $_image_id;

			$this->db->set( 'image_id', $_image_id );

		endif;

		//	Excerpt
		if ( ! empty( $data['excerpt'] ) ) :

			$this->db->set( 'excerpt', trim( strip_tags( $data['excerpt'] ) ) );

		elseif ( ! empty( $data['body'] ) ) :

			$this->db->set( 'excerpt', word_limiter( trim( strip_tags( $data['body'] ) ) ), 50 );

		endif;

		$this->db->set( 'created',			'NOW()', FALSE );
		$this->db->set( 'modified',			'NOW()', FALSE );
		$this->db->set( 'created_by',		active_user( 'id' ) );
		$this->db->set( 'modified_by',		active_user( 'id' ) );

		if ( $data['is_published'] ) :

			$this->db->set( 'published',	'NOW()', FALSE );

		endif;

		$this->db->insert( NAILS_DB_PREFIX . 'blog_post' );

		if ( $this->db->affected_rows() ) :

			$_id = $this->db->insert_id();

			//	Add Gallery items, if any
			if ( isset( $data['gallery'] ) && $data['gallery'] ) :

				$_data = array();

				foreach ( $data['gallery'] AS $order => $image_id ) :

					$_data[] = array( 'post_id' => $_id, 'image_id' => $image_id, 'order' => $order );

				endforeach;

				$this->db->insert_batch( NAILS_DB_PREFIX . 'blog_post_image', $_data );

			endif;

			// --------------------------------------------------------------------------

			//	Add Categories and tags, if any
			if ( isset( $data['categories'] ) && $data['categories'] ) :

				$_data = array();

				foreach ( $data['categories'] AS $cat_id ) :

					$_data[] = array( 'post_id' => $_id, 'category_id' => $cat_id );

				endforeach;

				$this->db->insert_batch( NAILS_DB_PREFIX . 'blog_post_category', $_data );

			endif;

			if ( isset( $data['tags'] ) && $data['tags'] != FALSE ) :

				$_data = array();

				foreach ( $data['tags'] AS $tag_id ) :

					$_data[] = array( 'post_id' => $_id, 'tag_id' => $tag_id );

				endforeach;

				$this->db->insert_batch( NAILS_DB_PREFIX . 'blog_post_tag', $_data );

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
		$_current = $this->db->get( NAILS_DB_PREFIX . 'blog_post' )->row();

		if ( ! $_current->is_published && $data['is_published'] ) :

			$_counter = 0;

			if ( ! isset( $data['title'] ) || ! $data['title'] ) :

				$this->_set_error( 'Title missing' );
				return FALSE;

			endif;

			//	Generate a slug
			$_prefix	= array_search( $data['title'], $this->_reserved ) !== FALSE ? 'post-' : '';
			$_slug		= $this->_generate_slug( $data['title'], $_prefix );

			// --------------------------------------------------------------------------

			//	Also update the published datetime
			$this->db->set( 'published',	'NOW()', FALSE );

		else :

			$_slug = FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Set data
		if ( isset( $data['title'] ) ) :			$this->db->set( 'title',			$data['title'] );			endif;
		if ( isset( $data['body'] ) ) :				$this->db->set( 'body',				$data['body'] );			endif;
		if ( isset( $data['seo_title'] ) ) :		$this->db->set( 'seo_title',		$data['title'] );			endif;
		if ( isset( $data['seo_description'] ) ) :	$this->db->set( 'seo_description',	$data['seo_description'] );	endif;
		if ( isset( $data['seo_keywords'] ) ) :		$this->db->set( 'seo_keywords',		$data['seo_keywords'] );	endif;
		if ( isset( $data['is_published'] ) ) :		$this->db->set( 'is_published',		$data['is_published'] );	endif;
		if ( isset( $data['is_deleted'] ) ) :		$this->db->set( 'is_deleted',		$data['is_deleted'] );		endif;

		//	Safety first!
		if ( array_key_exists( 'image_id', $data ) ) :

			$_image_id = (int) $data['image_id'];
			$_image_id = ! $_image_id ? NULL : $_image_id;

			$this->db->set( 'image_id', $_image_id );

		endif;

		//	Excerpt
		if ( ! empty( $data['excerpt'] ) ) :

			$this->db->set( 'excerpt', trim( strip_tags( $data['excerpt'] ) ) );

		elseif ( ! empty( $data['body'] ) ) :

			$this->db->set( 'excerpt', word_limiter( trim( strip_tags( $data['body'] ) ) ), 50 );

		endif;

		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( active_user( 'id' ) ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		if ( $_slug ) :

			$this->db->set( 'slug', $_slug );

		endif;

		$this->db->where( 'id', $id );

		$this->db->update( NAILS_DB_PREFIX . 'blog_post' );

		// --------------------------------------------------------------------------

		//	Update/reset the post gallery if it's been defined
		if ( isset( $data['gallery'] ) ) :

			//	Delete all categories
			$this->db->where( 'post_id', $id );
			$this->db->delete( NAILS_DB_PREFIX . 'blog_post_image' );

			//	Recreate new ones
			if ( $data['gallery'] ) :

				$_data = array();

				foreach ( $data['gallery'] AS $order => $image_id ) :

					$_data[] = array( 'post_id' => $id, 'image_id' => $image_id, 'order' => $order );

				endforeach;

				$this->db->insert_batch( NAILS_DB_PREFIX . 'blog_post_image', $_data );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Update/reset any categories/tags if any have been defined
		if ( isset( $data['categories'] ) ) :

			//	Delete all categories
			$this->db->where( 'post_id', $id );
			$this->db->delete( NAILS_DB_PREFIX . 'blog_post_category' );

			//	Recreate new ones
			if ( $data['categories'] ) :

				$_data = array();

				foreach ( $data['categories'] AS $cat_id ) :

					$_data[] = array( 'post_id' => $id, 'category_id' => $cat_id );

				endforeach;

				$this->db->insert_batch( NAILS_DB_PREFIX . 'blog_post_category', $_data );

			endif;

		endif;

		if ( isset( $data['tags'] ) ) :

			//	Delete all tags
			$this->db->where( 'post_id', $id );
			$this->db->delete( NAILS_DB_PREFIX . 'blog_post_tag' );

			//	Recreate new ones
			if ( $data['tags'] ) :

				$_data = array();

				foreach ( $data['tags'] AS $tag_id ) :

					$_data[] = array( 'post_id' => $id, 'tag_id' => $tag_id );

				endforeach;

				$this->db->insert_batch( NAILS_DB_PREFIX . 'blog_post_tag', $_data );

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
	 * Fetches all objects
	 *
	 * @access public
	 * @param int $page The page number of the results, if NULL then no pagination
	 * @param int $per_page How many items per page of paginated results
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @param bool $include_deleted If non-destructive delete is enabled then this flag allows you to include deleted items
	 * @param string $_caller Internal flag to pass to _getcount_common(), contains the calling method
	 * @return array
	 **/
	public function get_all( $page = NULL, $per_page = NULL, $data = NULL, $include_deleted = FALSE, $_caller = 'GET_ALL' )
	{
		$_posts = parent::get_all( $page, $per_page, $data, $include_deleted, 'GET_ALL' );

		foreach ( $_posts AS $post ) :

			//	Fetch associated categories
			if ( ! empty( $data['include_categories'] ) ) :

				$this->db->select( 'c.id,c.slug,c.label' );
				$this->db->join( NAILS_DB_PREFIX . 'blog_category c', 'c.id = pc.category_id' );
				$this->db->where( 'pc.post_id', $post->id );
				$this->db->group_by( 'c.id' );
				$this->db->order_by( 'c.label' );
				$post->categories = $this->db->get( NAILS_DB_PREFIX . 'blog_post_category pc' )->result();

			else :

				$post->categories = array();

			endif;

			// --------------------------------------------------------------------------

			//	Fetch associated tags
			if ( ! empty( $data['include_tags'] ) ) :

				//	Fetch associated tags
				$this->db->select( 't.id,t.slug,t.label' );
				$this->db->join( NAILS_DB_PREFIX . 'blog_tag t', 't.id = pt.tag_id' );
				$this->db->where( 'pt.post_id', $post->id );
				$this->db->group_by( 't.id' );
				$this->db->order_by( 't.label' );
				$post->tags = $this->db->get( NAILS_DB_PREFIX . 'blog_post_tag pt' )->result();

			else :

				$post->tags = array();

			endif;

			// --------------------------------------------------------------------------

			//	Fetch other associations
			$_associations	= $this->config->item( 'blog_post_associations' );

			if ( ! empty( $data['include_associations'] ) && $_associations ) :

				foreach( $_associations AS $index => $assoc ) :

					$post->associations[$index] = $assoc;

					//	Fetch the association data from the source, fail ungracefully - the dev should have this configured correctly.
					$this->db->select( 'src.' . $assoc->source->id . ' id, src.' . $assoc->source->label . ' label' );
					$this->db->join( $assoc->source->table . ' src', 'src.' . $assoc->source->id . '=target.associated_id', 'LEFT' );
					$this->db->where( 'target.post_id', $post->id );
					$post->associations[$index]->current = $this->db->get( $assoc->target . ' target')->result();

				endforeach;

			else :

				$post->associations = array();

			endif;

			// --------------------------------------------------------------------------

			//	Fetch associated images
			if ( ! empty( $data['include_gallery'] )  ) :

				$this->db->where( 'post_id', $post->id );
				$this->db->order_by( 'order' );
				$post->gallery = $this->db->get( NAILS_DB_PREFIX . 'blog_post_image' )->result();

			else :

				$post->gallery = array();

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		return $_posts;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's ID
	 *
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @return	stdClass
	 **/
	public function get_by_id( $id, $data = NULL )
	{
		$data = $this->_include_everything( $data );
		return parent::get_by_id( $id, $data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's slug
	 *
	 * @access public
	 * @param int $slug The slug of the object to fetch
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @return	stdClass
	 **/
	public function get_by_slug( $id, $data = NULL )
	{
		$data = $this->_include_everything( $data );
		return parent::get_by_slug( $id, $data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's id or slug
	 *
	 * Auto-detects whether to use the ID or slug as the selector when fetching
	 * an object. Note that this method uses is_numeric() to determine whether
	 * an ID or a slug has been passed, thus numeric slugs (which are against
	 * Nails style guidelines) will be interpreted incorrectly.
	 *
	 * @access public
	 * @param mixed $id_slug The ID or slug of the object to fetch
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @return stdClass
	 **/
	public function get_by_id_or_slug( $id, $data = NULL )
	{
		$data = $this->_include_everything( $data );
		return parent::get_by_id( $id, $data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Applies common conditionals
	 *
	 * This method applies the conditionals which are common across the get_*()
	 * methods and the count() method.
	 *
	 * @access public
	 * @param string $data Data passed from the calling method
	 * @param string $_caller The name of the calling method
	 * @return void
	 **/
	protected function _getcount_common( $data = NULL, $_caller = NULL )
	{
		parent::_getcount_common( $data, $_caller );

		// --------------------------------------------------------------------------

		$this->db->select( 'bp.id, bp.slug, bp.title, bp.image_id, bp.excerpt, bp.seo_title' );
		$this->db->select( 'bp.seo_description, bp.seo_keywords, bp.is_published, bp.is_deleted, bp.created, bp.created_by, bp.modified, bp.modified_by, bp.published' );

		$this->db->select( 'u.first_name, u.last_name, ue.email, u.profile_img, u.gender' );

		$this->db->join( NAILS_DB_PREFIX . 'user u', 'bp.modified_by = u.id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );

		// --------------------------------------------------------------------------

		if ( ! empty( $data['include_body'] ) ) :

			$this->db->select( 'bp.body' );

		endif;

		// --------------------------------------------------------------------------

		if ( ! empty( $data['search'] ) ) :

			$this->db->or_like( $this->_table_prefix . '.title', $data['search'] );
			$this->db->or_like( $this->_table_prefix . '.excerpt', $data['search'] );
			$this->db->or_like( $this->_table_prefix . '.body', $data['search'] );
			$this->db->or_like( $this->_table_prefix . '.seo_description', $data['search'] );
			$this->db->or_like( $this->_table_prefix . '.seo_keywords', $data['search'] );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Sets the data array to include everything
	 *
	 * This method is called by the get_by_*() methods and, if not already set,
	 * will alter the $data array so that all the include_* parameters are set.
	 *
	 * @access public
	 * @param string $data Data passed from the calling method
	 * @return void
	 **/
	protected function _include_everything( $data )
	{
		if ( NULL === $data ) :

			$data = array();

		endif;

		if ( ! isset( $data['include_body'] ) ) :

			$data['include_body'] = TRUE;

		endif;

		if ( ! isset( $data['include_categories'] ) ) :

			$data['include_categories'] = TRUE;

		endif;

		if ( ! isset( $data['include_tags'] ) ) :

			$data['include_tags'] = TRUE;

		endif;

		if ( ! isset( $data['include_associations'] ) ) :

			$data['include_associations'] = TRUE;

		endif;

		if ( ! isset( $data['include_gallery'] ) ) :

			$data['include_gallery'] = TRUE;

		endif;

		// --------------------------------------------------------------------------

		return $data;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches latest
	 *
	 * @access public
	 * @param int $limit The number of posts to return
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @param bool $include_deleted If non-destructive delete is enabled then this flag allows you to include deleted items
	 * @return array
	 **/
	public function get_latest( $limit = 9, $data = NULL, $include_deleted = FALSE )
	{
		$this->db->limit( $limit );
		$this->db->order_by( 'bp.published', 'DESC' );
		return $this->get_all( NULL, NULL, $data, $include_deleted, 'GET_LATEST' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches posts published within a certain year and/or month
	 *
	 * @access public
	 * @param int $year The year to restrict the search to
	 * @param int $month The month to restrict the search to
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @param bool $include_deleted If non-destructive delete is enabled then this flag allows you to include deleted items
	 * @return array
	 **/
	public function get_archive( $year = NULL, $month = NULL, $data = NULL, $include_deleted = FALSE )
	{
		if ( $year ) :

			$this->db->where( 'YEAR( bp.published ) = ', (int) $year );

		endif;

		// --------------------------------------------------------------------------

		if ( $month ) :

			$this->db->where( 'MONTH( bp.published ) = ', (int) $month );

		endif;

		// --------------------------------------------------------------------------

		return $this->get_all( NULL, NULL, $data, $include_deleted, 'GET_ARCHIVE' );
	}


	// --------------------------------------------------------------------------


	public function get_with_category( $id_slug, $only_published = TRUE, $include_body = FALSE, $exclude_deleted = TRUE )
	{
		dumpanddie('<srong>TODO</strong>: blog_post_model::get_with_category(); use the get_all() method to run this (query logic will need changed)');
		$this->db->select( 'bp.id, bp.slug, bp.title, bp.image_id, bp.excerpt, bp.seo_title' );
		$this->db->select( 'bp.seo_description, bp.seo_keywords, bp.is_published, bp.is_deleted, bp.created, bp.created_by, bp.modified, bp.modified_by, bp.published' );

		if ( $include_body ) :

			$this->db->select( 'bp.body' );

		endif;

		$this->db->select( 'u.first_name, u.last_name, ue.email, u.profile_img, u.gender' );

		$this->db->join( NAILS_DB_PREFIX . 'blog_post bp', 'bp.id = bc.post_id' );
		$this->db->join( NAILS_DB_PREFIX . 'user u', 'bp.modified_by = u.id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );

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
			$this->db->join( NAILS_DB_PREFIX . 'blog_category c', 'c.id = bc.category_id' );

		endif;

		$_posts = $this->db->get( NAILS_DB_PREFIX . 'blog_post_category bc' )->result();

		foreach ( $_posts AS $post ) :

			$this->_format_post_object( $post );

		endforeach;

		return $_posts;
	}


	// --------------------------------------------------------------------------


	public function get_with_tag( $id_slug, $only_published = TRUE, $include_body = FALSE, $exclude_deleted = TRUE )
	{
		dumpanddie('<srong>TODO</strong>: blog_post_model::get_with_tag(); use the get_all() method to run this (query logic will need changed)');
		$this->db->select( 'bp.id, bp.slug, bp.title, bp.image_id, bp.excerpt, bp.seo_title' );
		$this->db->select( 'bp.seo_description, bp.seo_keywords, bp.is_published, bp.is_deleted, bp.created, bp.created_by, bp.modified, bp.modified_by, bp.published' );

		if ( $include_body ) :

			$this->db->select( 'bp.body' );

		endif;

		$this->db->select( 'u.first_name, u.last_name, ue.email, u.profile_img, u.gender' );

		$this->db->join( NAILS_DB_PREFIX . 'blog_post bp', 'bp.id = bt.post_id' );
		$this->db->join( NAILS_DB_PREFIX . 'user u', 'bp.modified_by = u.id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );

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
			$this->db->join( NAILS_DB_PREFIX . 'blog_tag t', 't.id = bt.tag_id' );

		endif;

		$_posts = $this->db->get( NAILS_DB_PREFIX . 'blog_post_tag bt' )->result();

		foreach ( $_posts AS $post ) :

			$this->_format_post_object( $post );

		endforeach;

		return $_posts;
	}


	// --------------------------------------------------------------------------


	public function get_with_association( $association_index, $associated_id )
	{
		$this->config->load( 'blog', FALSE, TRUE );

		$_associations	= $this->config->item( 'blog_post_associations' );

		if ( ! isset( $_associations[$association_index] ) ) :

			return array();

		endif;

		$this->db->select( 'post_id' );
		$this->db->where( 'associated_id', $associated_id );
		$_posts = $this->db->get( $_associations[$association_index]->target )->result();

		$_ids = array();
		foreach ( $_posts AS $post ) :

			$_ids[] = $post->post_id;

		endforeach;

		$this->db->where_in( $this->_table_prefix . '.id', $_ids );
		return $this->get_all();

	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$post )
	{
		parent::_format_object( $post );

		// --------------------------------------------------------------------------

		//	Type casting
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG_POST_MODEL' ) ) :

	class Blog_post_model extends NAILS_Blog_post_model
	{
	}

endif;

/* End of file blog_post_model.php */
/* Location: ./application/models/blog_post_model.php */