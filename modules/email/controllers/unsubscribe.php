<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Unsubscribe
*
* Description:	Allows users to unsubscribe from a particular email type
*
*/

//	Include _email.php; executes common functionality
require_once '_email.php';

/**
 * OVERLOADING NAILS' EMAIL MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Unsubscribe extends NAILS_Email_Controller
{

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function index()
	{
		if ( ! $this->user->is_logged_in() ) :

			unauthorised();

		endif;

		$_token = $this->input->get( 'token' );
		$_token = $this->encrypt->decode( $_token, APP_PRIVATE_KEY );

		if ( ! $_token ) :

			show_404();

		endif;

		$_token = explode( '|', $_token );

		if ( count( $_token ) != 3 ) :

			show_404();

		endif;

		$_user = $this->user->get_by_email( $_token[2] );

		if ( ! $_user || $_user->id != active_user( 'id ' ) ) :

			show_404();

		endif;

		$this->load->library( 'emailer' );
		$_email = $this->emailer->get_by_ref( $_token[1] );

		if ( ! $_email ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	All seems above board, action the request
		if ( $this->input->get( 'undo' ) ) :

			if ( $this->emailer->user_has_unsubscribed( active_user( 'id' ), $_token[0] ) ) :

				$this->emailer->subscribe_user( active_user( 'id' ), $_token[0] );

			endif;

		else :

			if ( ! $this->emailer->user_has_unsubscribed( active_user( 'id' ), $_token[0] ) ) :

				$this->emailer->unsubscribe_user( active_user( 'id' ), $_token[0] );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'email/utilities/unsubscribe', $this->data );

	}


	// --------------------------------------------------------------------------


	/**
	 * Map all requests to index
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function _remap()
	{
		$this->index();
	}

}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' EMAIL MODULES
 *
 * The following block of code makes it simple to extend one of the core admin
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_UNSUBSCRIBE' ) ) :

	class Unsubscribe extends NAILS_Unsubscribe
	{
	}

endif;


/* End of file unsubscribe.php */
/* Location: ./application/modules/email/controllers/view_online.php */