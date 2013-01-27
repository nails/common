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
		
		//	Determine which modules are to be loaded; include default modules
		//	Duplicated from CORE_NAILS_Controller.php
		
		$_app_modules			= explode( ',', 'auth,admin,dashboard' . APP_NAILS_MODULES );
		$this->nails_modules	= array_unique( $_app_modules );
		$this->nails_modules	= array_filter( $this->nails_modules );
		
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
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether a module is defined in APP_NAILS_MODULE
	 *
	 * @access	protected
	 * @param	string	$module	The module to look for
	 * @return	bool
	 * @author	Pablo
	 **/
	protected function _module_is_enabled( $module )
	{
		return array_search( strtolower( $module ), $this->nails_modules ) !== FALSE;
	}
}

/* End of file NAILS_Model.php */
/* Location: ./system/application/core/NAILS_Model.php */