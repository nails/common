<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin
*
* Docs:			-
*
* Created:		14/10/2010
* Modified:		20/06/2011
*
* Description:	-
* 
*/

//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

/**
 * OVERLOADING NAILS'S ADMIN MODULES
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/
 
class NAILS_Dashboard extends Admin_Controller {

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
		
		//	Configurations
		$d->name				= 'Dashboard';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['index']		= 'Dashboard';					//	Sub-nav function.
		$d->funcs['help']		= 'Help';						//	Sub-nav function.
		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Administration homepage / dashboard
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		$this->data['page']->admin_m	= 'index';
		$this->data['page']->title		= 'Welcome';
		
		// --------------------------------------------------------------------------
		
		//	Load header
		$this->nails->load_view( 'admin/structure/header',	'modules/admin/views/structure/header',	$this->data );
		
		// --------------------------------------------------------------------------
		
		//	If no modules have been discovered it means that while this user is an admin
		//	no modules have either a) been enabled for the site or b) for the user. Either
		//	way we should show a friendly error.
		
		if ( ! $this->_loaded_modules ) :
		
			$this->nails->load_view( 'admin/dashboard/no_modules',	'modules/admin/views/dashboard/no_modules',	$this->data );
			
		else :
		
			$this->nails->load_view( 'admin/dashboard/dashboard',	'modules/admin/views/dashboard/dashboard',	$this->data );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Footer
		$this->nails->load_view( 'admin/structure/footer',	'modules/admin/views/structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Administration help
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function help()
	{
		//	Set method info
		$this->data['page']->admin_m	= 'help';
		$this->data['page']->title		= 'Help';
		
		// --------------------------------------------------------------------------
		
		//	Load model
		$this->load->model( 'admin_help_model' );
		
		// --------------------------------------------------------------------------
		
		//	Get data
		$this->data['videos'] = $this->admin_help_model->get_all();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->nails->load_view( 'admin/structure/header',	'modules/admin/views/structure/header',	$this->data );
		$this->nails->load_view( 'admin/dashboard/help/overview',	'modules/admin/views/dashboard/help/overview',	$this->data );
		$this->nails->load_view( 'admin/structure/footer',	'modules/admin/views/structure/footer',	$this->data );
	}

}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S ADMIN MODULES
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

	class Dashboard extends NAILS_Dashboard
	{
	}

endif;


/* End of file admin.php */
/* Location: ./application/modules/admin/controllers/admin.php */