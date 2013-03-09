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
 
class NAILS_Utilities extends Admin_Controller {
	
	
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
		$d->funcs['test_email']		= lang( 'utilities_nav_test_email' );
		$d->funcs['user_access']	= lang( 'utilities_nav_user_access' );

		
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
		
			$this->load->library( 'form_validation' );
			
			$this->form_validation->set_rules( 'display_name',			'Display Name',		'xss_clean|required' );
			$this->form_validation->set_rules( 'name',					'Slug',				'xss_clean|required' );
			$this->form_validation->set_rules( 'description',			'Description',		'xss_clean|required' );
			$this->form_validation->set_rules( 'default_homepage',		'Default Homepage', 'xss_clean|required' );
			$this->form_validation->set_rules( 'acl[]',					'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[superuser]',		'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin]',			'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin][]',			'Permissions', 		'xss_clean' );
			
			if ( $this->form_validation->run() ) :
			
				$_data = array();
				$_data['display_name']		= $this->input->post( 'display_name' );
				$_data['name']				= $this->input->post( 'name' );
				$_data['description']		= $this->input->post( 'description' );
				$_data['default_homepage']	= $this->input->post( 'default_homepage' );
				
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