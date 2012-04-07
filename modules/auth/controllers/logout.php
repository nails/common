<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Auth [logout]
*
* Docs:			-
*
* Created:		14/10/2010
* Modified:		04/04/2012
*
* Description:	-
* 
*/
class Logout extends NAILS_Controller {
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Load model
		$this->load->model( 'auth_model' );
		
		// --------------------------------------------------------------------------
		
		//	Load language files
		$this->nails->load_lang( 'english/auth',	'modules/auth/language/english/auth');
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Log user out and forward to homepage (or via helper method if needed).
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{	
		//	If already logged out just send them silently on their way
		if ( ! $this->user->is_logged_in() )
			redirect( '/' );
		
		// --------------------------------------------------------------------------
		
		//	Handle flashdata, if there's anything there pass it along as GET variables.
		//	We're about to destroy the session so they'll go bye-bye unless we do
		//	something with 'em.
		
		$_flash['success']	= $this->session->flashdata( 'success' );
		$_flash['error']	= $this->session->flashdata( 'error' );
		$_flash['notice']	= $this->session->flashdata( 'notice' );
		$_flash['message']	= $this->session->flashdata( 'message' );
		
		// --------------------------------------------------------------------------
		
		//	Log user out
		$this->auth_model->logout();
		
		// --------------------------------------------------------------------------
		
		//	Redirect via helper method
		redirect( 'auth/logout/bye?' . http_build_query( $_flash ) );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Helper function to recreate a session (seeing as we destroyed it
	 * during logout); allows us to pass a message along if needed.
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function bye()
	{
		//	If there's no 'success' GET set our default log out message
		//	otherwise keep any which might be coming our way.
		
		$_get = $this->input->get();
		
		// --------------------------------------------------------------------------
		
		if ( isset( $_get['success'] ) && $_get['success'] ) :
		
			$this->session->set_flashdata( 'success', $_get['success'] );
		
		else :
		
			$this->session->set_flashdata( 'success', lang( 'logout_successful' ) );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set any other flashdata which might be needed
		if ( is_array( $_get ) ) :
		
			foreach ( $_get AS $key => $value ) :
			
				if ( $value )
					$this->session->set_flashdata( $key, $value );
					
			endforeach;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		redirect( '/' );
	}
}

/* End of file logout.php */
/* Location: ./application/modules/auth/controllers/logout.php */