<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin - Internships
*
* Docs:			-
*
* Created:		01/06/2011
* Modified:		10/01/2012
*
* Description:	-
* 
*/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

class Internships extends Admin_Controller {
	
	
	private $type;
	private $active_only;
	private $pending_only;
	
	
	// --------------------------------------------------------------------------
	
	
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
		$d->priority					= 11;								//	Module's order in nav (unique).
		$d->name						= 'Internships';					//	Display name.
		$d->funcs['index']				= 'All Internships';				//	Sub-nav function.
		$d->funcs['active_internal']	= 'Active Internships (Internal)';	//	Sub-nav function.
		$d->funcs['active_external']	= 'Active Internships (External)';	//	Sub-nav function.
		$d->funcs['pending']			= 'Pending Approval';				//	Sub-nav function.
		$d->announce_to					= array();							//	Which groups can access this module.
		$d->searchable					= FALSE;							//	Is module searchable?
		
		//	Dynamic
		$d->base_url					= basename( __FILE__, '.php' );		//	For link generation.
		
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
		$this->load->model( 'internship_model' );
		
		//	Set defaults
		$this->type			= FALSE;
		$this->active_only	= FALSE;
		$this->pending_only	= FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * All Internships
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		//	Method details and vars
		$this->data['page']->admin_m	= 'index';
		
		// --------------------------------------------------------------------------
		
		$_search = $this->input->get( 'search' );
		
		// --------------------------------------------------------------------------
		
		//	First lot of pagination data
		//	Done like this due to the double call to get_internships() - need to apply conditionals.
		
		$_page->order->column		= ( $this->uri->segment( 4 ) !== FALSE )		? $this->uri->segment( 4 ) : 'i.date_added';
		$_page->order->direction	= ( $this->uri->segment( 5 ) !== FALSE )		? $this->uri->segment( 5 ) : 'desc';
		$_page->per_page			= 25;
		$_page->page				= $this->uri->segment( 6, 0 );
		$_page->offset				= $_page->page * $_page->per_page;
		
			//	Set some query helper data
			$_order		= NULL;
			$_limit		= NULL;
			$_where		= NULL;
			$_filters	= NULL;
			
			//	Set Order
			$_order[0] = $_page->order->column;
			$_order[1] = $_page->order->direction;
			
			//	Set limits	
			$_limit[0] = $_page->per_page;
			$_limit[1] = $_page->offset;
			
			if ( $this->type ) :
			
				//	Set the flag for internal internships
				$_filters['type'] = $this->type;
			
			endif;
			
			if ( $this->active_only ) :
			
				//	Set the flag for internal internships
				$_filters['active_only'] = $this->active_only;
			
			endif;
			
			if ( $this->pending_only ) :
			
				//	Set the flag for internal internships
				$_filters['pending_only'] = $this->pending_only;
			
			endif;
		
		//	Second lot of pagination data; no, no, nonono no, no, no there's no $_limit!
		//	http://toaty.co.uk/limits
		
		$_page->total				= count( $this->internship_model->get_internships( NULL, $_filters, $_order, $_where, $_search ) );
		$_page->num_pages			= ceil( $_page->total / $_page->per_page );
		
		$this->data['pagination']	= $_page;
		
		// --------------------------------------------------------------------------
		
