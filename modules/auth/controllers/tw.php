<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [Twitter]
 *
 * Description:	This controller handles connecting accounts to Twitter
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

class NAILS_Tw extends NAILS_Auth_Controller
{
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
		$this->_return_to = $this->input->get( 'return_to' );
		
		//	If nothing, check the 'nailsTWConnectReturnTo' GET var which may be passed back
		if ( ! $this->_return_to ) :
		
			$this->_return_to = $this->input->get( 'nailsTWConnectReturnTo' );
			
			//	Still empty? Group homepage
			if ( ! $this->_return_to ) :
			
				$this->_return_to = active_user( 'group_homepage' );
			
			endif;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set a return_to_fail if available
		$this->_return_to_fail = $this->input->get( 'return_to_fail' );
		
		//	If nothing, check the GET var which may be passed back
		if ( ! $this->_return_to_fail ) :
		
			$this->_return_to_fail = $this->input->get( 'nailsTWConnectReturnToFail' );
			
			if ( ! $this->_return_to_fail ) :
			
				//	Fallback to the value of $this->return_to
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
		//	If the Twitter is already linked then we need to acknowledge it
		if ( ! $this->input->get( 'force' ) && $this->tw->user_is_linked() ) :
		
			$this->session->set_flashdata( 'message', '<strong>Woah there!</strong> You have already linked your Twitter account.' );
			$this->_connect_fail();
			return;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->_redirect( $this->tw->get_login_url( $this->_return_to, $this->_return_to_fail ) );
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
		
		if ( ! $this->session->userdata( 'tw_access_token' ) ) :
		
			$_request_token = $this->session->userdata( 'tw_request_token' );
			$this->session->unset_userdata( 'tw_request_token' );
			
			if ( $_request_token ) :
			
				//	Set the token to use
				$this->tw->set_access_token( $_request_token->oauth_token, $_request_token->oauth_token_secret );
				
				$_access_token = $this->tw->get_access_token( $this->input->get( 'oauth_verifier' ), $this->_return_to, $this->_return_to_fail );
				
				if ( ! isset( $_access_token->oauth_token ) || ! isset( $_access_token->oauth_token_secret )  ) :
				
					$this->session->set_flashdata( 'error', '<strong>There was a problem.</strong> We could not validate your account with Twitter, you may be able to try again.' );
					$this->_connect_fail();
					return;
				
				endif;
				
				// --------------------------------------------------------------------------
				
				//	We have a valid access token, continue
				$this->tw->set_access_token( $_access_token->oauth_token, $_access_token->oauth_token_secret );
				$this->_connect_success( $_access_token );
			
			else :
			
				$this->session->set_flashdata( 'error', '<strong>There was a problem.</strong> We could not validate your account with Twitter, you may be able to try again.' );
				$this->_connect_fail();
				return;
			
			endif;
		
		else :
		
			$_access_token = $this->session->userdata( 'tw_access_token' );
			
			if ( ! isset( $_access_token->oauth_token ) || ! isset( $_access_token->oauth_token_secret )  ) :
			
				$this->session->set_flashdata( 'error', '<strong>There was a problem.</strong> We could not validate your account with Twitter, you may be able to try again.' );
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
		
		$_user = $this->user->get_user_by_twid( $access_token->user_id );

		if ( $this->user->is_logged_in() && $_user ) :
		
			//	This Twitter ID is already in use, tell the user so and prevent anything else from happening.
			$this->session->set_flashdata( 'error', '<strong>Sorry</strong>, the Twitter account you\'re currently logged into is already linked with another ' . APP_NAME . ' account.' );
			$this->_connect_fail();
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If the user is already logged in then skip the email check and link the
		//	two accounts together.
		
		if ( $this->user->is_logged_in() ) :
		
			$this->_link_user( $access_token );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If we recognise the user, update their access token, if not create a new account
		$_user = $this->user->get_user_by_twid( $access_token->user_id );
		
		if ( ! $_user ) :
		
			//	OK, fine, this is a new user!
			$this->_create_user( $access_token );
		
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
		$this->_redirect( $this->return_to_fail );
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
		//	Set Facebook details
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
		
		//	Redirect
		$this->session->set_flashdata( 'success', '<strong>Success</strong>, your Twitter account is now linked.' );
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
		
