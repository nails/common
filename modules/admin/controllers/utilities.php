<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin - Utilities
 *
 * Description:	-
 * 
 **/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

/**
 * OVERLOADING NAILS'S ADMIN MODULES
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/
 
class NAILS_Utilities extends NAILS_Admin_Controller
{
	
	
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
		
		//	Load the laguage file
		get_instance()->lang->load( 'admin_utilities', RENDER_LANG );
		
		// --------------------------------------------------------------------------
		
		//	Configurations
		$d->name = lang( 'utilities_module_name' );
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs					= array();
		$d->funcs['test_email']		= lang( 'utilities_nav_test_email' );
		$d->funcs['user_access']	= lang( 'utilities_nav_user_access' );
		$d->funcs['languages']		= lang( 'utilities_nav_languages' );

		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
	}
	
	// --------------------------------------------------------------------------
	

	/**
	 * FAQ edit
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function test_email()
	{
		//	Page Title
		$this->data['page']->title = lang ( 'utilities_test_email_title' );
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post() ) :
		
			//	Form validation and update
			$this->load->library( 'form_validation' );
			
			//	Define rules
			$this->form_validation->set_rules( 'recipient',	lang( 'utilities_test_email_field_name' ), 'xss_clean|required|valid_email' );
			
			//	Set Messages
			$this->form_validation->set_message( 'required',	lang( 'fv_required' ) );
			$this->form_validation->set_message( 'valid_email',	lang( 'fv_valid_email' ) );
			
			//	Execute
			if ( $this->form_validation->run() ) :
			
				//	Prepare date
				$_email				= new stdClass();
				$_email->to_email	= $this->input->post( 'recipient' );
				$_email->type		= 'test_email';
				
				//	Send the email
				$this->load->library( 'emailer' );
				
				if ( $this->emailer->send( $_email ) ) :
				
					$this->data['success'] = lang( 'utilities_test_email_success', array( $_email->to_email, date( 'Y-m-d H:i:s' ) ) );
					
				else:
				
					echo '<h1>' . lang( 'utilities_test_email_error' ) . '</h1>';
					echo $this->email->print_debugger();
					return;
				
				endif;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/utilities/send_test',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Manage user groups ACL's
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function user_access()
	{
		//	Page Title
		$this->data['page']->title = lang ( 'utilities_user_access_title' );
		
		// --------------------------------------------------------------------------
		
		$this->data['groups'] = $this->user->get_groups();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/utilities/user_access',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Edit a group
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function edit_group()
	{
		$_gid = $this->uri->segment( 4, NULL );
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post() ) :
		
			//	Load library
			$this->load->library( 'form_validation' );
			
			//	Define rules
			$this->form_validation->set_rules( 'display_name',			lang( 'utilities_edit_group_basic_field_label_display' ),		'xss_clean|required' );
			$this->form_validation->set_rules( 'name',					lang( 'utilities_edit_group_basic_field_label_name' ),			'xss_clean|required' );
			$this->form_validation->set_rules( 'description',			lang( 'utilities_edit_group_basic_field_label_description' ),	'xss_clean|required' );
			$this->form_validation->set_rules( 'default_homepage',		lang( 'utilities_edit_group_basic_field_label_homepage' ), 		'xss_clean|required' );
			$this->form_validation->set_rules( 'registration_redirect',	lang( 'utilities_edit_group_basic_field_label_registration' ), 	'xss_clean' );
			$this->form_validation->set_rules( 'acl[]',					lang( 'utilities_edit_group_permission_legend' ), 				'xss_clean' );
			$this->form_validation->set_rules( 'acl[superuser]',		lang( 'utilities_edit_group_permission_legend' ), 				'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin]',			lang( 'utilities_edit_group_permission_legend' ), 				'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin][]',			lang( 'utilities_edit_group_permission_legend' ), 				'xss_clean' );
			
			//	Set messages
			$this->form_validation->set_message( 'required', lang( 'fv_required' ) );
			
			if ( $this->form_validation->run() ) :
			
				$_data = array();
				$_data['display_name']			= $this->input->post( 'display_name' );
				$_data['name']					= url_title( $this->input->post( 'name' ), 'dash', TRUE );
				$_data['description']			= $this->input->post( 'description' );
				$_data['default_homepage']		= $this->input->post( 'default_homepage' );
				$_data['registration_redirect']	= $this->input->post( 'registration_redirect' );
				
				//	Parse ACL's
				$_acl = $this->input->post( 'acl' );
				
				if ( isset( $_acl['admin'] ) ) :
				
					//	Remove ACLs which have no enabled methods - pointless	
					$_acl['admin'] = array_filter( $_acl['admin'] );
				
				endif;
				
				$_data['acl']				= serialize( $_acl );
				
				$this->user->update_group( $_gid, $_data );
				
				$this->session->set_flashdata( 'success', '<strong>Huzzah!</strong> Group updated successfully!' );
				redirect( 'admin/utilities/user_access' );
				return;
				
			else :
			
				$this->data['error'] = validation_errors();
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->data['group'] = $this->user->get_group( $_gid );
		
		if ( ! $this->data['group'] ) :
		
			$this->session->set_flashdata( 'error', 'Group does not exist.' );
			redirect( 'admin/utilities/user_access' );
		
		endif;
		
		$this->data['admin_modules'] = $this->_loaded_modules;
		
		// --------------------------------------------------------------------------
		
		//	Page title
		$this->data['page']->title = lang( 'utilities_edit_group_title', $this->data['group']->display_name );
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/utilities/edit_group',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	public function languages()
	{
		//	Page Title
		$this->data['page']->title = lang ( 'utilities_languages_title' );
		
		// --------------------------------------------------------------------------
		
		$this->data['languages'] = $this->language_model->get_all();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',					$this->data );
		$this->load->view( 'admin/utilities/languages/index',	$this->data );
		$this->load->view( 'structure/footer',					$this->data );
	}


	// --------------------------------------------------------------------------


	public function mark_lang_supported()
	{
		$_id = $this->uri->segment( 4 );

		if ( $this->language_model->mark_supported( $_id ) ) :

			$this->session->set_flashdata( 'success', lang( 'utilities_languages_mark_supported_ok' ) );

		else :

			$this->session->set_flashdata( 'success', lang( 'utilities_languages_mark_supported_fail' ) );

		endif;

		redirect( 'admin/utilities/languages' );
	}


	// --------------------------------------------------------------------------


	public function mark_lang_unsupported()
	{
		$_id = $this->uri->segment( 4 );

		if ( $this->language_model->mark_unsupported( $_id ) ) :

			$this->session->set_flashdata( 'success', lang( 'utilities_languages_mark_unsupported_ok' ) );

		else :

			$this->session->set_flashdata( 'success', lang( 'utilities_languages_mark_unsupported_fail' ) );

		endif;

		redirect( 'admin/utilities/languages' );
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
 * CodeIgniter instanciates a class with the same name as the file, therefore
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_UTILITIES' ) ) :

	class Utilities extends NAILS_Utilities
	{
	}

endif;


/* End of file faq.php */
/* Location: ./application/modules/admin/controllers/faq.php */