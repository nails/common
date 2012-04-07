<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Paypal {

	private $CI;
	private $last_error;
	private $ipn_response;
	private $paypal_url;
	private $paypal_email;
	private $paypal_password;
	
	
	// --------------------------------------------------------------------------
	
	
	public function __construct()
	{
		$this->CI =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Fetch config values
		$_config = $this->CI->config->item( 'paypal' );
		
		// --------------------------------------------------------------------------
		
		//	Prep all the config variables
		$this->last_error	= '';
		$this->ipn_response	= '';
		
		// --------------------------------------------------------------------------
		
		//	Which PayPal account is being used?
		switch ( ENVIRONMENT ) :
		
			case 'production' :
			
				$this->paypal_url		= $_config['live']['src'];
				$this->paypal_email		= $_config['live']['email'];
				$this->paypal_password	= $_config['live']['password'];
			
			break;
			
			default :
			
				$this->paypal_url		= $_config['sandbox']['src'];
				$this->paypal_email		= $_config['sandbox']['email'];
				$this->paypal_password	= $_config['sandbox']['password'];
			
			break;
		
		endswitch;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function validate_ipn()
	{
		//	Parse the paypal URL into it's components
		$_url_parsed = parse_url( $this->paypal_url );
		
		// --------------------------------------------------------------------------
		
		$_post_string = http_build_query( $_POST ) . '&cmd=_notify-validate';
		
		// --------------------------------------------------------------------------
		
		// 	Open the connection to PayPal
		$_ch = curl_init();
		
		curl_setopt( $_ch, CURLOPT_URL, $this->paypal_url );
		curl_setopt( $_ch, CURLOPT_POST, TRUE );
		curl_setopt( $_ch, CURLOPT_POSTFIELDS, $_post_string );
		curl_setopt( $_ch, CURLOPT_FOLLOWLOCATION, TRUE ); 
		curl_setopt( $_ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $_ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $_ch, CURLOPT_HEADER, FALSE );
		curl_setopt( $_ch, CURLOPT_USERAGENT, 'cURL/PHP' );
		
		$this->ipn_response = curl_exec( $_ch );
		
		curl_close( $_ch );
		
		// --------------------------------------------------------------------------
		
		//	Check the response
		if ( preg_match( '/VERIFIED/i', $this->ipn_response ) ) :
		
			//	Valid IPN transaction.
			return TRUE;		 
		
		else :
		
			// Invalid IPN transaction.  Check the log for details.
			$this->last_error = 'IPN Validation Failed.';
			return FALSE;
		
		endif;
	}
}


/* End of file paypal.php */
/* Location: ./application/libraries/paypal.php */