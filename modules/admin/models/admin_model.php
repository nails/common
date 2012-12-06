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
	 * if it's there and return it's details (no response means the user doesn't have
	 * permission to execute this module).
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
					$_out->class_name = $module;
					
					//	List the public methods of this module (can't rely on the ['funcs'] array as it
					//	might not list a method which the active user needs in their ACL)
					
					$_methods = get_class_methods( $module );
					
					//	Strip out anything which is not public or which starts with a _ (pseudo private)
					$_remove_keys = array();
					foreach ( $_methods AS $key => $method ) :
					
						if ( substr( $method, 0, 1 ) == '_' ) :
						
							$_remove_keys[] = $key;
							continue;
						
						endif;
						
						// --------------------------------------------------------------------------
						
						$_method = new ReflectionMethod( $module, $method );
						
						if ( $_method->isStatic() ) :
						
							$_remove_keys[] = $key;
							continue;
						
						endif;
					
					endforeach;
					
					foreach ( $_remove_keys AS $key ) :
					
						unset( $_methods[$key] );
					
					endforeach;
					
					//	Build the methods array so that the method names are the keys
					$_details->methods = array();
					foreach ( $_methods AS $method ) :
					
						if ( isset( $_details->funcs[$method] ) ) :
						
							$_details->methods[$method] =  $_details->funcs[$method];
						
						else :
						
							$_details->methods[$method] =  '<em style="font-style:italic">' . ucwords( str_replace( '_', ' ', $method ) ) . '</em> <span style="color:#999;">- Unlisted</span>';
						
						endif;
					
					endforeach;
					
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