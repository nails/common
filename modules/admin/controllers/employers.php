<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin - Employers
*
* Docs:			-
*
* Created:		01/06/2011
* Modified:		09/01/2012
*
* Description:	Manage companies within the system.
* 
*/

//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

class Employers extends Admin_Controller {
	
	
	private $just_pending;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constucts the class and sets defaults
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		parent::__construct();
		
		$this->just_pending = FALSE;
	}
	
	
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
		$d->priority				= 10;							//	Module's order in nav (unique).
		$d->name					= 'Employers';					//	Display name.
		$d->funcs['index']			= 'All Employers';				//	Sub-nav function.
		$d->funcs['pending']		= 'Pending Approval';			//	Sub-nav function.
		$d->funcs['transaction']	= 'Transaction History';		//	Sub-nav function.
		$d->announce_to				= array();						//	Which groups can access this module.
		$d->searchable				= FALSE;						//	Is module searchable?
		
		//	Dynamic
		$d->base_url		= basename( __FILE__, '.php' );	//	For link generation.
		
		return $d;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * All Employers
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		$this->data['page']->admin_m = 'index';
		
		//	Load model
		$this->load->model( 'companies_model' );
		
		//	Variable defaults
		$_order		= NULL;
		$_limit		= NULL;
		$_where		= NULL;
		$_filter	= NULL;
		
		
		// --------------------------------------------------------------------------
		
		//	Handle searching
		$_search = $this->input->get( 'search', NULL );
		
		// --------------------------------------------------------------------------
		
		
		//	First lot of pagination data
		//	Done like this due to the double call to get_companies() - need to apply conditionals.
		
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
			
			//	Just pending
			if ( $this->just_pending )
				$_filter['pending'] = TRUE;
			
			
		//	Second lot of pagination data; no, no, nonono no, no, no there's no $_limit!
		//	http://toaty.co.uk/limits
		
		$_page->total				= count( $this->companies_model->get_companies( NULL, $_filter, $_order, $_where, $_search ) );
		$_page->num_pages			= ceil( $_page->total / $_page->per_page );
		
		$this->data['pagination']	= $_page;
		
		// --------------------------------------------------------------------------
		
		//	Get the accounts
		
		$this->data['employers'] = $this->companies_model->get_companies( $_limit, $_filter, $_order, $_where, $_search );
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'employers/index',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * All Employers Pending Approval
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function pending()
	{
		$this->data['page']->admin_m = 'pending';
		
		//	Set the active conditional
		$this->just_pending = TRUE;
		
		$this->index();
		
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Edit Employer
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary & Pablo (adapted from Pablo's original temporary dataentry code)
	 **/
	public function edit()
	{
		$this->data['page']->admin_m = 'edit';
		
		// --------------------------------------------------------------------------
		
		//	Load models
		$this->load->model( 'companies_model' );
		$this->load->model( 'sector_model' );
		$this->load->model( 'package_model' );
		
		// --------------------------------------------------------------------------
		
		//	Page ID
		$this->data['id']	= $this->uri->segment(4, NULL);
		
		// --------------------------------------------------------------------------
		
		
		//	If updating (saving)
		if ( $this->input->post( 'update' ) ) :
		
			//	Load and set rules for form validation
			$this->load->library( 'form_validation' );
			$this->form_validation->set_rules( 'name',				'Name',					'xss_clean|required|htmlentities|callback_unique_if_diff' );
			$this->form_validation->set_rules( 'name_long',			'Name Long',			'xss_clean|htmlentities' );
			$this->form_validation->set_rules( 'url_id',			'Slug',					'xss_clean' );
			$this->form_validation->set_rules( 'description',		'Description',			'xss_clean' );
			$this->form_validation->set_rules( 'description_short',	'Description Short',	'xss_clean|strip_tags' );
			$this->form_validation->set_rules( 'email',				'Email',				'xss_clean|valid_email' );
			$this->form_validation->set_rules( 'telephone',			'Telephone',			'xss_clean' );
			$this->form_validation->set_rules( 'url_main',			'URL (Main)',			'xss_clean|valid_url' );
			$this->form_validation->set_rules( 'url_careers',		'URL (Careers)',		'xss_clean|valid_url' );
			$this->form_validation->set_rules( 'address_street',	'Address Street',		'xss_clean|htmlentities' );
			$this->form_validation->set_rules( 'address_town',		'Address Town',			'xss_clean|htmlentities' );
			$this->form_validation->set_rules( 'address_area',		'Address Area',			'xss_clean|htmlentities' );
			$this->form_validation->set_rules( 'address_postcode',	'Address Postcode',		'xss_clean|htmlentities' );
			$this->form_validation->set_rules( 'twitter',			'Twitter Username',		'xss_clean' );
			$this->form_validation->set_rules( 'facebook',			'Facebook Page',		'xss_clean' );
			$this->form_validation->set_rules( 'active',			'Active',				'xss_clean' );
			$this->form_validation->set_rules( 'package_id',		'Package',				'xss_clean|integer' );
			$this->form_validation->set_rules( 'sector',			'Sectors',				'xss_clean' );
			
			// --------------------------------------------------------------------------
			
			//	Set messages
			$this->form_validation->set_message( 'integer', 'Invalid selection for the %s field.' );
			
			// --------------------------------------------------------------------------
			
			//	If it all validates, run the update
			if ( $this->form_validation->run() ) :
			
				$_post = $this->input->post();
				
				//	Prepare data
				$_data['id']				= $_post['id'];
				$_data['url_id']			= $_post['url_id'];
				$_data['name']				= $_post['name'];
				$_data['name_long']			= $_post['name_long'];
				$_data['description']		= $_post['description'];
				$_data['description_short']	= $_post['description_short'];
				$_data['email']				= $_post['email'];
				$_data['telephone']			= $_post['telephone'];
				$_data['url_main']			= $_post['url_main'];
				$_data['url_careers']		= $_post['url_careers'];
				$_data['address_street']	= $_post['address_street'];
				$_data['address_town']		= $_post['address_town'];
				$_data['address_area']		= $_post['address_area'];
				$_data['address_postcode']	= $_post['address_postcode'];
				$_data['twitter']			= $_post['twitter'];
				$_data['active']			= $_post['active'];
				$_data['package_id']		= ( $_post['package_id'] ) ? $_post['package_id'] : NULL;
				$_data['facebook']			= $_post['facebook'];
				$_data['private_notes']		= $_post['private_notes'];
				
				//	Intern requirements
				$_data['company_size']				= $_post['company_size'];
				$_data['annual_intern_timings']		= $_post['annual_intern_timings'];
				$_data['education_level']			= $_post['education_level'];
				$_data['annual_intern_usage']		= $_post['annual_intern_usage'];
				$_data['typical_salary']			= $_post['typical_salary'];
				$_data['typical_duration']			= $_post['typical_duration'];
				$_data['why']						= $_post['why'];
				$_data['assessment']				= $_post['assessment'];
				$_data['interest']					= $_post['interest'];
				$_data['annual_recruitment_budget']	= $_post['annual_recruitment_budget'];
				$_data['staff_time']				= $_post['staff_time'];
				
				
				//	Fix for active
				if ( $_data['active'] == '' )
					$_data['active'] = NULL;
			
				//	Generate URL_ID based on the title, must be unique... (using CI's URL helper)
				$_data['url_id']				= url_title( $_data['name'], 'dash', TRUE );
				
				//	Make sure twitter has an @
				if ( ! empty( $_data['twitter'] ) )
					$_data['twitter'] = ( substr( $_data['twitter'], 0, 1 ) == '@' ) ? $_data['twitter'] : '@'.$_data['twitter'] ;
				
				//	Prep WYSIWYG
				$_data['description'] = str_replace( '<p><strong><span style="font-weight: normal;">', '<p>', $_data['description'] );
				$_data['description'] = str_replace( '</span></strong></p>', '</p>', $_data['description'] );
				
				// --------------------------------------------------------------------------
				
				//	Prep sectors
				$_chosen_sectors = array();
				
				if ( isset( $_post['sector'] ) && is_array( $_post['sector'] ) ) :
				
					foreach ( $_post['sector'] AS $s ) :
					
						$_chosen_sectors[] = array( 'sectors_id' => $s, 'employers_id' => $this->data['id'] );
						
					endforeach;
					
					$_data['sectors'] = $_chosen_sectors;
					
				endif;
				
				// --------------------------------------------------------------------------
				
				//	Do the update
				if ( $this->companies_model->update( $this->data['id'], $_data ) == TRUE ) :
					
					//	Prep and process logo
					if ( $_FILES['userfile']['name'] != '' ) :
					
					
						$_config['upload_path']		= CDN_PATH . 'employer_images';
						$_config['allowed_types']	= 'jpg|gif|png';
						$_config['encrypt_name']	= TRUE;
						$this->load->library( 'upload' );
						$this->upload->initialize( $_config );
				
						if ( ! $this->upload->do_upload( 'userfile' ) ) :
						
							$this->data['error'] = $this->upload->display_errors();	//	Custom error
						
						else :
						
							$_file = $this->upload->data();
							$_data = NULL;
							$_data['logo'] = $_file['file_name'];
							
							//	Remove old file
							$this->load->model( 'employer_model' );
							$_employer = $this->employer_model->get_employer_info( $this->data['id'] );
							@unlink( $_config['upload_path'] . '/' . $_employer->logo );
							
							// --------------------------------------------------------------------------
							
							//	Update the employer record
							$this->db->where( 'id', $this->data['id'] );
							$this->db->update( 'employers', $_data );
							
						endif;
					
					endif;
					
					// --------------------------------------------------------------------------
					
					//	Add credit to the employer if needed
					if ( ! $this->data['error'] && $this->input->post( 'add_credit' ) ) :
					
						//	Load transaction lib
						$this->load->library( 'transaction' );
						
						//	Generate a new transaction
						$_transaction = $this->transaction->create( $this->data['id'] , $this->input->post( 'add_credit' ), 'admin_deposit', 'Funding employer account (credit added by ' . active_user( 'first_name,last_name' ) . ')' );
						
						$this->transaction->add_credit( $this->input->post( 'add_credit' ), $this->data['id'] );
											
					endif;
					
					// --------------------------------------------------------------------------
					
					if (  ! $this->data['error'] ) :
					
						$this->session->set_flashdata( 'success', 'Employer edited successfully!' );
						redirect( 'admin/employers' );
						return;
						
					endif;
					
				else:
				
					$this->data['error'] = "There was a problem updating this employer. Please try again.";
					
				endif;
												
			endif;

		endif; //	End if updating (saving)
		
		// --------------------------------------------------------------------------
		
		
		//	Get data
		$this->data['employer']	= $this->companies_model->get_company( $this->data['id'] );
		$this->data['sectors']	= $this->sector_model->get_all();
		$this->data['packages']	= $this->package_model->get_flat();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'employers/edit/index',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Activate an employer - mark as active, award beta credits
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function activate()
	{
		//	Load model
		$this->load->model( 'companies_model' );
		
		if ( $this->companies_model->activate( $this->uri->segment( 4 ) ) ) :
		
			$this->session->set_flashdata( 'success', 'Employer activated successfully!' );
		
		else :
			
			$this->session->set_flashdata( 'error', 'Unable to activate employer.' );
			
		endif;
		
		redirect( $this->input->get( 'return_to' ) );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Deactivate an employer - mark as inactive
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function deactivate()
	{
		//	Load model
		$this->load->model( 'companies_model' );
		
		if ( $this->companies_model->deactivate( $this->uri->segment( 4 ) ) ) :
		
			$this->session->set_flashdata( 'success', 'Employer deactivated successfully' );
		
		else :
			
			$this->session->set_flashdata( 'error', 'Unable to deactivate employer.' );
			
		endif;
		
		redirect( $this->input->get( 'return_to' ) );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Browse the transaction history for the site
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function transaction()
	{
		$this->load->library( 'transaction' );
		
		// --------------------------------------------------------------------------
		
		//	Get data
		$this->db->where( 'et.status', 'VERIFIED' );
		$this->data['transactions'] = $this->transaction->get_all( NULL, $this->input->get( 'search' ) );
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'employers/transaction',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
}

/* End of file employers.php */
/* Location: ./application/modules/admin/controllers/employers.php */