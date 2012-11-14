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

/**
 * OVERLOADING NAILS'S AUTH MODULE
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/
 
class NAILS_Logout extends NAILS_Controller {
	
	
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
		
		//	Generate an event for this log in
		create_event( 'did_log_out', active_user( 'id' ) );
		
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


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S AUTH MODULE
 * 
 * The following block of code makes it simple to extend one of the core auth
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 * 
 * Here's how it works:
 * 
 * CodeIgniter instanciate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 * 
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
 * 
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 * 
 **/
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION' ) ) :

	class Logout extends NAILS_Logout
	{
	}

endif;

/* End of file logout.php */
/* Location: ./application/modules/auth/controllers/logout.php */