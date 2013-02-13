<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			FreeAgent
*
* Description:	Gateway to the FreeAgent API wrapper provided by HostLikeToast
* 
*/

class Freeagent {
	
	private $ci;
	private $settings;
	private $freeagent;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct( $params = array() )
	{
		$this->ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Fetch our config variables
		$this->ci->config->load( 'freeagent', FALSE, TRUE );
		$this->settings = $this->ci->config->item( 'freeagent' );
		
		// --------------------------------------------------------------------------
		
		//	Fire up and initialize the libraries
		require NAILS_PATH . 'libraries/_resources/freeagent-api/base_api.php';
		require NAILS_PATH . 'libraries/_resources/freeagent-api/freeagent_api.php';
		require NAILS_PATH . 'libraries/_resources/freeagent-api/xml_generator.php';
		
		// --------------------------------------------------------------------------
		
		//	Check credentials
		
		//	Passed as a parameter?
		if ( ! isset( $params['subdomain'] ) ) :
		
			//	Exist in a settings file?
			if ( ! isset( $this->settings['subdomain'] ) ) :
			
				show_error( 'FreeAgent Setting missing: Subdomain not set.' );
			
			endif;
			
		else :
		
			$this->settings['subdomain'] = $params['subdomain'];
		
		endif;
		
		//	Passed as a parameter?
		if ( ! isset( $params['username'] ) ) :
		
			//	Exist in a settings file?
			if ( ! isset( $this->settings['username'] ) ) :
			
				show_error( 'FreeAgent Setting missing: Username not set.' );
			
			endif;
			
		else :
		
			$this->settings['username'] = $params['username'];
		
		endif;
		
		//	Passed as a parameter?
		if ( ! isset( $params['password'] ) ) :
		
			//	Exist in a settings file?
			if ( ! isset( $this->settings['password'] ) ) :
			
				show_error( 'FreeAgent Setting missing: Password not set.' );
			
			endif;
			
		else :
		
			$this->settings['password'] = $params['password'];
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Fire up new instance
		$this->freeagent = new FreeAgent_Api( $this->settings['subdomain'], $this->settings['username'], $this->settings['password'] );
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Map method calls to the FreeAgent library
	 *
	 * @access	public
	 * @return	mixed
	 * @author	Pablo
	 **/
	public function __call( $method, $arguments )
	{
		if ( method_exists( $this->freeagent, $method ) ) :
		
			return call_user_func_array( array( $this->freeagent, $method ), $arguments );
		
		else:
		
			show_error( 'Method does not exist Freeagent_Api::' . $method );
		
		endif;
	}
}

/* End of file freeagent.php */
/* Location: ./application/libraries/freeagent.php */