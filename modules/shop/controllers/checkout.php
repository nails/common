<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop - Checkout
 *
 * Description:	This controller handles the user's checkout experience
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

class NAILS_Checkout extends NAILS_Shop_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Handle the checkout process
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function index()
	{
		if ( ! $this->_can_checkout() ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you can\'t checkout right now: ' . implode( '', $this->get_errors() ) );
			redirect( 'shop/basket' );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $this->user->is_logged_in() || $this->input->get( 'guest' ) ) :
			
			//	Continue, user is logged in or is checking out as a guest
			if ( $this->input->get( 'guest' ) ) :
			
				$this->data['guest'] = TRUE;
			
			else :
			
				$this->data['guest'] = FALSE;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Check the order to see if we need to take shipping information
			$this->data['requires_shipping'] = FALSE;
			foreach ( $this->data['basket']->items AS $item ) :
			
				if ( $item->type->requires_shipping ) :
				
					$this->asset->load( 'jquery.chosen.min.js', TRUE );
					$this->data['requires_shipping'] = TRUE;
					break;
				
				endif;
			
			endforeach;
			
			// --------------------------------------------------------------------------
			
			//	If there's no shipping and only one payment gateway then skip this page
			//	entirely - simples!
			
			if ( ! $this->data['requires_shipping'] && count( $this->data['payment_gateways'] ) == 1 ) :
			
				//	Save payment gateway info to the session
				$this->basket->add_payment_gateway( $this->data['payment_gateways'][0]->id );
				
				//	... and redirect to confirm
				$_uri  = 'shop/checkout/confirm';
				$_uri .= $this->data['guest'] ? '?guest=true' : '';
				
				redirect( $_uri );
				return;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	If there's post data, then deal with that. If shipping is required then verify shipping info
			//	If not then punt onto shop/checkout/confirm
			
			if ( $this->input->post() ) :
			
				//	Validate
				$this->load->library( 'form_validation' );
				
				if ( $this->data['requires_shipping'] ) :
				
					$this->form_validation->set_rules( 'addressee',	'Addressee',	'xss_clean|required' );
					$this->form_validation->set_rules( 'line_1',	'Line_1',		'xss_clean|required' );
					$this->form_validation->set_rules( 'line_2',	'Line_2',		'xss_clean|required' );
					$this->form_validation->set_rules( 'town',		'Town',			'xss_clean|required' );
					$this->form_validation->set_rules( 'postcode',	'Postcode',		'xss_clean|required' );
					$this->form_validation->set_rules( 'country',	'Country',		'xss_clean|required' );
					
					dump( 'TODO: state form validation rules' );
					//	If country is USA then us_state is required
					if ( $this->input->post( 'country' ) == 'ID OF USA' ) :
					
						$this->form_validation->set_rules( 'us_state',		'State',		'xss_clean|required' );
						
					else :
					
						$this->form_validation->set_rules( 'us_state',		'State',		'xss_clean' );
					
					endif;
					
					//	If country is AUSTRALIE then aus_state is required
					if ( $this->input->post( 'country' ) == 'ID OF AUSTRALIA' ) :
					
						$this->form_validation->set_rules( 'aus_state',		'State',		'xss_clean|required' );
						
					else :
					
						$this->form_validation->set_rules( 'aus_state',		'State',		'xss_clean' );
					
					endif;
				
				endif;
				
				//	Payment gateway
				$this->form_validation->set_rules( 'payment_gateway', 'Payment Gateway', 'xss_clean|required|is_natural' );
				
				//	Set messages
				$this->form_validation->set_message( 'required',	lang( 'fv_required' ) );
				$this->form_validation->set_message( 'is_natural',	lang( 'fv_required' ) );
				
				if ( $this->form_validation->run() ) :

					//	Save shipping info to the session
					if ( $this->data['requires_shipping'] ) :
					
						$_details = new stdClass();
						$_details->addressee	= $this->input->post( 'addressee' );
						$_details->line_1		= $this->input->post( 'line_1' );
						$_details->line_2		= $this->input->post( 'line_2' );
						$_details->town			= $this->input->post( 'town' );
						$_details->postcode		= $this->input->post( 'postcode' );
						$_details->country		= $this->input->post( 'country' );
						
						if ( $this->input->post( 'country' ) == 'ID OF USA' ) :
						
							$_details->state	= $this->input->post( 'us_state' );
							
						elseif ( $this->input->post( 'country' ) == 'ID OF AUSTRALIA' ) :
						
							$_details->state	= $this->input->post( 'aus_state' );
						
						else :
						
							$_details->state	= '';
						
						endif;
						
						$this->basket->add_shipping_info( $_details );
					
					endif;
					
					// --------------------------------------------------------------------------
					
					//	Redirect to the appropriate payment gateway
					foreach ( $this->data['payment_gateways'] AS $pg ) :
					
						if ( $pg == $this->input->post( 'payment_gateway' ) ) :
						
							//	Save payment gateway info to the session
							$this->basket_model->add_payment_gateway( $pg->id );
							
							//	... and confirm
							$_uri  = 'shop/checkout/confirm';
							$_uri .= $this->data['guest'] ? '?guest=true' : '';
							
							redirect( $_uri );
							break;
						
						endif;
					
					endforeach;
					
					// --------------------------------------------------------------------------
					
					//	Something went wrong.
					$this->data['error'] = '<strong>Sorry,</strong> we couldn\'t verify your payment option. Please try again.';
				
				else :
				
					$this->data['error'] = lang( 'fv_there_were_errors' );
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Set appropriate title
			if ( $this->data['requires_shipping'] && count( $this->data['payment_gateways'] ) > 1 ) :
			
				$this->data['page']->title = 'Checkout &rsaquo; Shipping and Payment Options';
			
			elseif ( $this->data['requires_shipping'] ) :
			
				$this->data['page']->title = 'Checkout &rsaquo; Shipping Options';
				
			else :
			
				$this->data['page']->title = 'Checkout &rsaquo; Payment Options';
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Load veiws
			$this->load->view( 'structure/header',			$this->data );
			$this->load->view( 'shop/checkout/checkout',	$this->data );
			$this->load->view( 'structure/footer',			$this->data );
		
		else :
		
			$this->data['page']->title = 'Checkout &rsaquo; Please Sign In';
			
			// --------------------------------------------------------------------------
			
			$this->lang->load( 'auth/auth', RENDER_LANG );
			
			// --------------------------------------------------------------------------
			
			$this->load->view( 'structure/header',		$this->data );
			$this->load->view( 'shop/checkout/signin',	$this->data );
			$this->load->view( 'structure/footer',		$this->data );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Handle the checkout process
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function confirm()
	{
		if ( ! $this->_can_checkout() ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you can\'t checkout right now: ' . implode( '', $this->get_errors() ) );
			redirect( 'shop/basket' );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $this->user->is_logged_in() || $this->input->get( 'guest' ) ) :
			
			//	Continue, user is logged in or is checking out as a guest
			if ( $this->input->get( 'guest' ) ) :
			
				$this->data['guest'] = TRUE;
			
			else :
			
				$this->data['guest'] = FALSE;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	If there's no shipping required and there's only one payment gateway then
			//	just create the order and punt the user to the payment gateway's processing
			//	page.
			
			if ( ! $this->data['basket']->requires_shipping && count( $this->data['payment_gateways'] ) == 1 ) :
			
				$this->basket->add_payment_gateway( $this->data['payment_gateways'][0]->id );
				
				$_uri  = 'shop/checkout/payment';
				$_uri .= $this->data['guest'] ? '?guest=true' : '';
				
				redirect( $_uri );
				return;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			$this->data['page']->title = 'Checkout &rsaquo; Confirm Your Order';
			
			// --------------------------------------------------------------------------
			
			$this->load->view( 'structure/header',		$this->data );
			$this->load->view( 'shop/checkout/confirm',	$this->data );
			$this->load->view( 'structure/footer',		$this->data );
			
		else :
		
			redirect( 'shop/checkout' );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function payment()
	{
		if ( ! $this->_can_checkout() ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you can\'t checkout right now: ' . implode( '', $this->get_errors() ) );
			redirect( 'shop/basket' );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $this->user->is_logged_in() || $this->input->get( 'guest' ) ) :
			
			//	Continue, user is logged in or is checking out as a guest
			if ( $this->input->get( 'guest' ) ) :
			
				$this->data['guest'] = TRUE;
			
			else :
			
				$this->data['guest'] = FALSE;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			switch ( $this->data['basket']->payment_gateway ) :
			
				//	Known payment gateways
				case 1 :	$this->_payment_paypal();	break;
				case 2 :	$this->_payment_shedpay();	break;
				case 3 :	$this->_payment_cardsave();	break;
				case 4 :	$this->_payment_sagepay();	break;
				case 5 :	$this->_payment_worldpay();	break;
				case 6 :	$this->_payment_eway();		break;
				
				// --------------------------------------------------------------------------
				
				//	Unknown
				default :
				
					$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> there was a problem verifying your chosen payment option. Please try again.' );
					redirect( 'shop/basket' );
				
				break;
			
			endswitch;
			
		else :
		
			redirect( 'shop/checkout' );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _payment_paypal()
	{
		//	Create the order
		$this->data['order'] = new stdClass();
		$this->data['order']->id	= 123;
		$this->data['order']->ref	= 'ABCREF';
		$this->data['order']->code	= '123ABCCODE';
		
		// --------------------------------------------------------------------------
		
		//	Fetch payment gateway details
		foreach( $this->data['payment_gateways'] AS $pg ) :
		
			if ( $this->data['basket']->payment_gateway == $pg->id ) :
			
				$_payment_gateway =& $pg;
				break;
			
			endif;
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		//	Prepapre variables for the template
		$this->data['paypal'] = new stdClass();
		
		switch ( ENVIRONMENT ) :
		
			case 'production' :
			
				$this->data['paypal']->url	= 'https://www.paypal.com/cgi-bin/webscr';
			
			break;
			
			default :
			
				$this->data['paypal']->url	= 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			
			break;
		
		endswitch;
		
		$this->data['paypal']->business		= $_payment_gateway->account_id;
		$this->data['paypal']->notify		= site_url( 'shop/checkout/notify/paypal' );
		$this->data['paypal']->cancel		= site_url( 'shop/checkout/cancel' );
		$this->data['paypal']->processing	= site_url( 'shop/checkout/processing' );
		
		// --------------------------------------------------------------------------
		
		//	Load the views
		$this->load->view( 'shop/payment/paypal/index',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _payment_shedpay()
	{
		dumpanddie( 'TODO: Shedpay interface' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _payment_cardsave()
	{
		dumpanddie( 'TODO: CardSave interface' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _payment_sagepay()
	{
		dumpanddie( 'TODO: SagePay interface' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _payment_worldpay()
	{
		dumpanddie( 'TODO: WordlPay interface' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _payment_eway()
	{
		dumpanddie( 'TODO: eWay interface' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _can_checkout()
	{
		//	Check basket isn't empty
		$this->data['basket'] = $this->basket->get_basket();
		
		if ( ! $this->data['basket']->items ) :
		
			$this->_set_error( 'Your basket is empty.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load the payment gateway model
		$this->load->model( 'shop_payment_gateway_model', 'payment_gateway' );
		
		//	Fetch the supported payment gateways
		$this->data['payment_gateways'] = $this->payment_gateway->get_all_supported();
		
		if ( ! $this->data['payment_gateways'] ) :
		
			//	Uh-oh, no supported payment gateways. Bad times but feedback to the user.			
			$this->_set_error( 'There\'s an issue at the moment which is preventing ' . APP_NAME . ' form accepting online payment at the moment. Please try again later.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function processing()
	{
		here( 'TODO: Wait for the order status to be updated.' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function cancel()
	{
		here( 'TODO: Abandon the order and go back to the basket.' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function notify()
	{
		here( 'TODO: Handle payment gateways notifying' );
	}
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_CHECKOUT' ) ) :

	class Checkout extends NAILS_Checkout
	{
	}

endif;

/* End of file checkout.php */
/* Location: ./application/modules/shop/controllers/checkout.php */