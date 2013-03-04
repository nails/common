<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin API
 *
 * Description:	This controller handles API emthods relating to admin
 * 
 **/

require_once '_api.php';

class Admin extends NAILS_API_Controller
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
		
		// --------------------------------------------------------------------------
		
		$this->_authorised	= TRUE;
		$this->_error		= '';
		
		// --------------------------------------------------------------------------
		
		//	Instant search specific constructor mabobs.
		
		//	Only logged in users
		if ( ! $this->user->is_logged_in() ) :
		
			$this->_authorised	= FALSE;
			$this->_error		= lang( 'auth_require_session' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Only admins
		if ( ! $this->user->is_admin() ) :
		
			$this->_authorised	= FALSE;
			$this->_error		= lang( 'auth_require_administrator' );
		
		endif;
	}
}

/* End of file admin.php */
/* Location: ./application/modules/api/controllers/admin.php */