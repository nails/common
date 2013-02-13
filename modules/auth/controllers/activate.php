<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [activate]
 *
 * Description:	This controller handles activating users
 * 
 **/

/**
 * OVERLOADING NAILS'S AUTH MODULE
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

require_once '_auth.php';

class NAILS_Activate extends NAILS_Auth_Controller
{
	/**
	 * Attempt to validate the user's activation code
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		//	Define the key variables
		$_id	= $this->uri->segment( 3, NULL );
		$_code	= $this->uri->segment( 4, NULL );
		
		// --------------------------------------------------------------------------
		
		//	Incorrect data - fail
		if ( $_id === NULL || $_code === NULL ) :
		
			$this->session->set_flashdata( 'error', lang( 'no_access_bad_data' ) );
			redirect( '/' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		// Validate activation code
		if ( $this->user->activate( $_id, $_code ) ) :
			
			//	Fetch the suer
			$_u = $this->user->get_user( $_id );
			
			// --------------------------------------------------------------------------
			
			//	Reward referrer (if any)
			if ( ! empty( $_u->referred_by ) ) :
			
				$this->load->model( 'referral_model' );
				$this->referral_model->reward_referral( $_id, $_u->referred_by );
				
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Send user on their way
			if ( ! $this->user->is_logged_in() )
				$this->user->set_login_data( $_u->id, $_u->email, $_u->group_id );
			
			// --------------------------------------------------------------------------
			
			$this->session->set_flashdata( 'success', '<strong>Email verified successfully, thanks!</strong>' );
			
			// --------------------------------------------------------------------------
			
			//	Where are we redirecting too?	
			redirect( $_u->group_homepage );
			return;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load the views; using the auth_model view loader as we need to check if
		//	an overload file exists which should be used instead
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'auth/activate/fail',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 *  Map the class so that index() does all the work
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function _remap()
	{
		$this->index();
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
 * CodeIgniter  instanciate a class with the same name as the file, therefore
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

	class Activate extends NAILS_Activate
	{
	}

endif;

/* End of file activate.php */
/* Location: ./application/modules/auth/controllers/activate.php */