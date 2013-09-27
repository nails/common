<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NALS_SHOP_Shipping_Driver
 *
 * Description:	This controller executes various bits of common Shop functionality
 *
 **/


class NALS_SHOP_Shipping_Driver
{
	private $db;


	// --------------------------------------------------------------------------

	public function __construct()
	{
		$this->db =& get_instance()->db;
	}
}

/* End of file _driver.php */
/* Location: ./modules/shop/drivers/shipping/_driver.php */