		$this->data['internships'] = $this->internship_model->get_internships( $_limit, $_filters, $_order, $_where, $_search );
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'internships/index',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Active Internal Internships
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function active_internal()
	{
		$this->type = 1;
		$this->active_only = TRUE;
		$this->data['page']->title = 'Active Internal Internships'; 
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Active External Internships
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function active_external()
	{
		$this->type = 2;
		$this->active_only = TRUE;
		$this->data['page']->title = 'Active External Internships'; 
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Pending Internships
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function pending()
	{
		$this->type = 1;
		$this->pending_only = TRUE;
		$this->data['page']->title = 'Internships Pending Approval';
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Edit an existing internship
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function edit()
	{
		//	Method details
		$this->data['page']->admin_m	= 'edit';
		
		// --------------------------------------------------------------------------
		
		//	Define vars
		$_return_to	= $this->input->get( 'return_to' );
		$_return_to	= ( empty( $return_to ) ) ? 'admin/internships' : $return_to;
		$_id		= $this->uri->segment( 4 );
		
		// --------------------------------------------------------------------------
		
		//	Load models
		$this->load->model('companies_model');
		$this->load->model('sector_model');
		$this->load->model('location_model');
		
		// --------------------------------------------------------------------------
		
		//	Validate if we're saving, otherwise get the data and display the edit form
		if ( $this->input->post() ) :
			
			$_post = $this->input->post();
			
			//	Load validation library
			$this->load->library( 'form_validation' );

			$this->form_validation->set_rules( 'job_title',			'Job Title',		'required|strip_tags' );
			$this->form_validation->set_rules( 'job_description',	'Job Description',	'required|strip_tags' );
			$this->form_validation->set_rules( 'company',			'Employer',			'required|is_natural_no_zero' );
			$this->form_validation->set_rules( 'sector_id',			'Sector',			'required|is_natural_no_zero' );
			$this->form_validation->set_rules( 'location',			'Location',			'required|is_natural_no_zero' );
			$this->form_validation->set_rules( 'post_code',			'Post Code',		'required|valid_postcode' );
			$this->form_validation->set_rules( 'date_added',		'Added Date',		'required|callback_date_ok[' . $_post['date_added'] . ']' );
			$this->form_validation->set_rules( 'date_deadline',		'Deadline',			'required|callback_date_ok[' . $_post['date_start'] . ']' );
			$this->form_validation->set_rules( 'date_start',		'Start Date',		'required|callback_date_ok[' . $_post['date_start'] . ']' );
			$this->form_validation->set_rules( 'pay_frequency',		'Pay Frequency',	'is_natural_no_zero' );
			$this->form_validation->set_rules( 'internal',			'Internal/External','required|is_natural_no_zero' );
		
			$this->form_validation->set_message( 'is_natural_no_zero', 'The %s field is required' );
			
			if ( $this->form_validation->run() ) :
			
				$_data = array(
					'job_title' 			=> $_post['job_title'],
					'job_description' 		=> $_post['job_description'],
					'company' 				=> $_post['company'],
					'sector_id' 			=> $_post['sector_id'],
					'location'				=> $_post['location'],
					'post_code'				=> $_post['post_code'],
					'date_added'			=> $_post['date_added'],
					'date_deadline'			=> $_post['date_deadline'],
					'date_start'			=> $_post['date_start'],
					'pay_rate'				=> $_post['pay_rate'],
					'pay_frequency'			=> $_post['pay_frequency'],
					'duration'				=> $_post['duration'],
					'duration_term'			=> $_post['duration_term'],
					'internal'				=> $_post['internal'],
					'active'				=> $_post['active'] 
				);
				
				
				// --------------------------------------------------------------------------
				
				
				if ( $_data['active'] == 0 )
					$_data['active'] = NULL;
				
				if ( $_data['pay_rate'] == 0 )
					$_data['pay_rate'] = NULL;
				
				if ( $_data['pay_frequency'] == 0 )
					$_data['pay_frequency'] = NULL;
				
				if ( $_data['duration'] == 0 )
					$_data['duration'] = NULL;
				
				if ( $_data['duration_term'] == 0 )
					$_data['duration_term'] = NULL;
				
				
				// --------------------------------------------------------------------------
				
				
				if ( $this->internship_model->update( $_id, $_data ) ) :
				
					$this->session->set_flashdata('success', 'Internship Update Successfully!');
					
					redirect ( $_return_to );
					
				else:
				
					$this->data['error'] = 'There was a problems updating the internship.';
					
				endif;
				
			endif;
			
		endif;	//	End Post check
		
		
		// --------------------------------------------------------------------------
		
		
		//	Get data...
		$this->data['internship'] 		= $this->internship_model->get_internship( $_id );
		$this->data['pay_frequencies']	= $this->internship_model->get_pay_frequency();
		$this->data['duration_terms'] 	= $this->internship_model->get_duration_terms();
		
		$this->data['employers'] 		= $this->companies_model->get_companies();
		
		$this->data['locations']	 	= $this->location_model->get_all();
		
		$this->data['sectors'] 			= $this->sector_model->get_all();
		
		
		// --------------------------------------------------------------------------
		
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'internships/edit',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Delete an internship
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function delete()
	{

		$id 	= $this->uri->segment(4);
		if ( $this->internship_model->delete_internship($id) == TRUE ) :
			$this->session->set_flashdata('success', 'Internship Deleted!');
			redirect('/admin/internships/index');
		else:
			$this->session->set_flashdata('error', 'There was a problem deleting this internship. Please check and try again.');
			redirect('/admin/internships/index');
		endif;

	}
}

/* End of file internships.php */
/* Location: ./application/modules/admin/controllers/internships.php */