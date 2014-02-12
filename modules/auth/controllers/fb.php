<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [Facebook]
 *
 * Description:	This controller handles connecting accounts to Facebook
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

class NAILS_Fb extends NAILS_Auth_Controller
{
	protected $_return_to;
	protected $_return_to_fail;
	protected $_register_token;


	// --------------------------------------------------------------------------


	/**
	 * Construct the class, set some defaults
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Ensure the sub-module is enabled
		if ( ! module_is_enabled( 'auth[facebook]' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Load the Facebook Library
		$this->load->library( 'Facebook_connect', NULL, 'fb' );

		// --------------------------------------------------------------------------

		//	Set a return_to if available
		$this->_register_use_return	= TRUE;
		$this->_return_to			= $this->input->get( 'return_to' );

		//	If nothing, check the 'nailsFBConnectReturnTo' GET var which may be passed back
		if ( ! $this->_return_to ) :

			$this->_return_to = $this->input->get( 'nailsFBConnectReturnTo' );

			//	Still empty? Group homepage
			if ( ! $this->_return_to ) :

				$this->_return_to			= active_user( 'group_homepage' );
				$this->_register_use_return	= FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Set a return_to_fail if available
		$this->_return_to_fail = $this->input->get( 'return_to_fail' );

		//	If nothing, check the GET var which may be passed back
		if ( ! $this->_return_to_fail ) :

			$this->_return_to_fail = $this->input->get( 'nailsFBConnectReturnToFail' );

			if ( ! $this->_return_to_fail ) :

				//	Fallback to the value of $this->_return_to
				$this->_return_to_fail = $this->_return_to;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Default register token is empty
		$this->_register_token = array();
	}


	// --------------------------------------------------------------------------

	/* ! CONNECTING TO FACEBOOK */

	// --------------------------------------------------------------------------


