<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop - Checkout
 *
 * Description:	This controller handles the user's checkout flow
 * 
 **/

require_once '_shop.php';

class Checkout extends NAILS_Shop_Controller
{
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
		$this->data['page']->title = 'Checkout';
		
		// --------------------------------------------------------------------------
		
		$this->data['basket'] = $this->basket->get_basket();
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'shop/checkout/index',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
}

/* End of file checkout.php */
/* Location: ./application/modules/shop/controllers/checkout.php */