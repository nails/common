<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [LinkedIn]
 *
 * Description:	This controller handles connecting accounts to LinkedIn
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

class NAILS_Li extends NAILS_Auth_Controller
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Ensure the sub-module is enabled
		if ( ! module_is_enabled( 'auth[linkedin]' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Load the LinkedIn Library
		$this->load->library( 'Linkedin_connect', NULL, 'li' );

		// --------------------------------------------------------------------------

		//	Set a return_to if available
		$this->_register_use_return	= TRUE;
		$this->_return_to			= $this->input->get( 'return_to' );

		//	If nothing, check the 'nailsFBConnectReturnTo' GET var which may be passed back
		if ( ! $this->_return_to ) :

			$this->_return_to = $this->input->get( 'nailsLIConnectReturnTo' );

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

			$this->_return_to_fail = $this->input->get( 'nailsLIConnectReturnToFail' );

			if ( ! $this->_return_to_fail ) :

				//	Fallback to the value of $this->_return_to
				$this->_return_to_fail = $this->_return_to;

			endif;

		endif;
	}


	// --------------------------------------------------------------------------

	/* ! CONNECTING TO LINKEDIN */

	// --------------------------------------------------------------------------


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
		//	If the LinkedIn is already linked then we need to acknowledge it
		if ( ! $this->input->get( 'force' ) && $this->li->user_is_linked() ) :

			$this->session->set_flashdata( 'message', lang( 'auth_social_already_linked', 'LinkedIn' ) );
			$this->_connect_fail();
			return;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'token' ) ) :

			//	Drop a cookie
			$this->input->set_cookie( 'liRegisterToken', $this->input->get( 'token' ), 900 );

		endif;

		// --------------------------------------------------------------------------

		//	Redirect the user to the LinkedIn Authorisation page, specifying a callback
		$_params								= array();
		$_params['nailsLIConnectReturnTo']		= $this->_return_to;
		$_params['nailsLIConnectReturnToFail']	= $this->_return_to_fail;
		$_params = array_filter( $_params );

		$_callback = site_url( 'auth/li/connect/verify' ) . '?' . http_build_query( $_params );

		//	Set and save the state
		$_state			= array();
		$_state['guid']	= uniqid();
		$_state['time']	= time();
		$_state['ip']	= $this->input->ip_address();
		$_state['hash']	= md5( $_state['guid'] . $_state['time'] . $_state['ip'] . APP_PRIVATE_KEY );

		$_state = $this->encrypt->encode( implode( '|', $_state ), APP_PRIVATE_KEY );
		$this->session->set_userdata( 'li_state', $_state );

		$this->_redirect( $this->li->get_auth_url( $_callback, $_state ) );
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
		//	Check the state
		$_code			= $this->input->get( 'code' );
		$_state			= $this->input->get( 'state' );
		$_session_state	= $this->session->userdata( 'li_state' );

		if ( $_state !== $_session_state ) :

			//	Possible CSRF
			//	TODO
			dumpanddie( 'bad state' );

		else :

			//	Check integrity of state
			$_state = $this->encrypt->decode( $_session_state, APP_PRIVATE_KEY );
			$_state = explode( '|', $_state );

			//	Same IP?

			if ( $this->input->ip_address() != $_state[2] ) :

				//	TODO
				dumpanddie( 'bad IP' );

			endif;

			//	Hash ok?
			$_hash = md5( $_state[0] . $_state[1] . $_state[2] . APP_PRIVATE_KEY );

			if ( $_hash != $_state[3]) :

				//	TODO
				dumpanddie( 'bad hash' );

			endif;

		endif;

		//$this->session->unset_userdata( 'li_state' );

		// --------------------------------------------------------------------------

		//	Errors?
		if ( $this->input->get( 'error' ) ) :

			$this->_connect_fail();
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Request an access token
		$_params								= array();
		$_params['nailsLIConnectReturnTo']		= $this->_return_to;
		$_params['nailsLIConnectReturnToFail']	= $this->_return_to_fail;
		$_params = array_filter( $_params );

		$_callback = site_url( 'auth/li/connect/verify' ) . '?' . http_build_query( $_params );

		$_access_token	= $this->li->get_access_token( $_callback, $_code );

		// --------------------------------------------------------------------------

		if ( ! empty( $_access_token->access_token ) ) :

			//	Fetch the user's ID
			$_me = $this->li->get( 'people/~:(id,firstName,lastName,email-address)' );

			if ( empty( $_me->id ) ) :

				$this->_connect_fail();
				return;

			else :

				$_access_token->user_id		= $_me->id;
				$_access_token->first_name	= $_me->firstName;
				$_access_token->last_name	= $_me->lastName;
				$_access_token->email		= $_me->emailAddress;

			endif;

			$this->_connect_success( $_access_token );

		else :

			$this->_connect_fail();

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
	protected function _connect_success( $access_token )
	{
		//	Check if the user has previously connected this LinkedIn account
		//	to another registered account

		$_user = $this->user->get_by_liid( $access_token->user_id );

		if ( $this->user->is_logged_in() && $_user ) :

			//	This LinkedIn ID is already in use, tell the user so and prevent anything else from happening.
			$this->session->set_flashdata( 'error', lang( 'auth_social_account_in_use', array( 'LinkedIn', APP_NAME ) ) );
			$this->_connect_fail();
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Test for a register token, if there verify and store it in memory
		//	We'll delete the cookie once a user has successfully registered.

		$this->_register_token = get_cookie( 'liRegisterToken' );

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

		// --------------------------------------------------------------------------

		//	If the user is already logged in then skip the email check and link the
		//	two accounts together.

		if ( $this->user->is_logged_in() ) :

			$this->_link_user( $access_token );

		endif;

		// --------------------------------------------------------------------------

		//	If we recognise the user, update their access token, if not create a new account

		if ( ! $_user ) :

			//	Not recognised via LinkedIn ID, what about via their email?
			$_user = $this->user->get_by_email( $access_token->email );

			if ( ! $_user ) :

				//	OK, fine, this is a new user! Registerm buyt only if registration is allowed
				if ( defined( 'APP_USER_ALLOW_REGISTRATION' ) && APP_USER_ALLOW_REGISTRATION ) :

					$this->_create_user( $access_token );

				else :

					//	Registration is not enabled, fail with error
					$this->session->set_flashdata( 'error', lang( 'auth_social_register_disabled' ) );
					$this->_redirect( $this->_return_to_fail );

				endif;

			else :

				//	An account has been found which uses this email but this LinkedIn ID
				//	is not associated with any account. We need to alert the user that the email
				//	is already regsitered to an account and that they need to log in and link the
				//	account from their settings page, if one is defined.

				$_settings = $this->config->load( 'linkedin' );

				if ( ! empty( $_settings['settings_url'] ) ) :

					$this->session->set_flashdata( 'message', lang( 'auth_social_email_in_use', array( 'LinkedIn', APP_NAME ) ) );
					$this->_redirect( 'auth/login?return_to=' . urlencode( $_settings['settings_url'] ) );

				else :

					$this->session->set_flashdata( 'message', lang( 'auth_social_email_in_use_no_settings', array( 'LinkedIn', APP_NAME, site_url( 'auth/forgotten_password?email=' . urlencode( $access_token->email ) ) ) ) );
					$this->_redirect( 'auth/login' );

				endif;

				return;

			endif;

		else :

			//	Existing account, log them in, update the token and bump along to the group
			//	homepage with a welcome message.

			$this->_login_user( $access_token, $_user );

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

	/* ! DISCONNECTING FROM LINKEDIN */

	// --------------------------------------------------------------------------


	public function disconnect()
	{
		//	TODO: handle doisconnection
		return FALSE;
	}


	// --------------------------------------------------------------------------

	/* ! HELPER METHODS */

	// --------------------------------------------------------------------------


	/**
	 * Link a user's accounts together
	 *
	 * @access	public
	 * @param	object $access_token The user's access token
	 * @return	void
	 **/
	protected function _link_user( $access_token )
	{
		//	Set LinkeDInm details
		$_data				= array();
		$_data['li_id']		= $access_token->user_id;
		$_data['li_token']	= $access_token->access_token;

		// --------------------------------------------------------------------------

		//	Update the user
		$this->user->update( active_user( 'id' ), $_data );

		// --------------------------------------------------------------------------

		create_event( 'did_link_li', active_user( 'id' ) );

		// --------------------------------------------------------------------------

		//	Delete register token
		delete_cookie( 'liRegisterToken' );

		// --------------------------------------------------------------------------

		//	Redirect
		$this->session->set_flashdata( 'success', lang( 'auth_social_linked_ok', 'LinkedIn' ) );
		$this->_redirect( $this->_return_to );
		return;
	}


	// --------------------------------------------------------------------------


	/**
	 * Update a user's access token and log them in to the app
	 *
	 * @access	public
	 * @param	object $access_token The user's access token
	 * @return	void
	 **/
	protected function _login_user( $access_token, $user )
	{
		//	Load the auth lang file
		$this->lang->load( 'auth', 'english' );

		// --------------------------------------------------------------------------

		//	Check if the user is suspended.
		if ( $user->is_suspended ) :

			$this->session->set_flashdata( 'error', lang( 'auth_login_fail_suspended' ) );
			$this->_redirect( $this->_return_to_fail );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Update token
		$_data['li_token']	= $access_token->access_token;
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
		create_event( 'did_log_in', $user->id, 0, NULL, array( 'method' => 'linkedin' ) );

		// --------------------------------------------------------------------------

		//	Delete register token
		delete_cookie( 'liRegisterToken' );

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
	 * Create a new user, needs to interrupt the authentication flow to request specific details from the user
	 *
	 * @access	public
	 * @param	object $access_token The users access token
	 * @return	void
	 **/
	protected function _create_user( $access_token )
	{
		$email		= $access_token->email;
		$password	= NULL;
		$remember	= TRUE;

		$_data = array();
		$_data['li_id']				= $access_token->user_id;
		$_data['li_token']			= $access_token->access_token;
		$_data['auth_method_id']	= 5;	//	LinkedIn, obviously.
		$_data['first_name']		= trim( $access_token->first_name );
		$_data['last_name']			= trim( $access_token->last_name );

		// --------------------------------------------------------------------------

		//	Handle referrals
		if ( $this->session->userdata( 'referred_by' ) ) :

			$_data['referred_by'] = $this->session->userdata( 'referred_by' );

		endif;

		// --------------------------------------------------------------------------

		//	Which group?
		//	If there's a register_token set, use that if not fall back to the default

		if ( ! empty( $this->_register_token['group'] ) ) :

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

			//	Some nice data...
			$this->data['email']	= $email;
			$this->data['user_id']	= $_uid['id'];
			$this->data['hash']		= $_uid['activation'];

			// --------------------------------------------------------------------------

			//	Registration was successfull, send the welcome email...
			$this->load->library( 'emailer' );

			$_email							= new stdClass();
			$_email->type					= 'verify_email_' . $_group_id;
			$_email->to_id					= $_uid['id'];
			$_email->data					= array();
			$_email->data['user']			= $_user;
			$_email->data['group']			= $_group->display_name;

			if ( ! $this->emailer->send( $_email, TRUE ) ) :

				//	Failed to send using the group email, try using the generic email
				$_email->type = 'register_li';

				if ( ! $this->emailer->send( $_email, TRUE ) ) :

					//	Email failed to send, for now, do nothing.

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	Log the user in
			$this->user->set_login_data( $_uid['id'] );

			// --------------------------------------------------------------------------

			//	Create an event for this event
			create_event( 'did_register', $_uid['id'], 0, NULL, array( 'method' => 'linkedin' ) );

			// --------------------------------------------------------------------------

			//	Delete register token
			delete_cookie( 'liRegisterToken' );

			// --------------------------------------------------------------------------

			//	Redirect
			$this->session->set_flashdata( 'success', lang( 'auth_social_register_ok', $_user->first_name ) );
			$this->session->set_flashdata( 'from_linkedin', TRUE );

			//	Registrations will be forced to the registration redirect, regardless of what else has been set
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
	 * @return	void
	 **/
	protected function _redirect( $_goto = FALSE )
	{
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

	class Li extends NAILS_Li
	{
	}

endif;

/* End of file li.php */
/* Location: ./application/modules/auth/controllers/li.php */