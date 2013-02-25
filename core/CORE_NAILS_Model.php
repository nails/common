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
		
		//	Determine which modules are to be loaded
		//	Duplicated from CORE_NAILS_Controller.php
		
		$_app_modules	= explode( ',', APP_NAILS_MODULES );
		$_app_modules	= array_unique( $_app_modules );
		$_app_modules	= array_filter( $_app_modules );
		$_app_modules	= array_combine( $_app_modules, $_app_modules );
		
		$this->nails_modules = array();
		foreach ( $_app_modules AS $module ) :
		
			preg_match( '/^(.*?)(\[(.*?)\])?$/', $module, $_matches );
			
			if ( isset( $_matches[1] ) && isset( $_matches[3] ) ) :
			
				$this->nails_modules[$_matches[1]] = explode( '|', $_matches[3] );
			
			elseif ( isset( $_matches[1] ) ) :
			
				$this->nails_modules[$_matches[1]] = array();
			
			endif;
		
		endforeach;
		
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
		preg_match( '/^(.*?)(\[(.*?)\])?$/', $module, $_matches );
		
		$_module	= isset( $_matches[1] ) ? $_matches[1] : '';
		$_submodule	= isset( $_matches[3] ) ? $_matches[3] : '';
		
		if ( isset( $this->nails_modules[$_module] ) ) :
		
			//	Are we testing for a submodule in particular?
			if ( $_submodule ) :
			
				return array_search( $_submodule, $this->nails_modules[$_module] ) !== FALSE;
			
			else :
			
				return TRUE;
			
			endif;
		
		else :
		
			return FALSE;
		
		endif;
	}
}

/* End of file NAILS_Model.php */
/* Location: ./system/application/core/NAILS_Model.php */