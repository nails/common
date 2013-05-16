<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin : Blog
*
* Description:	Blog Manager
* 
*/

//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

/**
 * OVERLOADING NAILS'S ADMIN MODULES
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/
 
class NAILS_Blog extends Admin_Controller {

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access static
	 * @param none
	 * @return void
	 **/
	static function announce()
	{
		$d = new stdClass();
		
		// --------------------------------------------------------------------------
		
		//	Configurations
		$d->name				= 'Blog';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['index']				= 'Manage Posts';		//	Sub-nav function.
		$d->funcs['create']				= 'Create New Post';	//	Sub-nav function.
		
		get_instance()->load->helper( 'blog_helper' );

		if ( blog_setting( 'categories_enabled' ) ) :

			$d->funcs['manager_category']	= 'Manage Categories';	//	Sub-nav function.

		endif;

		if ( blog_setting( 'tags_enabled' ) ) :

			$d->funcs['manager_tag']		= 'Manage Tags';		//	Sub-nav function.

		endif;

		$d->funcs['settings']			= 'Blog Settings';		//	Sub-nav function.
		
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
		
		//	Fetch posts
		$this->data['posts'] = $this->post->get_all( FALSE, FALSE );
		
		// --------------------------------------------------------------------------
		
		//	Load assets
		$this->asset->load( 'nails.admin.blog.min.js', TRUE );
		
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
			
			$this->form_validation->set_rules( 'is_published',		'Is Published',		'xss_clean|required' );
			$this->form_validation->set_rules( 'title',				'Title',			'xss_clean|required' );
			$this->form_validation->set_rules( 'excerpt',			'Excerpt',			'xss_clean|required' );
			$this->form_validation->set_rules( 'image',				'Featured Image',	'xss_clean' );
			$this->form_validation->set_rules( 'body',				'Body',				'required' );
			$this->form_validation->set_rules( 'seo_description',	'SEO Description',	'xss_clean|required' );
			$this->form_validation->set_rules( 'seo_keywords',		'SEO Keywords',		'xss_clean|required' );
			
			$this->form_validation->set_message( 'required', lang( 'fv_required' ) );
			
			if ( $this->form_validation->run() ) :
			
				//	Prepare data
				$_data = array();
				$_data['title']				= $this->input->post( 'title' );
				$_data['excerpt']			= $this->input->post( 'excerpt' );
				$_data['image']				= $this->input->post( 'image' );
				$_data['body']				= $this->input->post( 'body' );
				$_data['seo_description']	= $this->input->post( 'seo_description' );
				$_data['seo_keywords']		= $this->input->post( 'seo_keywords' );
				$_data['is_published']		= $this->input->post( 'is_published' );

				if ( blog_setting( 'categories_enabled' ) ) :

					$_data['categories']	= $this->input->post( 'categories' );

				endif;

				if ( blog_setting( 'tags_enabled' ) ) :

					$_data['tags']			= $this->input->post( 'tags' );

				endif;
				
				if ( $this->post->create( $_data ) ) :
				
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

		//	Load assets
		$this->asset->library( 'ckeditor' );
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/blog/create',	$this->data );
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
			
			$this->form_validation->set_rules( 'is_published',		'Is Published',		'xss_clean|required' );
			$this->form_validation->set_rules( 'title',				'Title',			'xss_clean|required' );
			$this->form_validation->set_rules( 'excerpt',			'Excerpt',			'xss_clean|required' );
			$this->form_validation->set_rules( 'image',				'Featured Image',	'xss_clean' );
			$this->form_validation->set_rules( 'body',				'Body',				'required' );
			$this->form_validation->set_rules( 'seo_description',	'SEO Description',	'xss_clean|required' );
			$this->form_validation->set_rules( 'seo_keywords',		'SEO Keywords',		'xss_clean|required' );
			
			$this->form_validation->set_message( 'required', lang( 'fv_required' ) );
			
			if ( $this->form_validation->run() ) :
			
				//	Prepare data
				$_data						= array();
				$_data['title']				= $this->input->post( 'title' );
				$_data['excerpt']			= $this->input->post( 'excerpt' );
				$_data['image']				= $this->input->post( 'image' );
				$_data['body']				= $this->input->post( 'body' );
				$_data['seo_description']	= $this->input->post( 'seo_description' );
				$_data['seo_keywords']		= $this->input->post( 'seo_keywords' );
				$_data['is_published']		= $this->input->post( 'is_published' );

				if ( blog_setting( 'categories_enabled' ) ) :

					$_data['categories']	= $this->input->post( 'categories' );


				endif;

				if ( blog_setting( 'tags_enabled' ) ) :

					$_data['tags']			= $this->input->post( 'tags' );

				endif;
				
				if ( $this->post->update( $_post_id, $_data ) ) :
				
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

		//	Load assets
		$this->asset->library( 'ckeditor' );
		
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
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Post was deleted successfully. ' . anchor( 'admin/blog/recover/' . $_post_id, 'Undo?' ) );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I failed to delete that post.' );
		
		endif;
		
		redirect( 'admin/blog' );
		return;

	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function recover()
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
		
		if ( $this->post->recover( $_post_id ) ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Post was recovered successfully. ' );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I failed to recover that post.' );
		
		endif;
		
		redirect( 'admin/blog' );
		return;

	}


	// --------------------------------------------------------------------------


	public function manager_category()
	{
		$this->data['page']->title = 'Category Manager';

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		if ( $this->input->get( 'rebuild' ) ) :

			$this->data['rebuild'] = TRUE;

		else :

			$this->data['rebuild'] = FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Handle POST
		if ( $this->input->post() ) :

			if ( $this->category->create( $this->input->post( 'category' ) ) ) :

				$this->data['success'] = '<strong>Success!</strong> Category created.';
				$this->data['rebuild'] = TRUE;

			else :

				$this->data['error'] = '<strong>Sorry,</strong> could not create category.';

			endif;

		endif;

		// --------------------------------------------------------------------------

		$this->data['categories']	= $this->category->get_all( TRUE );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/blog/manager/category',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	public function delete_category()
	{
		if ( $this->category->delete( $this->uri->segment( 4 ) ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Category Deleted.' );
			$_rebuild = 1;

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> there was a problem deleting that category.' );
			$_rebuild = 0;

		endif;

		// --------------------------------------------------------------------------

		$_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=true&rebuild=' . $_rebuild : '';

		redirect( 'admin/blog/manager_category' . $_fancybox );
	}


	// --------------------------------------------------------------------------


	public function manager_tag()
	{
		$this->data['page']->title = 'Tag Manager';

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		if ( $this->input->get( 'rebuild' ) ) :

			$this->data['rebuild'] = TRUE;

		else :

			$this->data['rebuild'] = FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Handle POST
		if ( $this->input->post() ) :

			if ( $this->tag->create( $this->input->post( 'tag' ) ) ) :

				$this->data['success'] = '<strong>Success!</strong> Tag created.';
				$this->data['rebuild'] = TRUE;

			else :

				$this->data['error'] = '<strong>Sorry,</strong> could not create tag.';

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load Tags
		$this->data['tags']	= $this->tag->get_all( TRUE );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/blog/manager/tag',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	public function delete_tag()
	{
		if ( $this->tag->delete( $this->uri->segment( 4 ) ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Tag Deleted.' );
			$_rebuild = 1;

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> there was a problem deleting that tag.' );
			$_rebuild = 0;

		endif;

		// --------------------------------------------------------------------------

		$_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=true&rebuild=' . $_rebuild : '';

		redirect( 'admin/blog/manager_tag' . $_fancybox );
	}


	// --------------------------------------------------------------------------


	/**
	 * Configure the blog
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function settings()
	{
		//	Set method info
		$this->data['page']->title = 'Blog Settings';

		// --------------------------------------------------------------------------

		//	Load models
		$this->load->model( 'blog/blog_model', 'blog' );
		
		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :
		
			switch ( $this->input->post( 'update' ) ) :

				case 'settings' :

					$this->_settings_update_settings();

				break;

				case 'sidebar' :

					$this->_settings_update_sidebar();

				break;

				// --------------------------------------------------------------------------

				default :

					$this->data['error'] = '<strong>Sorry,</strong> I can\'t determine what type of update you are trying to perform.';

				break;

			endswitch;
		
		endif;

		// --------------------------------------------------------------------------

		//	Get data
		$this->data['settings'] = $this->blog->settings();
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/blog/settings',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	private function _settings_update_settings()
	{
		//	Prepare update
		$_settings							= array();
		$_settings['blog_url']				= $this->input->post( 'blog_url' );
		$_settings['categories_enabled']	= (bool) $this->input->post( 'categories_enabled' );
		$_settings['tags_enabled']			= (bool) $this->input->post( 'tags_enabled' );

		// --------------------------------------------------------------------------

		//	Sanitize blog url
		$_settings['blog_url'] .= substr( $_settings['blog_url'], -1 ) != '/' ? '/' : '';

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->blog->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Blog settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}


	// --------------------------------------------------------------------------


	private function _settings_update_sidebar()
	{
		//	Prepare update
		$_settings						= array();
		$_settings['sidebar_enabled']	= (bool) $this->input->post( 'sidebar_enabled' );
		$_settings['sidebar_position']	= $this->input->post( 'sidebar_position' );

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->blog->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Sidebar settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S ADMIN MODULES
 * 
 * The following block of code makes it simple to extend one of the core admin
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
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
/* Location: ./application/modules/admin/controllers/blog.php */