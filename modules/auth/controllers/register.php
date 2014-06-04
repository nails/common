<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [Register]
 *
 * Description:	Handles user registration
 *
 **/

/**
 * OVERLOADING NAILS' AUTH MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

require_once '_auth.php';

class NAILS_Register extends NAILS_Auth_Controller
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Is registration enabled
		if ( ! APP_USER_ALLOW_REGISTRATION ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Load libraries
		$this->load->library( 'form_validation' );

		// --------------------------------------------------------------------------

		//	Specify a default title for this page
		$this->data['page']->title = lang( 'auth_title_register' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Display registration form, validate data and create user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function index()
	{
		//	If you're logged in you shouldn't be accessing this method
		if ( $this->user->is_logged_in() ) :

			$this->session->set_flashdata( 'error', lang( 'auth_no_access_already_logged_in', active_user( 'email' ) ) );
			redirect( '/' );

		endif;

		// --------------------------------------------------------------------------

		//	If there's POST data attempt to log user in
		if ( $this->input->post() ) :

			//	Validate input
			$this->form_validation->set_rules( 'first_name',	'',	'required|xss_clean' );
			$this->form_validation->set_rules( 'last_name',		'',	'required|xss_clean' );
			$this->form_validation->set_rules( 'password',		'',	'required|xss_clean' );

			if ( APP_NATIVE_LOGIN_USING == 'EMAIL' ) :

				$this->form_validation->set_rules( 'email',	'',	'xss_clean|required|valid_email|is_unique[' . NAILS_DB_PREFIX . 'user_email.email]' );

				if ( $this->input->post( 'username' ) ) :

					$this->form_validation->set_rules( 'email',	'',	'xss_clean|is_unique[' . NAILS_DB_PREFIX . 'user.username]' );

				endif;

			elseif ( APP_NATIVE_LOGIN_USING == 'USERNAME' ) :

				$this->form_validation->set_rules( 'username',	'',	'xss_clean|required|is_unique[' . NAILS_DB_PREFIX . 'user.username]' );

				if ( $this->input->post( 'email' ) ) :

					$this->form_validation->set_rules( 'email',	'',	'xss_clean|valid_email|is_unique[' . NAILS_DB_PREFIX . 'user_email.email]' );

				endif;

			elseif ( APP_NATIVE_LOGIN_USING == 'BOTH' ) :

				$this->form_validation->set_rules( 'email',		'',	'xss_clean|required|valid_email|is_unique[' . NAILS_DB_PREFIX . 'user_email.email]' );
				$this->form_validation->set_rules( 'username',	'',	'xss_clean|required|is_unique[' . NAILS_DB_PREFIX . 'user.username]' );

			endif;

			// --------------------------------------------------------------------------

			//	Change default messages
			$this->form_validation->set_message( 'required',				lang( 'fv_required' ) );
			$this->form_validation->set_message( 'valid_email',				lang( 'fv_valid_email' ) );

			if ( APP_NATIVE_LOGIN_USING == 'EMAIL' ) :

				$this->form_validation->set_message( 'is_unique',			lang( 'auth_register_email_is_unique', site_url( 'auth/forgotten_password' ) ) );

			elseif ( APP_NATIVE_LOGIN_USING == 'USERNAME' ) :

				$this->form_validation->set_message( 'is_unique',			lang( 'auth_register_username_is_unique', site_url( 'auth/forgotten_password' ) ) );

			elseif ( APP_NATIVE_LOGIN_USING == 'BOTH' ) :

				$this->form_validation->set_message( 'is_unique',			lang( 'auth_register_identity_is_unique', site_url( 'auth/forgotten_password' ) ) );

			endif;

			// --------------------------------------------------------------------------

			//	Run validation
			if ( $this->form_validation->run() ) :

				//	Attempt the registration
				$_data					= array();
				$_data['email']			= $this->input->post( 'email' );
				$_data['username']		= $this->input->post( 'username' );
				$_data['group_id']		= APP_USER_DEFAULT_GROUP;
				$_data['password']		= $this->input->post( 'password' );
				$_data['first_name']	= $this->input->post( 'first_name' );
				$_data['last_name']		= $this->input->post( 'last_name' );

				// --------------------------------------------------------------------------

				//	Handle referrals
				if ( $this->session->userdata( 'referred_by' ) ) :

					$_data['referred_by'] = $this->session->userdata( 'referred_by' );

				endif;

				// --------------------------------------------------------------------------

				//	Create new user
				$_new_user = $this->user->create( $_data );

				if ( $_new_user ) :

					//	Fetch user and group data
					$_group	= $this->user_group->get_by_id( $_data['group_id'] );

					// --------------------------------------------------------------------------

					//	Log the user in
					$this->user->set_login_data( $_new_user->id );

					// --------------------------------------------------------------------------

					//	Create an event for this event
					create_event( 'did_register', $_new_user->id, 0, NULL, array( 'method' => 'native' ) );

					// --------------------------------------------------------------------------

					//	Redirect to the group homepage
					//	TODO: There should be the option to enable/disable forced activation

					$this->session->set_flashdata( 'success', lang( 'auth_register_flashdata_welcome', $_new_user->first_name ) );

					$_redirect = $_group->registration_redirect ? $_group->registration_redirect : $_group->default_homepage;

					redirect( $_redirect );
					return;

				else :

					$this->data['error'] = 'Could not create new user account. ' . $this->user->last_error();

				endif;

			else:

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load the views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'auth/register/form',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Allows a user to resend their activation email
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function resend()
	{
		$_id	= $this->uri->segment( 4 );
		$_hash	= $this->uri->segment( 5 );

		// --------------------------------------------------------------------------

		//	We got details?
		if ( $_id === FALSE || $_hash === FALSE ):

			$this->session->set_flashdata( 'error', lang( 'auth_register_resend_invalid' ) );
			redirect( '/' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Valid user?
		$_u = $this->user->get_by_id( $_id );

		if ( $_u === FALSE ) :

			$this->session->set_flashdata( 'error', lang( 'auth_register_resend_invalid' ) );
			redirect( '/' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Account active?
		if ( $_u->email_is_verified ) :

			$this->session->set_flashdata( 'message', lang( 'auth_register_resend_already_active', site_url( 'auth/login' ) ) );
			redirect( 'auth/login' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Hash match?
		if ( md5( $_u->activation_code ) != $_hash ) :

			$this->session->set_flashdata( 'error', lang( 'auth_register_resend_invalid' ) );
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

		//	Load the views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'auth/register/resend',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );

	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' AUTH MODULE
 *
 * The following block of code makes it simple to extend one of the core auth
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION' ) ) :

	class Register extends NAILS_Register
	{
	}

endif;

/* End of file register.php */
/* Location: ./application/modules/auth/controllers/register.php */