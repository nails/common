<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin - Orders
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

class Orders extends Admin_Controller {
	
	
	private $status;
	
	
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
		$d->priority				= 12;						//	Module's order in nav (unique).
		$d->name					= 'Orders';					//	Display name.
		$d->funcs['index']			= 'All Orders';				//	Sub-nav function.
		$d->funcs['pending']		= 'Pending Orders';			//	Sub-nav function.
		$d->funcs['processing']		= 'Processing Orders';		//	Sub-nav function.
		$d->funcs['closing']		= 'Closing Orders';			//	Sub-nav function.
		$d->funcs['closed']			= 'Closed Orders';			//	Sub-nav function.
		$d->funcs['ready']			= 'Ready Orders';			//	Sub-nav function.
		$d->funcs['complete']		= 'Complete Orders';		//	Sub-nav function.
		$d->funcs['declined']		= 'Declined Orders';		//	Sub-nav function.
		$d->funcs['cancelled']		= 'Cancelled Orders';		//	Sub-nav function.
		$d->announce_to				= array();					//	Which groups can access this module.
		$d->searchable				= FALSE;					//	Is module searchable?
		
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
		
		//	Load models
		$this->load->model( 'internship_model' );
		$this->load->model( 'order_model' );
		
		//	Set defaults
		$this->status = FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse all orders
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		$this->data['page']->admin_m = 'index';
		
		// --------------------------------------------------------------------------
		
		//	Load model
		$this->load->model( 'companies_model' );
		
		// --------------------------------------------------------------------------
		
		//	Variable defaults
		$_order		= NULL;
		$_limit		= NULL;
		$_where		= NULL;
		
		// --------------------------------------------------------------------------
		
		//	Handle searching
		$_search = $this->input->get( 'search', NULL );
		
		// --------------------------------------------------------------------------
		
		
		//	First lot of pagination data
		//	Done like this due to the double call to get_all() - need to apply conditionals.
		
		$_page->order->column		= ( $this->uri->segment( 4 ) !== FALSE )		? $this->uri->segment( 4 ) : 'e.name';
		$_page->order->direction	= ( $this->uri->segment( 5 ) !== FALSE )		? $this->uri->segment( 5 ) : 'asc';
		$_page->per_page			= 25;
		$_page->page				= $this->uri->segment( 6, 0 );
		$_page->offset				= $_page->page * $_page->per_page;
		
			//	Set some query helper data
			
			//	Set Order
			$_order[0] = $_page->order->column;
			$_order[1] = $_page->order->direction;
			
			//	Set limits	
			$_limit[0] = $_page->per_page;
			$_limit[1] = $_page->offset;
			
			if ( $this->status )
				$_where['o.status'] = $this->status;
			
			
		//	Second lot of pagination data; no, no, nonono no, no, no there's no $_limit!
		//	http://toaty.co.uk/limits
		
		$_page->total				= count( $this->order_model->get_all( $_order, NULL, $_where, $_search ) );
		$_page->num_pages			= ceil( $_page->total / $_page->per_page );
		
		$this->data['pagination']	= $_page;
		
		// --------------------------------------------------------------------------
		
		//	Get the accounts
		
