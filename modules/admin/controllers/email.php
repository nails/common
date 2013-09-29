<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin
 *
 * Description:	Admin Email module
 *
 **/

//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Email extends NAILS_Admin_Controller
{

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function announce()
	{
		$d = new stdClass();

		// --------------------------------------------------------------------------

		//	Load the laguage file
		get_instance()->lang->load( 'admin_email', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------

		//	Configurations
		$d->name = lang( 'email_module_name' );

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs				= array();
		$d->funcs['index']		= lang( 'email_nav_index' );
		$d->funcs['compose']	= lang( 'email_nav_compose' );
		$d->funcs['campaign']	= lang( 'email_nav_campaign' );

		// --------------------------------------------------------------------------

		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns an array of notifications for various methods
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function notifications()
	{
		$_ci =& get_instance();
		$_notifications = array();

		// --------------------------------------------------------------------------

		//$_notifications['index']			= array();
		//$_notifications['index']['value']	= $_ci->db->count_all( NAILS_DB_PREFIX . 'user' );

		//	TODO: Notifications for draft messages
		//	TODO: Notifications for draft campaigns

		// --------------------------------------------------------------------------

		return $_notifications;
	}


	// --------------------------------------------------------------------------


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

		$this->load->library( 'emailer' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Email archive browser
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		//	Page Title
		$this->data['page']->title = lang( 'email_index_title' );

		// --------------------------------------------------------------------------

		//	Fetch emails from the archive
		$_offset = $this->input->get( 'offset' );

		$this->data['emails']		= new stdClass();
		$this->data['emails']->data	= $this->emailer->get_all( NULL, 'DESC', $_offset );

		//	Work out pagination
		$this->data['emails']->pagination					= new stdClass();
		$this->data['emails']->pagination->total_results	= $this->emailer->count_all();

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/email/index',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Compose a new message
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function compose()
	{
		//	Page Title
		$this->data['page']->title = lang( 'email_compose_title' );

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/email/compose',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Manage email campaigns
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function campaign()
	{
		//	Page Title
		$this->data['page']->title = lang( 'email_campaign_title' );

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/email/campaign',	$this->data );
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
 * CodeIgniter  instanciate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_DASHBOARD' ) ) :

	class Email extends NAILS_Email
	{
	}

endif;


/* End of file admin.php */
/* Location: ./application/modules/admin/controllers/admin.php */