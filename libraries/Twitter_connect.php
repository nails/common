<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Twitter
*
* Description:	Gateway to the Twitter API
* 
*/

class Facebook_Connect {
	
	private $ci;
	private $settings;
	private $twitter;
	
	
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
		$this->ci->config->load( 'twitter' );
		$this->settings = $this->ci->config->item( 'twitter' );
		
		// --------------------------------------------------------------------------
		
		//	Fire up and initialize the SDK
		//	TODO
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether the active user has already linked their Twitter profile
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function user_is_linked()
	{
		return (bool) active_user( 'tw_id' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Unlinks a local account from Twitter
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function unlink_user( $user_id )
	{
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Map method calls to the Twitter library
	 *
	 * @access	public
	 * @return	mixed
	 * @author	Pablo
	 **/
	public function __call( $method, $arguments )
	{
		if ( method_exists( $this->twitter, $method ) ) :
		
			return call_user_func_array( array( $this->twitter, $method ), $arguments );
		
		else:
		
			show_error( 'Method does not exist Twitter::' . $method );
		
		endif;
	}
}

/* End of file Twitter_connect.php */
/* Location: ./application/libraries/Twitter_connect.php */