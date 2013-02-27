<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Facebook
*
* Description:	Gateway to the FB PHP SDK
* 
*/

class Facebook_Connect {
	
	private $ci;
	private $settings;
	private $facebook;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 **/
	public function __construct()
	{
		$this->ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Fetch our config variables
		$this->ci->config->load( 'facebook' );
		$this->settings = $this->ci->config->item( 'facebook' );
		array_unshift( $this->settings['scope'], 'email' );
		
		// --------------------------------------------------------------------------
		
		//	Fire up and initialize the SDK
		require NAILS_PATH . 'libraries/_resources/facebook-php-sdk/src/facebook.php';
		$this->facebook = new Facebook( $this->settings );
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether the active user has already linked their Facebook profile
	 *
	 * @access	public
	 * @return	void
	 **/
	public function user_is_linked()
	{
		return (bool) active_user( 'fb_id' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches the login URL
	 *
	 * @access	public
	 * @param	string $success Where to redirect the user to on successful login
	 * @param	string $fail Where to redirect the user to on failed login
	 * @return	void
	 **/
	public function get_login_url( $success, $fail )
	{	
		//	Prep params
		//$_params['scope']			= 'email,user_location,user_photos,user_birthday,user_work_history,friends_education_history,friends_work_history';
		$_params['scope']			= implode( ',', $this->settings['scope'] );
		$_params['redirect_uri']	= $this->_get_redirect_url( $success, $fail );
		$_params['display']			= 'page';
		
		return $this->getLoginUrl( $_params );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Gets the URL where the user will be redirected to after connecting/logging in
	 *
	 * @access	public
	 * @param	string $success Where to redirect the user to on successful login
	 * @param	string $fail Where to redirect the user to on failed login
	 * @return	void
	 **/
	private function _get_redirect_url( $success, $fail )
	{
		//	Set a little userdata for when we come back
		$_data									= array();
		$_data['nailsFBConnectReturnTo']		= $success ? $success : active_user( 'group_homepage' );
		$_data['nailsFBConnectReturnToFail']	= $fail ? $fail : $success;
		
		//	Filter out empty items
		$_data = array_filter( $_data );
		$_query_string = $_data ? '?' . http_build_query( $_data ) : NULL;
		
		return site_url( 'auth/fb/connect/verify' . $_query_string  );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches a user's access token
	 *
	 * @access	public
	 * @param	string $code The verification code
	 * @param	string $success Where to redirect the user to on successful login
	 * @param	string $fail Where to redirect the user to on failed login
	 * @return	void
	 **/
	public function get_access_token( $code, $success, $fail )
	{
		$_url	= 'https://graph.facebook.com/oauth/access_token?client_id=' .
				  $this->settings['appId'] . '&redirect_uri=' . urlencode( $this->_get_redirect_url( $success, $fail ) ) .
				  '&client_secret=' . $this->settings['secret'] . '&code=' . $code;
		
		$_data	= @file_get_contents( $_url );
		
		if ( $_data ) :
		
			parse_str( $_data, $_access_token );
			return $_access_token;
		
		else :
		
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Sets a user's access token
	 *
	 * @access	public
	 * @param	string $access_token The Access token to use
	 * @return	void
	 **/
	public function set_access_token( $access_token )
	{
		$this->setAccessToken( $access_token );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Unlinks a local account from Facebook
	 *
	 * @access	public
	 * @param	int	$user_id The ID of the user to unlink
	 * @return	void
	 **/
	public function unlink_user( $user_id )
	{
		//	TODO Use the supplied user_id rather than the active_user
		//	Attempt to revoke permissions on Facebook
		$this->api( '/' . active_user( 'fb_id' ) . '/permissions', 'DELETE' );
		
		// --------------------------------------------------------------------------
		
		$this->destroySession();
		
		// --------------------------------------------------------------------------
		
		//	Update our user
		$_data['fb_id']		= NULL;
		$_data['fb_token']	= NULl;
		
		return get_userobject()->update( $user_id, $_data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Map method calls to the FB library
	 *
	 * @access	public
	 * @return	mixed
	 **/
	public function __call( $method, $arguments )
	{
		if ( method_exists( $this->facebook, $method ) ) :
		
			return call_user_func_array( array( $this->facebook, $method ), $arguments );
		
		else:
		
			show_error( 'Method does not exist Facebook::' . $method );
		
		endif;
	}
}

/* End of file Facebook_connect.php */
/* Location: ./application/libraries/Facebook_connect.php */