		$this->data['orders'] = $this->order_model->get_all( $_order, $_limit, $_where, $_search );
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'orders/index',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse pending orders
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function pending()
	{
		$this->data['page']->title = 'Orders &rsaquo; Pending';
		$this->status = 1;
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse active orders
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function processing()
	{
		$this->data['page']->title = 'Orders &rsaquo; Processing';
		$this->status = 2;
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse orders which are closing soon
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function closing()
	{
		$this->data['page']->title = 'Orders &rsaquo; Closing';
		$this->status = 3;
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse orders ready for admin signoff
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function closed()
	{
		$this->data['page']->title = 'Orders &rsaquo; Closed';
		$this->status = 4;
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse orders ready for employer selection
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function ready()
	{
		$this->data['page']->title = 'Orders &rsaquo; Ready';
		$this->status = 5;
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse complete orders
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function complete()
	{
		$this->data['page']->title = 'Orders &rsaquo; Complete';
		$this->status = 6;
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse declined orders
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function declined()
	{
		$this->data['page']->title = 'Orders &rsaquo; Declined';
		$this->status = 7;
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse cancelled orders
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function cancelled()
	{
		$this->data['page']->title = 'Orders &rsaquo; Cancelled';
		$this->status = 8;
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Set the status of an order
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function set_status()
	{
		//	Get vars
		$_new_status = $this->uri->segment( 4 );
		$_order_id	= $this->uri->segment( 5 );
		$_return_to	= $this->input->get( 'return_to' );
		
		//	New status is valid?
		if ( array_search( $_new_status, array(1,2,3,4,5,6,7,8) ) === FALSE ) :
		
			$this->session->set_flashdata( 'error', 'Unable to update status; new status not recognised.' );
			redirect( $_return_to );
			return;
		
		endif;
		
		//	Order ID appears valid?
		if ( $_order_id === FALSE || ! is_numeric( $_order_id  ) ) :
		
			$this->session->set_flashdata( 'error', 'Invalid Order ID.' );
			redirect( $_return_to );
			return;
		
		endif;
		
		
		if ( $this->order_model->set_status( $_order_id, $_new_status ) ) :
		
			$this->session->set_flashdata( 'success', 'Order #' . $_order_id . ' updated successfully.' );
			redirect( $_return_to );
			return;
		
		else :
		
			$this->session->set_flashdata( 'error', 'Unable to update order #' . $_order_id . ', perhaps the ID is invalid?' );
			redirect( $_return_to );
			return;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse order's matched candidates
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function show_matches()
	{
		//	Method details and vars
		$this->data['page']->admin_m	= 'show_matches';
		
		//	Get the order details
		$this->data['order'] = $this->order_model->get_order( $this->uri->segment( 4 ) );
		
		//	Get data
		$this->data['users'] = $this->order_model->get_matches( $this->uri->segment( 4 ) );
				
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'orders/show_matches',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function complete_order()
	{
		//	Check the order exists
		$_oid		= $this->uri->segment( 4 );
		$_return_to	= $this->input->get( 'return_to' );
		
		// --------------------------------------------------------------------------
		
		$this->data['order'] = $this->order_model->get_order( $_oid );
		
		// --------------------------------------------------------------------------
		
		if ( empty( $this->data['order'] ) ) :
		
			$this->session->set_flashdata( 'error', 'Unknown Order ID' );
			redirect( $_return_to );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Process the order if required
		if ( $this->input->post( 'process' ) ) :
		
			$chosen_ones = $this->input->post( 'chosen_one' );
			
			
			if ( ! empty( $chosen_ones ) ) :
		
				if ( $this->order_model->generate_shortlist( $_oid ) ) :
				
					$this->session->set_flashdata( 'success', 'Shortlist for order ' . $this->data['order']->ref . ' generated and sent to employer,');
					redirect( $_return_to );
					return;
				
				endif;
				
			else :
			
				$this->data['error'] = 'You must select at least one candidate to shortlist.';
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Get this order's top 100
		$this->db->limit( 100 );
		$this->data['order']->matches = $this->order_model->get_matches( $_oid );
		
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'orders/complete_order',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );

	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	/**
	 * Edit an order
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function edit()
	{
		//	Method details
		$this->data['page']->admin_m	= 'edit';
		
		//	Don't be doin' stoopid stuff now!
		$this->data['message'] = 'This form is for <strong>ADVANCED</strong> use only; only change data if you <strong>know what you\'re doing</strong>!';
		
		// --------------------------------------------------------------------------
		
		//	Define vars
		$_return_to = $this->input->get( 'return_to' );
		$_return_to = ( empty( $return_to ) ) ? 'admin/orders' : $return_to;
		$_id		= $this->uri->segment(4);
		
		// --------------------------------------------------------------------------
		
		//	Load models
		$this->load->model( 'internship_model' );
		
		// --------------------------------------------------------------------------
		
		//	Get data...
		$this->data['order'] 		= $this->order_model->get_order( $_id );
		$this->data['internship'] 	= $this->internship_model->get_internship( $_id );
		
		// --------------------------------------------------------------------------
			
		//	Validate if we're saving, otherwise get the data and display the edit form
		if ( $this->input->post( 'save' ) ) :
			
			//	Load validation library
			$this->load->library( 'form_validation' );
			
			//	Set rules
			$this->form_validation->set_rules( 'employer_id',			'Employer',			'required|strip_tags' );
			$this->form_validation->set_rules( 'user_id',				'User',				'required|strip_tags' );
			$this->form_validation->set_rules( 'internship_id',			'Internship',		'required|is_natural_no_zero' );
			$this->form_validation->set_rules( 'date_created',			'Date Created',		'required' );
			$this->form_validation->set_rules( 'cost',					'Cost',				'required' );
			$this->form_validation->set_rules( 'ref',					'Reference',		'required' );
			$this->form_validation->set_rules( 'status',				'Status',			'required' );
			
			//	Set messages
			$this->form_validation->set_message( 'is_natural_no_zero', 'The %s field is required' );
			
			//	Validate
			if ( $this->form_validation->run() ) :
			
				$data = array(
					'employer_id' 		=> $this->input->post( 'employer_id' ),
					'user_id' 			=> $this->input->post( 'user_id' ),
					'internship_id' 	=> $_id,
					'date_created' 		=> $this->input->post( 'date_created' ),
					'cost'				=> $this->input->post( 'cost' ),
					'ref'				=> $this->input->post( 'ref' ),
					'status'			=> $this->input->post( 'status' )
				);
								
				if ( $this->order_model->edit_order( $_id, $data ) ) :
				
					$this->session->set_flashdata('success', 'Order Updated Successfully!');
					redirect ( $_return_to );
					
				endif;
				
			endif;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'orders/edit',		$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * View an order's history
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function history()
	{
		$_oid = $this->uri->segment( 4 );
		
		// --------------------------------------------------------------------------
		
		//	Get data
		$this->data['history'] 		= $this->order_model->get_history( $_oid );
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'orders/history',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * View an order's feedback
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function feedback()
	{
		$_oid = $this->uri->segment( 4 );
		
		// --------------------------------------------------------------------------
		
		//	Get Employer feedback
		$this->data['feedback'] = $this->order_model->get_feedback_for_order( $_oid );
		
		// --------------------------------------------------------------------------
		
		//	Load assets
		$this->asset->load( 'jquery-ui.custom.min.js' );
		$this->asset->load( 'jquery.ui.stars.js' );
		$this->asset->load( 'jquery.ui.stars.min.css' );
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header_blank',	$this->data );
		$this->load->view( 'orders/feedback',			$this->data );
		$this->load->view( 'structure/footer_blank',	$this->data );
	}
}

/* End of file admin.php */
/* Location: ./application/modules/admin/controllers/admin.php */