	/**
	 * Handle the Facebook connect process.
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function connect()
	{
		switch ( $this->uri->segment( 4 ) ) :

			case 'verify' :		$this->_connect_verify();	break;
			case 'connect' :
			default:			$this->_connect_connect();	break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Initiate a connection request
	 *
	 * @access	protected
	 * @param	none
	 * @return	void
	 **/
	protected function _connect_connect()
	{
		//	If the Facebook is already linked then we need to acknowledge it
		if ( ! $this->input->get( 'force' ) && $this->fb->user_is_linked() ) :

			$this->session->set_flashdata( 'message', lang( 'auth_social_already_linked', 'Facebook' ) );
			$this->_connect_fail();
			return;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'token' ) ) :

			//	Drop a cookie
			$this->input->set_cookie( 'fbRegisterToken', $this->input->get( 'token' ), 900 );

		endif;

		// --------------------------------------------------------------------------

		$this->_redirect( $this->fb->get_login_url( $this->_return_to, $this->_return_to_fail ), TRUE );
	}


	// --------------------------------------------------------------------------


	/**
	 * Verify the connection request
	 *
	 * @access	private
	 * @param	none
	 * @return	void
	 **/
	private function _connect_verify()
	{
		//	Handle the user denying the request
		if ( isset( $_get['error'] ) && $_get['error_reason'] == 'user_denied' ) :

			$this->_connect_fail();
			return;

		else :

			//	Fetch the user's access token
			$_access_token = $this->fb->get_access_token( $this->input->get( 'code' ), $this->_return_to, $this->_return_to_fail );

			if ( ! $_access_token ) :

				$this->session->set_flashdata( 'error', lang( 'auth_social_no_access_token', 'Facebook' ) );
				$this->_connect_fail();
				return;

			endif;

			// --------------------------------------------------------------------------

			//	Alriiiiight, we haz token - set it and proceed with success method
			$this->fb->set_access_token( $_access_token['access_token'] );
			$this->_connect_success();

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Handles a successfull connection request, This method can be overridden if more
	 * than the basic data (i.e email, name, gender) is needed for account creation.
	 * By this point the FB library is set up with the user's access token.
	 *
	 * @access	protected
	 * @return	void
	 **/
	protected function _connect_success()
	{
		//	Get some information about the user
		$_me = $this->fb->api( '/me' );

		// --------------------------------------------------------------------------

		//	First up, check if the user has previously connected this Facebook account
		//	to another registered account

		$_user = $this->user->get_by_fbid( $_me['id'] );

		if ( $this->user->is_logged_in() && $_user ) :

			//	This Facebook ID is already in use, tell the user so and prevent anything else from happening.
			$this->session->set_flashdata( 'error', lang( 'auth_social_account_in_use', array( 'Facebook', APP_NAME ) ) );
			$this->_connect_fail();
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Test for a register token, if there verify and store it in memory then delete

		$this->_register_token = get_cookie( 'fbRegisterToken' );

		if ( $this->_register_token ) :

			$this->_register_token = $this->encrypt->decode( $this->_register_token, APP_PRIVATE_KEY );

			if ( $this->_register_token ) :

				$this->_register_token = unserialize( $this->_register_token );

				if ( $this->_register_token ) :

					if ( ! isset( $this->_register_token['ip'] ) || $this->_register_token['ip'] != $this->input->ip_address() ) :

						$this->_register_token = array();

					endif;

				else :

					$this->_register_token = array();

				endif;

			else :

				$this->_register_token = array();

			endif;

		else :

			$this->_register_token = array();

		endif;

		delete_cookie( 'fbRegisterToken' );

		// --------------------------------------------------------------------------

		//	If the user is already logged in then skip the email check and link the
		//	two accounts together.

		if ( $this->user->is_logged_in() ) :

			$this->_link_user( $_me );

		endif;

		// --------------------------------------------------------------------------

		//	If we recognise the user, update their access token, if not create a new account
		if ( ! $_user ) :

			//	Not recognised via Facebook ID, what about via their email?
			$_user = $this->user->get_by_email( $_me['email'] );

			if ( ! $_user ) :

				//	OK, fine, this is a new user! Register but only if registration is allowed
				if ( defined( 'APP_USER_ALLOW_REGISTRATION' ) && APP_USER_ALLOW_REGISTRATION ) :

					$this->_create_user( $_me );

				else :

					//	Registration is not enabled, fail with error
					$this->session->set_flashdata( 'error', lang( 'auth_social_register_disabled' ) );
					$this->_redirect( $this->_return_to_fail );

				endif;

			else :

				//	An account has been found which uses this email but this Facebook
				//	ID is not associated with any account. We need to alert the user that the email
				//	is already regsitered to an account and that they need to log in and link the
				//	account from their settings page, if one is defined.

				$_settings = $this->config->load( 'facebook' );

				if ( ! empty( $_settings['settings_url'] ) ) :

					$this->session->set_flashdata( 'message', lang( 'auth_social_email_in_use', array( 'Facebook', APP_NAME ) ) );
					$this->_redirect( 'auth/login?return_to=' . urlencode( $_settings['settings_url'] ) );

				else :

					switch( APP_NATIVE_LOGIN_USING ) :

						case 'EMAIL' :

							$_forgot_url = site_url( 'auth/forgotten_password?identifier=' . urlencode( $_user->email ) );

						break;

						// --------------------------------------------------------------------------

						case 'USERNAME' :

							$_forgot_url = site_url( 'auth/forgotten_password?identifier=' . urlencode( $_user->username ) );

						break;

						// --------------------------------------------------------------------------

						case 'BOTH' :
						default :


							$_forgot_url = site_url( 'auth/forgotten_password?identifier=' . urlencode( $_user->email ) );

						break;

					endswitch;

					$this->session->set_flashdata( 'message', lang( 'auth_social_email_in_use_no_settings', array( 'Facebook', APP_NAME, $_forgot_url ) ) );
					$this->_redirect( 'auth/login' );

				endif;

				return;

			endif;

		else :

			//	Existing account, log them in, update the token and bump along to the group
			//	homepage with a welcome message.

			$this->_login_user( $_user );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Handles a failed connection request
	 *
	 * @access	protected
	 * @param	none
	 * @return	void
	 **/
	protected function _connect_fail()
	{
		$this->_redirect( $this->_return_to_fail );
	}


	// --------------------------------------------------------------------------

	/* ! DISCONNECTING FROM FACEBOOK */

	// --------------------------------------------------------------------------


	/**
	 * Disconnect a user's account
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function disconnect()
	{
		if ( $this->user->is_logged_in() ) :

			if ( $this->fb->user_is_linked() ) :

				//	User is currently linked, disconnect them
				if ( $this->fb->unlink_user() ) :

					$this->session->set_flashdata( 'success', lang( 'auth_social_disconnect_ok', 'Facebook' ) );
					$this->_redirect( $this->_return_to );

				else :

					$this->session->set_flashdata( 'error', lang( 'auth_social_no_disconnect_fail', 'Facebook' ) );
					$this->_redirect( $this->_return_to_fail );

				endif;

			else :

				$this->session->set_flashdata( 'error', lang( 'auth_social_no_disconnect_not_linked', 'Facebook' ) );
				$this->_redirect( $this->_return_to_fail );

			endif;

		else :

			$this->session->set_flashdata( 'error', lang( 'auth_social_no_disconnect_not_logged_in', 'Facebook' ) );
			$this->_redirect( $this->_return_to_fail );

		endif;
	}


	// --------------------------------------------------------------------------

	/* ! HELPER METHODS */

	// --------------------------------------------------------------------------


	/**
	 * Link a user's accounts together
	 *
	 * @access	public
	 * @param	array $me The $me array from Facebook
	 * @return	void
	 **/
	protected function _link_user( $me )
	{
		//	Set Facebook details
		$_data				= array();
		$_data['fb_id']		= $me['id'];
		$_data['fb_token']	= $this->fb->getAccessToken();

		// --------------------------------------------------------------------------

		//	Update the user
		$this->user->update( active_user( 'id' ), $_data );

		// --------------------------------------------------------------------------

		create_event( 'did_link_fb', active_user( 'id' ) );

		// --------------------------------------------------------------------------

		//	Redirect
		$this->session->set_flashdata( 'success', lang( 'auth_social_linked_ok', 'Facebook' ) );
		$this->_redirect( $this->_return_to );
		return;
	}


	// --------------------------------------------------------------------------


	/**
	 * Update a user's access token and log them in to the app
	 *
	 * @access	public
	 * @param	object $user The user's basic userobject
	 * @return	void
	 **/
	protected function _login_user( $user )
	{
		//	Load the auth lang file
		$this->lang->load( 'auth', 'english' );

		// --------------------------------------------------------------------------

		//	Check if the user is suspended.
		if ( $user->is_suspended) :

			$this->session->set_flashdata( 'error', lang( 'auth_login_fail_suspended' ) );
			$this->_redirect( $this->_return_to_fail );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Update token
		$_data['fb_token'] = $this->fb->getAccessToken();
		$this->user->update( $user->id, $_data );

		// --------------------------------------------------------------------------

		//	Set login details
		$this->user->set_login_data( $user->id );

		// --------------------------------------------------------------------------

		//	Set welcome message
		if ( $user->last_login ) :

			$_last_login =  nice_time( $user->last_login );
			$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome', array( $user->first_name, $_last_login ) ) );

		else :

			$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_notime', array( $user->first_name ) ) );

		endif;

		// --------------------------------------------------------------------------

		//	Update the last login
		$this->user->update_last_login( $user->id );

		// --------------------------------------------------------------------------

		//	Create an event for this event
		create_event( 'did_log_in', $user->id, 0, NULL, array( 'method' => 'facebook' ) );

		// --------------------------------------------------------------------------

		//	If no return to value is defined, default to the group homepage
		if ( ! $this->_return_to ) :

			$this->_return_to = $user->group_homepage;

		endif;

		// --------------------------------------------------------------------------

		//	Redirect
		$this->_redirect( $this->_return_to );
		return;
	}


	// --------------------------------------------------------------------------


	/**
	 * Create a new user from FB Details
	 *
	 * @access	public
	 * @param	object $user The user's basic userobject
	 * @return	void
	 **/
	protected function _create_user( $me )
	{
		//	Attempt the registration
		$email		= $me['email'];
		$password	= NULL;
		$remember	= TRUE;

		//	Meta data
		$_data						= array();
		$_data['first_name']		= $me['first_name'];
		$_data['last_name']			= $me['last_name'];
		$_data['username']			= $me['username'];
		$_data['fb_id']				= $me['id'];
		$_data['fb_token']			= $this->fb->getAccessToken();
		$_data['auth_method_id']	= 'facebook';
		$_data['is_verified']		= TRUE;	//	Trust the email from Facebook

		// --------------------------------------------------------------------------

		//	Use gender, if supplied
		if ( isset( $me['gender'] ) ) :

			if ( $me['gender'] == 'male' ) :

				$_data['gender'] = 'male';

			elseif ( $me['gender'] == 'female' ) :

				$_data['gender'] = 'female';

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Handle referrals
		if ( $this->session->userdata( 'referred_by' ) ) :

			$_data['referred_by'] = $this->session->userdata( 'referred_by' );

		endif;

		// --------------------------------------------------------------------------

		//	Which group?
		//	If there's a register_token set, use that if not fall back to the default

		if ( isset( $this->_register_token['group'] ) && $this->_register_token['group'] ) :

			$_group_id = $this->_register_token['group'];

		else :

			$_group_id = APP_USER_DEFAULT_GROUP;

		endif;

		//	Create new user
		$_uid = $this->user->create( $email, $password, $_group_id, $_data );

		if ( $_uid ) :

			//	Fetch user and group data
			$_user	= $this->user->get_by_id( $_uid['id'] );
			$_group	= $this->user->get_group( $_group_id );

			// --------------------------------------------------------------------------

			//	Send the user the welcome email (that is, if there is one)
			$this->load->library( 'emailer' );

			$_email					= new stdClass();
			$_email->type			= 'new_user_' . $_group->id;
			$_email->to_id			= $_user->id;
			$_email->data			= array();
			$_email->data['method']	= 'facebook';


			if ( ! $this->emailer->send( $_email, TRUE ) ) :

				//	Failed to send using the group email, try using the generic email template
				$_email->type = 'new_user';

				if ( ! $this->emailer->send( $_email, TRUE ) ) :

					//	Email failed to send, musn't exist, oh well.

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	Log the user in
			$this->user->set_login_data( $_user->id );

			// --------------------------------------------------------------------------

			//	Create an event for this event
			create_event( 'did_register', $_user->id, 0, NULL, array( 'method' => 'facebook' ) );

			// --------------------------------------------------------------------------

			//	Redirect
			$this->session->set_flashdata( 'success', lang( 'auth_social_register_ok', $_user->first_name ) );
			$this->session->set_flashdata( 'from_facebook', TRUE );

			//	Registrations will be forced to the registration redirect, regardless of
			//	what else has been set_error_handler

			if ( $this->_register_use_return ) :

				$_redirect = $this->_return_to;

			else :

				$_redirect = $_group->registration_redirect ? $_group->registration_redirect : $_group->default_homepage;

			endif;

			$this->_redirect( $_redirect );
			return;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Perform a redirect
	 *
	 * @access	public
	 * @param	string $goto The URL to redirect to
	 * @param	bool $destory_session Whether to destroy the active FB session or not
	 * @return	void
	 **/
	protected function _redirect( $_goto = FALSE, $destory_session = TRUE )
	{
		if ( $destory_session ) :

			//	Destory the FB session (so all cookies are unset)
			$this->fb->destroySession();

			// --------------------------------------------------------------------------

			//	Remove the PHPSESSID cookie
			delete_cookie( 'PHPSESSID' );

		endif;

		// --------------------------------------------------------------------------

		//	Where are we going?
		$_goto = ( $_goto ) ? $_goto : '/';
		redirect( $_goto );
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

	class Fb extends NAILS_Fb
	{
	}

endif;

/* End of file fb.php */
/* Location: ./application/modules/auth/controllers/fb.php */