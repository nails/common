<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop API
 *
 * Description:	This controller handles Shop API methods
 * 
 **/

require_once '_api.php';

class Shop extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Instant search specific constructor
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Check this module is enabled in settings
		if ( ! module_is_enabled( 'shop' ) ) :
		
			//	Cancel execution, module isn't enabled
			$this->_method_not_found( $this->uri->segment( 2 ) );
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function basket()
	{
		//	Action?
		$_method = $this->uri->rsegment( 3 );
		switch ( $_method ) :
		
			case 'add' :		$this->_basket_add( );						break;
			case 'remove' :		$this->_basket_remove();					break;
			case 'increment' :	$this->_basket_increment();					break;
			case 'decrement' :	$this->_basket_decrement();					break;
			default :			$this->_basket_unknown_action( $_method );	break;
		
		endswitch;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _basket_add()
	{
		$_out = array();
		
		// --------------------------------------------------------------------------
		
		//	Do something
		
		// --------------------------------------------------------------------------
		
		$this->_out( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _basket_remove()
	{
		$_out = array();
		
		// --------------------------------------------------------------------------
		
		//	Do something
		
		// --------------------------------------------------------------------------
		
		$this->_out( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _basket_increment()
	{
		$_out = array();
		
		// --------------------------------------------------------------------------
		
		//	Do something
		
		// --------------------------------------------------------------------------
		
		$this->_out( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _basket_decrement()
	{
		$_out = array();
		
		// --------------------------------------------------------------------------
		
		//	Do something
		
		// --------------------------------------------------------------------------
		
		$this->_out( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _basket_unknown_action( $method )
	{
		$this->_method_not_found( 'basket/' . $method );
	}
}

/* End of file auth.php */
/* Location: ./application/modules/api/controllers/auth.php */