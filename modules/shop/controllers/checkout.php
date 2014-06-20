<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop - Checkout
 *
 * Description:	This controller handles the user's checkout experience
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
	 *
	 **/
	public function index()
	{
		if ( ! $this->_can_checkout() ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you can\'t checkout right now: ' . $this->data['error'] );
			redirect( app_setting( 'url', 'shop' ) . 'basket' );
			return;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->user_model->is_logged_in() || $this->input->get( 'guest' ) ) :

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

					$this->data['requires_shipping'] = TRUE;
					break;

				endif;

			endforeach;

			// --------------------------------------------------------------------------

			//	If there's no shipping and only one payment gateway then skip this page
			//	entirely - simples! Unless they are a guest, in which case we need to take
			//	some personal details

			if ( ! $this->data['guest'] && ! $this->data['requires_shipping'] && ( count( $this->data['payment_gateways'] ) == 1 || $this->data['basket']->totals->grand == 0 ) ) :

				//	Save payment gateway info to the session
				if ( $this->data['basket']->totals->grand != 0 ) :

					$this->shop_basket_model->add_payment_gateway( $this->data['payment_gateways'][0]->id );

				else :

					$this->shop_basket_model->remove_payment_gateway();

				endif;

				//	... and redirect to confirm
				$_uri  = app_setting( 'url', 'shop' ) . 'checkout/confirm';
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

				if ( $this->data['guest'] ) :

					$this->form_validation->set_rules( 'first_name',	'First Name',	'xss_clean|required' );
					$this->form_validation->set_rules( 'last_name',		'Surname',		'xss_clean|required' );
					$this->form_validation->set_rules( 'email',			'Email',		'xss_clean|required|valid_email' );

				endif;

				// --------------------------------------------------------------------------

				if ( $this->data['requires_shipping'] ) :

					$this->form_validation->set_rules( 'addressee',	'Addressee',	'xss_clean|required' );
					$this->form_validation->set_rules( 'line_1',	'Line_1',		'xss_clean|required' );
					$this->form_validation->set_rules( 'line_2',	'Line_2',		'xss_clean|required' );
					$this->form_validation->set_rules( 'town',		'Town',			'xss_clean|required' );
					$this->form_validation->set_rules( 'postcode',	'Postcode',		'xss_clean|required' );
					$this->form_validation->set_rules( 'country',	'Country',		'xss_clean|required' );

					//	If country is USA then us_state is required
					if ( $this->input->post( 'country' ) == 'ID OF USA' ) :

						$this->form_validation->set_rules( 'us_state',		'State',		'xss_clean|required' );

					else :

						$this->form_validation->set_rules( 'us_state',		'State',		'xss_clean' );

					endif;

					//	If country is AUSTRALIA then aus_state is required
					if ( $this->input->post( 'country' ) == 'ID OF AUSTRALIA' ) :

						$this->form_validation->set_rules( 'aus_state',		'State',		'xss_clean|required' );

					else :

						$this->form_validation->set_rules( 'aus_state',		'State',		'xss_clean' );

					endif;

				endif;

				// --------------------------------------------------------------------------

				//	Payment gateway
				if ( $this->data['basket']->totals->grand > 0 ) :

					$this->form_validation->set_rules( 'payment_gateway', 'Payment Gateway', 'xss_clean|required|is_natural' );

				endif;

				// --------------------------------------------------------------------------

				//	Set messages
				$this->form_validation->set_message( 'required',	lang( 'fv_required' ) );
				$this->form_validation->set_message( 'is_natural',	lang( 'fv_required' ) );
				$this->form_validation->set_message( 'valid_email',	lang( 'fv_valid_email' ) );

				if ( $this->form_validation->run() ) :

					//	Save personal info to session
					if ( $this->data['guest'] ) :

						$_details				= new stdClass();
						$_details->first_name	= $this->input->post( 'first_name' );
						$_details->last_name	= $this->input->post( 'last_name' );
						$_details->email		= $this->input->post( 'email' );

						$this->shop_basket_model->add_personal_details( $_details );

					else :

						//	In case it's already there for some reason
						$this->shop_basket_model->remove_personal_details();

					endif;

					// --------------------------------------------------------------------------

					//	Save shipping info to the session
					if ( $this->data['requires_shipping'] ) :

						$_details				= new stdClass();
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

						$this->shop_basket_model->add_shipping_details( $_details );

					else :

						//	In case it's already there for some reason
						$this->shop_basket_model->remove_shipping_details();

					endif;

					// --------------------------------------------------------------------------

					//	Redirect to the appropriate payment gateway. If there's only one, then
					//	bump straight along to that one

					if ( $this->data['basket']->totals->grand > 0 && count( $this->data['payment_gateways'] ) == 1 ) :

						//	Save payment gateway info to the session
						$this->shop_basket_model->add_payment_gateway( $this->data['payment_gateways'][0]->id );

						//	... and confirm
						$_uri  = app_setting( 'url', 'shop' ) . 'checkout/confirm';
						$_uri .= $this->data['guest'] ? '?guest=true' : '';

						redirect( $_uri );

					elseif ( $this->data['basket']->totals->grand > 0 && count( $this->data['payment_gateways'] ) >= 1 ) :

						foreach ( $this->data['payment_gateways'] AS $pg ) :

							if ( $pg->id == $this->input->post( 'payment_gateway' ) ) :

								//	Save payment gateway info to the session
								$this->shop_basket_model->add_payment_gateway( $pg->id );

								//	... and confirm
								$_uri  = app_setting( 'url', 'shop' ) . 'checkout/confirm';
								$_uri .= $this->data['guest'] ? '?guest=true' : '';

								redirect( $_uri );
								break;

							endif;

						endforeach;

					elseif ( $this->data['basket']->totals->grand == 0 ) :

						//	Incase it's already there for some reason
						$this->shop_basket_model->remove_payment_gateway();

						// --------------------------------------------------------------------------

						$_uri  = app_setting( 'url', 'shop' ) . 'checkout/confirm';
						$_uri .= $this->data['guest'] ? '?guest=true' : '';

						redirect( $_uri );

					endif;

					// --------------------------------------------------------------------------
					here();
					//	Something went wrong.
					$this->data['error'] = '<strong>Sorry,</strong> we couldn\'t verify your payment option. Please try again.';

				else :

					$this->data['error'] = lang( 'fv_there_were_errors' );

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	Set appropriate title
			$_titles = array();

			if ( $this->data['guest'] ) :

				$_titles[] = 'Personal Details';

			endif;

			if ( $this->data['requires_shipping'] ) :

				$_titles[] = 'Shipping Details';

			endif;

			if ( count( $this->data['payment_gateways'] ) > 1 ) :

				$_titles[] = 'Payment Options';

			endif;

			$this->data['page']->title = 'Checkout &rsaquo; ' . str_lreplace( ', ', ' &amp; ', implode( ', ', $_titles ) );

			// --------------------------------------------------------------------------

			//	Load veiws
			$this->load->view( 'structure/header',									$this->data );
			$this->load->view( 'shop/' . $this->_skin->dir . '/checkout/checkout',	$this->data );
			$this->load->view( 'structure/footer',									$this->data );

		else :

			$this->data['page']->title = 'Checkout &rsaquo; Please Sign In';

			// --------------------------------------------------------------------------

			$this->lang->load( 'auth/auth' );

			// --------------------------------------------------------------------------

			$this->load->view( 'structure/header',									$this->data );
			$this->load->view( 'shop/' . $this->_skin->dir . '/checkout/signin',	$this->data );
			$this->load->view( 'structure/footer',									$this->data );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Handle the checkout process
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function confirm()
	{
		if ( ! $this->_can_checkout() ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you can\'t checkout right now: ' . $this->data['error'] );
			redirect( app_setting( 'url', 'shop' ) . 'basket' );
			return;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->user_model->is_logged_in() || $this->input->get( 'guest' ) ) :

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

				$this->shop_basket_model->add_payment_gateway( $this->data['payment_gateways'][0]->id );

				$_uri  = app_setting( 'url', 'shop' ) . 'checkout/payment';
				$_uri .= $this->data['guest'] ? '?guest=true' : '';

				redirect( $_uri );
				return;

			endif;

			// --------------------------------------------------------------------------

			$this->data['page']->title	= 'Checkout &rsaquo; Confirm Your Order';
			$this->data['currencies']	= $this->shop_currency_model->get_all();

			// --------------------------------------------------------------------------

			$this->load->view( 'structure/header',									$this->data );
			$this->load->view( 'shop/' . $this->_skin->dir . '/checkout/confirm',	$this->data );
			$this->load->view( 'structure/footer',									$this->data );

		else :

			redirect( app_setting( 'url', 'shop' ) . 'checkout' );

		endif;
	}


	// --------------------------------------------------------------------------


	public function payment()
	{
		if ( ! $this->_can_checkout() ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you can\'t checkout right now: ' . $this->data['error'] );
			redirect( app_setting( 'url', 'shop' ) . 'basket' );
			return;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->user_model->is_logged_in() || $this->input->get( 'guest' ) ) :

			//	Continue, user is logged in or is checking out as a guest
			if ( $this->input->get( 'guest' ) ) :

				$this->data['guest'] = TRUE;

			else :

				$this->data['guest'] = FALSE;

			endif;

			// --------------------------------------------------------------------------

			//	Mute the logger (causes issues on non-production environments)
			_LOG_MUTE_OUTPUT();

			// --------------------------------------------------------------------------

			//	Is the order a zero-value order? If so, just mark it as paid and send
			//	to processing immediately

			if ( $this->data['basket']->totals->grand == 0 ) :

				//	Create order, then set as paid and redirect to processing page
				$_order = $this->shop_order_model->create( $this->data['basket'], TRUE );

				if ( ! $_order ) :

					$this->session->set_flashdata( 'error', 'There was a problem checking out: ' . $this->data['error'] );
					redirect( app_setting( 'url', 'shop' ) . 'basket' );
					return;

				endif;

				//	Set as paid
				$this->shop_order_model->paid( $_order->id );

				//	Process the order, send receipt and send order notification
				$this->shop_order_model->process( $_order );
				$this->shop_order_model->send_receipt( $_order );
				$this->shop_order_model->send_order_notification( $_order );

				if ( $_order->voucher ) :

					$this->shop_voucher_model->redeem( $_order->voucher->id, $_order );

				endif;

				// --------------------------------------------------------------------------

				//	Destory the basket
				$this->shop_basket_model->destroy();

				// --------------------------------------------------------------------------

				//	Redirect to processing page
				redirect( app_setting( 'url', 'shop' ) . 'checkout/processing?ref=' . $_order->ref );

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
					redirect( app_setting( 'url', 'shop' ) . 'basket' );

				break;

			endswitch;

		else :

			redirect( app_setting( 'url', 'shop' ) . 'checkout' );

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _payment_paypal()
	{
		//	Create the order
		$this->data['order'] = $this->shop_order_model->create( $this->data['basket'], TRUE );

		if ( ! $this->data['order'] ) :

			$this->session->set_flashdata( 'error', 'There was a problem checking out: ' . $this->data['error'] );
			redirect( app_setting( 'url', 'shop' ) . 'basket' );
			return;

		endif;

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

				$this->data['paypal']->url			= 'https://www.paypal.com/cgi-bin/webscr';
				$this->data['paypal']->business		= $_payment_gateway->account_id;

			break;

			default :

				$this->data['paypal']->url			= 'https://www.sandbox.paypal.com/cgi-bin/webscr';
				$this->data['paypal']->business		= $_payment_gateway->sandbox_account_id;

			break;

		endswitch;

		$this->data['paypal']->notify		= site_url( app_setting( 'url', 'shop' ) . 'checkout/notify/paypal' );
		$this->data['paypal']->cancel		= site_url( app_setting( 'url', 'shop' ) . 'checkout/cancel' );
		$this->data['paypal']->processing	= site_url( app_setting( 'url', 'shop' ) . 'checkout/processing' );

		// --------------------------------------------------------------------------

		//	Load the views
		$this->load->view( 'shop/' . $this->_skin->dir . '/checkout/payment/paypal/index',	$this->data );
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
		$this->data['basket'] = $this->shop_basket_model->get_basket();

		if ( ! $this->data['basket']->items ) :

			$this->data['error'] = 'Your basket is empty.';
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	public function processing()
	{
		$this->data['order'] = $this->shop_order_model->get_by_ref( $this->input->get( 'ref' ) );

		if ( ! $this->data['order'] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Empty the basket
		$this->shop_basket_model->destroy();

		// --------------------------------------------------------------------------

		switch( $this->data['order']->status ) :

			case 'UNPAID' :		$this->_processing_unpaid();		break;
			case 'PAID' :		$this->_processing_paid();			break;
			case 'PENDING' :	$this->_processing_pending();		break;
			case 'FAILED' :		$this->_processing_failed();		break;
			case 'ABANDONED' :	$this->_processing_abandoned();		break;
			case 'CANCELLED' :	$this->_processing_cancelled();		break;
			default :			$this->_processing_error();			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _processing_unpaid()
	{
		$this->load->view( 'shop/' . $this->_skin->dir . '/checkout/payment/processing/unpaid', $this->data );
	}


	// --------------------------------------------------------------------------


	protected function _processing_pending()
	{
		$this->load->view( 'structure/header',														$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/checkout/payment/processing/pending',	$this->data );
		$this->load->view( 'structure/footer',														$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _processing_paid()
	{
		$this->data['page']->title	= 'Thanks for your order!';
		$this->data['success']		= '<strong>Success!</strong> Your order has been processed.';

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',													$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/checkout/payment/processing/paid',	$this->data );
		$this->load->view( 'structure/footer',													$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _processing_failed()
	{
		$this->_processing_error();
	}


	// --------------------------------------------------------------------------


	protected function _processing_abandoned()
	{
		$this->_processing_error();
	}


	// --------------------------------------------------------------------------


	protected function _processing_cancelled()
	{
		$this->_processing_error();
	}


	// --------------------------------------------------------------------------


	protected function _processing_error()
	{
		if ( ! $this->data['error'] ) :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem processing your order';

		endif;

		if ( ! isset( $this->data['page']->title ) || ! $this->data['page']->title ) :

			$this->data['page']->title = 'An error occurred';

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',													$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/checkout/payment/processing/error',	$this->data );
		$this->load->view( 'structure/footer',													$this->data );
	}


	// --------------------------------------------------------------------------


	public function cancel()
	{
		$this->data['order'] = $this->shop_order_model->get_by_ref( $this->input->get( 'ref' ) );

		if ( ! $this->data['order'] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->shop_order_model->cancel( $this->data['order']->id );

		$this->session->set_flashdata( 'message', '<strong>Checkout was cancelled.</strong><br />At your request, we cancelled checkout - you have not been charged.' );

		redirect( app_setting( 'url', 'shop' ) . 'basket' );
	}


	// --------------------------------------------------------------------------


	public function notify()
	{
		//	Testing, testing, 1, 2, 3?
		$this->data['testing'] = $this->_notify_is_testing();

		//	Handle the notification in a way appropriate to the payment gateway
		switch( $this->uri->rsegment( 3 ) ) :

			case 'paypal';	$this->_notify_paypal();	break;

			// --------------------------------------------------------------------------

			default : /*	Silence is golden	*/	break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _notify_paypal()
	{
		//	Configure log
		_LOG_FILE( app_setting( 'url', 'shop' ) . 'notify/paypal/ipn-' . date( 'Y-m-d' ) . '.php' );

		_LOG();
		_LOG( '- - - - - - - - - - - - - - - - - - -' );
		_LOG( 'Waking up IPN responder; handling with PayPal' );

		// --------------------------------------------------------------------------

		//	POST data?

		//	Want to test a previous IPN message?
		//	Paste the IPN message into the following and uncomment the following lines

		//	$_message = '';
		//	$_message = str_replace( '+', '%2B', $_message );
		//	parse_str( $_message, $_POST );

		if ( ! $this->data['testing'] && ! $this->input->post() ) :

			_LOG( 'No POST data, going back to sleep...' );
			_LOG( '- - - - - - - - - - - - - - - - - - -' );
			_LOG();

			return;

		endif;

		// --------------------------------------------------------------------------

		//	Are we testing?
		if ( $this->data['testing'] ) :

			$_ipn = TRUE;
			_LOG();
			_LOG( '**TESTING**' );
			_LOG( '**Simulating data sent from PayPal**' );
			_LOG();

			//	Check order exists
			$_order = $this->shop_order_model->get_by_ref( $this->input->get( 'ref' ) );

			if ( ! $_order ) :

				_LOG( 'Invalid order reference, aborting.' );
				_LOG( '- - - - - - - - - - - - - - - - - - -' );
				_LOG();

				return;

			endif;

			// --------------------------------------------------------------------------

			$_paypal					= array();
			$_paypal['payment_type']	= 'instant';
			$_paypal['invoice']			= $_order->ref;
			$_paypal['custom']			=  $this->encrypt->encode( md5( $_order->ref . ':' . $_order->code ), APP_PRIVATE_KEY );
			$_paypal['txn_id']			= 'TEST:' . random_string( 'alpha', 6 );
			$_paypal['txn_type']		= 'cart';
			$_paypal['payment_status']	= 'Completed';
			$_paypal['pending_reason']	= 'PaymentReview';
			$_paypal['mc_fee']			= 0.00;

		else :

			_LOG( 'Validating the IPN call' );
			$this->load->library( 'paypal' );

			$_ipn		= $this->paypal->validate_ipn();
			$_paypal	= $this->input->post();

			$_order = $this->shop_order_model->get_by_ref( $this->input->post( 'invoice' ) );

			if ( ! $_order ) :

				_LOG( 'Invalid order ID, aborting. Likely a transaction not initiated by the site.' );
				_LOG( '- - - - - - - - - - - - - - - - - - -' );
				_LOG();

				return;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Did the IPN validate?
		if ( $_ipn ) :

			_LOG( 'IPN Verified with PayPal' );
			_LOG();

			// --------------------------------------------------------------------------

			//	Extra verification step, check the 'custom' variable decodes appropriately
			_LOG( 'Verifying data' );
			_LOG();

			$_verification = $this->encrypt->decode( $_paypal['custom'], APP_PRIVATE_KEY );

			if ( $_verification != md5( $_order->ref . ':' . $_order->code ) ) :

				$_data = array(
					'pp_txn_id'	=> $_paypal['txn_id']
				);
				$this->shop_order_model->fail( $_order->id, $_data );

				_LOG( 'Order failed secondary verification, aborting.' );
				_LOG( '- - - - - - - - - - - - - - - - - - -' );
				_LOG();

				// --------------------------------------------------------------------------

				//	Inform developers
				send_developer_mail( 'An IPN request failed', 'An IPN request was made which failed secondary verification, Order: ' . $_paypal['invoice'] );

				return;

			endif;

			// --------------------------------------------------------------------------

			//	Only bother to handle certain types
			//	TODO: handle refunds
			_LOG( 'Checking txn_type is supported' );
			_LOG();

			if ( $_paypal['txn_type'] != 'cart' ) :

				_LOG( '"' . $_paypal['txn_type'] . '" is not a supported PayPal txn_type, gracefully aborting.' );
				_LOG( '- - - - - - - - - - - - - - - - - - -' );
				_LOG();

				return;

			endif;

			// --------------------------------------------------------------------------

			//	Check if order has already been processed
			_LOG( 'Checking if order has already been processed' );
			_LOG();

			if ( ENVIRONMENT == 'production' && $_order->status != 'UNPAID' ) :

				_LOG( 'Order has already been processed, aborting.' );
				_LOG( '- - - - - - - - - - - - - - - - - - -' );
				_LOG();

				return;

			elseif ( ENVIRONMENT != 'production' && $_order->status != 'UNPAID' ) :

				_LOG( 'Order has already been processed, but not on production so continuing anyway.' );
				_LOG();

			endif;

			// --------------------------------------------------------------------------

			//	Check the status of the payment
			_LOG( 'Checking the status of the payment' );
			_LOG();


			switch( strtolower( $_paypal['payment_status'] ) ) :


				case 'completed' :

					//	Do nothing, this transaction is OK
					_LOG( 'Payment status is "completed"; continuing...' );

				break;

				// --------------------------------------------------------------------------

				case 'reversed' :

					//	Transaction was cancelled, mark order as FAILED
					_LOG( 'Payment was reversed, marking as failed and aborting' );

					$_data = array(
						'pp_txn_id'	=> $_paypal['txn_id']
					);
					$this->shop_order_model->fail( $_order->id, $_data );

				break;

				// --------------------------------------------------------------------------

				case 'pending' :

					//	Check the pending_reason, if it's 'paymentreview' then gracefully stop
					//	processing; PayPal will send a further IPN once the payment is complete

					_LOG( 'Payment status is "pending"; check the reason.' );

					if ( strtolower( $_paypal['pending_reason'] ) == 'paymentreview' ) :

						//	The transaction is pending review, gracefully stop proicessing, but don't cancel the order
						_LOG( 'Payment is pending review by PayPal, gracefully aborting just now.' );
						$this->shop_order_model->pending( $_order->id );
						return;

					else :

						_LOG( 'Unsupported payment reason "' . $_paypal['pending_reason'] . '", aborting.' );

						// --------------------------------------------------------------------------

						$_data = array(
							'pp_txn_id'	=> $_paypal['txn_id']
						);
						$this->shop_order_model->fail( $_order->id, $_data );

						// --------------------------------------------------------------------------

						//	Inform developers
						send_developer_mail( 'A PayPal payment failed', '<strong>' . $_order->user->first_name . ' ' . $_order->user->last_name . ' (' . $_order->user->email . ')</strong> has just attempted to pay for order ' . $_order->ref . '. The payment failed with status "' . $_paypal['payment_status'] . '" and reason "' . $_paypal['pending_reason'] . '".' );
						return;


					endif;

					// --------------------------------------------------------------------------

					return;

				break;

				// --------------------------------------------------------------------------

				default :

					//	Unknown/invalid payment status
					_LOG( 'Invalid payment status' );

					$_data = array(
						'pp_txn_id'	=> $_paypal['txn_id']
					);
					$this->shop_order_model->fail( $_order->id, $_data );

					// --------------------------------------------------------------------------

					//	Inform developers
					send_developer_mail( 'A PayPal payment failed', '<strong>' . $_order->user->first_name . ' ' . $_order->user->last_name . ' (' . $_order->user->email . ')</strong> has just attempted to pay for order ' . $_order->ref . '. The payment failed with status "' . $_paypal['payment_status'] . '" and reason "' . $_paypal['pending_reason'] . '".' );
					return;

				break;

			endswitch;

			// --------------------------------------------------------------------------

			//	All seems good, continue with order processing
			_LOG( 'All seems well, continuing...' );
			_LOG();

			_LOG( 'Setting txn_id (' . $_paypal['txn_id'] . ') and fees_deducted (' . $_paypal['mc_fee'] . ').' );
			_LOG();

			$_data = array(
				'pp_txn_id'		=> $_paypal['txn_id'],
				'fees_deducted'	=> $_paypal['mc_fee']
			);
			$this->shop_order_model->paid( $_order->id, $_data );

			// --------------------------------------------------------------------------

			//	PROCESSSSSS...
			$this->shop_order_model->process( $_order );
			_LOG();

			// --------------------------------------------------------------------------

			//	Send a receipt to the customer
			_LOG( 'Sending receipt to customer: ' . $_order->user->email );
			$this->shop_order_model->send_receipt( $_order );
			_LOG();

			// --------------------------------------------------------------------------

			//	Send a notification to the store owner(s)
			_LOG( 'Sending notification to store owner(s): ' . notification( 'notify_order', 'shop' ) );
			$this->shop_order_model->send_order_notification( $_order );

			// --------------------------------------------------------------------------

			if ( $_order->voucher ) :

				//	Redeem the voucher, if it's there
				_LOG( 'Redeeming voucher: ' . $_order->voucher->code . ' - ' . $_order->voucher->label );
				$this->shop_voucher_model->redeem( $_order->voucher->id, $_order );

			endif;

			// --------------------------------------------------------------------------

			_LOG();

			// --------------------------------------------------------------------------

			_LOG( 'All done here, going back to sleep...' );
			_LOG( '- - - - - - - - - - - - - - - - - - -' );
			_LOG();

			if ( $this->data['testing'] ) :

				echo anchor( app_setting( 'url', 'shop' ) . 'checkout/processing?ref=' . $_order->ref, 'Continue to Processing Page' );

			endif;

		else :

			_LOG( 'PayPal did not verify this IPN call, aborting.' );
			_LOG( '- - - - - - - - - - - - - - - - - - -' );
			_LOG();

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _notify_is_testing()
	{
		if ( ENVIRONMENT == 'production' )
			return FALSE;

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'testing' ) && $this->input->get( 'ref' ) ) :

			return TRUE;

		else :

			return FALSE;

		endif;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CHECKOUT' ) ) :

	class Checkout extends NAILS_Checkout
	{
	}

endif;

/* End of file checkout.php */
/* Location: ./application/modules/shop/controllers/checkout.php */