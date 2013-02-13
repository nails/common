<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		whois
 *
 * Description:	Gateway to the Whois Class
 * 
 **/

class Whois {
	
	private $ci;
	private $settings;
	private $whois;
	
	
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
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Perform lookup
	 *
	 * @access	public
	 * @return	mixed
	 * @author	Pablo
	 **/
	public function lookup( $domain )
	{
		$domain = prep_url( $domain );
		$domain = parse_url($domain);
		$domain = preg_replace( '/^www\./', '', $domain['host'] );
		
		// --------------------------------------------------------------------------
		
		$_ch = curl_init();
		curl_setopt( $_ch, CURLOPT_URL, 'http://www.freedomainwhois.com/src/webservice.php' );
		curl_setopt( $_ch, CURLOPT_POST, TRUE );
		curl_setopt( $_ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $_ch, CURLOPT_POSTFIELDS, array( 'action' => 'whois', 'f_domainname' => $domain ) );
		$result = curl_exec( $_ch );
		curl_close( $_ch );
		
		return json_decode($result);
	}
}

/* End of file whois.php */
/* Location: ./application/libraries/whois.php */