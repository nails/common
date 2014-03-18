<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [Twitter]
 *
 * Description:	This controller handles connecting accounts to Twitter
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

class NAILS_Tw extends NAILS_Auth_Controller
{
	protected $_register_token;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Ensure the sub-module is enabled
		if ( ! module_is_enabled( 'auth[twitter]' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Load the LinkedIn Library
		$this->load->library( 'twitter_connect', NULL, 'tw' );

		// --------------------------------------------------------------------------

		//	Set a return_to if available
		$this->_register_use_return	= TRUE;
		$this->_return_to			= $this->input->get( 'return_to' );

		//	If nothing, check the 'nailsTWConnectReturnTo' GET var which may be passed back
		if ( ! $this->_return_to ) :

			$this->_return_to = $this->input->get( 'nailsTWConnectReturnTo' );

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

			$this->_return_to_fail = $this->input->get( 'nailsTWConnectReturnToFail' );

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

	/* ! CONNECTING TO TWITTER */

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
		//	If Twitter is already linked then we need to acknowledge it
		if ( ! $this->input->get( 'force' ) && $this->tw->user_is_linked() ) :

			$this->session->set_flashdata( 'message', lang( 'auth_social_already_linked', 'Twitter' ) );
			$this->_connect_fail();
			return;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'token' ) ) :

			//	Drop a cookie
			$this->input->set_cookie( 'twRegisterToken', $this->input->get( 'token' ), 900 );

		endif;

		// --------------------------------------------------------------------------

		//	Generate the login URL
		$_login_url = $this->tw->get_login_url( $this->_return_to, $this->_return_to_fail );

		// --------------------------------------------------------------------------

		//	Send the user on their way
		$this->_redirect( $_login_url );
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
		//	If there is an access_token in the session then use that, this will have been set just
		//	before the auth flow was interrupted to request more data from the user.

		if ( $this->session->userdata( 'tw_request_token' ) ) :

			$_request_token = $this->session->userdata( 'tw_request_token' );
			$this->session->unset_userdata( 'tw_request_token' );

			if ( $_request_token ) :

				//	Set the token to use
				$this->tw->set_access_token( $_request_token->oauth_token, $_request_token->oauth_token_secret );

				$_access_token = $this->tw->get_access_token( $this->input->get( 'oauth_verifier' ) );

				if ( ! isset( $_access_token->oauth_token ) || ! isset( $_access_token->oauth_token_secret )  ) :

					$this->session->set_flashdata( 'error', lang( 'auth_social_no_access_token', 'Twitter' ) );
					$this->_connect_fail();
					return;

				endif;

				// --------------------------------------------------------------------------

				//	We have a valid access token, continue
				$this->tw->set_access_token( $_access_token->oauth_token, $_access_token->oauth_token_secret );
				$this->_connect_success( $_access_token );

			else :

				$this->session->set_flashdata( 'error', lang( 'auth_social_no_access_token', 'Twitter' ) );
				$this->_connect_fail();
				return;

			endif;

		else :

			$_access_token = $this->session->userdata( 'tw_access_token' );

			if ( ! isset( $_access_token->oauth_token ) || ! isset( $_access_token->oauth_token_secret )  ) :

				$this->session->set_flashdata( 'error', lang( 'auth_social_no_access_token', 'Twitter' ) );
				$this->_connect_fail();
				return;

			endif;

			// --------------------------------------------------------------------------

