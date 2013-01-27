<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			API
 *
 * Created:			18/11/2012
 * Modified:		18/11/2012
 *
 * Description:	This controller handles generic API methods
 * 
 **/

require_once '_api.php';

class Api extends API_Controller
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