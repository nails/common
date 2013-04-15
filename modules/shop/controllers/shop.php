<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop
 *
 * Description:	This controller handles the frontpage of the shop
 * 
 **/

require_once '_shop.php';

class Shop extends NAILS_Shop_Controller
{
	/**
	 * Shop front
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function index()
	{
		$this->data['page']->title = 'Shop';
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'shop/front/index',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
}

/* End of file shop.php */
/* Location: ./application/modules/shop/controllers/shop.php */