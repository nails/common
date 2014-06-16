<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Override
*
* Description:	Allows admins to log in as another user
*
*/

/**
 * OVERLOADING NAILS' AUTH MODULE
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
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	If you're not a admin then you shouldn't be accessing this class
		if ( ! $this->user_model->was_admin() && ! $this->user_model->is_admin() ) :

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
	 **/
	public function login_as( )
	{
		//	Perform lookup of user
		$_hashid = $this->uri->segment( 4 );
		$_hashpw = $this->uri->segment( 5 );

		$_u = $this->user_model->get_by_hashes( $_hashid, $_hashpw, TRUE );

		if ( ! $_u ) :

			show_error( lang( 'auth_override_invalid' ) );

		endif;

		// --------------------------------------------------------------------------

		//	Check sign-in permissions; ignore if recovering.
		//	Users cannot:
		//	- Sign in as themselves
		//	- Sign in as superusers (unless they are a superuser)

		if ( ! $this->session->userdata( 'admin_recovery' ) ) :

			$_permission	= user_has_permission( 'admin.accounts.can_login_as' );
			$_cloning		= active_user( 'id' ) == $_u->id ? TRUE : FALSE;
			$_superuser		= ! user_has_permission( 'superuser' ) && user_has_permission( 'superuser', $_u ) ? TRUE : FALSE;

			if ( ! $_permission || $_cloning || $_superuser ) :

				if ( ! $_permission ) :

					$this->session->set_flashdata( 'error', lang( 'auth_override_fail_nopermission' ) );
					redirect( 'admin/dashboard' );
					return;

				elseif ( $_cloning ) :

					show_error( lang( 'auth_override_fail_cloning' ) );

				elseif ( $_superuser ) :

					show_error( lang( 'auth_override_fail_superuser' ) );

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Prep recovery data
		$_recovery_data						= new stdClass();
		$_recovery_data->id					= md5( active_user( 'id' ) );
		$_recovery_data->hash				= md5( active_user( 'password' ) );
		$_recovery_data->email				= active_user( 'email' );
		$_recovery_data->name				= active_user( 'first_name' );
		$_recovery_data->logged_in_as		= $_u->id;
		$_recovery_data->now_where_was_i	= $this->input->get( 'return_to' );
		$_recovery_data->back_to_admin_url	= site_url( 'auth/override/login_as/' . $_recovery_data->id . '/' . $_recovery_data->hash );

		// --------------------------------------------------------------------------

		//	Replace current user's session data
		$this->user_model->set_login_data( $_u->id );

		// --------------------------------------------------------------------------

		//	Unset our admin recovery session data if we're recovering
		if ( $this->session->userdata( 'admin_recovery' ) ) :

			//	Where we sending the user back to? If not set go to the group homepage
			$_redirect = $this->session->userdata( 'admin_recovery' )->now_where_was_i;
			$_redirect = ( $_redirect ) ? $_redirect : $_u->group_homepage;

			//	Are we logging back in as the original admin? If so, unset the admin recovery,
			//	if not, leave it as it is so they can log back in in the future.

			$_original_admin = $this->session->userdata( 'admin_recovery' );

			if ( $_original_admin->id === $_u->id_md5 ) :

				$this->session->unset_userdata( 'admin_recovery' );

			else :

				//	We're logging in as someone else, update the recovery data
				//	to reflect the new user

				$_recovery_data = $this->session->userdata( 'admin_recovery' );
				$_recovery_data->logged_in_as = $_u->id;
				$this->session->set_userdata( 'admin_recovery', $_recovery_data );

			endif;

			//	Welcome home!
			$this->session->set_flashdata( 'success', lang( 'auth_override_return', $_u->first_name ) );

		else :

			//	It worked, it actually worked! They said I was crazy but it actually worked!
			$this->session->set_flashdata( 'success', lang( 'auth_override_ok', title_case( $_u->first_name . ' ' . $_u->last_name ) ) );

			//	Prep redirect variable
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
 * OVERLOADING NAILS' AUTH MODULE
 *
 * The following block of code makes it simple to extend one of the core auth
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
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