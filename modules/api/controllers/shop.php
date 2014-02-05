<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop API
 *
 * Description:	This controller handles Shop API methods
 *
 **/

require_once '_api.php';

/**
 * OVERLOADING NAILS' API MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
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


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' API MODULES
 *
 * The following block of code makes it simple to extend one of the core API
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP' ) ) :

	class Shop extends NAILS_Shop
	{
	}

endif;

/* End of file shop.php */
/* Location: ./modules/api/controllers/shop.php */