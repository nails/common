<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin - FAQ
*
* Docs:			-
*
* Created:		11/01/2012
* Modified:		11/01/2012
*
* Description:	-
* 
*/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

class Faq extends Admin_Controller {
	
	
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
		$d->priority			= 18;					//	Module's order in nav (unique).
		$d->name				= 'FAQ';				//	Display name.
		$d->funcs['index']		= 'See All';			//	Sub-nav function.
		$d->funcs['create']		= 'New FAQ';			//	Sub-nav function.
		$d->announce_to			= array();				//	Which groups can access this module.
		$d->searchable			= FALSE;				//	Is module searchable?
		
		//	Dynamic
		$d->base_url		= basename( __FILE__, '.php' );	//	For link generation.
		
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
		
		//	Load model
		$this->load->model( 'faq_model' );
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
	public function index()
	{
		$this->data['page']->admin_m = 'faq_index';
		$_search = $this->input->get( 'search' );
		
		// --------------------------------------------------------------------------
		
		$this->load->model( 'faq_model' );
		$this->data['faq'] = $this->faq_model->get_all( $_search );
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'faq/index',			$this->data );
		$this->load->view( 'structure/footer',	$this->data );
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
	public function edit()
	{
		$this->data['page']->admin_m = 'faq_edit';
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post( 'update' ) ) :
		
			//	Form validation and update
			$this->load->library( 'form_validation' );
			$this->form_validation->set_rules( 'slug',	'Slug',		'xss_clean|required' );
			$this->form_validation->set_rules( 'label',	'Label',	'xss_clean|required' );
			$this->form_validation->set_rules( 'body',	'Content',	'xss_clean|required' );

			if ( $this->form_validation->run() ) :
			
				//	Prepare data
				$_id			= $this->input->post( 'id' );
				$_data['slug']	= $this->input->post( 'slug' );
				$_data['label']	= $this->input->post( 'label' );
				$_data['body']	= $this->input->post( 'body' );
				
				//	Prep WYSIWYG
				$_data['body'] = str_replace( '<p><strong><span style="font-weight: normal;">', '<p>', $_data['body'] );
				$_data['body'] = str_replace( '</span></strong></p>', '</p>', $_data['body'] );
				
				//	Do the update
				if ( $this->faq_model->update_faq( $_id, $_data ) == TRUE ) :
				
					$this->session->set_flashdata('success', 'FAQ edited successfully');
					redirect( 'admin/faq' );
					return;
					
				else:
				
					$this->data['error'] 	= "There were problems updating this FAQ.";
				
				endif;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->data['faq'] = $this->faq_model->get_single( $this->uri->segment( 4 ) );
		
		if ( ! $this->data['faq'] )
			show_404();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'faq/edit',			$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * FAQ create
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function create()
	{
		$this->data['page']->admin_m = 'faq_create';
		
		// --------------------------------------------------------------------------
					
		if ( $this->input->post( 'create' ) ) :
		
			//	Form validation and update
			$this->load->library( 'form_validation' );
			$this->form_validation->set_rules( 'label',	'Label',	'xss_clean|required' );
			$this->form_validation->set_rules( 'body',	'Content',	'xss_clean|required' );

			if ( $this->form_validation->run() ) :
			
				//	Prepare data
				$data['label']				= $this->input->post( 'label' );	
				$data['body']				= $this->input->post( 'body' );			
		
				//	Generate URL_ID based on the title, must be unique... (using CI's URL helper)
				$data['slug']				= url_title( $data['label'], 'dash', TRUE );
				
				//	Prep WYSIWYG
				$data['body'] = str_replace( '<p><strong><span style="font-weight: normal;">', '<p>', $data['body'] );
				$data['body'] = str_replace( '</span></strong></p>', '</p>', $data['body'] );
				
				//	Do the update
				if ( $this->faq_model->create_faq( $data ) == TRUE ) :
				
					$this->session->set_flashdata('success', 'FAQ created successfully');
					redirect( 'admin/faq' );
					return;
					
				else:
				
					$this->data['error'] 	= "There were problems creating this FAQ.";
				
				endif;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'faq/create',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------


	/**
	 * FAQ delete
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function delete()
	{
	
		$_id = $this->uri->segment( '5' );
		
		if ( $_id == NULL ) :
		
			$this->session->set_flashdata( 'error', 'No ID specified' );
			redirect( 'admin/faq' );
			return;
			
		endif;
		
		// --------------------------------------------------------------------------
	
		if ( $this->faq_model->delete_faq( $id ) ) :
		
			$this->session->set_flashdata('success', 'Successfully deleted!');
			redirect( 'admin/faq' );
		
		else:
		
			$this->session->set_flashdata('error', 'There was a problem deleting this FAQ. Try again.');
			redirect( '/admin/faq' );
		
		endif;
	
	}
}

/* End of file faq.php */
/* Location: ./application/modules/admin/controllers/faq.php */