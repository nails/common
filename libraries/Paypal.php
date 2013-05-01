<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Paypal
{
	private $curl;
	
	
	// --------------------------------------------------------------------------
	
	
	public function validate_ipn()
	{
		//	Load the cURL library and configure for the IPN URL
		$this->curl = get_instance()->load->library( 'curl' );
		
		if ( ENVIRONMENT == 'production' ) :
		
			$this->curl->create( 'https://www.paypal.com/cgi-bin/webscr' );
			
		else :
		
			$this->curl->create( 'https://www.sandbox.paypal.com/cgi-bin/webscr' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Prepare POST variables
		$_post			= $_POST;
		$_post['cmd']	= '_notify-validate';
		
		$this->curl->post( $_post );
		
		// --------------------------------------------------------------------------
		
		//	Execute
		return $this->curl->execute() == 'VALID' ? TRUE : FALSE;
	}
}


/* End of file paypal.php */
/* Location: ./application/libraries/paypal.php */