<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin: Log
* Description:	Log manager
*
*/

//	Include Admin_Controller; executes common admin functionality.
require_once NAILS_PATH . 'modules/admin/controllers/_admin.php';

/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Logs extends NAILS_Admin_Controller
{

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access static
	 * @param none
	 * @return void
	 **/
	static function announce()
	{
		$d = new stdClass();

		// --------------------------------------------------------------------------

		//	Configurations
		$d->name				= 'Logs';					//	Display name.

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs['index']			= 'Browse Site Logs';	//	Sub-nav function.
		$d->funcs['changelog']		= 'Browse Admin Logs';	//	Sub-nav function.

		// --------------------------------------------------------------------------

		//	Only announce the controller if the user has permission to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


	/**
	 * Log File browser
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function index()
	{
		$this->data['page']->title = 'Browse Logs';

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/logs/index',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Admin Change Log Browser
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function changelog()
	{
		$this->data['page']->title = 'Admin Changelog';

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/logs/changelog',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' ADMIN MODULES
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_LOGS' ) ) :

	class Logs extends NAILS_Logs
	{
	}

endif;

/* End of file logs.php */
/* Location: ./modules/admin/controllers/logs.php */