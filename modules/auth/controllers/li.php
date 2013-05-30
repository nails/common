<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [LinkedIn]
 *
 * Description:	This controller handles connecting accounts to LinkedIn
 * 
 **/

/**
 * OVERLOADING NAILS'S AUTH MODULE
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
		$this->_redirect( $this->li->get_auth_url( site_url( 'auth/li/connect/verify?nailsLIConnectReturnTo=' . urlencode( $this->_return_to ) . '&nailsLIConnectReturnToFail=' . urlencode( $this->_return_to_fail ) ) ) );
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
		
		if ( ! $this->session->userdata( 'li_access_token' ) ) :
		
			$_request_token = $this->session->userdata( 'li_request_token' );
			//$this->session->unset_userdata( 'li_request_token' );
			
			if ( $_request_token ) :
			
				//	Set the token to use
				$this->li->set_access_token( $_request_token['oauth_token'], $_request_token['oauth_token_secret'] );
				
				$_access_token = $this->li->get_access_token( $this->input->get( 'oauth_verifier' ) );
				
				if ( ! isset( $_access_token['oauth_token'] ) || ! isset( $_access_token['oauth_token_secret'] )  ) :
				
					$this->session->set_flashdata( 'error', lang( 'auth_social_no_access_token', 'LinkedIn' ) );
					$this->_connect_fail();
					return;
				
				endif;
				
				// --------------------------------------------------------------------------
				
				//	We have a valid access token, continue
				$this->li->set_access_token( $_access_token['oauth_token'], $_access_token['oauth_token_secret'] );
				$this->_connect_success( $_access_token );
			
			else :
			
				$this->session->set_flashdata( 'error', lang( 'auth_social_no_access_token', 'LinkedIn' ) );
				$this->_connect_fail();
				return;
			
			endif;
		
		else :
		
			$_access_token = $this->session->userdata( 'li_access_token' );
			
			if ( ! isset( $_access_token['oauth_token'] ) || ! isset( $_access_token['oauth_token_secret'] )  ) :
			
				$this->session->set_flashdata( 'error', lang( 'auth_social_no_access_token', 'LinkedIn' ) );
				$this->_connect_fail();
				return;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	We have a valid access token, continue
			$this->li->set_access_token( $_access_token['oauth_token'], $_access_token['oauth_token_secret'] );
			$this->_connect_success( $_access_token );
		
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
		//	Set the user ID if it's not already set
		if ( ! isset( $access_token['user_id'] ) ) :
		
			$access_token['user_id'] = $this->li->call( 'people/~/id' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Check if the user has previously connected this LinkedIn account
		//	to another registered account
		
		$_user = $this->user->get_user_by_liid( $access_token['user_id'] );
		
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
		$_user = $this->user->get_user_by_liid( $access_token['user_id'] );
		
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
		dumpanddie( 'TODO Handle disconnection' );
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
		$_data['li_id']		= $access_token['user_id'];
		$_data['li_token']	= $access_token['oauth_token'];
		$_data['li_secret']	= $access_token['oauth_token_secret'];
		
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
			
			$this->session->set_flashdata( 'error', lang( 'auth_login_fail_banned' ) );
			$this->_redirect( $this->_return_to_fail );
			return;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Update token
		$_data['li_token']	= $access_token['oauth_token'];
		$_data['li_secret']	= $access_token['oauth_token_secret'];
		$this->user->update( $user->id, $_data );
		
		// --------------------------------------------------------------------------
		
		//	Set login details
		$this->user->set_login_data( $user->id, $user->email, $user->group_id, $user->lang );
		
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
		$_fields = array(
			'first-name',
			'last-name'
		);
		$_me = $this->li->call( 'people/~:(' . implode( ',', $_fields ) . ')' );
		
		//	Try and determine the user's first name and surname
		if ( isset( $_me->firstName ) ) :
		
			$this->data['first_name']	= trim( $_me->firstName );
		
		else :
		
			$this->data['first_name']	= '';
		
		endif;
		
		if ( isset( $_me->lastName ) ) :
		
			$this->data['last_name']	= trim( $_me->lastName );
		
		else :
		
			$this->data['last_name']	= '';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post() ) :
		
			//	Validate the form and attempt the registration
			$this->load->library( 'form_validation' );
			
			//	Set rules
			$this->form_validation->set_rules( 'email',	'Email',	'xss_clean|required|is_unique[user.email]' );
			
			if ( ! $this->data['first_name'] || ! $this->data['last_name'] ) :
			
				$this->form_validation->set_rules( 'first_name',	'First Name',	'xss_clean|required' );
				$this->form_validation->set_rules( 'last_name',		'Surname',		'xss_clean|required' );
			
			endif;
			
			//	Set messages
			$this->form_validation->set_message( 'required',	lang( 'fv_required' ) );
			$this->form_validation->set_message( 'is_unique',	lang( 'fv_email_already_registered' ) );
			
			//	Execute
			if ( $this->form_validation->run() ) :
			
				$email		= $this->input->post( 'email' );
				$password	= NULL;
				$remember	= TRUE;
				
				$_data = array();
				
				//	Meta data
				if ( ! $this->data['first_name'] || ! $this->data['last_name'] ) :
				
					$_data['first_name']	= $this->input->post( 'first_name' );
					$_data['last_name']		= $this->input->post( 'last_name' );
					
				else :
				
					$_data['first_name']	= $this->data['first_name'];
					$_data['last_name']		= $this->data['last_name'];
				
				endif;
				
				$_data['li_id']				= $access_token['user_id'];
				$_data['li_token']			= $access_token['oauth_token'];
				$_data['li_secret']			= $access_token['oauth_token_secret'];
				$_data['auth_method_id']	= 5;	//	LinkedIn, obviously.
				
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
					$_user	= $this->user->get_user( $_uid['id'] );
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
					$this->user->set_login_data( $_uid['id'], $email, $_group_id );
		
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
			
			else :
			
				$this->data['error'] = lang( 'fv_there_were_errors' );
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Store the access token in the Session so we can interrupt the auth flow cleanly
		$this->session->set_userdata( 'li_access_token', $access_token );
		
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
		$_goto = ( $_goto ) ? $_goto : '/';
		redirect( $_goto );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S AUTH MODULE
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