<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin: CDN
* Description:	CDN manager
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

class NAILS_Cdnadmin extends NAILS_Admin_Controller
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
		$d->name = 'CDN';

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs['browse']	= 'Browse Objects';
		$d->funcs['trash']	= 'Browse Trash';

		// --------------------------------------------------------------------------

		//	Only announce the controller if the user has permission to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


	/**
	 * Browse CDN buckets and objects
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function browse()
	{
		$this->data['page']->title = 'Browse Objects';

		// --------------------------------------------------------------------------

		//	Define the $_data variable, this'll be passed to the get_all() and count_all() methods
		$_data = array( 'where' => array(), 'sort' => array() );

		// --------------------------------------------------------------------------

		//	Set useful vars
		$_page			= $this->input->get( 'page' )		? $this->input->get( 'page' )		: 0;
		$_per_page		= $this->input->get( 'per_page' )	? $this->input->get( 'per_page' )	: 25;
		$_sort_on		= $this->input->get( 'sort_on' )	? $this->input->get( 'sort_on' )	: 'o.id';
		$_sort_order	= $this->input->get( 'order' )		? $this->input->get( 'order' )		: 'desc';
		$_search		= $this->input->get( 'search' )		? $this->input->get( 'search' )		: '';

		//	Set sort variables for view and for $_data
		$this->data['sort_on']		= $_data['sort']['column']	= $_sort_on;
		$this->data['sort_order']	= $_data['sort']['order']	= $_sort_order;
		$this->data['search']		= $_data['search']			= $_search;

		//	Define and populate the pagination object
		$this->data['pagination']				= new stdClass();
		$this->data['pagination']->page			= $_page;
		$this->data['pagination']->per_page		= $_per_page;
		$this->data['pagination']->total_rows	= $this->cdn->count_all_objects( $_data );

		$this->data['objects'] = $this->cdn->get_objects( $_page, $_per_page, $_data );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/cdn/browse',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Browse CDN buckets and objects
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function trash()
	{
		$this->data['page']->title = 'Browse Trash';

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/cdn/trash',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Browse CDN buckets and objects
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function create()
	{
		$this->data['page']->title = 'Upload Items';

		// --------------------------------------------------------------------------

		if ( $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/header/blank';

		endif;

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/cdn/create',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Browse CDN buckets and objects
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function edit()
	{
		$this->data['page']->title = 'Edit Object';

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'admin/cdn/edit',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Browse CDN buckets and objects
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function delete()
	{
		$_return = $this->input->get( 'return' ) ? $this->input->get( 'return' ) : 'admin/cdnadmin/browse';
		$this->session->set_flashdata( 'message', '<strong>TODO:</strong> Delete objects from admin' );
		redirect( $_return );
	}


	// --------------------------------------------------------------------------


	/**
	 * Browse CDN buckets and objects
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function purge()
	{
		$_return = $this->input->get( 'return' ) ? $this->input->get( 'return' ) : 'admin/cdnadmin/trash';
		$this->session->set_flashdata( 'message', '<strong>TODO:</strong> empty trash' );
		redirect( $_return );
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CDN' ) ) :

	class Cdnadmin extends NAILS_Cdnadmin
	{
	}

endif;

/* End of file cdnadmin.php */
/* Location: ./modules/admin/controllers/cdn.php */