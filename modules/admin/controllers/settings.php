<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin - Settings
*
* Docs:			-
*
* Created:		16/03/2012
* Modified:		16/03/2012
*
* Description:	-
* 
*/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

class Settings extends Admin_Controller {
	
	
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
		//	Configurations
		$d->priority				= 19;					//	Module's order in nav (unique).
		$d->name					= 'Settings';			//	Display name.
		$d->funcs['user_access']	= 'Manage User Access';	//	Sub-nav function.
		$d->announce_to				= array();				//	Which groups can access this module.
		$d->searchable				= FALSE;				//	Is module searchable?
		
		//	Dynamic
		$d->base_url				= basename( __FILE__, '.php' );	//	For link generation.
		
		return $d;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		parent::__construct();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * FAQ index
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function user_access()
	{
		$this->data['page']->admin_m = 'user_access';
		
		// --------------------------------------------------------------------------
		
		$this->data['groups'] = $this->user->get_groups();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'settings/user_access',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * FAQ index
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function edit_group()
	{
		$this->data['page']->admin_m = 'edit_group';
		
		$_gid = $this->uri->segment( 4, NULL );
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post() ) :
		
			$this->load->library( 'form_validation' );
			
			$this->form_validation->set_rules( 'display_name',			'Display Name',		'xss_clean|required' );
			$this->form_validation->set_rules( 'name',					'Slug',				'xss_clean|required' );
			$this->form_validation->set_rules( 'description',			'Description',		'xss_clean|required' );
			$this->form_validation->set_rules( 'default_homepage',		'Default Homepage', 'xss_clean|required' );
			$this->form_validation->set_rules( 'acl[]',					'Permissions', 		'xss_clean|required' );
			$this->form_validation->set_rules( 'acl[superuser]',		'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin]',			'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[intern]',			'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[employer_manager]',	'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[employer_team]',	'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin][]',			'Permissions', 		'xss_clean' );
			
			if ( $this->form_validation->run() ) :
			
				$_data = array();
				$_data['display_name']		= $this->input->post( 'display_name' );
				$_data['name']				= $this->input->post( 'name' );
				$_data['description']		= $this->input->post( 'description' );
				$_data['default_homepage']	= $this->input->post( 'default_homepage' );
				$_data['acl']				= serialize( $this->input->post( 'acl' ) );
				
				$this->user->update_group( $_gid, $_data );
				
				$this->session->set_flashdata( 'success', 'Group updated successfully!' );
				redirect( 'admin/settings/user_access' );
				return;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->data['group'] = $this->user->get_group( $_gid );
		
		if ( ! $this->data['group'] ) :
		
			$this->session->set_flashdata( 'error', 'Group does not exist.' );
			redirect( 'admin/settings/user_access' );
		
		endif;
		
		$this->data['admin_modules'] = $this->admin_model->get_available_modules();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'settings/edit_group',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
}

/* End of file settings.php */
/* Location: ./application/modules/admin/controllers/settings.php */