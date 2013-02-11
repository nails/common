<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Auth [reset password]
*
* Docs:			http://nails.shedcollective.org/docs/auth/
*
* Created:		30/10/2010
* Modified:		04/01/2012
*
* Description:	This controller handles the resetting of a user's temporary password
* 
*/

/**
 * OVERLOADING NAILS'S AUTH MODULE
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

require_once '_auth.php';

class NAILS_Reset_Password extends NAILS_Controller
{
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
		
		// --------------------------------------------------------------------------
		
		//	If user is logged in they shouldn't be accessing this method
		if ( $this->user->is_logged_in() ) :
		
			$this->session->set_flashdata( 'error', lang( 'no_access_already_logged_in', $this->user->active_user( 'email' ) ) );
			redirect( '/' );
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Validate the supplied assets and if valid present the user with a reset form
	 *
	 * @access	public
	 * @param	int		$id		The ID fo the user to reset
	 * @param	strgin	hash	The hash to validate against
	 * @return	void
	 * @author	Pablo
	 **/
	private function _validate( $id, $hash )
	{
		//	Check auth credentials
		$_user = $this->user->get_user( $id );
		
		// --------------------------------------------------------------------------
		
		if ( $_user !== FALSE && isset( $_user->salt ) && $hash == md5( $_user->salt ) ) :
		
			//	Valid combination
			if ( $this->input->post() ) :
			
				// Validate data
				$this->load->library( 'form_validation' );
				
				// --------------------------------------------------------------------------
				
				//	Define rules
				$this->form_validation->set_rules( 'new_password',	'password',		'required|matches[confirm_pass]' );
				$this->form_validation->set_rules( 'confirm_pass',	'confirmation',	'required' );
				
				// --------------------------------------------------------------------------
				
				//	Set custom messages
				$this->form_validation->set_message( 'required',	lang( 'required_reset' ) );
				$this->form_validation->set_message( 'matches',		lang( 'matches' ) );
				$this->form_validation->set_message( 'min_length',	lang( 'min_length_change_temp' ) );
				
				// --------------------------------------------------------------------------
				
				//	Run validation
				if ( $this->form_validation->run() ) :
					
					//	Validated, update user and login.
					$_data['forgotten_password_code']	= NULL;
					$_data['temp_pw']					= NULL;
					$_data['password']					= $this->input->post( 'new_password' );
					
					//	Reset the password
					$this->user->update( $id, $_data );
					
					//	Log the user in
					$_login = $this->auth_model->login( $_user->email, $this->input->post( 'new_password' ), TRUE );
					
					$this->session->set_flashdata( 'message', lang( 'login_ok_welcome', array ( title_case( $_login['first_name'] ), nice_time( $_login['last_login'] ) ) ) );
					
					//	Log user in and forward to wherever they need to go
					if ( $this->input->get( 'return_to' ) ):
					
						redirect( $this->input->get( 'return_to' ) );
						return;
						
					elseif ( $_user->group_homepage ) :
					
						redirect( $_user->group_homepage );
						return;
						
					else :
					
						redirect( '/' );
						return;
						
					endif;
					
				else:
				
					$this->data['error'] = lang( 'register_error' );
					
				endif;
				
			endif;
			
			// --------------------------------------------------------------------------
			
			$this->data['auth']			= new stdClass();
			$this->data['auth']->id		= $id;
			$this->data['auth']->hash	= $hash;
			$this->data['return_to']	= ( $this->input->get( 'return_to' ) ) ? '?return_to=' . urlencode( $this->input->get( 'return_to' ) ) : NULL;
			
			// --------------------------------------------------------------------------
			
			//	Load the views; using the auth_model view loader as we need to check if
			//	an overload file exists which should be used instead
			
			$this->nails->load_view( 'structure/header',			'views/structure/header',					$this->data );
			$this->nails->load_view( 'auth/password/change_temp',	'modules/auth/views/password/change_temp',	$this->data );
			$this->nails->load_view( 'structure/footer',			'views/structure/footer',					$this->data );
			
			return;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		show_404();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Route requests to the right method
	 *
	 * @access	public
	 * @param	string	$id	the ID of the user to reset, as per the URL
	 * @return	void
	 * @author	Pablo
	 **/
	public function _remap( $id )
	{
		$this->_validate( $id, $this->uri->segment( 4 ) );
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

	class Reset_Password extends NAILS_Reset_Password
	{
	}

endif;

/* End of file reset_password.php */
/* Location: ./application/modules/auth/controllers/reset_password.php */