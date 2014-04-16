<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin: Blog
* Description:	Blog Manager
*
*/

//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Blog extends NAILS_Admin_Controller
{

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access static
	 * @param none
	 * @return void
	 **/
	static function announce()
	{
		if ( ! module_is_enabled( 'blog' ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$d = new stdClass();

		// --------------------------------------------------------------------------

		//	Configurations
		$d->name = 'Blog';	//	Display name.

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs				= array();
		$d->funcs['index']		= 'Manage Posts';		//	Sub-nav function.
		$d->funcs['create']		= 'Create New Post';	//	Sub-nav function.

		get_instance()->load->helper( 'blog_helper' );

		if ( blog_setting( 'categories_enabled' ) ) :

			$d->funcs['manage/categories']	= 'Manage Categories';	//	Sub-nav function.

		endif;

		if ( blog_setting( 'tags_enabled' ) ) :

			$d->funcs['manage/tags']		= 'Manage Tags';		//	Sub-nav function.

		endif;

		// --------------------------------------------------------------------------

		//	Only announce the controller if the user has permission to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->load->model( 'blog/blog_model',			'blog' );
		$this->load->model( 'blog/blog_post_model',		'post' );
		$this->load->model( 'blog/blog_category_model',	'category' );
		$this->load->model( 'blog/blog_tag_model',		'tag' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Post overview
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function index()
	{
		//	Set method info
		$this->data['page']->title = 'Manage Posts';

		// --------------------------------------------------------------------------

		//	Define the $_data variable, this'll be passed to the get_all() and count_all() methods
		$_data = array( 'where' => array(), 'sort' => array() );

		// --------------------------------------------------------------------------

		//	Set useful vars
		$_page			= $this->input->get( 'page' )		? $this->input->get( 'page' )		: 0;
		$_per_page		= $this->input->get( 'per_page' )	? $this->input->get( 'per_page' )	: 50;
		$_sort_on		= $this->input->get( 'sort_on' )	? $this->input->get( 'sort_on' )	: 'bp.published';
		$_sort_order	= $this->input->get( 'order' )		? $this->input->get( 'order' )		: 'desc';
		$_search		= $this->input->get( 'search' )		? $this->input->get( 'search' )		: '';

		//	Set sort variables for view and for $_data
		$this->data['sort_on']		= $_data['sort']['column']	= $_sort_on;
		$this->data['sort_order']	= $_data['sort']['order']	= $_sort_order;
		$this->data['search']		= $_data['search']			= $_search;

		//	Define and populate the pagination object
		$this->data['pagination']				= new stdClass();
		$this->data['pagination']->page			= $_page;
		$this->data['pagination']->per_page		= $_per_page;
		$this->data['pagination']->total_rows	= $this->post->count_all( $_data );

		//	Fetch all the items for this page
		$this->data['posts'] = $this->post->get_all( $_page, $_per_page, $_data );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/blog/index',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Create a new post
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function create()
	{
		//	Set method info
		$this->data['page']->title = 'Create New Post';

		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :

			$this->load->library( 'form_validation' );

			$this->form_validation->set_rules( 'is_published',		'',	'xss_clean' );
			$this->form_validation->set_rules( 'published',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'title',				'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'excerpt',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'image_id',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'body',				'',	'required' );
			$this->form_validation->set_rules( 'seo_description',	'',	'xss_clean' );
			$this->form_validation->set_rules( 'seo_keywords',		'',	'xss_clean' );

			$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

			if ( $this->form_validation->run() ) :

				//	Prepare data
				$_data						= array();
				$_data['title']				= $this->input->post( 'title' );
				$_data['excerpt']			= $this->input->post( 'excerpt' );
				$_data['image_id']			= $this->input->post( 'image_id' );
				$_data['body']				= $this->input->post( 'body' );
				$_data['seo_description']	= $this->input->post( 'seo_description' );
				$_data['seo_keywords']		= $this->input->post( 'seo_keywords' );
				$_data['is_published']		= (bool) $this->input->post( 'is_published' );
				$_data['published']			= $this->input->post( 'published' );
				$_data['associations']		= $this->input->post( 'associations' );
				$_data['gallery']			= $this->input->post( 'gallery' );

				if ( blog_setting( 'categories_enabled' ) ) :

					$_data['categories'] = $this->input->post( 'categories' );

				endif;

				if ( blog_setting( 'tags_enabled' ) ) :

					$_data['tags'] = $this->input->post( 'tags' );

				endif;

				$_post_id = $this->post->create( $_data );

				if ( $_post_id ) :

					//	Update admin changelog
					_ADMIN_CHANGE_ADD( 'created', 'a', 'blog post', $_post_id, $_data['title'], 'admin/blog/edit/' . $_post_id );

					// --------------------------------------------------------------------------

					//	Set flashdata and redirect
					$this->session->set_flashdata( 'success', '<strong>Success!</strong> Post was created.' );
					redirect( 'admin/blog' );
					return;

				else :

					$this->data['error'] = lang( 'fv_there_were_errors' );

				endif;

			else :

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load Categories and Tags
		if ( blog_setting( 'categories_enabled' ) ) :

			$this->data['categories']	= $this->category->get_all();

		endif;

		if ( blog_setting( 'tags_enabled' ) ) :

			$this->data['tags']			= $this->tag->get_all();

		endif;

		// --------------------------------------------------------------------------

		//	Load associations
		$this->data['associations'] = $this->blog->get_associations();

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->library( 'ckeditor' );
		$this->asset->load( 'jquery.serializeobject.min.js', TRUE );
		$this->asset->load( 'nails.admin.blog.create_edit.js', TRUE );
		$this->asset->load( 'jquery.uploadify.min.js', TRUE );
		$this->asset->load( 'mustache.min.js', TRUE );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/blog/edit',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Edit an existing post
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function edit()
	{
		//	Fetch and check post
		$_post_id = $this->uri->segment( 4 );

		$this->data['post'] = $this->post->get_by_id( $_post_id );

		if ( ! $this->data['post'] ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I could\'t find a post by that ID.' );
			redirect( 'admin/blog' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Set method info
		$this->data['page']->title = 'Edit Post &rsaquo; ' . $this->data['post']->title;

		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :

			$this->load->library( 'form_validation' );

			$this->form_validation->set_rules( 'is_published',		'',	'xss_clean' );
			$this->form_validation->set_rules( 'published',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'title',				'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'excerpt',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'image_id',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'body',				'',	'required' );
			$this->form_validation->set_rules( 'seo_description',	'',	'xss_clean' );
			$this->form_validation->set_rules( 'seo_keywords',		'',	'xss_clean' );

			$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

			if ( $this->form_validation->run() ) :

				//	Prepare data
				$_data						= array();
				$_data['title']				= $this->input->post( 'title' );
				$_data['excerpt']			= $this->input->post( 'excerpt' );
				$_data['image_id']			= $this->input->post( 'image_id' );
				$_data['body']				= $this->input->post( 'body' );
				$_data['seo_description']	= $this->input->post( 'seo_description' );
				$_data['seo_keywords']		= $this->input->post( 'seo_keywords' );
				$_data['is_published']		= (bool) $this->input->post( 'is_published' );
				$_data['published']			= $this->input->post( 'published' );
				$_data['associations']		= $this->input->post( 'associations' );
				$_data['gallery']			= $this->input->post( 'gallery' );

				if ( blog_setting( 'categories_enabled' ) ) :

					$_data['categories'] = $this->input->post( 'categories' );

				endif;

				if ( blog_setting( 'tags_enabled' ) ) :

					$_data['tags'] = $this->input->post( 'tags' );

				endif;

				if ( $this->post->update( $_post_id, $_data ) ) :

					//	Update admin change log
					foreach ( $_data AS $field => $value ) :

						if ( isset( $this->data['post']->$field ) ) :

							switch( $field ) :

								case 'associations' :

									//	TODO: changelog associations

								break;

								case 'categories' :

									$_old_categories = array();
									$_new_categories = array();

									foreach( $this->data['post']->$field AS $v ) :

										$_old_categories[] = $v->label;

									endforeach;

									if ( is_array( $value ) ) :

										foreach( $value AS $v ) :

											$_temp = $this->category->get_by_id( $v );

											if ( $_temp ) :

												$_new_categories[] = $_temp->label;

											endif;

										endforeach;

									endif;

									asort( $_old_categories );
									asort( $_new_categories );

									$_old_categories = implode( ',', $_old_categories );
									$_new_categories = implode( ',', $_new_categories );

									_ADMIN_CHANGE_ADD( 'updated', 'a', 'blog post', $_post_id,  $_data['title'], 'admin/accounts/edit/' . $_post_id, $field, $_old_categories, $_new_categories, FALSE );

								break;

								case 'tags' :

									$_old_tags = array();
									$_new_tags = array();

									foreach( $this->data['post']->$field AS $v ) :

										$_old_tags[] = $v->label;

									endforeach;

									if ( is_array( $value ) ) :

										foreach( $value AS $v ) :

											$_temp = $this->tag->get_by_id( $v );

											if ( $_temp ) :

												$_new_tags[] = $_temp->label;

											endif;

										endforeach;

									endif;

									asort( $_old_tags );
									asort( $_new_tags );

									$_old_tags = implode( ',', $_old_tags );
									$_new_tags = implode( ',', $_new_tags );

									_ADMIN_CHANGE_ADD( 'updated', 'a', 'blog post', $_post_id,  $_data['title'], 'admin/accounts/edit/' . $_post_id, $field, $_old_tags, $_new_tags, FALSE );

								break;

								default :

										_ADMIN_CHANGE_ADD( 'updated', 'a', 'blog post', $_post_id,  $_data['title'], 'admin/accounts/edit/' . $_post_id, $field, $this->data['post']->$field, $value, FALSE );

								break;

							endswitch;

						endif;

					endforeach;

					// --------------------------------------------------------------------------

					$this->session->set_flashdata( 'success', '<strong>Success!</strong> Post was updated.' );
					redirect( 'admin/blog' );
					return;

				else :

					$this->data['error'] = lang( 'fv_there_were_errors' );

				endif;

			else :

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load Categories and Tags
		if ( blog_setting( 'categories_enabled' ) ) :

			$this->data['categories']	= $this->category->get_all();

		endif;

		if ( blog_setting( 'tags_enabled' ) ) :

			$this->data['tags']			= $this->tag->get_all();

		endif;

		// --------------------------------------------------------------------------

		//	Load associations
		$this->data['associations'] = $this->blog->get_associations( $this->data['post']->id );

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->library( 'ckeditor' );
		$this->asset->load( 'jquery.serializeobject.min.js', TRUE );
		$this->asset->load( 'nails.admin.blog.create_edit.js', TRUE );
		$this->asset->load( 'jquery.uploadify.min.js', TRUE );
		$this->asset->load( 'mustache.min.js', TRUE );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/blog/edit',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	public function delete()
	{
		//	Fetch and check post
		$_post_id = $this->uri->segment( 4 );

		$_post = $this->post->get_by_id( $_post_id );

		if ( ! $_post ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I could\'t find a post by that ID.' );
			redirect( 'admin/blog' );
			return;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->post->delete( $_post_id ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Post was deleted successfully. ' . anchor( 'admin/blog/restore/' . $_post_id, 'Undo?' ) );

			//	Update admin changelog
			_ADMIN_CHANGE_ADD( 'deleted', 'a', 'blog post', $_post_id, $_post->title );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I failed to delete that post.' );

		endif;

		redirect( 'admin/blog' );
		return;

	}


	// --------------------------------------------------------------------------


	public function restore()
	{
		//	Fetch and check post
		$_post_id = $this->uri->segment( 4 );

		// --------------------------------------------------------------------------

		if ( $this->post->restore( $_post_id ) ) :

			$_post = $this->post->get_by_id( $_post_id );

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Post was restored successfully. ' );

			//	Update admin changelog
			_ADMIN_CHANGE_ADD( 'restored', 'a', 'blog post', $_post_id, $_post->title, 'admin/blog/edit/' . $_post_id );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I failed to restore that post.' );

		endif;

		redirect( 'admin/blog' );
		return;

	}


	// --------------------------------------------------------------------------


	public function manage()
	{
		switch ( $this->uri->segment( 4 ) ) :

			case 'categories'	: $this->_manage_categories();	break;
			case 'tags'			: $this->_manage_tags();		break;
			default				: show_404();					break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _manage_categories()
	{
		$this->data['page']->title = 'Category Manager';

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		// --------------------------------------------------------------------------

		//	Handle POST
		if ( $this->input->post() ) :

			if ( $this->category->create( $this->input->post( 'category' ) ) ) :

				$this->data['success'] = '<strong>Success!</strong> Category created.';

			else :

				$this->data['error'] = '<strong>Sorry,</strong> could not create category.';

			endif;

		endif;

		// --------------------------------------------------------------------------

		$this->data['categories']	= $this->category->get_all( TRUE );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/blog/manage/category',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	public function delete_category()
	{
		if ( $this->category->delete( $this->uri->segment( 4 ) ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Category Deleted.' );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> there was a problem deleting that category.' );

		endif;

		// --------------------------------------------------------------------------

		$_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=true' : '';

		redirect( 'admin/blog/manage/categories' . $_fancybox );
	}


	// --------------------------------------------------------------------------


	protected function _manage_tags()
	{
		$this->data['page']->title = 'Tag Manager';

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		// --------------------------------------------------------------------------

		//	Handle POST
		if ( $this->input->post() ) :

			if ( $this->tag->create( $this->input->post( 'tag' ) ) ) :

				$this->data['success'] = '<strong>Success!</strong> Tag created.';

			else :

				$this->data['error'] = '<strong>Sorry,</strong> could not create tag.';

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load Tags
		$this->data['tags']	= $this->tag->get_all( TRUE );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/blog/manage/tag',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	public function delete_tag()
	{
		if ( $this->tag->delete( $this->uri->segment( 4 ) ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Tag Deleted.' );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> there was a problem deleting that tag.' );

		endif;

		// --------------------------------------------------------------------------

		$_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=true' : '';

		redirect( 'admin/blog/manage/tags' . $_fancybox );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * The following block of code makes it simple to extend one of the core admin
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG' ) ) :

	class Blog extends NAILS_Blog
	{
	}

endif;


/* End of file blog.php */
/* Location: ./modules/admin/controllers/blog.php */