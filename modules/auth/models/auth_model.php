<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth_model
 *
 * Description:	This model handles all things auth.
 * 
 **/

/**
 * OVERLOADING NAILS' MODELS
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

class NAILS_Auth_model extends NAILS_Model
{
	public  $activation_code;
	private $_errors;
	private $_error_delimiter;
	private $_messages;
	private $_message_delimiter;
	
	
	// --------------------------------------------------------------------------
	
	
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
		
		//	Set variables
		$this->brute_force_protection			= array();
		$this->brute_force_protection['delay']	= 1500000;
		$this->brute_force_protection['limit']	= 10;
		$this->brute_force_protection['expire']	= 900;
		$this->error_delimiter					= array( '<p>', '</p>' );
		$this->message_delimiter				= array( '<p>', '</p>' );
		
		// --------------------------------------------------------------------------
		
		//	Load helpers
		$this->load->helper( 'date' );
		$this->load->helper( 'cookie' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Log a user in
	 *
	 * @access	public
	 * @param	string	$email		The user's email address
	 * @param	string	$password	The user's password
	 * @param	boolean	$remember	Whether to 'remember' the user or not
	 * @return	object
	 * @author	Pablo
	 **/
	public function login( $email, $password, $remember = FALSE )
	{
		//	Delay execution for a moment (reduces brute force efficienty)
		usleep( $this->brute_force_protection['delay'] );
		
		// --------------------------------------------------------------------------
		
		if ( empty( $email ) || empty( $password ) ) :
		
			$this->_set_error( 'auth_login_fail_missing_field' );
			return FALSE;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		$user = $this->user->get_by_email( $email );
		
		if ( $user ) :
		
			//	User was recognised; validate credentials
			
			//	Generate the hashed password to check against 
			$password = $this->user->hash_password_db( $email, $password );
			
			if ( $user->password === $password ) :
			
				//	Password accepted! Final checks...
				
				//	Banned user
				if ( $user->is_suspended ) :
				
					$this->_set_error( 'auth_login_fail_banned' );
					return FALSE;
					
				endif;
				
				//	Exceeded login count, temporarily blocked
				if ( $user->failed_login_count >= $this->brute_force_protection['limit'] ) :
				
					//	Check if the block has expired
					if ( time() < strtotime( $user->failed_login_expires ) ) :
					
						$block_time= ceil( $this->brute_force_protection['expire']/60 );
						$this->_set_error( 'auth_login_fail_blocked', $block_time );
						return FALSE;
						
					endif;
					
				endif;
				
				//	Reset user's failed login counter and allow login
				$this->user->reset_failed_login( $user->id );
				
				//	Set login data for this user
				$this->user->set_login_data( $user->id, $user->email, $user->group_id );
				
				//	If we're remembering this user set a cookie
				if ( $remember )
					$this->user->set_remember_cookie();
					
				//	Update their last login and increment their login count
				$this->user->update_last_login( $user->id );
				
				// return some helpful data
				$return = array(
					'user_id'		=> $user->id,
					'first_name'	=> $user->first_name,
					'last_login'	=> $user->last_login,
					'homepage'		=> $user->group_homepage
				);
				
				//	Temporary password?
				if ( $user->temp_pw ) :
				
					$return['temp_pw']['id']	= $user->id;
					$return['temp_pw']['hash']	= md5( $user->salt );
				
				endif;
				
				return $return;
			
			// --------------------------------------------------------------------------
			
			//	Is the password NULL? If so it means the account was created using an API of sorts
			elseif ( $user->password === NULL ) :
			
				switch( $user->auth_method_id ) :
				
					//	Facebook Connect
					case '2':		$this->_set_error( 'auth_login_fail_social_fb', site_url( 'auth/forgotten_password?email=' . $user->email ) );	break;
					
					//	Twitter
					case '3':		$this->_set_error( 'auth_login_fail_social_tw', site_url( 'auth/forgotten_password?email=' . $user->email ) );	break;
					
					//	LinkedIn
					case '5':		$this->_set_error( 'auth_login_fail_social_li', site_url( 'auth/forgotten_password?email=' . $user->email ) );	break;
					
					//	Other
					default:	$this->_set_error( 'auth_login_fail_social', site_url( 'auth/forgotten_password?email=' . $user->email ) );		break;
					
				endswitch;
				return FALSE;
			
			
			// --------------------------------------------------------------------------
			
			else :
			
				//	User was recognised but the password was wrong
				
				//	Increment the user's failed login count
				$this->user->increment_failed_login( $user->id, $this->brute_force_protection['expire'] );
				 
				//	Are we already blocked? Let them know...
				if ( $user->failed_login_count >= $this->brute_force_protection['limit'] ) :
				
					//	Check if the block has expired
					if ( time() < strtotime( $user->failed_login_expires ) ) :
					
						$block_time= ceil( $this->brute_force_protection['expire']/60 );
						$this->_set_error( 'auth_login_fail_blocked', $block_time );
						return FALSE;
						
					endif;
					
					//	Block has expired, reset the counter
					$this->user->reset_failed_login( $user->id );
					
				endif;
			
			endif;
			
		endif;
		
		//	Login failed
		$this->_set_error( 'auth_login_fail_general' );
		return FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Logs a user out
	 *
	 * @access	public
	 * @param	none
	 * @return	bool
	 * @author	Pablo
	 **/
	public function logout()
	{
		// Delete the remember me cookies if they exist
		$this->user->clear_remember_cookie();
		
		// --------------------------------------------------------------------------
		
		//	NULL the remember_code so that auto-logins stop
		$this->db->set( 'remember_code', NULL );
		$this->db->where( 'id', active_user( 'id' ) );
		$this->db->update( 'user' );
		
		// --------------------------------------------------------------------------
		
		//	Destroy key parts of the session (enough for user_model to report user as logged out)
		$this->session->unset_userdata( 'email' );
		$this->session->unset_userdata( 'group_id' );
		
		// --------------------------------------------------------------------------
		
		//	Destory CI session
		$this->session->sess_destroy();
		
		// --------------------------------------------------------------------------
		
		//	Destroy PHP session if it exists
		if ( session_id() ) :
		
			session_destroy();
			
		endif;
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Sets a new error message
	 *
	 * @access	protected
	 * @param	string	$error	The error string
	 * @param	array	$vars	Variables to parse into the error string
	 * @return	void	
	 * @author	Pablo
	 **/
	protected function _set_error( $error, $vars = NULL )
	{
		if ( ! $vars ) :
		
			$this->_errors[] = $error;
			
		else :
		
			//	This var has variables in it
			$this->_errors[]	= array( 'key' => $error, 'vars' => (array) $vars );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Gets and formats error messages
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function get_errors()
	{
		$_output = '';
		
		if ( ! is_array( $this->_errors ) )
			return FALSE;
			
		foreach ( $this->_errors as $error ) :
		
			if ( ! is_array( $error ) ) :
			
				$_output .= $this->_error_delimiter[0] . lang( $error ) . $this->_error_delimiter[1];
				
			else :
				$_output .= $this->_error_delimiter[0] . lang( $error['key'], $error['vars'] ) . $this->_error_delimiter[1];
			
			endif;
			
		endforeach;
		
		return $_output;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 * 
 * The following block of code makes it simple to extend one of the core Nails
 * models. Some might argue it's a little hacky but it's a simple 'fix'
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_AUTH_MODEL' ) ) :

	class Auth_model extends NAILS_Auth_model
	{
	}

endif;


/* End of file auth_model.php */
/* Location: ./modules/auth/models/auth_model.php */