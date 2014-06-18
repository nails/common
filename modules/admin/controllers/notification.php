<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin: Notification
 * Description:	Manage notification
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

class NAILS_Notification extends NAILS_Admin_Controller
{

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 **/
	static function announce()
	{
		$d = new stdClass();

		// --------------------------------------------------------------------------

		//	Load the laguage file
		get_instance()->lang->load( 'admin_notification' );

		// --------------------------------------------------------------------------

		//	Configurations
		$d->name = lang( 'notification_module_name' );

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs				= array();
		$d->funcs['index']		= lang( 'notification_nav_index' );

		// --------------------------------------------------------------------------

		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


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

		$this->load->model( 'system/app_notification_model' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Manage evcents
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function index()
	{
		//	Page Title
		$this->data['page']->title = lang( 'notification_index_title' );

		// --------------------------------------------------------------------------

		if ( $this->input->post() ) :

			$_notification = $this->input->post( 'notification' );

			if ( is_array( $_notification ) ) :

				$this->load->helper( 'email' );

				$_set = array();

				foreach( $_notification AS $grouping => $options ) :

					$_set[$grouping] = array();

					foreach( $options AS $key => $emails ) :

						$emails = explode( ',', $emails );
						$emails = array_filter( $emails );
						$emails = array_unique( $emails );

						foreach( $emails AS &$email ) :

							$email = trim( $email ) ;

							if ( ! valid_email( $email ) ) :

								$_error = '"<strong>' . $email . '</strong>" is not a valid email.';
								break 3;

							endif;

						endforeach;

						$_set[$grouping][$key] = $emails;

					endforeach;

				endforeach;

				if ( empty( $_error ) ) :

					foreach( $_set AS $grouping => $options ) :

						$this->app_notification_model->set( $options, $grouping );

					endforeach;

					$this->data['success'] = '<strong>Success!</strong> Notifications were updated successfully.';

				else :

					$this->data['error'] = $_error;

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Conditionally set this as this method may be overridden by the app to add
		//	custom notification types

		if ( empty( $this->data['notifications'] ) ) :

			$this->data['notifications'] = array();

		endif;

		//	Generic Site notifications
		// $this->data['notifications']['app']					= new stdClass();
		// $this->data['notifications']['app']->label			= 'Site';
		// $this->data['notifications']['app']->description	= 'General site notifications.';
		// $this->data['notifications']['app']->options		= array();
		// $this->data['notifications']['app']->options['foo']	= 'Bar';

		if ( module_is_enabled( 'shop' ) ) :

			$this->data['notifications']['shop']							= new stdClass();
			$this->data['notifications']['shop']->label						= 'Shop';
			$this->data['notifications']['shop']->description				= 'Shop related notifications.';
			$this->data['notifications']['shop']->options					= array();
			$this->data['notifications']['shop']->options['notify_order']	= 'Order Notifications';

		endif;

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/notification/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_DASHBOARD' ) ) :

	class Notification extends NAILS_Notification
	{
	}

endif;


/* End of file notification.php */
/* Location: ./modules/admin/controllers/notification.php */