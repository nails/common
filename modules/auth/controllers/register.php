<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Auth [Register]
*
* Docs:			-
*
* Created:		12/11/2010
* Modified:		04/04/2012
*
* Description:	-
* 
*/
class Register extends NAILS_Controller {
	
	
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
		
		// --------------------------------------------------------------------------
		
		//	Load libraries
		$this->load->library( 'form_validation' );
		
		// --------------------------------------------------------------------------
		
		//	Load model
		$this->load->model( 'auth_model' );
		
		// --------------------------------------------------------------------------
		
		//	Load language files
		$this->nails->load_lang( 'english/auth',	'modules/auth/language/english/auth');
		
		// --------------------------------------------------------------------------
		
		//	Specify a default title for this page
		$this->data['page']->title = 'Register';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Display registration form, validate data and create user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		//	If you're logged in you shouldn't be accessing this method
		if ( $this->user->is_logged_in() ) :
		
			$this->session->set_flashdata( 'error', lang( 'no_access_already_logged_in', active_user( 'email' ) ) );
			redirect( '/' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If there's POST data attempt to log user in
		if ( $this->input->post() ) :
		
			//	Validate input
			$this->form_validation->set_rules( 'first_name',	'First Name',			'required|xss_clean' );
			$this->form_validation->set_rules( 'last_name',		'Surname',				'required|xss_clean' );
			$this->form_validation->set_rules( 'email',			'Email',				'required|xss_clean|valid_email|is_unique[user.email]|is_unique[user.email_secondary]' );
			$this->form_validation->set_rules( 'password',		'Password',				'required|xss_clean' );
			$this->form_validation->set_rules( 'terms',			'Terms & Conditions',	'required|xss_clean' );
			
			// --------------------------------------------------------------------------
			
			//	Change default messages
			$this->form_validation->set_message( 'required',				lang( 'required_field' ) );
			$this->form_validation->set_message( 'valid_email',				lang( 'valid_email' ) );
			$this->form_validation->set_message( 'alpha_dash_space_accent',	lang( 'alpha_dash_space_accent' ) );
			$this->form_validation->set_message( 'matches',					lang( 'matches' ) );
			$this->form_validation->set_message( 'is_unique',				lang( 'is_unique', site_url( 'auth/forgotten_password' ) ) );
			
			// --------------------------------------------------------------------------
			
			//	Run validation
			if ( $this->form_validation->run() == TRUE ) :
			
				//	Attempt the registration
				$email		= $this->input->post( 'email' );
				$password	= $this->input->post( 'password' );
				$remember	= $this->input->post( 'remember' );
				
				// --------------------------------------------------------------------------
				
				//	Meta data
				$data['first_name']	= $this->input->post( 'first_name' );
				$data['last_name']	= $this->input->post( 'last_name' );
				$data['marketing']	= $this->input->post( 'marketing' );
				
				// --------------------------------------------------------------------------
				
				//	Handle referrals
				if ( $this->session->userdata( 'referred_by' ) ) :
				
					$data['referred_by'] = $this->session->userdata( 'referred_by' );
				
				endif;
				
				// --------------------------------------------------------------------------
				
				//	Create new user, group 2 (member)
				$_uid = $this->user->create( $email, $password, 2, $data );
				
				if ( $_uid ) :
				
					//	Some nice data...
					$this->data['email']	= $email;
					$this->data['user_id']	= $_uid['id'];
					$this->data['hash']		= $_uid['activation'];
					
					// --------------------------------------------------------------------------
					
					//	Registration was successfull, send the activation email...
					$this->load->library( 'emailer' );
					
					//	Initialise vars
					$_data = new StdClass();
					$_data->data = array();
					
					//	Fill 'em up!
					$_data->to						= $this->data['email'];
					$_data->type					= 'register_activate';
					$_data->data['first_name']		= $data['first_name'];
					$_data->data['user_id']			= $_uid['id'];
					$_data->data['activation_code']	= $_uid['activation'];
					
					// --------------------------------------------------------------------------
					
					//	Send the email
					$this->emailer->send_now( $_data );
					
					// --------------------------------------------------------------------------
					
					//	Log the user in
					$this->user->set_login_data( $_uid['id'], $email, 2 );
					
					// --------------------------------------------------------------------------
					
					//	Redirect to the group homepage
					$_user = $this->user->get_user( $_uid['id'] );
					$this->session->set_flashdata( 'success', '<strong>Welcome, ' . $_user->first_name . '!</strong>' );
					redirect( $_user->group_homepage );
					return;
				
				endif;
			
			else:
			
				$this->data['error'] = lang( 'register_error' );
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load the views; using the auth_model view loader as we need to check if
		//	an overload file exists which should be used instead
		
		$this->nails->load_view( 'structure/header',	'views/structure/header',			$this->data );
		$this->nails->load_view( 'auth/register/form',	'modules/auth/views/register/form',	$this->data );
		$this->nails->load_view( 'structure/footer',	'views/structure/footer',			$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Allows a user to resend their activation email
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function resend()
	{
		$_id	= $this->uri->segment( 4 );
		$_hash	= $this->uri->segment( 5 );
		
		// --------------------------------------------------------------------------
		
		//	We got details?
		if ( $_id === FALSE || $_hash === FALSE ):
		
			$this->session->set_flashdata( 'error', 'Invalid credentials supplied. Unable to resend activation email. <small class="right">Error #1</small>' );
			redirect( '/' );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Valid user?
		$_u = $this->user->get_user( $_id );
		
		if ( $_u === FALSE ) :
		
			$this->session->set_flashdata( 'error', 'Invalid credentials supplied. Unable to resend activation email.  <small class="right">Error #2</small>' );
			redirect( '/' );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Account active?
		if ( $_u->active ) :
		
			$this->session->set_flashdata( 'error', 'Account already active, please try logging in. <small class="right">Error #3</small>' );
			redirect( 'auth/login' );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Hash match?
		if ( md5( $_u->activation_code ) != $_hash ) :
		
			$this->session->set_flashdata( 'error', 'Invalid credentials supplied. Unable to resend activation email. <small class="right">Error #4</small>' );
			redirect( '/' );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	All good, resend now
		
		//	Load up emailer
		$this->load->library( 'emailer' );
		
		// --------------------------------------------------------------------------
		
		//	Send user their welcome email
		
		//	Initialise vars
		$_data = new StdClass();
		$_data->data = array();
		
		$_data->to						= $_u->email;
		$_data->type					= 'register_activate_resend';
		$_data->data['first_name']		= $_u->first_name;
		$_data->data['user_id']			= $_u->id;
		$_data->data['activation_code']	= $_u->activation_code;
		
		// --------------------------------------------------------------------------
		
		//	Send it off now
		$this->emailer->send_now( $_data );
		
		// --------------------------------------------------------------------------
		
		//	Set some data for the view
		$this->data['email'] = $_u->email;
		
		// --------------------------------------------------------------------------
		
		//	Load the views; using the auth_model view loader as we need to check if
		//	an overload file exists which should be used instead
		
		$this->nails->load_view( 'structure/header',		'views/structure/header',				$this->data );
		$this->nails->load_view( 'auth/register/resend',	'modules/auth/views/register/resend',	$this->data );
		$this->nails->load_view( 'structure/footer',		'views/structure/footer',				$this->data );

	}
}

/* End of file register.php */
/* Location: ./application/modules/auth/controllers/register.php */