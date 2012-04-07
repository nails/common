<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Facebook
*
* Created:		10/02/2012
* Modified:		10/02/2012
*
* Description:	Gateway to the FB PHP SDK
*
* Requirements:	-
*
* Change log:	-
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
	 * @author	Pablo
	 **/
	public function __construct()
	{
		$this->ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Fetch our config variables
		$this->ci->config->load( 'facebook' );
		$this->settings = $this->ci->config->item( 'facebook' );
		
		// --------------------------------------------------------------------------
		
		//	Fire up and initialize the SDK
		require FCPATH . APPPATH . 'libraries/_resources/facebook-php-sdk/src/facebook.php';
		$this->facebook = new Facebook( $this->settings );
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether the active user has already linked their Facebook profile
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function user_is_linked()
	{
		return (bool) active_user( 'fb_id' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Unlinks a local account from Facebook
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function unlink_user( $user_id )
	{
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
	 * @author	Pablo
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

/* End of file facebook.php */
/* Location: ./application/libraries/facebook.php */