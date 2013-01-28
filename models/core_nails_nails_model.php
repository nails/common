<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Nails_model
*
* Docs:			-
*
* Created:		06/04/2012
* Modified:		06/04/2012
*
* Description:	This model contains a handful of helper functions used throughout the Nails modules
* 
*/

class CORE_NAILS_Nails_Model extends NAILS_Model
{
	/**
	 * Loads a view, looking in the application first
	 *
	 * @access	protected
	 * @param	string	$desired	The view to look for, this view should exist at application/views/...
	 * @param	string	$fallback	The view to fallback to; this will exist within the NAILS package.
	 * @param	array	$data		The variables to pass to the loaded view
	 * @param	boolean	$return		Whether to return the data as a string or not
	 * @return	void
	 * @author	Pablo
	 **/
	public function load_view( $desired, $fallback, $data, $return = FALSE )
	{
		//	See if the view exists
		$_desired_view	= FCPATH . APPPATH . 'views/' . $desired . '.php';
		$_fallback_view	= NAILS_PATH . $fallback . '.php';
		
		// --------------------------------------------------------------------------
		
		if ( $desired && file_exists( $_desired_view ) ) :
		
			//	Desired view exists, load that
			$this->load->view( $_desired_view, $data, $return );
		
		elseif ( file_exists( $_fallback_view ) ) :
		
			//	Desired view doesn't exist, but the fallback does
			$this->load->view( $_fallback_view, $data, $return );
		
		else :
		
			//	Neither desired or fallback view exists; attempt to load view anyway and see what happens.
			//	Unexpected behaviour expected...
			
			$this->load->view( $desired, $data, $return );
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Loads a langfile, looking in the application first
	 *
	 * @access	protected
	 * @param	string	$desired	The langfile to look for, this view should exist at application/language/...
	 * @param	string	$fallback	The langfile to fallback to; this will exist within the NAILS package.
	 * @param	...		...
	 * @return	void
	 * @author	Pablo
	 **/
	public function load_lang( $desired, $fallback, $lang = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '', $_module = '' )
	{
		//	See if the langfile exists
		$_desired_lang	= FCPATH . APPPATH . 'language/' . $desired;
		$_fallback_lang	= NAILS_PATH . $fallback;
		
		// --------------------------------------------------------------------------
		
		if ( file_exists( $_desired_lang . '_lang.php' ) ) :
		
			//	Desired langfile exists, load that
			$this->lang->load( $_desired_lang, $lang, $return, $add_suffix, $alt_path, $_module );
		
		elseif ( file_exists( $_fallback_lang . '_lang.php' ) ) :
		
			//	Desired langfile doesn't exist, but the fallback does
			$this->lang->load( $_fallback_lang, $lang, $return, $add_suffix, $alt_path, $_module );
		
		else :
		
			//	Neither desired or fallback langfile exists; attempt to load langfile anyway and see what happens.
			//	Unexpected behaviour expected...
			
			$this->lang->load( $_desired_lang, $lang, $return, $add_suffix, $alt_path, $_module);
			
		endif;
	}
}

/* End of file CORE_NAILS_nails_model.php */
/* Location: ./public_html/packages/NAILS/models/CORE_NAILS_nails_model.php */