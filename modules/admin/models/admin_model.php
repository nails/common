<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin Model
 *
 * Docs:		http://nails.shedcollective.org/docs/users/
 *
 * Created:		28/03/2012
 * Modified:	28/03/2012
 *
 * Description:	This model exists to force CI to load into memory the NAILS_Model class;
 *				Unless you load a model using load->model() this base class is not loaded
 *				so when NAILS_Controller comes to laod the user_model PHP cries and falls over.
 *				Took me an age to figure this one out...
 * 
 */

class Admin_Model extends NAILS_Model
{
	private $search_paths;
	
	
	// --------------------------------------------------------------------------
	
	
	public function __construct()
	{
		$this->search_paths[] = FCPATH . APPPATH . 'modules/admin/controllers/';	//	Admin controllers specific for this app only.
		$this->search_paths[] = NAILS_PATH . 'modules/admin/controllers/';
	}
	
	
	// --------------------------------------------------------------------------
	
	
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
		//dump('-----------');
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _exec_announcer( $class )
	{
		return ( method_exists( $class, 'announce') ) ? $class::announce() : NULL;
	}
}

/* End of file dummy_model.php */
/* Location: ./application/models/dummy_model.php */