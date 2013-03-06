<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Override
*
* Description:	Allows admins to log in as another user
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

class NAILS_Override extends NAILS_Auth_Controller
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
		
		//	If you're not a admin then you shouldn't be accessing this class
		if ( ! $this->user->was_admin() && ! $this->user->is_admin() ) :
		
			$this->session->set_flashdata( 'error', lang( 'auth_no_access' ) );
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
		//	Perform lookup of user
		$_hashid = $this->uri->segment( 4 );
		$_hashpw = $this->uri->segment( 5 );
		
		$_u = $this->user->get_user_by_hashes( $_hashid, $_hashpw, TRUE );
		
		if ( ! $_u ) :
		
			show_error( lang( 'auth_override_invalid' ) );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Check sign-in permissions; ignore if recovering.
		//	Users cannot:
		//	- Sign in as themselves
		//	- Sign in as superusers or admins (groups 0 and 1)
		//	- Sign in as someone of the same group
		
		if ( ! $this->session->userdata( 'admin_recovery' ) ) :
		
			$_cloning	= ( active_user( 'id' ) == $_u->id );
			$_superuser	= ( isset( $_u->acl['superuser'] ) && $_u->acl['superuser'] );
			$_group		= ( active_user( 'group_id' ) == $_u->group_id );
			
			if ( $_cloning || $_superuser || $_group ) :
			
				if ( $_cloning ) :
				
					show_error( 'auth_override_fail_cloning' );
					
				elseif ( $_superuser ) :
				
					show_error( 'auth_override_fail_superuser' );
				
				elseif ( $_group ) :
				
					show_error( 'auth_override_fail_group' );
				
				endif;
				
			endif;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Prep recovery data
		$_recovery_data						= new stdClass();
		$_recovery_data->id					= md5( active_user( 'id' ) );
		$_recovery_data->hash				= md5( active_user( 'password' ) );
		$_recovery_data->email				= active_user( 'email' );
		$_recovery_data->now_where_was_i	= $this->input->get( 'return_to' );
		
		// --------------------------------------------------------------------------
		
		//	Replace current user's session data
		$this->user->set_login_data( $_u->id, $_u->email, $_u->group_id );
		
		$this->session->set_flashdata( 'success', lang( 'auth_override_ok', title_case( $_u->first_name . ' ' . $_u->last_name ) ) );
		
		// --------------------------------------------------------------------------
		
		//	Unset our admin recovery session data if we're recovering
		if ( $this->session->userdata( 'admin_recovery' ) ) :
		
			//	Where we sending the user back to? If not set go to the group homepage
			$_redirect = $this->session->userdata( 'admin_recovery' )->now_where_was_i;
			$_redirect = ( $_redirect ) ? $_redirect : $_u->group_homepage;
			
			$this->session->unset_userdata( 'admin_recovery' );
			
			//	Change the success message to reflect the user coming back
			$this->session->set_flashdata( 'success', lang( 'auth_override_return', $_u->first_name ) );
		
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