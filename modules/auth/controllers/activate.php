<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Auth [activate]
*
* Docs:			http://nails.shedcollective.org/docs/auth/
*
* Created:		13/11/2010
* Modified:		03/02/2012
*
* Description:	This controller handles activating users
* 
*/


class Activate extends NAILS_Controller {
	
	
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
		
		$this->nails->load_view( 'structure/header',	'views/structure/header',			$this->data );
		$this->nails->load_view( 'auth/activate/fail',	'modules/auth/views/activate/fail',	$this->data );
		$this->nails->load_view( 'structure/footer',	'views/structure/footer',			$this->data );
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

/* End of file activate.php */
/* Location: ./application/modules/auth/controllers/activate.php */