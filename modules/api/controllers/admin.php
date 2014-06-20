<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin API
 *
 * Description:	This controller handles API methods relating to admin
 *
 **/

require_once '_api.php';

/**
 * OVERLOADING NAILS' API MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Admin extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_authorised	= TRUE;
		$this->_error		= '';

		// --------------------------------------------------------------------------

		//	Constructor mabobs.

		//	IP whitelist?
		$_ip_whitelist = json_decode( APP_ADMIN_IP_WHITELIST );

		if ( $_ip_whitelist ) :

			if ( ! ip_in_range( $this->input->ip_address(), $_ip_whitelist ) ) :

				show_404();

			endif;

		endif;

		//	Only logged in users
		if ( ! $this->user_model->is_logged_in() ) :

			$this->_authorised	= FALSE;
			$this->_error		= lang( 'auth_require_session' );

		//	Only admins
		elseif ( ! $this->user_model->is_admin() ) :

			$this->_authorised	= FALSE;
			$this->_error		= lang( 'auth_require_administrator' );

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' API MODULES
 *
 * The following block of code makes it simple to extend one of the core API
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_ADMIN' ) ) :

	class Admin extends NAILS_Admin
	{
	}

endif;

/* End of file admin.php */
/* Location: ./modules/api/controllers/admin.php */