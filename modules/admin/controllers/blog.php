<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Blog
*
* Docs:			-
*
* Created:		30/03/2011
* Modified:		11/01/2012
*
* Description:	Controller for handling blog posts
* 
* TODO: This controller is a bit dated; this and the model could be refactored a little more; but if it aint broke...
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
	 * Announces this module's details to anyone who asks.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function announce()
	{
		$d = new stdClass();
		
		// --------------------------------------------------------------------------
		
		//	Configurations
		$d->name				= 'Blog';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['index']	= 'Posts Overview';			//	Sub-nav function.
		$d->funcs['add']	= 'New Post';		//	Sub-nav function.

		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Blog overview
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		//	Method details and vars
		$this->data['page']->admin_m = 'index';
		
		// --------------------------------------------------------------------------
		
		//	Load extra helper
		$this->load->helper( 'text' );
		
		// --------------------------------------------------------------------------
		
		//	Get data
		$this->data['order_col']	= ( $this->uri->segment( 4 ) !== FALSE ) ? $this->uri->segment( 4 ) : 'modified';
		$this->data['order_dir']	= ( $this->uri->segment( 5 ) !== FALSE ) ? $this->uri->segment( 5 ) : 'desc';
		$this->data['page']->search	= ( $this->input->get( 'search' ) !== FALSE ) ? $this->input->get( 'search' ) : FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Pagination
		$this->data['pagination']->per_page		= 25;
		$this->data['pagination']->page			= $this->uri->segment( 6, 0 );
		$this->data['pagination']->total		= $this->blog_model->count_posts( $this->data['page']->search );
		
		$this->data['pagination']->num_pages	= ceil( $this->data['pagination']->total / $this->data['pagination']->per_page );
		$offset = $this->data['pagination']->page * $this->data['pagination']->per_page;
		
		// --------------------------------------------------------------------------
		
		//	Get the accounts
		$this->data['posts'] = $this->blog_model->get_posts( $this->data['order_col'], $this->data['order_dir'], $this->data['pagination']->per_page, $offset, $this->data['page']->search );
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/overview',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Add a new blog post
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	 public function add()
	 {
		//	Method details and vars
		$this->data['page']->admin_m = 'index';
		
		// --------------------------------------------------------------------------
		
		//	Load Tiny MCE
		$this->load->library( 'tinymce' );
		$this->tinymce->load( 'textarea[name=post_body]' );
		
		// --------------------------------------------------------------------------
		
		//	Get the module name for blog
		$route_search = array_search( 'blog', $this->router->routes );
		if ( $route_search !== FALSE ) :
			
			$this->data['blog_module']	= $route_search;
			
		else :
		
			$this->data['blog_module'] = 'blog';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Deal with save.
		if ( $this->input->post( 'save' ) ) :
		
			
			//	Form validation
			$this->load->library( 'form_validation' );
			
			//	Define rules
			$this->form_validation->set_rules( 'title',				'Title',			'xss_clean|utf8_encode|required' );
			$this->form_validation->set_rules( 'url_slug',			'Slug',				'xss_clean|utf8_encode|required|good_for_url|callback_unique_for_date' );
			$this->form_validation->set_rules( 'post_body',			'Body',				'required|utf8_encode' );
			$this->form_validation->set_rules( 'seo_title',			'SEO Title',		'xss_clean|max_length[100]' );
			$this->form_validation->set_rules( 'seo_description',	'SEO Description',	'xss_clean|max_length[250]' );
			$this->form_validation->set_rules( 'seo_keywords',		'SEO Keywords',		'xss_clean|max_length[250]' );
			
			//	Reset default error messages
			if ( $this->form_validation->run( $this ) ) :
			
				//	Prep vars
				$data = array();
				
				//	Deal with file upload
				$this->load->library( 'upload' );
				
				//	Check the upload (but only if there is a file to upload)
				if ( $_FILES['featured_img']['size'] > 0 ) :
				
					$config['upload_path']		= CDN_PATH . 'blog/featured/';
					$config['allowed_types']	= 'jpg|gif|png';
					$config['encrypt_name']		= TRUE;
					
					$this->upload->initialize( $config );
			
					if ( ! $this->upload->do_upload( 'featured_img' ) ) :
					
						$this->data['fileerror'] = $this->upload->display_errors();
						$this->load->view( 'structure/header',	$this->data );
						$this->load->view( 'blog/add',			$this->data );
						$this->load->view( 'structure/footer',	$this->data );
						return;
					
					else :
					
						$file = $this->upload->data();
						$data['featured_img']	= $file['file_name'];
						
					endif;
				
				endif;
				
				//	Prep data
				$data['title']			= $this->input->post( 'title' );
				$data['slug']			= $this->input->post( 'url_slug' );
				$data['body']			= special_chars( $this->input->post( 'post_body' ) );
				$data['author_id']		= active_user( 'id' );
				$data['seo_title']		= $this->input->post( 'seo_title' );
				$data['seo_description']= $this->input->post( 'seo_description' );
				$data['seo_keywords']	= $this->input->post( 'seo_keywords' );
				
				//	Insertion... dirty... but what type?
				if ( $this->input->post( 'submit' ) == lang( 'blog_create_options_publish' ) ) :
				
					//	Publishing
					//	Do all publishing related things here then forward if no errors
					$data['status'] = 1;
					
					$id = $this->blog_model->create_post( $data );
					
					if ( $id === FALSE ) :
					
						$this->data['error'] = lang( 'blog_created_error_inserting' );
						
					else:
					
						$this->session->set_flashdata( 'success', lang( 'blog_created_ok' ) );
						redirect( 'admin/blog' );
						return;
						
					endif;
				
				else:
				
					//	Saving as a draft
					//	Save as a draft and forward to the editing form (set flashdata)
					$data['status'] = 0;
					
					$id = $this->blog_model->create_post( $data );
					
					if ( $id === FALSE ) :
					
						$this->data['error'] = lang( 'blog_error_savedraft' );
						
					else:

						$this->session->set_flashdata( 'success', lang( 'blog_draft_ok' ) );
						redirect( 'admin/blog/edit/'.$id );
						return;
						
					endif;
					
				endif;
					
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/add',			$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	 }
	 
	 
	 // --------------------------------------------------------------------------
	 
	 
	/**
	 * Edit a post
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function edit()
	{
		$this->data['page']->admin_m = "edit";
		
		// --------------------------------------------------------------------------
		
		$this->load->helper( 'date' );
		
		// --------------------------------------------------------------------------
		
		//	Gather data
		$this->data['editor'] = $this->blog_model->get_post( (int) $this->uri->segment( 4 ), TRUE );
		
		if ( ! $this->data['editor'] ) :
		
			$this->session->set_flashdata( 'error', lang( 'blog_unknown_post_id' ) );
			redirect( 'admin/blog' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load Tiny MCE
		$this->load->library( 'tinymce' );
		$this->tinymce->load( 'textarea[name=post_body]' );
		
		//	Load inline assets for publish date
		$_style = '<style>.publish_date label{ display:block;} .publish_date select{width:auto !important;}</style>';
		$this->asset->inline( $_style );
		
		// --------------------------------------------------------------------------
		
		//	Get the module name for blog
		$route_search = array_search( 'blog', $this->router->routes );
		if ( $route_search !== FALSE ) :
			
			$this->data['blog_module']	= $route_search;
			
		else :
		
			$this->data['blog_module'] = 'blog';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		
		//	Are we trying to save?
		if ( $this->input->post( 'save' ) !== FALSE ) :
		
			//	Get basic info about this page for validation
			$p = $this->blog_model->get_post( (int) $this->input->post( 'id' ), FALSE );
			
			//	Form validation
			$this->load->library( 'form_validation' );
			
			//	Define rules
			$this->form_validation->set_rules( 'title',				lang( 'blog_create_entertitle_title' ),			'xss_clean|required|utf8_encode' );
			$this->form_validation->set_rules( 'url_slug',			lang( 'blog_create_urlslug_title' ),			'xss_clean|required|utf8_encode|good_for_url|callback_unique_for_date' );
			$this->form_validation->set_rules( 'post_body',			lang( 'blog_create_pagebody_title' ),			'required|utf8_encode' );
			$this->form_validation->set_rules( 'seo_title',			lang( 'blog_create_seo_title_label' ),			'xss_clean|max_length[100]' );
			$this->form_validation->set_rules( 'seo_description',	lang( 'blog_create_seo_description_label' ),	'xss_clean|max_length[250]' );
			$this->form_validation->set_rules( 'seo_keywords',		lang( 'blog_create_seo_keywords_label' ),		'xss_clean|max_length[250]' );
			
			if ( $this->form_validation->run( $this ) ) :
				
				//	Prep vars
				$data = array();
				
				//	Deal with file upload
				$this->load->library( 'upload' );
				
				//	Check the upload (but only if there is a file to upload)
				if ( $_FILES['featured_img']['size'] > 0 ) :
				
					$config['upload_path']		= CDN_PATH . 'blog/featured/';
					$config['allowed_types']	= 'jpg|gif|png';
					$config['encrypt_name']		= TRUE;
					
					$this->upload->initialize( $config );
			
					if ( ! $this->upload->do_upload( 'featured_img' ) ) :
					
						$this->data['fileerror'] = $this->upload->display_errors();
						$this->load->view( 'structure/header',	$this->data );
						$this->load->view( 'blog/edit',			$this->data );
						$this->load->view( 'structure/footer',	$this->data );
						return;
					
					else :
					
						$file = $this->upload->data();
						$data['featured_img']	= $file['file_name'];
						
						//	If there was an old image, delete it
						if ( file_exists( CDN_PATH . 'blog/featured/' . $p->featured_img ) )
							@unlink( CDN_PATH . 'blog/featured/' . $p->featured_img );
						
					endif;
				
				endif;
				
				//	Prep data
				$data['title']			= $this->input->post( 'title' );
				$data['slug']			= $this->input->post( 'url_slug' );
				$data['author_id']		= active_user( 'id' );
				$data['seo_title']		= $this->input->post( 'seo_title' );
				$data['seo_description']= $this->input->post( 'seo_description' );
				$data['seo_keywords']	= $this->input->post( 'seo_keywords' );
				$data['body']			= special_chars( $this->input->post( 'post_body' ) );
				
				//	Insertion... dirty... but what type?
				if ( $this->input->post( 'submit' ) == lang( 'blog_create_options_publishchanges' ) ) :
					
					//	Publishing
					//	Do all publishing related things here then forward if no errors
					$id = $this->input->post( 'id' );
					$data['status'] = 1;
					
					//	Prep publish date
					$data['published']		=	$this->input->post( 'publish_year' ) .
												$this->input->post( 'publish_month' ) .
												$this->input->post( 'publish_day' ) .
												$this->input->post( 'publish_hour' ) .
												$this->input->post( 'publish_minute' ) .
												$this->input->post( 'publish_second' );
					
					if ( $this->blog_model->update_post( $id, $data ) == FALSE ) :
					
						$this->data['error'] = sprintf( lang( 'blog_edit_publish_error' ), $this->input->post( 'title' ) );
						
					else:
					
						$title = ( $this->input->post( 'title' ) !== FALSE ) ? $this->input->post( 'title' ) : $p->title; 
						$this->session->set_flashdata( 'success', sprintf( lang( 'blog_edit_publish_ok' ), $title ) );
						redirect( 'admin/blog' );
						return;
						
					endif;
				
				else:
					
					//	Save as a draft and reload editing form
					$id = $this->input->post( 'id' );
					$data['status'] = 0;
					
					if ( $this->blog_model->update_post ($id, $data ) == FALSE ) :
					
						$this->data['error'] = lang( 'blog_error_savedraft' );
						
					else:
					
						$this->data['success'] = lang( 'blog_draft_ok' );
						
					endif;
					
				endif;
				
			endif;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/edit',			$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Delete a 
	 post
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function delete()
	{
		//	Method details
		$this->data['page']->admin_m = "delete";
		
		//	Prep IDs...
		$_posts = $this->blog_model->prep_ids( 'admin/blog' );
				
		//	Decide what to do
		if ( $this->uri->segment( 5 ) == 'confirm' ) :	
			
			//	Check we aint doing anything stupid.
			$ok_ids = array();
			foreach ( $_posts AS $_post_id ) :
				
				$p = $this->blog_model->get_post( (int)$_post_id, FALSE );
				if ( ! $p ) :
				
					if ( count( $_posts ) > 1 ) :
						$this->session->set_flashdata( 'error', lang( 'blog_unknown_post_id_multiple' ) );
					else :
						$this->session->set_flashdata( 'error', lang( 'blog_unknown_post_id' ) );
					endif;
					redirect( 'admin/blog' );
				
				else :
					
					// Let's do this thing
					$ok_ids[] = $p->id;
					
				endif;
				
			endforeach;
			
			//	If it's just one page get it's details (for displaying in success message)
			if ( count( $ok_ids ) == 1 ) :
				$p = $this->blog_model->get_post( (int)$ok_ids[0], FALSE );
			endif;
			
			//	Delete all our pages
			$fail	= array();
			$pass	= array();
			$total	= count( $ok_ids );
			
			foreach ( $ok_ids AS $id ) :
			
				if ( $this->blog_model->delete_post( $id ) ) :
					$pass[] = $id;
				else :
					$fail[] = $id;
				endif;
				
			endforeach;
			
			//	Redirect to overview
			if ( count( $pass ) == $total ) :
				
				//	All pages were deleted
				if ( $total == 1 ) :
					$this->session->set_flashdata( 'success', sprintf( lang( 'blog_delete_ok' ), $p->title ) );
				else :
					$this->session->set_flashdata( 'success', sprintf( lang( 'blog_delete_ok_multiple_all' ) ) );
				endif;
				
			elseif ( count( $fail ) == $total ) :
			
				//	All pages failed to delete
				if ( $total == 1 ) :
					$this->session->set_flashdata( 'error', sprintf( lang( 'blog_delete_fail' ) ) );
				else :
					$this->session->set_flashdata( 'error', sprintf( lang( 'blog_delete_fail_multiple_all' ) ) );
				endif;
				
			else :
			
				//	Some deleted, some didn't, set appropriate messages
				$fail = implode( ', ', $fail );
				$pass = implode( ', ', $pass );
				$this->session->set_flashdata( 'success',	sprintf( lang( 'blog_delete_ok_multiple_some' ),	$pass ) );
				$this->session->set_flashdata( 'error',		sprintf( lang( 'blog_delete_fail_multiple_some' ),	$fail ) );
				
			endif;
	
			//	All done? Send user on their way
			redirect( 'admin/blog' );
		
		else :
			
			$error = false;
			foreach( $_posts AS $_post ) :
			
				//	Get this user's details
				$p = $this->blog_model->get_post( (int)$_post['id'], FALSE );
				
				//	Basic validation
				if ( ! $p ) :
					if ( count( $users ) > 1 ) :
						$this->session->set_flashdata( 'error', lang( 'unknown_page_id_multuiple' ) );
					else:
						$this->session->set_flashdata( 'error', lang( 'unknown_page_id' ) );
					endif;
					redirect( 'admin/pages' );
				else :
					$this->data['posts'][] = $p;
				endif;
			endforeach;

		endif;
		
		
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'blog/delete',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Callback function for form validation
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function unique_for_date( $str )
	{
		//	Prep date
		if ( $this->input->post( 'publish_year' ) ) :
		
			$date = $this->input->post( 'publish_year' ) . '-' . $this->input->post( 'publish_month' ) . '-' . $this->input->post( 'publish_day' );
			
		else :
		
			$date = date( 'Y-m-d' );
		
		endif;
		
		
		$this->db->like( 'published', $date, 'after' );
		$this->db->where( 'slug', $str );
		$q = $this->db->get( 'blog' );
		
		if ( $q->num_rows() ) :
		
			//	If the original slug is supplied, and it is the same as the new one and it's date is the same as before then that's ok.
			if ( $str == $this->input->post( 'slug_orig' ) && $date == $this->input->post( 'publish_orig' ) )
				return TRUE;
			
			$this->form_validation->set_message('unique_for_date', lang( 'blog_slug_not_unique_for_date' ) );
			return FALSE;
		
		else :
		
			return TRUE;
		
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