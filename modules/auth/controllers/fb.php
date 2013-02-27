<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [Facebook]
 *
 * Description:	This controller handles connecting accounts to Facebook
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

class NAILS_Fb extends NAILS_Auth_Controller
{
	protected $_return_to;
	protected $_return_to_fail;
	
	
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
		$this->_return_to = $this->input->get( 'return_to' );
		
		//	If nothing, check the 'nailsFBConnectReturnTo' GET var which may be passed back
		if ( ! $this->_return_to ) :
		
			$this->_return_to = $this->input->get( 'nailsFBConnectReturnTo' );
			
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
		
			$this->_return_to_fail = $this->input->get( 'nailsFBConnectReturnToFail' );
			
			if ( ! $this->_return_to_fail ) :
			
				//	Fallback to the value of $this->return_to
				$this->_return_to_fail = $this->_return_to;
				
			endif;
			
		endif;
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
		
			$this->session->set_flashdata( 'message', '<strong>Woah there!</strong> You have already linked your Facebook account.' );
			$this->_connect_fail();
			return;
			
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
			
				$this->session->set_flashdata( 'error', '<strong>There was a problem.</strong> We could not validate your account with Facebook, you may be able to try again.' );
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
		
		$_user = $this->user->get_user_by_fbid( $_me['id'] );

		if ( $this->user->is_logged_in() && $_user ) :
		
			//	This Twitter ID is already in use, tell the user so and prevent anything else from happening.
			$this->session->set_flashdata( 'error', '<strong>Sorry</strong>, the Facebook account you\'re currently logged into is already linked with another ' . APP_NAME . ' account.' );
			$this->_connect_fail();
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If the user is already logged in then skip the email check and link the
		//	two accounts together.
		
		if ( $this->user->is_logged_in() ) :
		
			$this->_link_user( $_me );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If we recognise the user, update their access token, if not create a new account
		$_user = $this->user->get_user_by_fbid( $_me['id'] );
		
		if ( ! $_user ) :
		
			//	Not recognised via Facebook ID, what about via their email?
			$_user = $this->user->get_user_by_email( $_me['email'] );
			
			if ( ! $_user ) :
			
				//	OK, fine, this is a new user!
				$this->_create_user( $_me );
				
			else :
				
				//	An account has been found which uses this email but this Facebook
				//	ID is not associated with any account. We need to alert the user that the email
				//	is already regsitered to an account and that they need to log in and link the
				//	account from their settings page.
				
				$this->session->set_flashdata( 'message', '<strong>You\'ve been here before?</strong> We noticed that the email associated with your Facebook account is already registered with ' . APP_NAME . '. In order to use Facebook to sign in you\'ll need to link your accounts via your Settings page. Log in below using your email address and we\'ll get you started.' );
				$this->_redirect( 'auth/login?return_to=' . urlencode( $this->settings['settings_url'] ) );
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
		$this->_redirect( $this->return_to_fail );
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
		dumpanddie( 'TODO Handle disconnection' );
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
		$this->session->set_flashdata( 'success', '<strong>Success</strong>, your Facebook account is now linked.' );
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
		
		//	Check if the user is banned.
		if ( $user->active == 2 ) :
			
			$this->session->set_flashdata( 'error', lang( 'login_fail_banned' ) );
			$this->_redirect( $this->return_to_fail );
			return;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Update token
		$_data['fb_token'] = $this->fb->getAccessToken();
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
		create_event( 'did_log_in', $user->id, 0, NULL, array( 'method' => 'facebook' ) );
		
		// --------------------------------------------------------------------------
		
		//	Redirect
		$this->_redirect( $this->return_to );
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
		$_data['active']			= 1;	//	Trust the email from Facebook
		$_data['auth_method_id']	= 2;	//	Facebook, obviously.
		
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
		
		//	Create new user, group 2, standard member
		$_uid = $this->user->create( $email, $password, 2, $_data );
		
		if ( $_uid ) :
		
			//	Some nice data...
			$this->data['email']	= $email;
			$this->data['user_id']	= $_uid['id'];
			$this->data['hash']		= $_uid['activation'];
			
			// --------------------------------------------------------------------------
			
			//	Registration was successfull, send the welcome email...
			$this->load->library( 'emailer' );
			
			$_email							= new stdClass();
			$_email->to_id					= $_uid['id'];
			$_email->type					= 'register_fb';
			$_email->data['first_name']		= $_data['first_name'];
			$_email->data['user_id']		= $_uid['id'];
			
			//	Send the email
			$this->emailer->send( $_email );
			
			// --------------------------------------------------------------------------
			
			//	Log the user in
			$this->user->set_login_data( $_uid['id'], $email, 2 );

			// --------------------------------------------------------------------------
			
			//	Create an event for this event
			create_event( 'did_register', $_uid['id'], 0, NULL, array( 'method' => 'facebook' ) );
			
			// --------------------------------------------------------------------------
			
			//	Redirect to the wizard
			$this->session->set_flashdata( 'success', '<strong>Hi, ' . $_data['first_name'] . '!</strong> Your account has been set up and is ready to be used.' );
			$this->session->set_flashdata( 'from_facebook', TRUE );
			$this->_redirect( $this->_return_to );
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

	class Fb extends NAILS_Fb
	{
	}

endif;

/* End of file fb.php */
/* Location: ./application/modules/auth/controllers/fb.php */