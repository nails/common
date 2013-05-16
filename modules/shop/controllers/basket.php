<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop - Basket
 *
 * Description:	This controller handles the user's basket
 * 
 **/

/**
 * OVERLOADING NAILS'S AUTH MODULE
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

//	Include _shop.php; executes common functionality
require_once '_shop.php';

class NAILS_Basket extends NAILS_Shop_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		$this->data['return'] = $this->input->get( 'return' ) ? $this->input->get( 'return' ) : shop_setting( 'shop_url' ) . 'basket';
	}
	
	
	// --------------------------------------------------------------------------
	
	/* ! BAKSET */
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Render the user's basket
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function index()
	{
		$this->data['page']->title = 'Your Basket';
		
		// --------------------------------------------------------------------------
		
		$this->data['basket'] = $this->basket->get_basket();
		
		// --------------------------------------------------------------------------
		
		//	Load the payment gateway model
		$this->load->model( 'shop_payment_gateway_model', 'payment_gateway' );
		
		//	Fetch the supported payment gateways
		$this->data['payment_gateways'] = $this->payment_gateway->get_all_supported();
		
		if ( ! $this->data['payment_gateways'] ) :
		
			$this->data['message'] = '<strong>Sorry,</strong> there\'s an issue at the moment which is preventing ' . APP_NAME . ' form accepting online payment at the moment, you won\'t be able to checkout.';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'shop/basket/index',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	

	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Adds an item to the user's basket (fall back for when JS is not available)
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function add()
	{
		if ( $this->basket->add( $this->uri->rsegment( 3 ), $this->uri->rsegment( 4 ) ) ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Item was added to your basket. <a href="javascript: history.go(-1)">Continue Shopping</a>' );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> there was a problem adding to your basket: ' . implode( $this->basket->get_error() ) );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		redirect( $this->data['return'] );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Removes an item from the user's basket (fall back for when JS is not available)
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function remove()
	{
		if ( $this->basket->remove( $this->uri->rsegment( 3 ), $this->uri->rsegment( 4 ) ) ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Item was removed from your basket.' );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> there was a problem removing the item from your basket: ' . implode( $this->basket->get_error() ) );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		redirect( $this->data['return'] );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Empties a user's basket
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function destroy()
	{
		$this->basket->destroy();
		
		// --------------------------------------------------------------------------
		
		redirect( $this->data['return'] );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Increment an item in the user's basket (fall back for when JS is not available)
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function increment()
	{
		$this->basket->increment( $this->uri->rsegment( 3 ) );
		
		// --------------------------------------------------------------------------
		
		redirect( $this->data['return'] );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Decrement an item in the user's basket (fall back for when JS is not available)
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function decrement()
	{
		$this->basket->decrement( $this->uri->rsegment( 3 ) );
		
		// --------------------------------------------------------------------------
		
		redirect( $this->data['return'] );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S SHOP MODULE
 * 
 * The following block of code makes it simple to extend one of the core shop
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_BASKET' ) ) :

	class Basket extends NAILS_Basket
	{
	}

endif;

/* End of file basket.php */
/* Location: ./application/modules/shop/controllers/basket.php */