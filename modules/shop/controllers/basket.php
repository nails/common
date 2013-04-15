<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop - Basket
 *
 * Description:	This controller handles the user's basket
 * 
 **/

require_once '_shop.php';

class Basket extends NAILS_Shop_Controller
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
		$this->data['page']->title = 'Your Basket';
		
		// --------------------------------------------------------------------------
		
		$this->data['basket'] = $this->basket->get_basket();
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'shop/basket/index',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
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
		
		redirect( 'shop/basket' );
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
		
		redirect( 'shop/basket' );
	}
}

/* End of file basket.php */
/* Location: ./application/modules/shop/controllers/basket.php */