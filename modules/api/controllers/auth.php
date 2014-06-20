<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth API
 *
 * Description:	This controller handles auth API methods
 *
 **/

require_once '_api.php';

/**
 * OVERLOADING NAILS' API MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Auth extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Where are we returning user to?
		$this->data['return_to'] = $this->input->get( 'return_to' );
	}


	// --------------------------------------------------------------------------


	public function login()
	{
		$_email		= $this->input->post( 'email' );
		$_password	= $this->input->post( 'password' );
		$_remember	= $this->input->post( 'remember' );
		$_out		= array();

		$_login		= $this->auth_model->login( $_email, $_password, $_remember );

		if ( $_login ) :

			/**
			 * User was recognised and permitted to log in. Final check to
			 * determine whether they are using a temporary password or not.
			 *
			 * $login will be an array containing the keys first_name, last_login, homepage;
			 * the key temp_pw will be present if they are using a temporary password.
			 *
			 **/

			if ( isset( $_login['temp_pw'] ) ) :

				/**
				 * Temporary password detected, log user out and redirect to
				 * temp password reset page.
				 *
				 * temp_pw will be an array containing the user's ID and hash
				 *
				 **/

				$_return_to	= ( $this->data['return_to'] ) ? '?return_to='.urlencode( $this->data['return_to'] ) : NULL;

				$this->auth_model->logout();

				$_out['status']	= 401;
				$_out['error']	= 'Temporary Password';
				$_out['code']	= 2;
				$_out['goto']	= site_url( 'auth/reset_password/' . $_login['temp_pw']['id'] . '/' . $_login['temp_pw']['hash'] . $_return_to );

			else :

				//	Finally! Send this user on their merry way...
				$_first_name = $_login['first_name'];

				if ( $_login['last_login'] ) :

					$this->load->helper( 'date' );
					$this->config->load( 'auth' );

					$_last_login = $this->config->item( 'auth_show_nicetime_on_login' ) ? nice_time( strtotime( $_login['last_login'] ) ) : user_datetime( $_login['last_login'] );

					if ( $this->config->item( 'auth_show_last_ip_on_login' ) ) :

						$_last_ip = $_login['last_ip'];

						$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_with_ip', array( $_first_name, $_last_login, $_last_ip ) ) );

					else :

						$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome', array( $_first_name, $_last_login ) ) );

					endif;

				else :

					$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_notime', array( $_first_name ) ) );

				endif;

				$_redirect = ( $this->data['return_to'] ) ? $this->data['return_to'] : $_login['homepage'];

				// --------------------------------------------------------------------------

				//	Generate an event for this log in
				create_event( 'did_log_in', $_login['user_id'], 0, NULL, array( 'method' => 'api' ) );

				// --------------------------------------------------------------------------

				//	Login failed
				$_out['goto']	= site_url( $_redirect );

			endif;

		else :

			//	Login failed
			$_out['status']	= 401;
			$_out['error']	= $this->auth_model->get_errors();
			$_out['code']	= 1;

		endif;

		// --------------------------------------------------------------------------

		$this->_out( $_out );
	}


	// --------------------------------------------------------------------------


	public function logout()
	{
		//	Only create the event if the user is logged in
		if ( $this->user_model->is_logged_in() ) :

			//	Generate an event for this log in
			create_event( 'did_log_out', active_user( 'id' ) );

			// --------------------------------------------------------------------------

			//	Log user out
			$this->auth_model->logout();

		endif;

		// --------------------------------------------------------------------------

		$this->_out();
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' API MODULES
 *
 * The following block of code makes it simple to extend one of the core API
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_AUTH' ) ) :

	class Auth extends NAILS_Auth
	{
	}

endif;

/* End of file auth.php */
/* Location: ./modules/api/controllers/auth.php */