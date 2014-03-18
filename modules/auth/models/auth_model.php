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
	public $activation_code;
	protected $_errors;
	protected $_error_delimiter;
	protected $_messages;
	protected $_message_delimiter;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
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
	 * @param	string $identifier The identifier to use for the user lookup
	 * @param	string $password The user's password
	 * @param	boolean $remember Whether to 'remember' the user or not
	 * @return	object
	 **/
	public function login( $identifier, $password, $remember = FALSE )
	{
		//	Delay execution for a moment (reduces brute force efficiently)
		if ( ENVIRONMENT !== 'development' ) :

			usleep( $this->brute_force_protection['delay'] );

		endif;

		// --------------------------------------------------------------------------

		if ( empty( $identifier ) || empty( $password ) ) :

			$this->_set_error( 'auth_login_fail_missing_field' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Look up the user, how we do so depends on the login mode that the app is using
		switch( APP_NATIVE_LOGIN_USING ) :

			case 'EMAIL' :

				$_user = $this->user->get_by_email( $identifier );

			break;

			// --------------------------------------------------------------------------

			case 'USERNAME' :

				$_user = $this->user->get_by_username( $identifier );

			break;

			// --------------------------------------------------------------------------

			case 'BOTH' :
			default :

				$this->load->helper( 'email' );

				if ( valid_email( $identifier ) ) :

					$_user = $this->user->get_by_email( $identifier );

				else :

					$_user = $this->user->get_by_username( $identifier );

				endif;

			break;

		endswitch;

		// --------------------------------------------------------------------------

		if ( $_user ) :

			//	User was recognised; validate credentials

			//	Generate the hashed password to check against
			$password = $this->user->hash_password_db( $_user->id, $password );

			if ( $_user->password === $password ) :

				//	Password accepted! Final checks...

				//	Suspended user?
				if ( $_user->is_suspended ) :

					$this->_set_error( 'auth_login_fail_suspended' );
					return FALSE;

				endif;

				//	Exceeded login count, temporarily blocked
				if ( $_user->failed_login_count >= $this->brute_force_protection['limit'] ) :

					//	Check if the block has expired
					if ( time() < strtotime( $_user->failed_login_expires ) ) :

						$_block_time= ceil( $this->brute_force_protection['expire']/60 );
						$this->_set_error( 'auth_login_fail_blocked', $_block_time );
						return FALSE;

					endif;

				endif;

				//	Reset user's failed login counter and allow login
				$this->user->reset_failed_login( $_user->id );

				//	If two factor auth is enabled then don't _actually_ set login data
				//	the next process will confirm the login and set this.

				if ( ! $this->config->item( 'auth_two_factor_enable' ) ) :

					//	Set login data for this user
					$this->user->set_login_data( $_user->id );

					//	If we're remembering this user set a cookie
					if ( $remember ) :

						$this->user->set_remember_cookie( $_user->id, $_user->password, $_user->email );

					endif;

					//	Update their last login and increment their login count
					$this->user->update_last_login( $_user->id );

				endif;

				// Return some helpful data
				$_return = array(
					'user_id'		=> $_user->id,
					'first_name'	=> $_user->first_name,
					'last_login'	=> $_user->last_login,
					'last_ip'		=> $_user->last_ip,
					'homepage'		=> $_user->group_homepage,
					'remember'		=> $remember
				);

				//	Two factor auth?
				if (  $this->config->item( 'auth_two_factor_enable' ) ) :

					//	Generate token
					$_return['two_factor_auth'] = $this->generate_two_factor_token( $_user->id );

				endif;

				//	Temporary password?
				if ( $_user->temp_pw ) :

					$_return['temp_pw']			= array();
					$_return['temp_pw']['id']	= $_user->id;
					$_return['temp_pw']['hash']	= md5( $_user->salt );

				endif;

				return $_return;

			// --------------------------------------------------------------------------

			//	Is the password NULL? If so it means the account was created using an API of sorts
			elseif ( $_user->password === NULL ) :

				switch( APP_NATIVE_LOGIN_USING ) :

					case 'EMAIL' :

						$_identifier = $_user->email;

					break;

					// --------------------------------------------------------------------------

					case 'USERNAME' :

						$_identifier = $_user->username;

					break;

					// --------------------------------------------------------------------------

					case 'BOTH' :
					default :

						$_identifier = $_user->email;

					break;

				endswitch;

				switch( $user->auth_method_id ) :

					//	Facebook Connect
					case '2':		$this->_set_error( 'auth_login_fail_social_fb', site_url( 'auth/forgotten_password?identifier=' . $_identifier ) );	break;

					//	Twitter
					case '3':		$this->_set_error( 'auth_login_fail_social_tw', site_url( 'auth/forgotten_password?identifier=' . $_identifier ) );	break;

					//	LinkedIn
					case '5':		$this->_set_error( 'auth_login_fail_social_li', site_url( 'auth/forgotten_password?identifier=' . $_identifier ) );	break;

					//	Other
					default:		$this->_set_error( 'auth_login_fail_social', site_url( 'auth/forgotten_password?identifier=' . $_identifier ) );	break;

				endswitch;
				return FALSE;


			// --------------------------------------------------------------------------

			else :

				//	User was recognised but the password was wrong

				//	Increment the user's failed login count
				$this->user->increment_failed_login( $_user->id, $this->brute_force_protection['expire'] );

				//	Are we already blocked? Let them know...
				if ( $_user->failed_login_count >= $this->brute_force_protection['limit'] ) :

					//	Check if the block has expired
					if ( time() < strtotime( $_user->failed_login_expires ) ) :

						$_block_time= ceil( $this->brute_force_protection['expire']/60 );
						$this->_set_error( 'auth_login_fail_blocked', $_block_time );
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
	 **/
	public function logout()
	{
		// Delete the remember me cookies if they exist
		$this->user->clear_remember_cookie();

		// --------------------------------------------------------------------------

		//	NULL the remember_code so that auto-logins stop
		$this->db->set( 'remember_code', NULL );
		$this->db->where( 'id', active_user( 'id' ) );
		$this->db->update( NAILS_DB_PREFIX . 'user' );

		// --------------------------------------------------------------------------

		//	Destroy key parts of the session (enough for user_model to report user as logged out)
		$this->user->clear_login_data();

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


	public function generate_two_factor_token( $user_id )
	{
		$_salt		= $this->user->salt();
		$_ip		= $this->input->ip_address();
		$_created	= date( 'Y-m-d H:i:s' );
		$_expires	= date( 'Y-m-d H:i:s', strtotime( '+10 MINS' ) );

		$_token				= array();
		$_token['token']	= sha1( sha1( APP_PRIVATE_KEY . $user_id . $_created . $_expires . $_ip ) . $_salt );
		$_token['salt']		= md5( $_salt );

		//	Add this to the DB
		$this->db->set( 'user_id',	$user_id );
		$this->db->set( 'token',	$_token['token'] );
		$this->db->set( 'salt',		$_token['salt'] );
		$this->db->set( 'created',	$_created );
		$this->db->set( 'expires',	$_expires );
		$this->db->set( 'ip',		$_ip );

		if ( $this->db->insert( NAILS_DB_PREFIX . 'user_auth_two_factor_token' ) ) :

			$_token['id'] = $this->db->insert_id();

			return $_token;

		else :

			$this->_set_error( 'auth_twofactor_token_could_not_generate' );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function verify_two_factor_token( $user_id, $salt, $token, $ip )
	{
		$this->db->where( 'user_id',	$user_id );
		$this->db->where( 'salt',		$salt );
		$this->db->where( 'token',		$token );

		$_token		= $this->db->get( NAILS_DB_PREFIX . 'user_auth_two_factor_token' )->row();
		$_return	= TRUE;

		if ( ! $_token ) :

			$this->_set_error( 'auth_twofactor_token_invalid' );
			return FALSE;

		elseif ( strtotime( $_token->expires ) <= time() ) :

			$this->_set_error( 'auth_twofactor_token_expired' );
			$_return = FALSE;

		elseif ( $_token->ip != $ip ) :

			$this->_set_error( 'auth_twofactor_token_bad_ip' );
			$_return = FALSE;

		endif;

		//	Delete the token
		$this->db->where( 'id', $_token->id );
		$this->db->delete( NAILS_DB_PREFIX . 'user_auth_two_factor_token' );

		return $_return;
	}


	// --------------------------------------------------------------------------


	public function delete_two_factor_token( $token_id )
	{
		$this->db->where( 'id', $token_id );
		$this->db->delete( NAILS_DB_PREFIX . 'user_auth_two_factor_token' );
		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	/**
	 * Sets a new error message
	 *
	 * @access	protected
	 * @param	string	$error	The error string
	 * @param	array	$vars	Variables to parse into the error string
	 * @return	void
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
	 **/
	public function get_errors()
	{
		$_output = '';

		if ( ! is_array( $this->_errors ) ) :

			return FALSE;

		endif;

		foreach ( $this->_errors as $error ) :

			if ( ! is_array( $error ) ) :

				$_error = lang( $error ) ? lang( $error ) : $error;
				$_output .= $this->_error_delimiter[0] . $_error . $this->_error_delimiter[1];

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_AUTH_MODEL' ) ) :

	class Auth_model extends NAILS_Auth_model
	{
	}

endif;


/* End of file auth_model.php */
/* Location: ./modules/auth/models/auth_model.php */