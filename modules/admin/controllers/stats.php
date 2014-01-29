<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin: Stats
* Description:	Stats manager
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

class NAILS_Stats extends NAILS_Admin_Controller
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
		$d->name				= 'Statistics';					//	Display name.

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs['index']		= 'Browse Events';					//	Sub-nav function.

		// --------------------------------------------------------------------------

		//	Only announce the controller if the user has permission to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->load->library( 'event' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Events browser
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function index()
	{
		//	Set method info
		$this->data['page']->title = 'Browse Events';

		// --------------------------------------------------------------------------

		//	Define limit and order
		$_limit		= array(
						$this->input->get( 'per_page' ) ? $this->input->get( 'per_page' ) : 50,
						$this->input->get( 'offset' ) ? $this->input->get( 'offset' ) : 0
					);
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

		if ( $this->input->get( 'date_from' ) ) :

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
			$this->output->set_header( 'Pragma: public' );
			$this->output->set_header( 'Expires: 0' );
			$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			$this->output->set_header( 'Cache-Control: private', FALSE );
			$this->output->set_header( 'Content-Type: application/octet-stream' );
			$this->output->set_header( 'Content-Disposition: attachment; filename=stats-export-' . date( 'Y-m-d_h-i-s' ) . '.csv;' );
			$this->output->set_header( 'Content-Transfer-Encoding: binary' );

			// --------------------------------------------------------------------------

			//	Render view
			$this->load->view( 'admin/stats/csv',	$this->data );

		else :

			//	Fetch events
			$this->data['events'] = new stdClass();
			$this->data['events']->data = $this->event->get_all( $_order, $_limit, $_where );

			// --------------------------------------------------------------------------

			//	Pagination
			$this->data['events']->pagination					= new stdClass();
			$this->data['events']->pagination->total_results	= $this->event->count_all( $_where );

			// --------------------------------------------------------------------------

			//	Fetch users
			$this->data['users'] = $this->user->get_all_minimal();
			$this->data['types'] = $this->event->get_types_flat();

			// --------------------------------------------------------------------------

			//	Load views
			$this->load->view( 'structure/header',		$this->data );
			$this->load->view( 'admin/stats/index',	$this->data );
			$this->load->view( 'structure/footer',		$this->data );

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_STATS' ) ) :

	class Stats extends NAILS_Stats
	{
	}

endif;

/* End of file stats.php */
/* Location: ./modules/admin/controllers/stats.php */