<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin - Lists management
*
* Docs:			-
*
* Created:		01/06/2011
* Modified:		01/02/2012
*
* Description:	This area allows admin to update all types of content used on site
* 
*/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

class Lists extends Admin_Controller {
	
	
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
		$d->priority								= 13;					//	Module's order in nav (unique).
		$d->name									= 'List Management';	//	Display name.
		
		//	A
		$d->funcs['award_type']						= 'Awards &amp; Scholarships: Types';	
		
		//	G
		$d->funcs['sector']							= 'General: Sectors';			
		$d->funcs['location']						= 'General: Locations';			
		
		//	P	
		$d->funcs['professional_qualification']		= 'Professional Qualifications';	
			
		//	Q
		$d->funcs['qualification']					= 'Qualifications: List';		
		$d->funcs['qualification_course']			= 'Qualifications: Courses';		
		$d->funcs['qualification_class']			= 'Qualifications: Classifications';
		$d->funcs['institution']					= 'Qualifications: Institutions';
		
		//	R
		$d->funcs['religion']						= 'Religions: List';

		//	S
		$d->funcs['school_grade']					= 'School: Grades';		
		$d->funcs['school_level']					= 'School: Levels';		
		$d->funcs['school_subject']					= 'School: Subjects';

		$d->funcs['skill']							= 'Skills: List';				
		$d->funcs['language']						= 'Skills: Languages';			
					
		$d->funcs['society_position']				= 'Society: Positions';	
		$d->funcs['society_type']					= 'Society: Types';		
					
		//	W
		$d->funcs['experience_type']				= 'Work Experience: Types';	
		
		$d->announce_to					= array();							//	Which groups can access this module.
		$d->searchable					= FALSE;							//	Is module searchable?
		
		//	Dynamic
		$d->base_url		= basename( __FILE__, '.php' );	//	For link generation.
		
