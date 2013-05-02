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
		$d->funcs['index']		= 'Manage Posts';					//	Sub-nav function.
		$d->funcs['create']		= 'Create New Post';					//	Sub-nav function.
		
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
		
		$this->load->model( 'blog/blog_post_model', 'post' );
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
				$_data = array();
				$_data['title']				= $this->input->post( 'title' );
				$_data['excerpt']			= $this->input->post( 'excerpt' );
				$_data['image']				= $this->input->post( 'image' );
				$_data['body']				= $this->input->post( 'body' );
				$_data['seo_description']	= $this->input->post( 'seo_description' );
				$_data['seo_keywords']		= $this->input->post( 'seo_keywords' );
				$_data['is_published']		= $this->input->post( 'is_published' );
				
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
		
		//	Load assets
		$this->asset->library( 'ckeditor' );
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/blog/edit',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
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