			//	We have a valid access token, continue
			$this->tw->set_access_token( $_access_token->oauth_token, $_access_token->oauth_token_secret );
			$this->_connect_success( $_access_token );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Handles a successfull connection request, This method can be overridden if more
	 * than the basic data (i.e email and name) is needed for account creation.
	 * By this point the Twitter library is set up with the user's access token.
	 *
	 * @access	protected
	 * @param	object $access_token The access token to use
	 * @return	void
	 **/
	protected function _connect_success( $access_token )
	{
		//	First up, check if the user has previously connected this Twitter account
		//	to another registered account

		$_user = $this->user->get_by_twid( $access_token->user_id );

		if ( $this->user->is_logged_in() && $_user ) :

			//	This Twitter ID is already in use, tell the user so and prevent anything else from happening.
			$this->session->set_flashdata( 'error', lang( 'auth_social_account_in_use', array( 'Twitter', APP_NAME ) ) );
			$this->_connect_fail();
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Test for a register token, if there verify and store it in memory
		//	We'll delete the cookie once a user has successfully registered.

		$this->_register_token = get_cookie( 'twRegisterToken' );

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
		$_user = $this->user->get_by_twid( $access_token->user_id );

		if ( ! $_user ) :

			//	OK, fine, this is a new user! Registerm buyt only if registration is allowed
			if ( APP_USER_ALLOW_REGISTRATION ) :

				$this->_create_user( $access_token );

			else :

				//	Registration is not enabled, fail with error
				$this->session->set_flashdata( 'error', lang( 'auth_social_register_disabled' ) );
				$this->_redirect( $this->_return_to_fail );

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
		if ( $this->user->is_logged_in() ) :

			if ( $this->tw->user_is_linked() ) :

				//	User is currently linked, disconnect them
				if ( $this->tw->unlink_user() ) :

					$this->session->set_flashdata( 'success', lang( 'auth_social_disconnect_ok', 'Twitter' ) );
					$this->_redirect( $this->_return_to );

				else :

					$this->session->set_flashdata( 'error', lang( 'auth_social_no_disconnect_not_linked', 'Twitter' ) );
					$this->_redirect( $this->_return_to_fail );

				endif;

			else :

				$this->session->set_flashdata( 'error', lang( 'auth_social_no_disconnect_not_linked', 'Twitter' ) );
				$this->_redirect( $this->_return_to_fail );

			endif;

		else :

			$this->session->set_flashdata( 'error', lang( 'auth_social_no_disconnect_not_logged_in', 'Twitter' ) );
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
	 * @param	object $access_token The user's access token
	 * @return	void
	 **/
	protected function _link_user( $access_token )
	{
		//	Set Twitter details
		$_data				= array();
		$_data['tw_id']		= $access_token->user_id;
		$_data['tw_token']	= $access_token->oauth_token;
		$_data['tw_secret']	= $access_token->oauth_token_secret;

		// --------------------------------------------------------------------------

		//	Update the user
		$this->user->update( active_user( 'id' ), $_data );

		// --------------------------------------------------------------------------

		create_event( 'did_link_tw', active_user( 'id' ) );

		// --------------------------------------------------------------------------

		//	Delete register token
		delete_cookie( 'twRegisterToken' );

		// --------------------------------------------------------------------------

		//	Redirect
		$this->session->set_flashdata( 'success', lang( 'auth_social_linked_ok', 'Twitter' ) );
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
		$_data['tw_token']	= $access_token->oauth_token;
		$_data['tw_secret']	= $access_token->oauth_token_secret;
		$this->user->update( $user->id, $_data );

		// --------------------------------------------------------------------------

		//	Two factor auth enabled?
		if ( $this->config->item( 'auth_two_factor_enable' ) ) :

			//	Generate a token
			$this->load->model( 'auth_model' );
			$_token = $this->auth_model->generate_two_factor_token( $user->id );

			if ( ! $_token ) :

				show_fatal_error( 'Failed to generate two-factor auth token', 'A user tried to login with Twitter and the system failed to generate a two-factor auth token.' );

			endif;

			$_query					= array();
			$_query['return_to']	= $this->_return_to;

			$_query = array_filter( $_query );

			if ( $_query ) :

				$_query = '?' . http_build_query( $_query );

			else :

				$_query = '';

			endif;

			redirect( 'auth/security_questions/' . $user->id . '/' . $_token['salt'] . '/' . $_token['token'] . '/twitter' . $_query );

		else :

			//	Set login details
			$this->user->set_login_data( $user->id );

			// --------------------------------------------------------------------------

			//	Set welcome message
			if ( $user->last_login ) :

				$this->load->helper( 'date' );

				$_last_login = $this->config->item( 'auth_show_nicetime_on_login' ) ? nice_time( strtotime( $user->last_login ) ) : user_datetime( $user->last_login );

				if ( $this->config->item( 'auth_show_last_ip_on_login' ) ) :

					$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_with_ip', array( $user->first_name, $_last_login, $user->last_ip ) ) );

				else :

					$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome', array( $user->first_name, $_last_login ) ) );

				endif;

			else :

				$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_notime', array( $user->first_name ) ) );

			endif;

			// --------------------------------------------------------------------------

			//	Update the last login
			$this->user->update_last_login( $user->id );

			// --------------------------------------------------------------------------

			//	Create an event for this event
			create_event( 'did_log_in', $user->id, 0, NULL, array( 'method' => 'twitter' ) );

			// --------------------------------------------------------------------------

			//	Delete register token
			delete_cookie( 'twRegisterToken' );

			// --------------------------------------------------------------------------

			//	If no return to value is defined, default to the group homepage
			if ( ! $this->_return_to ) :

				$this->_return_to = $user->group_homepage;

			endif;

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
		//	Fetch some information about this user
		$_me = (array) $this->tw->users_lookup( 'user_id=' . $access_token->user_id );

		//	Try and determine the user's first name and surname
		if ( isset( $_me[0]->name ) ) :

			$this->data['first_name']	= trim( substr( $_me[0]->name, 0, strpos( $_me[0]->name, ' ' ) ) );
			$this->data['last_name']	= trim( substr( $_me[0]->name, strpos( $_me[0]->name, ' ' ) ) );

		else :

			$this->data['first_name']	= '';
			$this->data['last_name']	= '';

		endif;

		// --------------------------------------------------------------------------

		//	Set the user's username as their Twitter handle, check it's available, if
		//	it's not, try their name. If that fails stick a random number on the end
		//	of their handle

		if ( ! empty( $_me[0]->screen_name ) ) :

			//	Check if their Twitter handle is available
			$this->data['username'] = url_title( $_me[0]->screen_name, '-', TRUE );

			$_user = $this->user->get_by_username( $this->data['username'] );

			while( $_user ) :

				$this->data['username']  = increment_string( url_title( $_me[0]->screen_name, '-', TRUE ), '' );
				$_user = $this->user->get_by_username( $this->data['username'] );

			endwhile;

		elseif ( ! empty( $_me[0]->name ) ) :

			//	No handle, odd, try their name, keep trying it till it works
			$this->data['username'] = url_title( $_me[0]->name, '-', TRUE );

			$_user = $this->user->get_by_username( $this->data['username'] );

			while( $_user ) :

				$this->data['username']  = increment_string( url_title( $_me[0]->name, '-', TRUE ), '' );
				$_user = $this->user->get_by_username( $this->data['username'] );

			endwhile;

		else :

			//	Random string
			$this->data['username'] = 'user' . date( 'YmdHis' );

			$_user = $this->user->get_by_username( $this->data['username'] );

			while ( $_user ) :

				$this->data['username']  = increment_string( $this->data['username'], '' );
				$_user = $this->user->get_by_username( $this->data['username'] );

			endwhile;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->input->post() ) :

			//	Validate the form and attempt the registration
			$this->load->library( 'form_validation' );

			//	Set rules
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

			if ( ! $this->data['first_name'] || ! $this->data['last_name'] ) :

				$this->form_validation->set_rules( 'first_name',	'',	'xss_clean|required' );
				$this->form_validation->set_rules( 'last_name',		'',	'xss_clean|required' );

			endif;

			//	Set messages
			$this->form_validation->set_message( 'required',		lang( 'fv_required' ) );

			if ( APP_NATIVE_LOGIN_USING == 'EMAIL' ) :

				$this->form_validation->set_message( 'is_unique',	lang( 'fv_email_already_registered', site_url( 'auth/forgotten_password' ) ) );

			elseif ( APP_NATIVE_LOGIN_USING == 'USERNAME' ) :

				$this->form_validation->set_message( 'is_unique',	lang( 'fv_username_already_registered', site_url( 'auth/forgotten_password' ) ) );

			elseif ( APP_NATIVE_LOGIN_USING == 'BOTH' ) :

				$this->form_validation->set_message( 'is_unique',	lang( 'fv_identity_already_registered', site_url( 'auth/forgotten_password' ) ) );

			endif;

			//	Execute
			if ( $this->form_validation->run() ) :

				$_data				= array();
				$_data['email']		= $this->input->post( 'email' );
				$_data['username']	= $this->input->post( 'username' );

				if ( ! $this->data['first_name'] || ! $this->data['last_name'] ) :

					$_data['first_name']	= $this->input->post( 'first_name' );
					$_data['last_name']		= $this->input->post( 'last_name' );

				else :

					$_data['first_name']	= $this->data['first_name'];
					$_data['last_name']		= $this->data['last_name'];

				endif;

				$_data['tw_id']				= $access_token->user_id;
				$_data['tw_token']			= $access_token->oauth_token;
				$_data['tw_secret']			= $access_token->oauth_token_secret;
				$_data['auth_method_id']	= 'twitter';

				// --------------------------------------------------------------------------

				//	Handle referrals
				if ( $this->session->userdata( 'referred_by' ) ) :

					$_data['referred_by'] = $this->session->userdata( 'referred_by' );

				endif;

				// --------------------------------------------------------------------------

				//	Which group?
				//	If there's a register_token set, use that if not fall back to the default

				if ( isset( $this->_register_token['group'] ) && $this->_register_token['group'] ) :

					$_data['group_id'] = $this->_register_token['group'];

				else :

					$_data['group_id'] = APP_USER_DEFAULT_GROUP;

				endif;

				//	Create new user
				$_new_user = $this->user->create( $_data );

				if ( $_new_user ) :

					//	Fetch group data
					$_group	= $this->user->get_group( $_data['group_id'] );

					// --------------------------------------------------------------------------

					//	Send the user the welcome email (that is, if there is one)
					$this->load->library( 'emailer' );

					$_email					= new stdClass();
					$_email->type			= 'new_user_' . $_group->id;
					$_email->to_id			= $_new_user->id;
					$_email->data			= array();
					$_email->data['method']	= 'twitter';

					if ( ! $this->emailer->send( $_email, TRUE ) ) :

						//	Failed to send using the group email, try using the generic email template
						$_email->type = 'new_user';

						if ( ! $this->emailer->send( $_email, TRUE ) ) :

							//	Email failed to send, musn't exist, oh well.

						endif;

					endif;

					// --------------------------------------------------------------------------

					//	Log the user in
					$this->user->set_login_data( $_new_user->id );

					// --------------------------------------------------------------------------

					//	Create an event for this event
					create_event( 'did_register', $_new_user->id, 0, NULL, array( 'method' => 'twitter' ) );

					// --------------------------------------------------------------------------

					//	Delete register token
					delete_cookie( 'twRegisterToken' );

					// --------------------------------------------------------------------------

					//	Redirect
					$this->session->set_flashdata( 'success', lang( 'auth_social_register_ok', $_new_user->first_name ) );
					$this->session->set_flashdata( 'from_twitter', TRUE );

					//	Registrations will be forced to the registration redirect, regardless of
					//	what else has been set

					if ( $this->_register_use_return ) :

						$_redirect = $this->_return_to;

					else :

						$_redirect = $_group->registration_redirect ? $_group->registration_redirect : $_group->default_homepage;

					endif;

					$this->_redirect( $_redirect );
					return;

				endif;

			else :

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Store the access token in the Session so we can interrupt the auth flow cleanly
		$this->session->set_userdata( 'tw_access_token', $access_token );

		// --------------------------------------------------------------------------

		//	Set some view data
		$this->data['page']				= new stdClass();
		$this->data['page']->title		= lang( 'auth_register_extra_title' );

		$this->data['return_to']		= $this->_return_to;
		$this->data['return_to_fail']	= $this->_return_to_fail;

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'auth/register/extra-info',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
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
		$_goto = $_goto ? $_goto : '/';
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

	class Tw extends NAILS_Tw
	{
	}

endif;

/* End of file tw.php */
/* Location: ./application/modules/auth/controllers/tw.php */