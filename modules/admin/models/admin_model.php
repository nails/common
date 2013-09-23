<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin Model
 *
 * Description:	This model contains some basic common admin functionality.
 *
 */

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Admin_Model extends NAILS_Model
{
	protected $search_paths;


	// --------------------------------------------------------------------------


	/**
	 * Constructor; set the defaults
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

		/**
		 * Set the search paths to look for modules within; paths listed first
		 * take priority over those listed after it.
		 *
		 **/

		$this->search_paths[] = FCPATH . APPPATH . 'modules/admin/controllers/';	//	Admin controllers specific for this app only.
		$this->search_paths[] = NAILS_PATH . 'modules/admin/controllers/';
	}


	// --------------------------------------------------------------------------


	/**
	 * Look for modules which reside within the search paths; execute the announcer
	 * if it's there and return it's details (no response means the user doesn't have
	 * permission to execute this module).
	 *
	 * @access	public
	 * @param	string	$module	The name of the module to search for
	 * @return	stdClass
	 * @author	Pablo
	 **/
	public function find_module( $module )
	{
		$_out = new stdClass();

		// --------------------------------------------------------------------------

		//	Look in our search paths for a controller of the same name as the module.
		foreach ( $this->search_paths AS $path ) :

			if ( file_exists( $path . $module . '.php' ) ) :

				require_once $path . $module . '.php';

				$_details = $module::announce();

				if ( $_details ) :

					$_out				= $_details;
					$_out->class_name	= $module;

					//	List the public methods of this module (can't rely on the ['funcs'] array as it
					//	might not list a method which the active user needs in their ACL)

					$_methods = get_class_methods( $module );

					//	Strip out anything which is not public or which starts with a _ (pseudo private)
					$_remove_keys = array();
					foreach ( $_methods AS $key => $method ) :

						if ( substr( $method, 0, 1 ) == '_' ) :

							$_remove_keys[] = $key;
							continue;

						endif;

						// --------------------------------------------------------------------------

						$_method = new ReflectionMethod( $module, $method );

						if ( $_method->isStatic() ) :

							$_remove_keys[] = $key;
							continue;

						endif;

					endforeach;

					foreach ( $_remove_keys AS $key ) :

						unset( $_methods[$key] );

					endforeach;

					// --------------------------------------------------------------------------

					//	Build the methods array so that the method names are the keys
					$_out->methods = array();
					foreach ( $_methods AS $method ) :

						if ( isset( $_out->funcs[$method] ) ) :

							$_out->methods[$method] =  $_out->funcs[$method];

						else :

							$_out->methods[$method] =  '<em style="font-style:italic">' . ucwords( str_replace( '_', ' ', $method ) ) . '</em> <span style="color:#999;">- ' . lang( 'admin_nav_unlisted' ) . '</span>';

						endif;

					endforeach;

					// --------------------------------------------------------------------------

					//	Any extra permissions?
					$_out->extra_permissions = $module::permissions();

				endif;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core Nails
 * models. Some might argue it's a little hacky but it's a simple 'fix'
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_ADMIN_MODEL' ) ) :

	class Admin_model extends NAILS_Admin_model
	{
	}

endif;

/* End of file admin_model.php */
/* Location: ./modules/admin/models/admin_model.php */