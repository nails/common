<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Mini
*
* Docs:			http://nails.shedcollective.org/docs/utilities/mini/
*
* Created:		22/07/2011
* Modified:		20/12/2011
*
* Description:	This class generates handles the detection and decruption of IA 'short' urls.
*				It tries to keep load as low as possible so will only load the minimum
*				required resources (i.e not extend the main CI super object).
* 
*/


class Mini {

	private $_ci;
	private $_hash;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Construct the class; set defaults
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function __construct()
	{
		$this->_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Load models
		$this->_ci->load->model( 'mini_model' );
		
		// --------------------------------------------------------------------------
		
		//	Load langfile
		$this->_ci->lang->load( 'utilities' );
		
		// --------------------------------------------------------------------------
		
		//	Set the hash we're checking
		$this->_hash = $this->_ci->uri->rsegment( 4 );
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Takes a short URL ref and looks it up, if it hasn't expired we'll redirect
	 * the user, otherwise we'll fall over and error.
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function index()
	{
		$_hash = $this->_ci->mini_model->expand( $this->_hash );
		
		// --------------------------------------------------------------------------
		
		if ( $_hash == 'EXPIRED' )
			show_error( lang( 'mini_expired', site_url( 'auth/login' ) ) );
		
		// --------------------------------------------------------------------------
		
		if ( $_hash === FALSE )
			show_error( lang( 'mini_invalid' ) );
		
		// --------------------------------------------------------------------------
		
		redirect( $_hash );
	}
}

/* End of file mini.php */
/* Location: ./application/modules/utilities/controllers/mini.php */