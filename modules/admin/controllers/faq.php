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

/**
 * OVERLOADING NAILS'S ADMIN MODULES
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/
 
class NAILS_Faq extends Admin_Controller {
	
	
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
		$d->name				= 'FAQ';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['index']		= 'See All';			//	Sub-nav function.
		$d->funcs['create']		= 'New FAQ';			//	Sub-nav function.

		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		$_acl = active_user( 'acl' );
		if ( active_user( 'group_id' ) != 1 && ( ! isset( $_acl['admin'] ) || array_search( basename( __FILE__, '.php' ), $_acl['admin'] ) === FALSE ) )
			return NULL;
		
		// --------------------------------------------------------------------------
		
		//	Hey user! Pick me! Pick me!
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 * 
 **/
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION' ) ) :

	class Faq extends NAILS_Faq
	{
	}

endif;


/* End of file faq.php */
/* Location: ./application/modules/admin/controllers/faq.php */