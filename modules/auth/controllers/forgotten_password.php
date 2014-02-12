<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [forgotten password]
 *
 * Description:	This controller handles the resetting of a user's password
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

class NAILS_Forgotten_Password extends NAILS_Auth_Controller
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

		//	Load libraries
		$this->load->library( 'form_validation' );

		// --------------------------------------------------------------------------

		//	Specify a default title for this page
		$this->data['page']->title = lang( 'auth_title_forgotten_password' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Reset password form
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function index()
	{
		//	If user is logged in they shouldn't be accessing this method
		if ( $this->user->is_logged_in() ) :

			$this->session->set_flashdata( 'error', lang( 'auth_no_access_already_logged_in', active_user( 'email' ) ) );
			redirect( '/' );

		endif;

		//	If there's POST data attempt to validate the user
		if ( $this->input->post() || $this->input->get( 'identifier' ) ) :

			//	Define vars
			$_identifier = $this->input->post( 'identifier' );

			//	Override with the $_GET variable if POST failed to return anything.
			//	Populate the $_POST var with some data so form validation continues
			//	as normal, feels hacky but works.

			if ( ! $_identifier && $this->input->get( 'identifier' ) ) :

				$_POST['identifier']	= $this->input->get( 'identifier' );
				$_identifier			= $this->input->get( 'identifier' );

			endif;

			// --------------------------------------------------------------------------

			//	Set rules
			//	The rules vary depending on what login methods are enabled.
			switch( APP_NATIVE_LOGIN_USING ) :

				case 'EMAIL' :

					$this->form_validation->set_rules( 'identifier',	'Email',	'required|xss_clean|trim|valid_email' );

				break;

				// --------------------------------------------------------------------------

				case 'USERNAME' :

					$this->form_validation->set_rules( 'identifier',	'Username',	'required|xss_clean|trim' );

				break;

				// --------------------------------------------------------------------------

				case 'BOTH' :
				default:

					$this->form_validation->set_rules( 'identifier',	'Username or Email',	'xss_clean|trim' );

				break;

			endswitch;

			// --------------------------------------------------------------------------

			//	Override default messages
			$this->form_validation->set_message( 'required',	lang( 'fv_required' ) );
			$this->form_validation->set_message( 'valid_email',	lang( 'fv_valid_email' ) );

			// --------------------------------------------------------------------------

			//	Run validation
			if ( $this->form_validation->run() ) :

				//	Attempt to reset password
				if ( $this->user->set_password_token( $_identifier ) ) :

					//	Send email to user; load library
					$this->load->library( 'emailer' );

					// --------------------------------------------------------------------------

					//	Define basic email data
					switch( APP_NATIVE_LOGIN_USING ) :

						case 'EMAIL' :

							$this->data['reset_user'] = $this->user->get_by_username( $_identifier );

						break;

						// --------------------------------------------------------------------------

						case 'USERNAME' :

							$this->data['reset_user'] = $this->user->get_by_username( $_identifier );

						break;

						// --------------------------------------------------------------------------

						case 'BOTH' :
						default:

							$this->load->helper( 'email' );

							if ( valid_email( $_identifier ) ) :

								$this->data['reset_user'] = $this->user->get_by_email( $_identifier );

							else :

								$this->data['reset_user'] = $this->user->get_by_username( $_identifier );

							endif;

						break;

					endswitch;

					$_data			= new stdClass();
					$_data->to_id	= $this->data['reset_user']->id;
					$_data->type	= 'forgotten_password';

					// --------------------------------------------------------------------------

					//	Add data for the email view
					$_code = explode( ':', $this->data['reset_user']->forgotten_password_code );

					$_data->data							= array();
					$_data->data['first_name']				= title_case( $this->data['reset_user']->first_name );
					$_data->data['forgotten_password_code']	= $_code[1];
					$_data->data['identifier']				= $_identifier;

					// --------------------------------------------------------------------------

					//	Send user the password reset email
					if ( $this->emailer->send( $_data ) ) :

						$this->data['success'] = lang( 'auth_forgot_success' );

					else :

						$this->data['error'] = lang( 'auth_forgot_email_fail' );

					endif;

				else :

					switch( APP_NATIVE_LOGIN_USING ) :

						case 'EMAIL' :

							$this->data['error'] = lang( 'auth_forgot_code_not_set_email', $_identifier );

						break;

						// --------------------------------------------------------------------------

						case 'USERNAME' :

							$this->data['error'] = lang( 'auth_forgot_code_not_set_username', $_identifier );

						break;

						// --------------------------------------------------------------------------

						case 'BOTH' :
						default:

							$this->data['error'] = lang( 'auth_forgot_code_not_set' );

						break;

					endswitch;

				endif;

			else :

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load the views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'auth/password/forgotten',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Validate a code
	 *
	 * @access	private
	 * @param	string	$code	The code to validate
	 * @return	void
	 */
	public function _validate( $code )
	{
		//	Attempt to verify code
		$_new_pw = $this->user->validate_password_token( $code );

		// --------------------------------------------------------------------------

		//	Determine outcome of validation
		if ( $_new_pw === 'EXPIRED' ) :

			//	Code has expired
			$this->data['error'] = lang( 'auth_forgot_expired_code' );

		elseif ( $_new_pw === FALSE ) :

			//	Code was invalid
			$this->data['error'] = lang( 'auth_forgot_invalid_code' );

		else :

			//	Everything worked!
			$this->data['new_password'] = $_new_pw;

			// --------------------------------------------------------------------------

			//	Set some flashdata for the login page when they go to it; just a little reminder
			$this->session->set_flashdata( 'notice', lang( 'auth_forgot_reminder', $_new_pw ) );

			// --------------------------------------------------------------------------

			//	Load the views; using the auth_model view loader as we need to check if
			//	an overload file exists which should be used instead

			$this->load->view( 'structure/header',				$this->data );
			$this->load->view( 'auth/password/forgotten_reset',	$this->data );
			$this->load->view( 'structure/footer',				$this->data );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Load the views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'auth/password/forgotten',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Route requests to the right method
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function _remap( $method )
	{
		//	If you're logged in you shouldn't be accessing this method
		if ( $this->user->is_logged_in() ) :

			$this->session->set_flashdata( 'error', lang( 'auth_no_access_already_logged_in', active_user( 'email' ) ) );
			redirect( '/' );

		endif;

		// --------------------------------------------------------------------------

		if ( $method == 'index' ) :

			$this->index();

		else :

			$this->_validate( $method );

		endif;
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

	class Forgotten_Password extends NAILS_Forgotten_Password
	{
	}

endif;

/* End of file forgotten_password.php */
/* Location: ./application/modules/auth/controllers/forgotten_password.php */