		//	Check if the user is banned.
		if ( $user->active == 2 ) :
			
			$this->session->set_flashdata( 'error', lang( 'login_fail_banned' ) );
			$this->_redirect( $this->return_to_fail );
			return;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Update token
		$_data['tw_token']	= $access_token->oauth_token;
		$_data['tw_secret']	= $access_token->oauth_token_secret;
		$this->user->update( $user->id, $_data );
		
		// --------------------------------------------------------------------------
		
		//	Set login details
		$this->user->set_login_data( $user->id, $user->email, $user->group_id, $user->lang );
		
		// --------------------------------------------------------------------------

		//	Set welcome message
		if ( $user->last_login ) :
		
			$_last_login =  nice_time( $user->last_login );
			$this->session->set_flashdata( 'message', lang( 'login_ok_welcome', array( $user->first_name, $_last_login ) ) );
		
		else :
		
			$this->session->set_flashdata( 'message', lang( 'login_ok_welcome_notime', array( $user->first_name ) ) );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Update the last login
		$this->user->update_last_login( $user->id );

		// --------------------------------------------------------------------------
		
		//	Create an event for this event
		create_event( 'did_log_in', $user->id, 0, NULL, array( 'method' => 'twitter' ) );
		
		// --------------------------------------------------------------------------
		
		//	Redirect
		$this->_redirect( $this->return_to );
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
			$this->form_validation->set_message( 'required', 'This field is required.' );
			$this->form_validation->set_message( 'is_unique', 'This email is already registered.' );
			
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
				
				$_data['username']			= $access_token->screen_name;
				$_data['tw_id']				= $access_token->user_id;
				$_data['tw_token']			= $access_token->oauth_token;
				$_data['tw_secret']			= $access_token->oauth_token_secret;
				$_data['auth_method_id']	= 3;	//	Twitter, obviously.
				
				// --------------------------------------------------------------------------
				
				//	Handle referrals
				if ( $this->session->userdata( 'referred_by' ) ) :
				
					$_data['referred_by'] = $this->session->userdata( 'referred_by' );
				
				endif;
				
				// --------------------------------------------------------------------------
				
				//	Create new user, group 2, standard member
				$_group_id = 2;
				$_uid = $this->user->create( $email, $password, $_group_id, $_data );
				
				if ( $_uid ) :
				
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
					$_email->data['user']			= $this->user->get_user( $_uid['id'] );
					$_email->data['group']			= $this->user->get_group( $_group_id )->display_name;
					
					if ( ! $this->emailer->send( $_email, TRUE ) ) :
					
						//	Failed to send using the group email, try using the generic email
						$_email->type = 'register_tw';
						
						if ( ! $this->emailer->send( $_email, TRUE ) ) :
						
							//	Email failed to send, for now, do nothing.
						
						endif;
					
					endif;
					
					// --------------------------------------------------------------------------
					
					//	Log the user in
					$this->user->set_login_data( $_uid['id'], $email, $_group_id );
		
					// --------------------------------------------------------------------------
					
					//	Create an event for this event
					create_event( 'did_register', $_uid['id'], 0, NULL, array( 'method' => 'facebook' ) );
					
					// --------------------------------------------------------------------------
					
					//	Redirect to the wizard
					$this->session->set_flashdata( 'success', '<strong>Hi, ' . $_data['first_name'] . '!</strong> Your account has been set up and is ready to be used.' );
					$this->session->set_flashdata( 'from_twitter', TRUE );
					$this->_redirect( $this->_return_to );
					return;
				
				endif;
			
			else :
			
				$this->data['error'] = '<strong>There was a problem.</strong> Please check highlighted fields for errors.';
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Store the access token in the Session so we can interrupt the auth flow cleanly
		$this->session->set_userdata( 'tw_access_token', $access_token );
		
		// --------------------------------------------------------------------------
		
		//	Set some view data
		$this->data['page']				= new stdClass();
		$this->data['page']->title		= 'Almost there!';
		
		$this->data['return_to']		= $this->_return_to;
		$this->data['return_to_fail']	= $this->_return_to_fail;
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'auth/register/twitter',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
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

	class Tw extends NAILS_Tw
	{
	}

endif;

/* End of file tw.php */
/* Location: ./application/modules/auth/controllers/tw.php */