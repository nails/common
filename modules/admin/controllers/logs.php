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
		$d->funcs['site']			= 'Browse Site Logs';	//	Sub-nav function.
		$d->funcs['event']			= 'Browse Event Logs';	//	Sub-nav function.
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
	public function site()
	{
		$this->data['page']->title = 'Browse Logs';

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/logs/site/index',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Event Browser
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function event()
	{
		//	Set method info
		$this->data['page']->title = 'Browse Events';

		// --------------------------------------------------------------------------

		//	Load event library
		$this->load->library( 'event' );

		// --------------------------------------------------------------------------

		//	Define limit and order
		//	A little messy but it's because the Event library doesn't follow the
		//	same standard as the models - it should. TODO.

		$_per_page	= $this->input->get( 'per_page' ) ? $this->input->get( 'per_page' ) : 50;
		$_page		= (int) $this->input->get( 'page' );
		$_page--;
		$_page		= $_page < 0 ? 0 : $_page;

		$_offset	= $_page * $_per_page;

		$_limit		= array( $_per_page, $_offset );

		$_order		= array(
						$this->input->get( 'sort' ) ? $this->input->get( 'sort' ) : 'e.created',
						$this->input->get( 'order' ) ? $this->input->get( 'order' ) : 'DESC'
					);

		// --------------------------------------------------------------------------

		//	Define the data user & type restriction and the date range
		$_where = array();

		if ( $this->input->get( 'date_from' ) ) :

			$_where[] = '(e.created >= \'' . $this->input->get( 'date_from' ) . '\')';

		endif;

		if ( $this->input->get( 'date_to' ) ) :

			$_where[] = '(e.created <=\'' . $this->input->get( 'date_to' ) . '\')';

		endif;

		if ( $this->input->get( 'user_id' ) ) :

			$_where[] = 'e.created_by IN (' . implode( ',', $this->input->get( 'user_id' ) ) . ')';

		endif;

		if ( $this->input->get( 'event_type' ) ) :

			$_where[] = 'e.type_id IN (' . implode( ',', $this->input->get( 'event_type' ) ) . ')';

		endif;

		$_where = implode( ' AND ', $_where );

		// --------------------------------------------------------------------------

		//	Are we downloading? Or viewing?
		if ( $this->input->get( 'dl' ) ) :

			//	Fetch events
			$this->data['events'] = new stdClass();
			$this->data['events'] = $this->event->get_all( $_order, NULL, $_where );

			// --------------------------------------------------------------------------

			//	Send header
			$this->output->set_content_type( 'application/octet-stream' );
			$this->output->set_header( 'Pragma: public' );
			$this->output->set_header( 'Expires: 0' );
			$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			$this->output->set_header( 'Cache-Control: private', FALSE );
			$this->output->set_header( 'Content-Disposition: attachment; filename=stats-export-' . date( 'Y-m-d_h-i-s' ) . '.csv;' );
			$this->output->set_header( 'Content-Transfer-Encoding: binary' );

			// --------------------------------------------------------------------------

			//	Render view
			$this->load->view( 'admin/logs/event/csv',	$this->data );

		else :

			//	Viewing, make sure we paginate
			//	=======================================
			$this->data['pagination']				= new stdClass();
			$this->data['pagination']->page			= $this->input->get( 'page' )		? $this->input->get( 'page' )		: 0;
			$this->data['pagination']->per_page		= $this->input->get( 'per_page' )	? $this->input->get( 'per_page' )	: 5;
			$this->data['pagination']->total_rows	= $this->event->count_all( $_where );

			//	Fetch all the items for this page
			$this->data['events'] = $this->event->get_all( $_order, $_limit, $_where );

			// --------------------------------------------------------------------------

			//	Fetch users
			$this->data['users'] = $this->user_model->get_all_minimal();
			$this->data['types'] = $this->event->get_types_flat();

			// --------------------------------------------------------------------------

			//	Load views
			$this->load->view( 'structure/header',			$this->data );
			$this->load->view( 'admin/logs/event/index',	$this->data );
			$this->load->view( 'structure/footer',			$this->data );

		endif;
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
		//	Set method info
		$this->data['page']->title = 'Browse Admin Changelog';

		// --------------------------------------------------------------------------

		//	Define the $_data variable, this'll be passed to the get_all() and count_all() methods
		$_data = array( 'where' => array() );

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'date_from' ) ) :

			$_data['where'][] = array(
				'column'	=> 'acl.created >=',
				'value'		=> $this->input->get( 'date_from' )
			);

		endif;

		if ( $this->input->get( 'date_to' ) ) :

			$_data['where'][] = array(
				'column'	=> 'acl.created <=',
				'value'		=> $this->input->get( 'date_to' )
			);

		endif;

		// --------------------------------------------------------------------------

		//	Are we downloading? Or viewing?
		if ( $this->input->get( 'dl' ) ) :

			//	Downloading, fetch the complete dataset
			//	=======================================

			//	Fetch events
			$this->data['items'] = new stdClass();
			$this->data['items'] = $this->admin_changelog_model->get_all( NULL, NULL, $_data );

			// --------------------------------------------------------------------------

			//	Send header
			$this->output->set_content_type( 'application/octet-stream' );
			$this->output->set_header( 'Pragma: public' );
			$this->output->set_header( 'Expires: 0' );
			$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			$this->output->set_header( 'Cache-Control: private', FALSE );
			$this->output->set_header( 'Content-Disposition: attachment; filename=admin-changelog-export-' . date( 'Y-m-d_h-i-s' ) . '.csv;' );
			$this->output->set_header( 'Content-Transfer-Encoding: binary' );

			// --------------------------------------------------------------------------

			//	Render view
			$this->load->view( 'admin/logs/changelog/csv',	$this->data );

		else :

			//	Viewing, make sure we paginate
			//	=======================================

			//	Define and populate the pagination object
			$_page		= $this->input->get( 'page' )		? $this->input->get( 'page' )		: 0;
			$_per_page	= $this->input->get( 'per_page' )	? $this->input->get( 'per_page' )	: 50;

			$this->data['pagination']				= new stdClass();
			$this->data['pagination']->page			= $_page;
			$this->data['pagination']->per_page		= $_per_page;
			$this->data['pagination']->total_rows	= $this->admin_changelog_model->count_all( $_data );

			//	Fetch all the items for this page
			$this->data['items'] = $this->admin_changelog_model->get_all( $_page, $_per_page, $_data );

			// --------------------------------------------------------------------------

			//	Fetch users
			$this->data['users'] = $this->user_model->get_all_minimal();

			// --------------------------------------------------------------------------

			//	Load views
			$this->load->view( 'structure/header',				$this->data );
			$this->load->view( 'admin/logs/changelog/index',	$this->data );
			$this->load->view( 'structure/footer',				$this->data );

		endif;
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