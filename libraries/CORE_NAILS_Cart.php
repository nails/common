<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Cart extends CI_Cart {
    function __construct()
    {
        parent::__construct();
        $this->product_name_rules = '\,\(\)\"\'\.\:\-_ a-z0-9';
    }
}

/* End of file NAILS_Cart.php */
/* Location: ./system/application/core/NAILS_Cart.php */