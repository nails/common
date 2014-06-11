<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop - Basket
 *
 * Description:	This controller handles the user's basket
 *
 **/

/**
 * OVERLOADING NAILS' AUTH MODULE
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

		$this->data['return'] = $this->input->get( 'return' ) ? $this->input->get( 'return' ) : app_setting( 'url', 'shop' ) . 'basket';
	}


	// --------------------------------------------------------------------------

	/* ! BAKSET */

	// --------------------------------------------------------------------------


	/**
	 * Render the user's basket
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function index()
	{
		//	Abandon any previous order
		if ( $this->basket->get_order_id() ) :

			//	Abandon this order and remove from basket
			$this->order->abandon( $this->basket->get_order_id() );
			$this->basket->remove_order_id();

		endif;

		// --------------------------------------------------------------------------

		$this->data['page']->title = 'Your Basket';

		// --------------------------------------------------------------------------

		$this->data['basket']			= $this->basket->get_basket();
		$this->data['shipping_methods'] = $this->shipping->get_all();
		$this->data['currencies']		= $this->currency->get_all();

		// --------------------------------------------------------------------------

		//	Load the payment gateway model
		$this->load->model( 'shop_payment_gateway_model', 'payment_gateway' );

		//	Fetch the supported payment gateways
		$this->data['payment_gateways'] = $this->payment_gateway->get_all_supported();

		if ( ! $this->data['payment_gateways'] ) :

			$this->data['message'] = '<strong>Sorry,</strong> there\'s an issue at the moment which is preventing ' . APP_NAME . ' form accepting online payment at the moment, you won\'t be able to checkout.';

		endif;

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->load( 'nails.shop.basket.min.js', TRUE );

		//	Inline assets
		$_js  = 'var _nails_shop_basket;';
		$_js .= '$(function(){';

		$_js .= 'if ( typeof( NAILS_Shop_Basket ) === \'function\' ){';
		$_js .= '_nails_shop_basket = new NAILS_Shop_Basket();';
		$_js .= '_nails_shop_basket.init();}';

		$_js .= '});';

		$this->asset->inline( '<script>' . $_js . '</script>' );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',								$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/basket/index',	$this->data );
		$this->load->view( 'structure/footer',								$this->data );
	}


	// --------------------------------------------------------------------------





	// --------------------------------------------------------------------------


	/**
	 * Adds an item to the user's basket (fall back for when JS is not available)
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function add()
	{
		if ( $this->basket->add( $this->uri->rsegment( 3 ), $this->uri->rsegment( 4 ) ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Item was added to your basket. <a href="javascript: history.go(-1)">Continue Shopping</a>' );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> there was a problem adding to your basket: ' . $this->basket->last_error() );

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
	 *
	 **/
	public function remove()
	{
		if ( $this->basket->remove( $this->uri->rsegment( 3 ), $this->uri->rsegment( 4 ) ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Item was removed from your basket.' );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> there was a problem removing the item from your basket: ' . $this->basket->last_error() );

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
	 *
	 **/
	public function decrement()
	{
		$this->basket->decrement( $this->uri->rsegment( 3 ) );

		// --------------------------------------------------------------------------

		redirect( $this->data['return'] );
	}


	// --------------------------------------------------------------------------


	/**
	 * Validate and add a voucher to a basket
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function add_voucher()
	{
		$_voucher = $this->voucher->validate( $this->input->post( 'voucher' ), get_basket() );

		if ( $_voucher ) :

			//	Validated, add to basket
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Voucher has been applied to your basket.' );
			$this->basket->add_voucher( $_voucher->code );

		else :

			//	Failed to validate, feedback
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> that voucher is not valid:<br />&rsaquo; ' . implode( '<br />&rsaquo;', $this->voucher->get_errors() ) );

		endif;

		// --------------------------------------------------------------------------

		redirect( $this->data['return'] );
	}


	// --------------------------------------------------------------------------


	/**
	 * Remove any associated voucher from the user's basket
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function remove_voucher()
	{
		$this->basket->remove_voucher();
		$this->session->set_flashdata( 'success', '<strong>Success!</strong> Your voucher was removed.' );

		// --------------------------------------------------------------------------

		redirect( $this->data['return'] );
	}


	// --------------------------------------------------------------------------


	/**
	 * Set the preferred shipping method
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function set_shipping_method()
	{
		$_method = $this->shipping->validate( $this->input->post( 'shipping_method' ) );

		if ( $_method ) :

			//	Validated, add to basket
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Your shipping method has been updated.' );
			$this->basket->add_shipping_method( $_method->id );

		else :

			//	Failed to validate, feedback
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> that shipping is not valid:<br />&rsaquo; ' . implode( '<br />&rsaquo;', $this->shipping->get_errors() ) );

		endif;

		// --------------------------------------------------------------------------

		redirect( $this->data['return'] );
	}


	// --------------------------------------------------------------------------


	/**
	 * Set the user's preferred currency
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function set_currency()
	{
		$_currency = $this->currency->get_by_id( $this->input->post( 'currency' ) );

		if ( $_currency && $_currency->is_active ) :

			//	Valid currency
			$this->session->set_userdata( 'shop_currency', $_currency->id );

			if ( $this->user->is_logged_in() ) :

				//	Save to the user object
				$this->user->update( active_user( 'id' ), array( 'shop_currency' => $_currency->id ) );

			endif;

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Your currency has been updated.' );

		else :

			//	Failed to validate, feedback
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> that currency is not valid.' );

		endif;

		// --------------------------------------------------------------------------

		redirect( $this->data['return'] );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' SHOP MODULE
 *
 * The following block of code makes it simple to extend one of the core shop
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