		return $d;
	}
	
	
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
	
	
	/**
	 * All Sectors
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function sector()
	{
		//	Load model
		$this->load->model( 'sector_model' );
		
		$this->data['page']->admin_m = 'sector';
		
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'title',
							'label'   => 'Title',
							'rules'   => 'required|unique[sectors.title]'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'title' 		=>	$this->input->post('title'),
				'title_short' 	=>	$this->input->post('title_short')
			);
			if ( $this->sector_model->add( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Sector added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['sectors'] = $this->sector_model->get_with_stats();
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'lists/sector/index',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	/**
	 * Edit Sector
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function sector_edit()
	{
		
		//	Load model
		$this->load->model( 'sector_model' );
	
		$this->data['page']->admin_m = 'sector';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'title',
							'label'   => 'Title',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules( $config );
		if ( $this->form_validation->run() == TRUE ) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'title' 		=>	$this->input->post( 'title' ),
				'title_short' 	=>	$this->input->post( 'title_short' )
			);
			if ( $this->sector_model->edit( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Sector updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/sector' );
			endif;		
		endif;
		//	Get data
		$this->data['sectors'] 	= $this->sector_model->get_with_stats();
		$this->data['sector'] 	= $this->sector_model->get_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'lists/sector/edit',		$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	/**
	 * Delete Sector
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function sector_delete()
	{
	
		//	Load model
		$this->load->model( 'sector_model' );
		
		$sector			= $this->uri->segment( 4, NULL );
		if ( $sector ) :
			$sectors 	= $this->sector_model->get_with_stats( $sector );
			$returned	= $sectors;
			//	Check that this sector has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->i_count == 0 && $returned[0]->u_count == 0 && $returned[0]->e_count == 0 ) :
				if ( $this->sector_model->delete($sector) == TRUE ) :
					$this->data['success'] 	= "Sector deleted successfully!";
					return $this->sector();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this sector. Please try again.";
					return $this->sector();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the sector you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->sector();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that sector. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->sector();
		endif;
	}
	
	
	/**
	 * All Locations
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function location()
	{
		//	Load mode
		$this->load->model( 'location_model' );
	
		$this->data['page']->admin_m = 'location';
		//	Run form validation if post (adding)
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'title',
							'label'   => 'Title',
							'rules'   => 'required|unique[locations.title]'
						),
						array(
							'field'   => 'parent',
							'label'   => 'Parent',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'title' 		=>	$this->input->post('title'),
				'parent' 		=>	$this->input->post('parent')
			);
			if ( $this->location_model->add( $data ) == TRUE ) :	//	Add to the dbs
				$this->data['success'] 	= "Location added successfully!";
			endif;		
		endif;
		//	Get data
		$this->data['locations'] = $this->location_model->get_with_stats();
		//	Load views
		$this->load->view( 'structure/header',					$this->data );
		$this->load->view( 'lists/location/index',	$this->data );
		$this->load->view( 'structure/footer',					$this->data );
	}


	/**
	 * Edit Location
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function location_edit()
	{
	
		//	Load mode
		$this->load->model( 'location_model' );
		
		$this->data['page']->admin_m = 'location';
		//	Run form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'title',
							'label'   => 'Title',
							'rules'   => 'required'
						),
						array(
							'field'   => 'parent',
							'label'   => 'Parent',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules( $config );
		if ( $this->form_validation->run() == TRUE ) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'title' 		=>	$this->input->post( 'title' ),
				'parent' 		=>	$this->input->post( 'parent' )
			);
			if ( $this->location_model->edit( $id, $data ) == TRUE ) :	//	Edit db
				$this->data['success'] 	= "Location updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/location' );
			endif;		
		endif;
		//	Get data
		$this->data['locations'] 	= $this->location_model->get_with_stats();
		$this->data['location'] 	= $this->location_model->get_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'lists/location/edit',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	/**
	 * Delete Location
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function location_delete()
	{
		//	Load mode
		$this->load->model( 'location_model' );
		
		$location			= $this->uri->segment(4, NULL);
		if ( $location ) :
			$locations 		= $this->location_model->get_with_stats($location);
			$returned 		= $locations->result();
			//	Check that this location has no children, we don't want any orphans
			if ( $returned && $returned[0]->i_count == 0 && $returned[0]->u_count == 0 ) :
				if ( $this->location_model->delete( $location ) == TRUE ) :				
					$this->data['success'] 	= "Location deleted successfully!";
					return $this->location();
				else:
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this location. Please try again.";
					return $this->location();
				endif;
			else:
				$this->data['error'] 		= "Sorry, the location you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->location();
			endif;		
		else :
			$this->data['error'] 			= "Sorry, we were unable to delete that location. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->location();
		endif;
	}


	/**
	 * All Institutions (Universities)
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function institution()
	{
		//	Load model
		$this->load->model( 'institution_model' );
		$this->data['page']->admin_m = 'institution';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required|unique[institution.name]'
						)
					);
		$this->form_validation->set_rules( $config );
		if ( $this->form_validation->run() == TRUE ) :
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			if ( $this->input->post( 'parent' ) == '' ) :
				$parent = NULL;
			else :
				$parent = $this->input->post( 'parent' );
			endif;
			$data = array(
				'name' 		=>	$this->input->post('name'),
				'active' 	=>	$active,
				'parent' 	=>	$parent,
				'website'	=>	$this->input->post('website')
			);
			if ( $this->institution_model->add( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Institution added successfully!";
			endif;
			//	Prep and process logo
			if ( $_FILES['userfile']['name'] != '' ) :
			
				//	Do upload settings
				$config['upload_path'] = '/home/internav/subdomains/cdn/institution_images/';
				$config['allowed_types'] = 'gif|jpg|png';
				$config['max_size']	= '7000';
				$config['max_width']  = '4200';
				$config['max_height']  = '4500';
				$ext = substr( $_FILES['userfile']['name'], strrpos( $_FILES['userfile']['name'], '.' ) );
				//	Load upload lib
				$this->load->library( 'upload', $config );
				//	If we have a failure
				if ( ! $this->upload->do_upload()) :
					$this->data['upload_error'] = $this->upload->display_errors();	//	Custom error
				else:
					//	Fill data
					$img_data = $this->upload->data();
					//	Rename the file
					$old = $img_data['full_path'];
					$new = '/home/internav/subdomains/cdn/institution_images/'.$this->db->insert_id().$ext;
					rename( $old, $new );
					$data = NULL;
					$data = array( 'logo' => $this->db->insert_id().$ext );
					$this->db->where( 'id', $this->db->insert_id() );
					$this->db->update( 'institution', $data );
					//	If all is well, set success
					$this->session->set_flashdata( 'success', 'Institution image added successfully!' );
				endif;
			
			endif;

		endif;
		//	Get data
		$this->data['institutions'] = $this->institution_model->get_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/institution/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Institution
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function institution_edit()
	{
		//	Load model
		$this->load->model( 'institution_model' );
		$this->data['page']->admin_m = 'institution';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			if ( $this->input->post( 'parent' ) == '' ) :
				$parent = NULL;
			else :
				$parent = $this->input->post( 'parent' );
			endif;
			$data = array(
				'name' 		=>	$this->input->post( 'name' ),
				'active' 	=>	$active,
				'parent' 	=>	$parent,
				'website'	=>	$this->input->post('website')
			);
			if ( $this->institution_model->edit( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Institution updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				$success = 1;
			endif;	
			
			//	Prep and process logo
			if ( $_FILES['userfile']['name'] != '' ) :
			
				//	Do upload settings
				$config['upload_path'] = '/home/internav/subdomains/cdn/institution_images/';
				$config['allowed_types'] = 'gif|jpg|png';
				$config['max_size']	= '7000';
				$config['max_width']  = '4200';
				$config['max_height']  = '4500';
				$ext = substr( $_FILES['userfile']['name'], strrpos( $_FILES['userfile']['name'], '.' ) );
				//	Load upload lib
				$this->load->library('upload', $config);
				//	If we have a failure
				if ( ! $this->upload->do_upload()) :
					$this->data['upload_error'] = $this->upload->display_errors();	//	Custom error
				else:
					//	Fill data
					$img_data = $this->upload->data();
					//	Rename the file
					$old = $img_data['full_path'];
					$new = '/home/internav/subdomains/cdn/institution_images/'.$id.$ext;
					rename( $old, $new );
					$data = NULL;
					$data = array( 'logo' => $id.$ext );
					$this->db->where( 'id', $id );
					$this->db->update( 'institution', $data );
					//	If all is well, set success
					$this->session->set_flashdata( 'success', 'Institution image added successfully!' );
				endif;
			
			endif;
			
			if ( $success == 1 )
				redirect( '/admin/lists/institution' );

		endif;
		//	Get data
		$this->data['institutions'] 	= $this->institution_model->get_with_stats();
		$this->data['institution'] 		= $this->institution_model->get_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/institution/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete Institution
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function institution_delete()
	{
		//	Load model
		$this->load->model( 'institution_model' );
		$institution	= $this->uri->segment(4, NULL);
		if ( $institution ) :
			$institutions 	= $this->institution_model->get_with_stats( $institution );
			$returned		= $institutions->result();
			//	Check that this institution has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->q_count == 0 && $returned[0]->s_count == 0 ) :
				if ( $this->institution_model->delete( $institution ) == TRUE ) :
					$this->data['success'] 	= "Institution deleted successfully!";
					return $this->institution();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this institution. Please try again.";
					return $this->institution();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the institution you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->institution();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that institution. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->institution();
		endif;
	}
	
	
	/**
	 * All Languages
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function language()
	{
		$this->load->model( 'language_model' );
		$this->data['page']->admin_m = 'language';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required|unique[language.name]'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'name' 		=>	$this->input->post('name')
			);
			if ( $this->language_model->add( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Language added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['languages'] = $this->language_model->get_language_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/language/index',		$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Language
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function language_edit()
	{
		$this->load->model( 'language_model' );
		$this->data['page']->admin_m = 'language';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'name' 		=>	$this->input->post( 'name' )
			);
			if ( $this->language_model->edit( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Language updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/language' );
			endif;		
		endif;
		//	Get data
		$this->data['languages'] 		= $this->language_model->get_language_with_stats();
		$this->data['language'] 		= $this->language_model->get_language_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/language/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete Language
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function language_delete()
	{
		$this->load->model( 'language_model' );
		$language	= $this->uri->segment(4, NULL);
		if ( $language ) :
			$languages 	= $this->language_model->get_language_with_stats( $language );
			$returned		= $languages->result();
			//	Check that this language has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->language_model->delete( $language ) == TRUE ) :
					$this->data['success'] 	= "Language deleted successfully!";
					return $this->language();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this language. Please try again.";
					return $this->language();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the language you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->language();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that language. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->language();
		endif;
	}


	
	/**
	 * All Qualifications
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function qualification()
	{
		$this->load->model( 'qualification_model' );
		$this->data['page']->admin_m = 'qualification';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required|unique[qualifications.name]'
						),
						array(
							'field'   => 'description',
							'label'   => 'Description',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules( $config );
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'name' 				=>	$this->input->post( 'name' ),
				'description' 		=>	$this->input->post( 'description' )
			);
			if ( $this->qualification_model->add_qualification( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Qualification added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['qualifications'] = $this->qualification_model->get_qualification_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/qualification/index',		$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Qualification
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function qualification_edit()
	{
		$this->load->model( 'qualification_model' );
		$this->data['page']->admin_m = 'qualification';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required'
						),
						array(
							'field'   => 'description',
							'label'   => 'Description',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'name' 				=>	$this->input->post( 'name' ),
				'description' 		=>	$this->input->post( 'description' )
			);
			if ( $this->qualification_model->edit_qualification( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Qualification updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/qualification' );
			endif;		
		endif;
		//	Get data
		$this->data['qualifications'] 	= $this->qualification_model->get_qualification_with_stats();
		$this->data['qualification'] 		= $this->qualification_model->get_qualification_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/qualification/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete Qualification
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function qualification_delete()
	{
		$this->load->model( 'qualification_model' );
		$qualification	= $this->uri->segment(4, NULL);
		if ( $qualification ) :
			$qualifications 	= $this->qualification_model->get_qualification_with_stats( $qualification );
			$returned		= $qualifications->result();
			//	Check that this qualification has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->qualification_model->delete_qualification( $qualification ) == TRUE ) :
					$this->data['success'] 	= "Qualification deleted successfully!";
					return $this->qualification();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this qualification. Please try again.";
					return $this->qualification();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the qualification you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->qualification();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that qualification. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->qualification();
		endif;
	}
	
	
	/**
	 * All Qualification Courses
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function qualification_course()
	{
		$this->load->model( 'qualification_model' );
		$this->data['page']->admin_m = 'qualification_course';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required|unique[qualification_course.name]'
						)
					);
		$this->form_validation->set_rules($config);
		if ( $this->form_validation->run() == TRUE ) :
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			$data = array(
				'name' 		=>	$this->input->post('name'),
				'active' 	=>	$active
			);
			if ( $this->qualification_model->add_course( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Course added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['qualification_courses'] = $this->qualification_model->get_course_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/qualification_course/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Qualification Course
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function qualification_course_edit()
	{
		$this->load->model( 'qualification_model' );
		$this->data['page']->admin_m = 'qualification_course';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			$data = array(
				'name' 		=>	$this->input->post( 'name' ),
				'active' 	=>	$active
			);
			if ( $this->qualification_model->edit_course( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Course updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/qualification_course' );
			endif;		
		endif;
		//	Get data
		$this->data['qualification_courses'] 		= $this->qualification_model->get_course_with_stats();
		$this->data['qualification_course'] 		= $this->qualification_model->get_course_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',					$this->data );
		$this->load->view( 'lists/qualification_course/edit',	$this->data );
		$this->load->view( 'structure/footer',					$this->data );
	}
	
	
	/**
	 * Delete Qualification Course
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function qualification_course_delete()
	{
		$this->load->model( 'qualification_model' );
		$qualification_course	= $this->uri->segment(4, NULL);
		if ( $qualification_course ) :
			$qualification_courses 	= $this->qualification_model->get_course_with_stats( $qualification_course );
			$returned		= $qualification_courses->result();
			//	Check that this qualification_course has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->qualification_model->delete_course( $qualification_course ) == TRUE ) :
					$this->data['success'] 	= "Course deleted successfully!";
					return $this->qualification_course();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this course. Please try again.";
					return $this->qualification_course();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the course you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->qualification_course();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that course. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->qualification_course();
		endif;
	}
	
	
	/**
	 * All qualification classes
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function qualification_class()
	{
		$this->load->model( 'qualification_model' );
		$this->data['page']->admin_m = 'qualification_class';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'class',
							'label'   => 'Name',
							'rules'   => 'required|unique[qualification_class.class]'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'class' 		=>	$this->input->post('class')
			);
			if ( $this->qualification_model->add_class( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Qualification Class added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['qualification_classes'] = $this->qualification_model->get_class_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/qualification_class/index',		$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Qualification classes
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function qualification_class_edit()
	{
		$this->load->model( 'qualification_model' );
		$this->data['page']->admin_m = 'qualification_class';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'class',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'class' 		=>	$this->input->post( 'class' )
			);
			if ( $this->qualification_model->edit_class( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Qualification class updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/qualification_class' );
			endif;		
		endif;
		//	Get data
		$this->data['qualification_classes'] 	= $this->qualification_model->get_class_with_stats();
		$this->data['qualification_class'] 		= $this->qualification_model->get_class_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/qualification_class/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete Qualification Class
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function qualification_class_delete()
	{
		$this->load->model( 'qualification_model' );
		$qualification_class	= $this->uri->segment(4, NULL);
		if ( $qualification_class ) :
			$qualification_classes 	= $this->qualification_model->get_class_with_stats( $qualification_class );
			$returned		= $qualification_classes->result();
			//	Check that this qualification_class has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->qualification_model->delete_class( $qualification_class ) == TRUE ) :
					$this->data['success'] 	= "Qualification class deleted successfully!";
					return $this->qualification_class();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this qualification class. Please try again.";
					return $this->qualification_class();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the qualification class you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->qualification_class();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that qualification class. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->qualification_class();
		endif;
	}	
	
	
	
	
	/**
	 * All Religions
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function religion()
	{
		$this->load->model( 'diversity_model' );
		$this->data['page']->admin_m = 'religion';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required|unique[religion.name]'
						)
					);
		$this->form_validation->set_rules( $config );
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'name' 		=>	$this->input->post('name')
			);
			if ( $this->diversity_model->add_religion( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Religion added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['religions'] = $this->diversity_model->get_religion_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/religion/index',		$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Religion
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function religion_edit()
	{
		$this->load->model( 'diversity_model' );
		$this->data['page']->admin_m = 'religion';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'name' 		=>	$this->input->post( 'name' )
			);
			if ( $this->diversity_model->edit_religion( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Religion updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/religion' );
			endif;		
		endif;
		//	Get data
		$this->data['religions'] 	= $this->diversity_model->get_religion_with_stats();
		$this->data['religion'] 		= $this->diversity_model->get_religion_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/religion/edit',		$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete Religion
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function religion_delete()
	{
		$this->load->model( 'diversity_model' );
		$religion	= $this->uri->segment(4, NULL);
		if ( $religion ) :
			$religions 	= $this->diversity_model->get_religion_with_stats( $religion );
			$returned		= $religions->result();
			//	Check that this religion has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->diversity_model->delete_religion( $religion ) == TRUE ) :
					$this->data['success'] 	= "Religion deleted successfully!";
					return $this->religion();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this religion. Please try again.";
					return $this->religion();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the religion you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->religion();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that religion. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->religion();
		endif;
	}

	
	/**
	 * All Award Types
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function award_type()
	{
		$this->load->model( 'award_model' );
		$this->data['page']->admin_m = 'award_type';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'type',
							'label'   => 'Name',
							'rules'   => 'required|unique[award_type.type]'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'type' 		=>	$this->input->post('type')
			);
			if ( $this->award_model->add( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Type added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['award_types'] = $this->award_model->get_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/award_type/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Award Type
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function award_type_edit()
	{
		$this->load->model( 'award_model' );
		$this->data['page']->admin_m = 'award_type';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'type',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'type' 		=>	$this->input->post( 'type' )
			);
			if ( $this->award_model->edit( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Type updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/award_type' );
			endif;		
		endif;
		//	Get data
		$this->data['award_types'] 		= $this->award_model->get_with_stats();
		$this->data['award_type'] 		= $this->award_model->get_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/award_type/edit',		$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete Award Type
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function award_type_delete()
	{
		$this->load->model( 'award_model' );
		$award_type	= $this->uri->segment(4, NULL);
		if ( $award_type ) :
			$award_types 	= $this->award_model->get_with_stats( $award_type );
			$returned		= $award_types->result();
			//	Check that this award_type has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->award_model->delete( $award_type ) == TRUE ) :
					$this->data['success'] 	= "Type deleted successfully!";
					return $this->award_type();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this award type. Please try again.";
					return $this->award_type();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the award type you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->award_type();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that award type. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->award_type();
		endif;
	}
	
	
	
	/**
	 * All Professional Qualifications
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function professional_qualification()
	{
		$this->load->model( 'professional_qualification_model' );
		$this->data['page']->admin_m = 'professional_qualification';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required|unique[qualifications_professional.name]'
						),
						array(
							'field'   => 'description',
							'label'   => 'Description',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules( $config );
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'name' 				=>	$this->input->post( 'name' ),
				'description' 		=>	$this->input->post( 'description' )
			);
			if ( $this->professional_qualification_model->add( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Qualification added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['professional_qualifications'] = $this->professional_qualification_model->get_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/professional_qualification/index',		$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Professional Qualification
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function professional_qualification_edit()
	{
		$this->load->model( 'professional_qualification_model' );
		$this->data['page']->admin_m = 'professional_qualification';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required'
						),
						array(
							'field'   => 'description',
							'label'   => 'Description',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'name' 				=>	$this->input->post( 'name' ),
				'description' 		=>	$this->input->post( 'description' )
			);
			if ( $this->professional_qualification_model->edit( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Qualification updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/professional_qualification' );
			endif;		
		endif;
		//	Get data
		$this->data['professional_qualifications'] 		= $this->professional_qualification_model->get_with_stats();
		$this->data['professional_qualification'] 		= $this->professional_qualification_model->get_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/professional_qualification/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete Professional Qualification
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function professional_qualification_delete()
	{
		$this->load->model( 'professional_qualification_model' );
		$professional_qualification	= $this->uri->segment( 4, NULL );
		if ( $professional_qualification ) :
			$professional_qualifications 	= $this->professional_qualification_model->get_with_stats( $professional_qualification );
			$returned		= $professional_qualifications->result();
			//	Check that this professional_qualification has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->professional_qualification_model->delete( $professional_qualification ) == TRUE ) :
					$this->data['success'] 	= "Qualification deleted successfully!";
					return $this->professional_qualification();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this qualification. Please try again.";
					return $this->professional_qualification();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the qualification you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->professional_qualification();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that qualification. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->professional_qualification();
		endif;
	}
	
	
	
	/**
	 * All school grades
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function school_grade()
	{
		$this->load->model( 'school_model' );
		$this->data['page']->admin_m = 'school_grade';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required|unique[school_grades.name]'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'name' 		=>	$this->input->post('name')
			);
			if ( $this->school_model->add_grade( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Grade added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['school_grades'] = $this->school_model->get_grade_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/school_grade/index',		$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit School Grades
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function school_grade_edit()
	{
		$this->load->model( 'school_model' );
		$this->data['page']->admin_m = 'school_grade';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'name' 		=>	$this->input->post( 'name' )
			);
			if ( $this->school_model->edit_grade( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Grade updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/school_grade' );
			endif;		
		endif;
		//	Get data
		$this->data['school_grades'] 		= $this->school_model->get_grade_with_stats();
		$this->data['school_grade'] 		= $this->school_model->get_grade_with_stats( $this->uri->segment( 4, NULL ) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/school_grade/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete School Grades
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function school_grade_delete()
	{
		$this->load->model( 'school_model' );
		$school_grade	= $this->uri->segment(4, NULL);
		if ( $school_grade ) :
			$school_grades 	= $this->school_model->get_grade_with_stats( $school_grade );
			$returned		= $school_grades->result();
			//	Check that this school_grade has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->school_model->delete_grade( $school_grade ) == TRUE ) :
					$this->data['success'] 	= "Grade deleted successfully!";
					return $this->school_grade();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this grade. Please try again.";
					return $this->school_grade();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the grade you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->school_grade();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that grade. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->school_grade();
		endif;
	}

	
	
	/**
	 * All school levels
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function school_level()
	{
		$this->load->model( 'school_model' );
		$this->data['page']->admin_m = 'school_level';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required|unique[school_levels.level]'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'level' 	=>	$this->input->post('name')
			);
			if ( $this->school_model->add_level( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Level added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['school_levels'] = $this->school_model->get_level_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/school_level/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit School Level
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function school_level_edit()
	{
		$this->load->model( 'school_model' );
		$this->data['page']->admin_m = 'school_level';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules( $config );
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'level' 		=>	$this->input->post( 'name' )
			);
			if ( $this->school_model->edit_level( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Level updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/school_level' );
			endif;		
		endif;
		//	Get data
		$this->data['school_levels'] 		= $this->school_model->get_level_with_stats();
		$this->data['school_level'] 		= $this->school_model->get_level_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/school_level/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete School Level
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function school_level_delete()
	{
		$this->load->model( 'school_model' );
		$school_level	= $this->uri->segment(4, NULL);
		if ( $school_level ) :
			$school_levels 	= $this->school_model->get_level_with_stats( $school_level );
			$returned		= $school_levels->result();
			//	Check that this school_level has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->school_model->delete_level( $school_level ) == TRUE ) :
					$this->data['success'] 	= "Level deleted successfully!";
					return $this->school_level();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this level. Please try again.";
					return $this->school_level();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the level you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->school_level();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that grade. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->school_level();
		endif;
	}
	
	
	/**
	 * All School Subjects
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function school_subject()
	{
		$this->load->model( 'school_model' );
		$this->data['page']->admin_m = 'school_subject';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required|unique[school_subjects.name]'
						)
					);
		$this->form_validation->set_rules($config);
		if ( $this->form_validation->run() == TRUE ) :
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			$data = array(
				'name' 		=>	$this->input->post('name'),
				'active' 	=>	$active
			);
			if ( $this->school_model->add_subject( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "School Subject added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['school_subjects'] = $this->school_model->get_subject_with_stats();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/school_subject/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit School Subject
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function school_subject_edit()
	{
		$this->load->model( 'school_model' );
		$this->data['page']->admin_m = 'school_subject';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'name',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			$data = array(
				'name' 		=>	$this->input->post( 'name' ),
				'active' 	=>	$active
			);
			if ( $this->school_model->edit_subject( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "School Subject updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/school_subject' );
			endif;		
		endif;
		//	Get data
		$this->data['school_subjects'] 		= $this->school_model->get_subject_with_stats();
		$this->data['school_subject'] 		= $this->school_model->get_subject_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/school_subject/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete School Subjects
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function school_subject_delete()
	{
		$this->load->model( 'school_model' );
		$school_subject	= $this->uri->segment(4, NULL);
		if ( $school_subject ) :
			$school_subjects 	= $this->school_model->get_subject_with_stats( $school_subject );
			$returned		= $school_subjects->result();
			//	Check that this school_subject has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->school_model->delete_subject( $school_subject ) == TRUE ) :
					$this->data['success'] 	= "School Subject deleted successfully!";
					return $this->school_subject();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this subject. Please try again.";
					return $this->school_subject();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the subject you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->school_subject();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that subject. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->school_subject();
		endif;
	}
	
	
	/**
	 * All Skills
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function skill()
	{
		$this->load->model( 'skill_model' );
		$this->data['page']->admin_m = 'skill';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'skill',
							'label'   => 'Skill',
							'rules'   => 'required|unique[skills.skill]'
						),
						array(
							'field'		=> 'category',
							'label'		=> 'Category',
							'rules'		=> 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ( $this->form_validation->run() == TRUE ) :
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			$data = array(
				'skill' 	=>	$this->input->post('skill'),
				'category'	=>	$this->input->post('category'),
				'active' 	=>	$active
			);
			if ( $this->skill_model->add_skill( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Skill added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['skills'] 			= $this->skill_model->get_skill_with_stats();
		$this->data['skill_categories'] = $this->skill_model->get_skill_categories();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/skill/index',			$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Skill
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function skill_edit()
	{
		$this->load->model( 'skill_model' );
		$this->data['page']->admin_m = 'skill';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'skill',
							'label'   => 'Skill',
							'rules'   => 'required'
						),
						array(
							'field'		=> 'category',
							'label'		=> 'Category',
							'rules'		=> 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$id = $this->uri->segment( 4, NULL );
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			$data = array(
				'skill' 	=>	$this->input->post('skill'),
				'category'	=>	$this->input->post('category'),
				'active' 	=>	$active
			);
			if ( $this->skill_model->edit_skill( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Skill updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/skill' );
			endif;		
		endif;
		//	Get data
		$this->data['skills'] 		= $this->skill_model->get_skill_with_stats();
		$this->data['skill'] 		= $this->skill_model->get_skill_with_stats( $this->uri->segment(4, NULL) );
		$this->data['skill_categories'] = $this->skill_model->get_skill_categories();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/skill/edit',			$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete Skill
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function skill_delete()
	{
		$this->load->model( 'skill_model' );
		$skill	= $this->uri->segment(4, NULL);
		if ( $skill ) :
			$skills 	= $this->skill_model->get_skill_with_stats( $skill );
			$returned		= $skills->result();
			//	Check that this skill has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->skill_model->delete_skill( $skill ) == TRUE ) :
					$this->data['success'] 	= "Skill deleted successfully!";
					return $this->skill();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this skill. Please try again.";
					return $this->skill();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the skill you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->skill();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that skill. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->skill();
		endif;
	}


	/**
	 * All Society Positions
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function society_position()
	{
		$this->load->model( 'society_model' );
		$this->data['page']->admin_m = 'society_position';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'position',
							'label'   => 'Position',
							'rules'   => 'required|unique[society_position.position]'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			$data = array(
				'position' 			=>	$this->input->post('position'),
				'description' 		=>	$this->input->post('description')
			);
			if ( $this->society_model->add_society_position( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Position added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['society_positions'] = $this->society_model->get_society_position_with_stats();
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'lists/society_position/index',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	/**
	 * Edit Society Position
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function society_position_edit()
	{
		$this->load->model( 'society_model' );
		$this->data['page']->admin_m = 'society_position';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'position',
							'label'   => 'Position',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules( $config );
		if ( $this->form_validation->run() == TRUE ) :
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'position' 				=>	$this->input->post( 'position' ),
				'description' 			=>	$this->input->post( 'description' )
			);
			if ( $this->society_model->edit_society_position( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Position updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/society_position' );
			endif;		
		endif;
		//	Get data
		$this->data['society_positions'] 	= $this->society_model->get_society_position_with_stats();
		$this->data['society_position'] 	= $this->society_model->get_society_position_with_stats( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'lists/society_position/edit',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}
	
	
	/**
	 * Delete Society Position
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function society_position_delete()
	{
		$this->load->model( 'society_model' );
		$society_position	= $this->uri->segment(4, NULL);
		if ( $society_position ) :
			$society_positions 	= $this->society_model->get_society_position_with_stats( $society_position );
			$returned		= $society_positions->result();
			//	Check that this society_position has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->society_model->delete_society_position( $society_position ) == TRUE ) :
					$this->data['success'] 	= "Position deleted successfully!";
					return $this->society_position();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this position. Please try again.";
					return $this->society_position();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the position you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->society_position();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that position. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->society_position();
		endif;
	}
	


	/**
	 * All Society Types
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function society_type()
	{
		$this->load->model( 'society_model' );
		$this->data['page']->admin_m = 'society_type';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'type',
							'label'   => 'Type',
							'rules'   => 'required|unique[society_type.type]'
						),
						array(
							'field'		=> 'group',
							'label'		=> 'Group',
							'rules'		=> 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ( $this->form_validation->run() == TRUE ) :
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			$data = array(
				'type' 		=>	$this->input->post('type'),
				'group'		=>	$this->input->post('group'),
				'active' 	=>	$active
			);
			if ( $this->society_model->add_society_type( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Type added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['society_types'] 			= $this->society_model->get_society_type_with_stats();

		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/society_type/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Society Type
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function society_type_edit()
	{
		$this->load->model( 'society_model' );
		$this->data['page']->admin_m = 'society_type';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'type',
							'label'   => 'Type',
							'rules'   => 'required|unique[society_type.type]'
						),
						array(
							'field'		=> 'group',
							'label'		=> 'Group',
							'rules'		=> 'required'
						)
					);
		$this->form_validation->set_rules( $config );
		if ( $this->form_validation->run() == TRUE ) :
			$id = $this->uri->segment( 4, NULL );
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			$data = array(
				'type' 		=>	$this->input->post( 'type' ),
				'group'		=>	$this->input->post( 'group' ),
				'active' 	=>	$active
			);
			if ( $this->society_model->edit_society_type( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Type updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/society_type' );
			endif;		
		endif;
		//	Get data
		$this->data['society_types'] 		= $this->society_model->get_society_type_with_stats();
		$this->data['society_type'] 		= $this->society_model->get_society_type_with_stats( $this->uri->segment( 4, NULL ) );

		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/society_type/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete Society Type
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function society_type_delete()
	{
		$this->load->model( 'society_model' );
		$society_type	= $this->uri->segment(4, NULL);
		if ( $society_type ) :
			$society_types 	= $this->society_model->get_society_type_with_stats( $society_type );
			$returned		= $society_types->result();
			//	Check that this society_type has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->society_model->delete_society_type( $society_type ) == TRUE ) :
					$this->data['success'] 	= "Type deleted successfully!";
					return $this->society_type();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this type. Please try again.";
					return $this->society_type();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the type you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->society_type();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that type. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->society_type();
		endif;
	}


	/**
	 * All Experience Types
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function experience_type()
	{
		$this->load->model( 'experience_model' );
		$this->data['page']->admin_m = 'experience_type';
		//	Do form validation if post (adding)	
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'type',
							'label'   => 'Name',
							'rules'   => 'required|unique[experience_type.type]'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
		
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;

			$data = array(
				'type' 			=>	$this->input->post('type'),
				'active' 		=>	$active
			);
			if ( $this->experience_model->add_experience_type( $data ) == TRUE ) :	//	Add to db
				$this->data['success'] 	= "Type added successfully!";
			endif;
		endif;
		//	Get data
		$this->data['experience_types'] = $this->experience_model->get_experience_types();
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/experience_type/index',		$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	/**
	 * Edit Experience Type
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function experience_type_edit()
	{
		$this->load->model( 'experience_model' );
		$this->data['page']->admin_m = 'experience_type';
		//	Form validation if editing
		$this->load->library('form_validation');
		$config = array(
						array(
							'field'   => 'type',
							'label'   => 'Name',
							'rules'   => 'required'
						)
					);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == TRUE) :
			if ( $this->input->post( 'active' ) == 'on' ) :
				$active = 1;
			else :
				$active = NULL;
			endif;
			$id = $this->uri->segment( 4, NULL );
			$data = array(
				'type' 		=>	$this->input->post( 'type' ),
				'active' 	=>	$active
			);
			if ( $this->experience_model->edit_experience_type( $id, $data ) == TRUE ) :	//	Edit in db
				$this->data['success'] 	= "Type updated successfully!";
				$this->session->set_flashdata( 'success', $this->data['success'] );
				redirect( '/admin/lists/experience_type' );
			endif;		
		endif;
		//	Get data
		$this->data['experience_types'] 		= $this->experience_model->get_experience_types();
		$this->data['experience_type'] 		= $this->experience_model->get_experience_types( $this->uri->segment(4, NULL) );
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'lists/experience_type/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	/**
	 * Delete experience Type
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function experience_type_delete()
	{
		$this->load->model( 'experience_model' );
		$experience_type	= $this->uri->segment(4, NULL);
		if ( $experience_type ) :
			$experience_types 	= $this->experience_model->get_experience_types( $experience_type );
			$returned		= $experience_types->result();
			//	Check that this experience_type has no dependancies before deleting (safeguard the db!)
			if ( $returned && $returned[0]->u_count == 0 ) :
				if ( $this->experience_model->delete_experience_type( $experience_type ) == TRUE ) :
					$this->data['success'] 	= "Type deleted successfully!";
					return $this->experience_type();
				else:				
					$this->data['error'] 	= "Sorry, an unexpected error occurred while attempting to delete this experience type. Please try again.";
					return $this->experience_type();
				endif;
			else:
				$this->data['error'] 	= "Sorry, the experience type you attempted to delete has dependencies. Deleting this would result in orphaned references within the database.";
				return $this->experience_type();
			endif;
		else :
			$this->data['error'] 	= "Sorry, we were unable to delete that experience type. There may be a problem with the ID supplied rs-navigation clicked Delete. Try again.";
			return $this->experience_type();
		endif;
	}
	

	
}

/* End of file admin.php */
/* Location: ./application/modules/admin/controllers/admin.php */