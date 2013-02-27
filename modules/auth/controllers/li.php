<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [LinkedIn]
 *
 * Description:	This controller handles connecting accounts to LinkedIn
 * 
 **/

/**
 * OVERLOADING NAILS'S AUTH MODULE
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

require_once '_auth.php';

class NAILS_Li extends NAILS_Auth_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Ensure the sub-module is enabled
		if ( ! module_is_enabled( 'auth[linkedin]' ) ) :
		
			show_404();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load the LinkedIn Library
		$this->load->library( 'Linkedin_connect', NULL, 'li' );
		
		// --------------------------------------------------------------------------
		
		//	Set a return_to if available
		$this->_return_to = $this->input->get( 'return_to' );
		
		//	If nothing, check the 'nailsFBConnectReturnTo' GET var which may be passed back
		if ( ! $this->_return_to ) :
		
			$this->_return_to = $this->input->get( 'nailsLIConnectReturnTo' );
			
			//	Still empty? Group homepage
			if ( ! $this->_return_to ) :
			
				$this->_return_to = active_user( 'group_homepage' );
			
			endif;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set a return_to_fail if available
		$this->_return_to_fail = $this->input->get( 'return_to_fail' );
		
		//	If nothing, check the GET var which may be passed back
		if ( ! $this->_return_to_fail ) :
		
			$this->_return_to_fail = $this->input->get( 'nailsLIConnectReturnToFail' );
			
			if ( ! $this->_return_to_fail ) :
			
				//	Fallback to the value of $this->return_to
				$this->_return_to_fail = $this->_return_to;
				
			endif;
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	/* ! CONNECTING TO LINKEDIN */
	
	// --------------------------------------------------------------------------
	
	
	public function connect()
	{
		switch ( $this->uri->segment( 4 ) ) :
		
			case 'verify' :		$this->_connect_verify();	break;
			case 'connect' :
			default:			$this->_connect_connect();	break;
		
		endswitch;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Initiate a connection request
	 *
	 * @access	protected
	 * @param	none
	 * @return	void
	 **/
	protected function _connect_connect()
	{
		//	If the LinkedIn is already linked then we need to acknowledge it
		if ( ! $this->input->get( 'force' ) && $this->li->user_is_linked() ) :
		
			$this->session->set_flashdata( 'message', '<strong>Woah there!</strong> You have already linked your LinkedIn account.' );
			$this->_connect_fail();
			return;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		dumpanddie( 'TODO Handle Connection' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Verify the connection request
	 *
	 * @access	private
	 * @param	none
	 * @return	void
	 **/
	private function _connect_verify()
	{
		dumpanddie( 'TODO Handle verifying connection' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Handles a successfull connection request, This method can be overridden if more
	 * than the basic data (i.e email, name, gender) is needed for account creation.
	 * By this point the FB library is set up with the user's access token.
	 *
	 * @access	protected
	 * @return	void
	 **/
	protected function _connect_success()
	{
		dumpanddie( 'TODO Handle successful connection' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Handles a failed connection request
	 *
	 * @access	protected
	 * @param	none
	 * @return	void
	 **/
	protected function _connect_fail()
	{
		$this->_redirect( $this->return_to_fail );
	}
	
	
	// --------------------------------------------------------------------------
	
	/* ! DISCONNECTING FROM LINKEDIN */
	
	// --------------------------------------------------------------------------
	
	
	public function disconnect()
	{
		dumpanddie( 'TODO Handle disconnection' );
	}
	
	
	// --------------------------------------------------------------------------
	
	/* ! HELPER METHODS */
	
	// --------------------------------------------------------------------------
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S AUTH MODULE
 * 
 * The following block of code makes it simple to extend one of the core auth
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION' ) ) :

	class Li extends NAILS_Li
	{
	}

endif;

/* End of file li.php */
/* Location: ./application/modules/auth/controllers/li.php */