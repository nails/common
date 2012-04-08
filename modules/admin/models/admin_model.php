<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin Model
 *
 * Docs:		http://nails.shedcollective.org/docs/users/
 *
 * Created:		28/03/2012
 * Modified:	08/04/2012
 *
 * Description:	This model contains some basic common admin functionality.
 * 
 */

class Admin_Model extends NAILS_Model
{
	private $search_paths;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor; set the defaults
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		/**
		 * Set the search paths to look for modules within; paths listed first
		 * take priority over those listed after it.
		 * 
		 **/
		$this->search_paths[] = FCPATH . APPPATH . 'modules/admin/controllers/';	//	Admin controllers specific for this app only.
		$this->search_paths[] = NAILS_PATH . 'modules/admin/controllers/';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Look for modules which reside within the search paths; execute the announcer
	 * if it's there and return it's details (no response means the suer doesn't have
	 * permission to execute this module
	 *
	 * @access	public
	 * @param	string	$module	The name of the module to search for
	 * @return	array
	 * @author	Pablo
	 **/
	public function find_module( $module )
	{
		$_out = array();
		
		// --------------------------------------------------------------------------
		
		//	Look in our search paths for a controller of the same name as the module.
		foreach ( $this->search_paths AS $path ) :
		
			if ( file_exists( $path . $module . '.php' ) ) :
			
				require_once $path . $module . '.php';
				
				$_details = $this->_exec_announcer( $module );
				
				if ( $_details ) :
				
					$_out = $_details;
					break;
					
				endif;
			
			endif;
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Execute the module announcer if it exists
	 *
	 * @access	public
	 * @param	string	$class	The name of the class we're announcing
	 * @return	array
	 * @author	Pablo
	 **/
	private function _exec_announcer( $class )
	{
		return ( method_exists( $class, 'announce') ) ? $class::announce() : NULL;
	}
}

/* End of file dummy_model.php */
/* Location: ./application/models/dummy_model.php */