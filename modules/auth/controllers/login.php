<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [login]
 *
 * Description:	This module handles the login process for all users.
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

class NAILS_Login extends NAILS_Auth_Controller
{
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
		
		//	Where are we returning user to?
		$this->data['return_to'] = $this->input->get( 'return_to' );
		
		// --------------------------------------------------------------------------
		
		//	Specify a default title for this page
		$this->data['page']->title = lang( 'auth_title_login' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Validate data and log the user in.
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
			$this->form_validation->set_rules( 'email',		'Email',	'required|xss_clean|valid_email' );
			$this->form_validation->set_rules( 'password',	'Password',	'required|xss_clean' );
			
			$this->form_validation->set_message( 'required',		lang( 'fv_required' ) );
			$this->form_validation->set_message( 'valid_email',	lang( 'fv_valid_email' ) );
			
			if ( $this->form_validation->run() ) :
			
				//	Attempt the log in
				$_email		= $this->input->post( 'email' );
				$_password	= $this->input->post( 'password' );
				$_remember	= $this->input->post( 'remember' );
				
				$_login = $this->auth_model->login( $_email, $_password, $_remember );
				
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
						
						redirect( 'auth/reset_password/' . $_login['temp_pw']['id'] . '/' . $_login['temp_pw']['hash'] . $_return_to );
						return;
					
					else :
					
						//	Finally! Send this user on their merry way...
						$_first_name	= $_login['first_name'];
						
						if ( $_login['last_login'] ) :
						
							$_last_login	=  nice_time( strtotime( $_login['last_login'] ) );
							$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome', array( $_first_name, $_last_login ) ) );
							
						else :
						
							$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_notime', array( $_first_name ) ) );
						
						endif;
						
						$_redirect = ( $this->data['return_to'] ) ? $this->data['return_to'] : $_login['homepage'];
						
						// --------------------------------------------------------------------------
						
						//	Generate an event for this log in
						create_event( 'did_log_in', $_login['user_id'], 0, NULL, array( 'method' => 'native' ) );
						
						// --------------------------------------------------------------------------
						
						redirect( $_redirect );
						return;
					
					endif;
					
				else :
				
					//	Login failed
					$this->data['error'] = $this->auth_model->get_errors();
				
				endif;
			
			else :
			
				$this->data['error'] = lang( 'fv_there_were_errors' );
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load the views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'auth/login/form',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Log a user in using hashes of their user ID and password; easy way of
	 * automatically logging a user in from the likes of an email.
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function with_hashes()
	{
		$_hash['id']	= $this->uri->segment( 4 );
		$_hash['pw']	= $this->uri->segment( 5 );
		
		if ( empty( $_hash['id'] ) || empty( $_hash['pw'] ) ) :
			
			show_error( $lang['auth_with_hashes_incomplete_creds'] );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		/**
		 * If the user is already logged in we need to check to see if we check to see if they are
		 * attempting to login as themselves, if so we redirect, otherwise we log them out and try
		 * again using the hashes.
		 * 
		 **/
		if ( $this->user->is_logged_in() ) :
		
			if ( md5( active_user( 'id' ) ) == $_hash['id'] ) :
			
				//	We are attempting to log in as who we're already logged in as, redirect normally
				if ( $this->data['return_to'] ) :
				
					redirect( $this->data['return_to'] );
				
				else :
				
					//	Nowhere to go? Send them to their default homepage
					redirect( active_user( 'group_homepage' ) );
				
				endif;
			
			else :
			
				//	We are logging in as someone else, log the current user out and try again
				$this->auth_model->logout();
				
				redirect( preg_replace( '/^\//', '', $_SERVER['REQUEST_URI'] ) );
			
			endif;
			
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		/**
		 * The active user is a guest, we must look up the hashed user and log them in
		 * if all is ok otherwise we report an error.
		 * 
		 **/
		
		$_user = $this->user->get_by_hashes( $_hash['id'], $_hash['pw'] );
		
		// --------------------------------------------------------------------------
		
		if ( $_user ) :
		
			//	User was verified, log the user in
			$this->user->set_login_data( $_user->id, $_user->email, $_user->group_id );
			
			// --------------------------------------------------------------------------
			
			//	Say hello
			$_welcome = lang( 'auth_login_ok_welcome', array( $_user->first_name, nice_time( strtotime( $_user->last_login ) ) ) );
			$this->session->set_flashdata( 'message', $_welcome );
			
			// --------------------------------------------------------------------------
			
			//	Update their last login
			$this->user->update_last_login( $_user->id );
			
			// --------------------------------------------------------------------------
			
			//	Redirect user
			if ( $this->data['return_to'] ) :
			
				//	We have somewhere we want to go
				redirect( $this->data['return_to'] );
			
			else :
			
				//	Nowhere to go? Send them to their default homepage
				redirect( $_user->group_homepage );
			
			endif;
		
		else :
		
			//	Bad lookup, invalid hash.
			$this->session->set_flashdata( 'error', lang( 'auth_with_hashes_autologin_fail' ) );
			redirect( '/' );
		
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

	class Login extends NAILS_Login
	{
	}

endif;

/* End of file login.php */
/* Location: ./application/modules/auth/controllers/login.php */