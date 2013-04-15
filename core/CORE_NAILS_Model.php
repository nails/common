<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Model extends CI_Model {

	protected $data;
	protected $user;
	protected $_error;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Construct the model
	 *
	 * @access	protected
	 * @param	string	$error	The error message
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct( )
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Ensure models all have access to the NAILS_USR_OBJ if it's defined
		$this->user =& get_userobject();
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Set a generic error
	 *
	 * @access	protected
	 * @param	string	$error	The error message
	 * @return	void
	 * @author	Pablo
	 **/
	protected function _set_error( $error )
	{
		$this->_error[] = $error;
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Get any errors
	 *
	 * @access	public
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_error()
	{
		return $this->_error;
	}
}

/* End of file CORE_NAILS_Model.php */
/* Location: ./core/CORE_NAILS_Model.php */