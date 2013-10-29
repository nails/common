<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			LinkedIn
*
* Description:	Gateway to the LinkedIn API
*
*/

class Linkedin_connect
{

	private $_ci;
	private $_settings;
	private $_oauth;
	private $_access_token;

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
		$this->_ci =& get_instance();

		// --------------------------------------------------------------------------

		//	Default vars
		$this->_access_token = '';

		// --------------------------------------------------------------------------

		//	Fetch our config variables
		$this->_ci->config->load( 'linkedin' );
		$this->_settings = $this->_ci->config->item( 'linkedin' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Determines whether the active user has already linked their LinkedIn profile
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function user_is_linked( $user_id = NULL )
	{
		if ( is_null( $user_id ) ) :

			return (bool) active_user( 'li_id' );

		else :

			$_u = get_userobject()->get_by_id( $user_id );

			return ! empty( $_u->li_id );

		endif;
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
	public function get_auth_url( $callback, $state )
	{
		$_params					= array();
		$_params['response_type']	= 'code';
		$_params['client_id']		= $this->_settings['api_key'];
		$_params['scope']			= 'r_basicprofile r_emailaddress';
		$_params['state']			= $state;
		$_params['redirect_uri']	= $callback;

		return 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query( $_params, '', '&', PHP_QUERY_RFC3986 );
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
	public function get_access_token( $callback, $code, $set_token_on_success = TRUE )
	{
		//	Build URL
		$_params					= array();
		$_params['grant_type']		= 'authorization_code';
		$_params['code']			= $code;
		$_params['redirect_uri']	= $callback;
		$_params['client_id']		= $this->_settings['api_key'];
		$_params['client_secret']	= $this->_settings['api_secret'];

		$_url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query( $_params, '', '&', PHP_QUERY_RFC3986 );

		//	Request token
		$this->_ci->load->library( 'curl' );
		$_response = $this->_ci->curl->simple_post( $_url );
		$_response = json_decode( $_response );

		if ( ! empty( $_response->access_token ) ) :

			if ( $set_token_on_success ) :

				$this->set_access_token( $_response->access_token );

			endif;

			return $_response;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Set the access_token to use for subsequent calls
	 *
	 * @access	public
	 * @param	string	$token			The token
	 * @return	void
	 * @author	Pablo
	 **/
	public function set_access_token( $token )
	{
		$this->_access_token = $token;
	}


	// --------------------------------------------------------------------------


	/**
	 * Call the LinkedIn API
	 *
	 * @access	private
	 * @param	string	$method		The API method to call
	 * @param	array	$params		Parameters to send along with the request
	 * @return	mixed
	 * @author	Pablo
	 **/
	private function _call( $method, $api_method, $params = array() )
	{
		//	Prep the API URL
		$_params	= array_merge( $params, array( 'oauth2_access_token' => $this->_access_token ) );
		$_url		= $this->_settings['api_endpoint'] . 'v1/' . $api_method;

		// --------------------------------------------------------------------------

		//	Make the call
		$this->_ci->load->library( 'curl' );

		$this->_ci->curl->option( CURLOPT_HTTPHEADER, array( 'x-li-format:json' )  );

		switch ( $method ) :

			case 'GET' :

				$_url .= '?' . http_build_query( $_params, '', '&', PHP_QUERY_RFC3986 );

				$this->_ci->curl->create( $_url );
				$_response = $this->_ci->curl->execute();

			break;

			case 'POST' :
			case 'PUT' :
			case 'DELETE' :

				$this->_ci->curl->create( $_url );

				$_method = strtolower( $method );

				$_response = $this->_ci->curl->$_method( $_params );

			break;

		endswitch;

		return json_decode( $_response );
	}


	// --------------------------------------------------------------------------


	public function call( $api_method, $params = array() )
	{
		return $this->_call( 'GET', $api_method, $params );
	}


	// --------------------------------------------------------------------------


	public function get( $api_method, $params = array() )
	{
		return $this->_call( 'GET', $api_method, $params );
	}


	// --------------------------------------------------------------------------


	public function post( $api_method, $params = array() )
	{
		return $this->_call( 'POST', $api_method, $params );
	}


	// --------------------------------------------------------------------------


	public function put( $api_method, $params = array() )
	{
		return $this->_call( 'PUT', $api_method, $params );
	}


	// --------------------------------------------------------------------------


	public function delete( $api_method, $params = array() )
	{
		return $this->_call( 'DELETE', $api_method, $params );
	}


	// --------------------------------------------------------------------------


	/**
	 * Unlinks a local account from LinkedIn
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function unlink_user( $user_id = NULL )
	{
		//	Grab reference to the userobject
		$_userobj =& get_userobject();

		// --------------------------------------------------------------------------

		if ( is_null( $user_id ) ) :

			$_uid = active_user( 'id' );

		else :

			if ( is_callable( array( $_userobj, 'get_by_id' ) ) ) :

				$_u = get_userobject()->get_by_id( $user_id );

				if ( ! empty( $_u->id ) ) :

					$_uid	= $_u->id;

				else :

					return FALSE;

				endif;

			else :

				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Update our user
		if ( is_callable( array( $_userobj, 'update' ) ) ) :

			$_data				= array();
			$_data['li_id']		= NULL;
			$_data['li_token']	= NULl;

			return $_userobj->update( $_uid, $_data );

		else :

			return TRUE;

		endif;
	}
}

/* End of file Linkedin_connect.php */
/* Location: ./application/libraries/Linkedin_connect.php */