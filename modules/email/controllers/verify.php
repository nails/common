<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Email [verify]
 *
 * Description:	This controller handles verifying email addresses
 *
 **/

/**
 * OVERLOADING NAILS' AUTH MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

require_once '_email.php';

class NAILS_Verify extends NAILS_Email_Controller
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

		//	Fetch the user
		$_u = $this->user->get_by_id( $_id );

		if ( $_u && $_code ) :

			//	User found, attempt to verify
			if ( $this->user->email_verify( $_u->id, $_code ) ) :

				//	Reward referrer (if any
				if ( ! empty( $_u->referred_by ) ) :

					$this->user->reward_referral( $_u->id, $_u->referred_by );

				endif;

				// --------------------------------------------------------------------------

				//	Send user on their way
				if ( $this->input->get( 'return_to' ) ) :

					//	Let the next page handle wetehr the user is logged in or not etc.
					//	Ahh, go on set a wee notice that the user's email has been verified

					$this->session->set_flashdata( 'message', lang( 'email_verify_ok_subtle' ) );

					redirect( $this->input->get( 'return_to' ) );

				elseif ( ! $this->user->is_logged_in() ) :

					//	Set success message
					$this->session->set_flashdata( 'success', lang( 'email_verify_ok' ) );

					// --------------------------------------------------------------------------

					//	If a password change is requested, then redirect here
					if ( $_u->temp_pw ) :

						//	Send user on their merry way
						redirect( 'auth/reset_password/' . $_u->id . '/' . md5( $_u->salt ) );
						return;

					else :

						//	Nope, log in as normal
						$this->user->set_login_data( $_u->id );

						// --------------------------------------------------------------------------

						//	Where are we redirecting too?
						redirect( $_u->group_homepage );
						return;

					endif;

				else :


					//	Set success message
					$this->session->set_flashdata( 'success', lang( 'email_verify_ok' ) );

					// --------------------------------------------------------------------------

					//	And bounce, bounce, c'mon, bounce.
					redirect( $_u->group_homepage );
					return;

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		$this->session->set_flashdata( 'error', lang( 'email_verify_fail_error' ) . ' ' . $this->user->last_error() );
		redirect( '/' );
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
 * OVERLOADING NAILS' EMAIL MODULE
 *
 * The following block of code makes it simple to extend one of the core email
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

	class Verify extends NAILS_Verify
	{
	}

endif;

/* End of file verify.php */
/* Location: ./application/modules/email/controllers/verify.php */