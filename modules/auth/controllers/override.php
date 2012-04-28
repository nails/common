<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Override
*
* Docs:			http://nails.shedcollective.org/docs/auth/
*
* Created:		30/06/2011
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
 
class NAILS_Override extends NAILS_Controller {
	
	
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
		
		//	If you're not a admin then you shouldn't be accessing this class
		if ( ! $this->user->was_admin() && ! $this->user->is_admin() ) :
		
			$this->session->set_flashdata( 'error', 'You do not have permission to access that content.' );
			redirect( '/' );
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Log in as another user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function login_as( )
	{
		//	Check we got something to work with
		if ( ! $this->uri->segment( 4 ) || ! $this->uri->segment( 5 ) )
			show_error( 'No security token!' );
		
		// --------------------------------------------------------------------------
		
		//	Perform lookup of user
		$_hashid = $this->uri->segment( 4 );
		$_hashpw = $this->uri->segment( 5 );
		
		$_u = $this->user->get_user_by_hashes( $_hashid, $_hashpw, TRUE );
		
		if ( ! $_u )
			show_error( 'Sorry, the supplied credentials failed validation.' );
		
		// --------------------------------------------------------------------------
		
		//	Check sign-in permissions; ignore if recovering.
		//	Users cannot:
		//	- Sign in as themselves
		//	- Sign in as superusers or admins (groups 0 and 1)
		//	- Sign in as someone of the same group
		if ( ! $this->session->userdata( 'admin_recovery' ) ) :
		
			if ( active_user( 'id' ) == $_u->id || active_user( 'group_id' ) == $_u->group_id || array_search( $_u->group_id, array( 0, 1 ) ) !== FALSE )
				show_error( 'You cannot sign in as this person. For security we do not allow users to sign in as another administrator or another user of
				the same group; it is also not possible to sign in as yourself for a second time; doing so will cause a break in the space-time continuum.
				I don\'t believe you want to be responsible for that now, do you?' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Prep recovery data
		$_recovery_data->id					= md5( active_user( 'id' ) );
		$_recovery_data->hash				= md5( active_user( 'password' ) );
		$_recovery_data->email				= active_user( 'email' );
		$_recovery_data->now_where_was_i	= $this->input->get( 'return_to' );
		
		// --------------------------------------------------------------------------
		
		//	Replace current user's session data
		$this->user->set_login_data( $_u->id, $_u->email, $_u->group_id );
		
		$this->session->set_flashdata( 'success', 'You were successfully logged in as <strong>' . title_case( $_u->first_name . ' ' . $_u->last_name ) . '</strong>' );
		
		// --------------------------------------------------------------------------
		
		//	Unset our admin recovery session data if we're recovering
		if ( $this->session->userdata( 'admin_recovery' ) ) :
		
			//	Where we sending the user back to? If not set go to the group homepage
			$_redirect = $this->session->userdata( 'admin_recovery' )->now_where_was_i;
			$_redirect = ( $_redirect ) ? $_redirect : $_u->group_homepage;
			
			$this->session->unset_userdata( 'admin_recovery' );
		
		//	Otherwise set this variable so we CAN come back.
		else :
			
			$_redirect = $_u->group_homepage;
			
			//	Set a session variable so we can come back as admin
			$this->session->set_userdata( 'admin_recovery', $_recovery_data );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Redirect our user
		redirect( $_redirect );
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

	class Override extends NAILS_Override
	{
	}

endif;

/* End of file reset_password.php */
/* Location: ./application/modules/auth/controllers/reset_password.php */