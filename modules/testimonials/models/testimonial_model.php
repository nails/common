<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			testimonial_model.php
 *
 * Description:		This model handles everything to do with Testimonials
 * 
 **/

class Testimonial_model extends NAILS_Model
{
	/**
	 * Model constructor
	 * 
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		$this->_table = 'testimonial';
	}


	// --------------------------------------------------------------------------


	public function get_all()
	{
		$this->db->order_by( 'order' );
		return parent::get_all();
	}
}

/* End of file testimonial_model.php */
/* Location: ./application/models/testimonial_model.php */