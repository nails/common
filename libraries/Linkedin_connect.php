<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			LinkedIn
* 
* Description:	Gateway to the LinkedIn API
* 
*/

class Linkedin_connect {
	
	private $ci;
	private $settings;
	private $oauth;
	private $access;
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		$this->ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Default vars
		$this->access			= new stdClass();
		$this->access->token	= NULL;
		$this->access->secret	= NULL;
		
		// --------------------------------------------------------------------------
		
		//	Fetch our config variables
		$this->ci->config->load( 'linkedin' );
		$this->settings = $this->ci->config->item( 'linkedin' );
		
		// --------------------------------------------------------------------------
		
		//	Fire up a new oAuth instance
		$this->oauth = new OAuth( $this->settings['consumer_key'], $this->settings['consumer_secret'] );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether the active user has already linked their LinkedIn profile
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function user_is_linked()
	{
		return (bool) active_user( 'li_id' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Generate a URL for authentication
	 *
	 * @access	public
	 * @param	string	$callback	The callback to return to the user to.
	 * @return	string
	 * @author	Pablo
	 **/
	public function get_auth_url( $callback = NULL )
	{
		$_request_url	= $this->settings['api_endpoint'] . 'uas/oauth/requestToken';
		$_response		= $this->oauth->getRequestToken( $_request_url, $callback );
		
		// --------------------------------------------------------------------------
		
		//	If an error was received throw back an error
		if ( ! $_response )
			return FALSE;
			
		// --------------------------------------------------------------------------
		
		//	Build URL
		$_out = 'https://www.linkedin.com/uas/oauth/authenticate?oauth_token=' . $_response['oauth_token'];
		
		// --------------------------------------------------------------------------
		
		//	Save the response into the session for picking up later (i.e when we get our access token)
		$this->ci->session->set_userdata( 'li_request_token', $_response );
		
		// --------------------------------------------------------------------------
		
		//	Return the URL
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an access token for the user
	 *
	 * @access	public
	 * @param	none
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_access_token( $verifier )
	{
		$_request_url	= $this->settings['api_endpoint'] . 'uas/oauth/accessToken';
		
		// --------------------------------------------------------------------------
		
		//	Fetch our previously saved request tokens and set as the oAuth token to use
		$_token			= $this->ci->session->userdata( 'li_request_token' );
		
		$this->oauth->setToken( $_token['oauth_token'], $_token['oauth_token_secret'] );
		
		// --------------------------------------------------------------------------
		
		//	Fetch the access token
		$_response		= $this->oauth->getAccessToken( $_request_url, NULL, $verifier );
		
		// --------------------------------------------------------------------------
		
		//	If an error was received throw back an error
		if ( ! $_response )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_response;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Set the oAuth tokens to use for subsequent calls
	 *
	 * @access	public
	 * @param	string	$token			The token
	 * @param	string	$token_secret	The token secret
	 * @return	void
	 * @author	Pablo
	 **/
	public function set_access_token( $token, $token_secret )
	{
		$this->access->token	= $token;
		$this->access->secret	= $token_secret;
		
		// --------------------------------------------------------------------------
		
		$this->oauth->setToken( $token, $token_secret );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Call the LinkedIn API
	 *
	 * @access	public
	 * @param	string	$method		The API method to call
	 * @param	array	$params		Parameters to send along with the request
	 * @return	mixed
	 * @author	Pablo
	 **/
	public function call( $method, $params = NULL )
	{
		//	Prep the API URL
		$_url = $this->settings['api_endpoint'] . 'v1/' . $method;
		
		$_params = array(
			//'format'					=>	'json',
		);
		
		$_params_oauth = array(
		
			//	Oauth
			'oauth_consumer_key'		=>	$this->settings['consumer_key'],
			'oauth_nonce'				=>	md5( microtime( TRUE ) * 10000 ),
			'oauth_signature_method'	=>	'HMAC-SHA1',
			'oauth_timestamp'			=>	time(),
			'oauth_token'				=>	$this->access->token,
			'oauth_version'				=>	'1.0',
		);
		
		// --------------------------------------------------------------------------
		
		//	Add in user supplied params
		if ( $params )
			$_params = array_merge( $_params, $params );
		
		// --------------------------------------------------------------------------
		
		//	Sign the call
		ksort( $_params_oauth );
		
		$_key	 = $this->settings['consumer_secret'] . '&' . $this->access->secret;
		$_base	 = 'GET&' . rawurlencode( $_url ) . '&' . rawurlencode( http_build_query( $_params_oauth ) );
		$_sig	 = base64_encode( hash_hmac( 'sha1', $_base, $_key, TRUE ) );
		$_url	.= ( $_params ) ? '?' . http_build_query( $_params ) : NULL;
		
		// --------------------------------------------------------------------------
		
		//	Build auth header
		$_auth_header = "Authorization: OAuth ";
		
		foreach( $_params_oauth AS $key => $value ) :
		
			$_auth_header .= $key . '="' . $value . '", ';
		
		endforeach;
		
		$_auth_header .= 'oauth_signature="' . rawurlencode( $_sig ) . '"';
		
		// --------------------------------------------------------------------------
		
		//	Make the call
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $_url );
		curl_setopt( $ch, CURLOPT_HEADER, FALSE );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $_auth_header, 'x-li-format: json' ) );
		$_response = curl_exec($ch);
		curl_close($ch);
		
		return json_decode( $_response );
	}
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Unlinks a local account from LinkedIn
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function unlink_user( $user_id )
	{
		//	Update our user
		$_data['li_id']		= NULL;
		$_data['li_token']	= NULl;
		$_data['li_secret']	= NULl;
		
		return get_userobject()->update( $user_id, $_data );
	}
}

/* End of file Linkedin_connect.php */
/* Location: ./application/libraries/Linkedin_connect.php */