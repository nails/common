<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		API
 *
 * Description:	This controller handles generic API methods
 * 
 **/

require_once '_api.php';

class Api extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Instant search specific constructor
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function __construct()
	{
		parent::__construct();
	}
}

/* End of file api.php */
/* Location: ./application/modules/api/controllers/api.php */