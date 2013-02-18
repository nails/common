<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth API
 *
 * Description:	This controller handles auth API methods
 * 
 **/

require_once '_api.php';

class Auth extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Instant search specific constructor
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
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
				$_first_name	= $_login['first_name'];
				
				if ( $_login['last_login'] ) :
				
					$_last_login	=  nice_time( strtotime( $_login['last_login'] ) );
					$this->session->set_flashdata( 'message', lang( 'login_ok_welcome', array( $_first_name, $_last_login ) ) );
					
				else :
				
					$this->session->set_flashdata( 'message', '<strong>Hey ' . $_first_name . '!</strong> Nice to see you again.' );
				
				endif;
				
				$_redirect = ( $this->data['return_to'] ) ? $this->data['return_to'] : $_login['homepage'];
				
				// --------------------------------------------------------------------------
				
				//	Generate an event for this log in
				create_event( 'did_log_in', $_login['user_id'] );
				
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
		if ( $this->user->is_logged_in() ) :
		
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

/* End of file auth.php */
/* Location: ./application/modules/api/controllers/auth.php */