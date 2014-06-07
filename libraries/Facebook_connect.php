<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Facebook
*
* Description:	Gateway to the FB PHP SDK
*
*/

class Facebook_Connect
{

	private $_ci;
	private $_settings;
	private $_facebook;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 **/
	public function __construct()
	{
		$this->_ci = get_instance();

		// --------------------------------------------------------------------------

		//	Fetch our config variables
		$this->_settings			= array();
		$this->_settings['appId']	= app_setting( 'social_signin_fb_app_id' );
		$this->_settings['secret']	= app_setting( 'social_signin_fb_app_secret' );
		$this->_settings['scope']	= app_setting( 'social_signin_fb_app_scope' );

		if ( ! is_array( $this->_settings['scope'] ) ) :

			$this->_settings['scope'] = (array) $this->_settings['scope'];

		endif;

		if ( $this->_settings['secret'] ) :

			$this->_settings['secret'] = $this->_ci->encrypt->decode( $this->_settings['secret'], APP_PRIVATE_KEY );

		endif;

		//	Add the email scope
		array_unshift( $this->_settings['scope'], 'email' );
		$this->_settings['scope'] = array_filter( $this->_settings['scope'] );
		$this->_settings['scope'] = array_unique( $this->_settings['scope'] );

		//	Sanity check
		if ( ! $this->_settings['appId'] || ! $this->_settings['secret'] ) :

			if ( ENVIRONMENT === 'production' ) :

				show_fatal_error( 'Facebook has not been configured correctly', 'The Facebook App ID and secret must be specified in Admin under Site Settings.' );

			else :

				show_error( 'The Facebook App ID and secret must be specified in Admin under Site Settings.' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Fire up and initialize the SDK
		$this->_facebook = new Facebook( $this->_settings );

	}


	// --------------------------------------------------------------------------


	/**
	 * Determines whether the active user has already linked their Facebook profile
	 *
	 * @access	public
	 * @return	void
	 **/
	public function user_is_linked( $user_id = NULL )
	{
		if ( NULL === $user_id ) :

			return (bool) active_user( 'fb_id' );

		else :

			$_u = get_userobject()->get_by_id( $user_id );

			return ! empty( $_u->fb_id );

		endif;
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
		$_params['scope']			= implode( ',', $this->_settings['scope'] );
		$_params['redirect_uri']	= $this->_get_redirect_url( $success, $fail );
		$_params['display']			= 'page';

		return $this->_facebook->getLoginUrl( $_params );
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
				  $this->_settings['appId'] . '&redirect_uri=' . urlencode( $this->_get_redirect_url( $success, $fail ) ) .
				  '&client_secret=' . $this->_settings['secret'] . '&code=' . $code;

		$this->_ci->load->library( 'curl' );
		$_data = $this->_ci->curl->simple_get( $_url );

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
		$this->_facebook->setAccessToken( $access_token );
	}


	// --------------------------------------------------------------------------


	/**
	 * Unlinks a local account from Facebook
	 *
	 * @access	public
	 * @param	int	$user_id The ID of the user to unlink
	 * @return	void
	 **/
	public function unlink_user( $user_id = NULL )
	{
		//	Grab reference to the userobject
		$_userobj =& get_userobject();

		// --------------------------------------------------------------------------

		if ( NULL === $user_id ) :

			$_uid	= active_user( 'id' );
			$_fb_id	= active_user( 'fb_id' );

		else :

			if ( is_callable( array( $_userobj, 'get_by_id' ) ) ) :

				$_u = get_userobject()->get_by_id( $user_id );

				if ( ! empty( $_u->fb_id ) ) :

					$_uid	= $_u->id;
					$_fb_id	= $_u->fb_id;

				else :

					return FALSE;

				endif;

			else :

				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Attempt to revoke permissions on Facebook
		$this->_facebook->api( '/' . $_fb_id . '/permissions', 'DELETE' );

		// --------------------------------------------------------------------------

		$this->_facebook->destroySession();

		// --------------------------------------------------------------------------

		//	Update our user
		if ( is_callable( array( $_userobj, 'update' ) ) ) :

			$_data				= array();
			$_data['fb_id']		= NULL;
			$_data['fb_token']	= NULl;

			return $_userobj->update( $_uid, $_data );

		else :

			return TRUE;

		endif;
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
		if ( is_callable( array( $this->_facebook, $method ) ) ) :

			return call_user_func_array( array( $this->_facebook, $method ), $arguments );

		else:

			show_error( 'Method does not exist Facebook::' . $method );

		endif;
	}
}

/* End of file Facebook_connect.php */
/* Location: ./application/libraries/Facebook_connect.php */