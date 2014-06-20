<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop - Orders
 *
 * Description:	This controller handles order, specifically invoice generation
 *
 **/

/**
 * OVERLOADING NAILS' SHOP MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

//	Include _shop.php; executes common functionality
require_once '_shop.php';

class NAILS_Orders extends NAILS_Shop_Controller
{
	public function invoice()
	{
		$this->data['order'] = $this->shop_order_model->get_by_ref( $this->uri->segment( 4 ) );

		//	Order exist?
		if ( ! $this->data['order'] ) :

			return $this->_bad_invoice( 'Invoice does not exist.' );

		endif;

		// --------------------------------------------------------------------------

		//	User have permission?
		$_id_match		= $this->data['order']->user->id && $this->data['order']->user->id != active_user( 'id' );
		$_email_match	= $this->data['order']->user->email && $this->data['order']->user->email != active_user( 'email' );

		if ( ! $this->user_model->is_admin() && ! $_id_match && ! $_email_match ) :

			return $this->_bad_invoice( 'Permission Denied.' );

		endif;

		// --------------------------------------------------------------------------

		//	Render PDF
		if ( isset( $_GET['dl'] ) && ! $_GET['dl'] ) :

			$this->load->view('shop/' . $this->_skin->dir . '/orders/invoice', $this->data );

		else :

			$this->load->library( 'pdf' );
			$this->pdf->load_view('shop/' . $this->_skin->dir . '/orders/invoice', $this->data );
			$this->pdf->render();
			$this->pdf->stream( 'INVOICE-' . $this->data['order']->ref . '.pdf' );

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _bad_invoice( $message )
	{
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Content-type: application/json' );
		header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 400 Bad Request' );

		// --------------------------------------------------------------------------

		$_out = array(

			'status'	=> 400,
			'message'	=> $message

		);

		echo json_encode( $_out );

		// --------------------------------------------------------------------------

		//	Kill script, th, th, that's all folks.
		//	Stop the output class from hijacking our headers and
		//	setting an incorrect Content-Type

		exit(0);
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_ORDERS' ) ) :

	class Orders extends NAILS_Orders
	{
	}

endif;

/* End of file orders.php */
/* Location: ./application/modules/shop/controllers/orders.php */