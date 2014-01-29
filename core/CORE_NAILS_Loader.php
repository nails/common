<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Modular Extensions HMVC
 *
 * @package		Nails
 * @subpackage	MX
 * @author		wiredesignz
 * @link		https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc
 */

/* load the MX Loader class */
require NAILS_PATH . 'MX/Loader.php';

class CORE_NAILS_Loader extends MX_Loader {


	public function model_is_loaded($model)
	{
		return array_search( $model, $this->_ci_models ) !== FALSE;
	}

	// --------------------------------------------------------------------------


	/**
	 *	Overloading this method so that if a view is supplied with the prefix of '/' then we
	 *	load that view directly rather than try and do anythign clever with the path
	 *
	 **/

	public function view($view, $vars = array(), $return = FALSE) {

		if ( strpos( $view, '/' ) === 0 ) :

			//	The supplied view is an absolute path, so use it.

			//	Add on .php if it's not there (so pathinfo() works as expected)
			if ( substr( $view, -4 ) != '.php' )
				$view .= '.php';

			//	Get path information about the view
			$_pathinfo = pathinfo( $view );
			$_path	= $_pathinfo['dirname'] . '/';
			$_view	=  $_pathinfo['filename'];

			//	Set the view path so the laoder knows where to look
			$this->_ci_view_paths = array($_path => TRUE) + $this->_ci_view_paths;

			//	Load the view
			return $this->_ci_load(array('_ci_view' => $_view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));

		else :

			//	Try looking in the application folder second - prevents Nails views being loaded
			//	over an application view.
			$_view = FCPATH . APPPATH . 'views/' . $view;

			if ( substr( $_view, -4 ) != '.php' )
				$_view .= '.php';

			if ( file_exists( $_view ) ) :

				//	Try again with this view
				return $this->view( $_view, $vars, $return );

			else :

				//	Fall back to the old method
				return parent::view( $view, $vars, $return );

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Load Helper
	 *
	 * This function loads the specified helper file.
	 * Overloading this method so that extended helpers in NAILS are correctly loaded.
	 * Slightly more complex as the helper() method for ModuleExtensions also needs to
	 * be fired (i.e it's functionality needs to exist in here too).
	 *
	 * @param	mixed
	 * @return	void
	 */
	public function helper($helpers = array())
	{
		//	Need to make the $helpers variable into an array immediately and loop through
		//	it so MX knows what to do. Also specify a to_load variable which will contain
		//	helpers which the fallback, CI, method will attempt to load.

		$_helpers	= $this->_ci_prep_filename($helpers, '_helper');
		$_to_load	= array();

		//	Modded MX Loader:
		foreach ( $_helpers AS $helper ) :

			if (isset($this->_ci_helpers[$helper]))	return;

			list($path, $_helper) = Modules::find($helper.'_helper', $this->_module, 'helpers/');

			if ($path === FALSE) :

				$_to_load[] = $helper;

			else:

				Modules::load_file($_helper, $path);
				$this->_ci_helpers[$_helper] = TRUE;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		foreach ($_to_load as $helper)
		{
			if (isset($this->_ci_helpers[$helper]))
			{
				continue;
			}

			$ext_helper					= APPPATH.'helpers/'.config_item('subclass_prefix').$helper.'.php';
			$_nails_ext_helper			= NAILS_PATH . 'helpers/'.config_item('subclass_prefix').$helper.'.php';
			$_nails_ext_module_helper	= ($this->router->current_module()) ? NAILS_PATH . 'modules/'.$this->router->current_module().'/helpers/'.config_item('subclass_prefix').$helper.'.php' : NULL;
			$_nails_module_helper		= ($this->router->current_module()) ? NAILS_PATH . 'modules/'.$this->router->current_module().'/helpers/'.$helper.'.php' : NULL;

			// Is this a helper extension request?
			if (file_exists($ext_helper))
			{
				$base_helper = BASEPATH.'helpers/'.$helper.'.php';

				if ( ! file_exists($base_helper))
				{
					show_error('Unable to load the requested file: helpers/'.$helper.'.php');
				}

				include_once($ext_helper);

				//	If a Nails version exists, load that too; allows the app to overload the nails version
				//	but also allows the app to extend the nails version without destorying existing functions
				if (file_exists($_nails_ext_helper)) :

					include_once($_nails_ext_helper);

				//	If there isn't an explicit Nails version, check the current module for one
				elseif($_nails_ext_module_helper&&file_exists($_nails_ext_module_helper)):

					include_once($_nails_ext_module_helper);


				endif;

				include_once($base_helper);

				$this->_ci_helpers[$helper] = TRUE;
				log_message('debug', 'Helper loaded: '.$helper);
				continue;
			}

			//	App version didn't exist, see if a Nails version does
			if (file_exists($_nails_ext_helper))
			{
				$base_helper = BASEPATH.'helpers/'.$helper.'.php';

				if ( ! file_exists($base_helper))
				{
					show_error('Unable to load the requested file: helpers/'.$helper.'.php');
				}

				include_once($_nails_ext_helper);
				include_once($base_helper);

				$this->_ci_helpers[$helper] = TRUE;
				log_message('debug', 'Helper loaded: '.$helper);
				continue;
			}

			//	See if the helper resides within the current Nails module
			if (file_exists($_nails_module_helper))
			{

				include_once($_nails_module_helper);

				$this->_ci_helpers[$helper] = TRUE;
				log_message('debug', 'Helper loaded: '.$helper);
				continue;
			}

			// Try to load the helper
			foreach ($this->_ci_helper_paths as $path)
			{
				if (file_exists($path.'helpers/'.$helper.'.php'))
				{
					include_once($path.'helpers/'.$helper.'.php');

					$this->_ci_helpers[$helper] = TRUE;
					log_message('debug', 'Helper loaded: '.$helper);
					break;
				}
			}

			// unable to load the helper
			if ( ! isset($this->_ci_helpers[$helper]))
			{
				show_error('Unable to load the requested file: helpers/'.$helper.'.php');
			}
		}
	}


	// --------------------------------------------------------------------------

	/**
	 * Load class
	 *
	 * This function loads the requested class. Overloading this method to include
	 * a lookup for a Nails version of the class if it exists.
	 *
	 * @param	string	the item that is being loaded
	 * @param	mixed	any additional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */
	protected function _ci_load_class($class, $params = NULL, $object_name = NULL)
	{
		// Get the class name, and while we're at it trim any slashes.
		// The directory path can be included as part of the class name,
		// but we don't want a leading slash
		$class = str_replace('.php', '', trim($class, '/'));

		// Was the path included with the class name?
		// We look for a slash to determine this
		$subdir = '';
		if (($last_slash = strrpos($class, '/')) !== FALSE)
		{
			// Extract the path
			$subdir = substr($class, 0, $last_slash + 1);

			// Get the filename from the path
			$class = substr($class, $last_slash + 1);
		}

		// We'll test for both lowercase and capitalized versions of the file name

		foreach (array(ucfirst($class), strtolower($class)) as $class)
		{
			$subclass	= APPPATH.'libraries/'.$subdir.config_item('subclass_prefix').$class.'.php';
			$nailsclass = NAILS_PATH.'libraries/'.$subdir.'CORE_'.config_item('subclass_prefix').$class.'.php';

			// Is this a class extension request?
			if (file_exists($subclass))
			{
				$baseclass = BASEPATH.'libraries/'.ucfirst($class).'.php';

				if ( ! file_exists($baseclass))
				{
					log_message('error', "Unable to load the requested class: ".$class);
					show_error("Unable to load the requested class: ".$class);
				}

				// Safety:  Was the class already loaded by a previous call?
				if (in_array($subclass, $this->_ci_loaded_files))
				{
					// Before we deem this to be a duplicate request, let's see
					// if a custom object name is being supplied.  If so, we'll
					// return a new instance of the object
					if ( NULL !== $object_name )
					{
						$CI =& get_instance();
						if ( ! isset($CI->$object_name))
						{
							return $this->_ci_init_class($class, config_item('subclass_prefix'), $params, $object_name);
						}
					}

					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}

				include_once($baseclass);

				//	If a Nails version exists, load that too; allows the app to overload the nails version
				//	but also allows the app to extend the nails version without destorying existing functions
				if (file_exists($nailsclass)) :
					include_once($nailsclass);
					$this->_ci_loaded_files[] = $nailsclass;
				endif;

				include_once($subclass);
				$this->_ci_loaded_files[] = $subclass;

				return $this->_ci_init_class($class, config_item('subclass_prefix'), $params, $object_name);
			}


			// Ok, so it wasn't a subclass request, but does the subclass exist within Nails?
			if (file_exists($nailsclass))
			{
				$baseclass = BASEPATH.'libraries/'.ucfirst($class).'.php';

				if ( ! file_exists($baseclass))
				{
					log_message('error', "Unable to load the requested class: ".$class);
					show_error("Unable to load the requested class: ".$class);
				}

				// Safety:  Was the class already loaded by a previous call?
				if (in_array($nailsclass, $this->_ci_loaded_files))
				{
					// Before we deem this to be a duplicate request, let's see
					// if a custom object name is being supplied.  If so, we'll
					// return a new instance of the object
					if ( NULL !== $object_name )
					{
						$CI =& get_instance();
						if ( ! isset($CI->$object_name))
						{
							return $this->_ci_init_class($class, config_item('subclass_prefix'), $params, $object_name);
						}
					}

					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}

				include_once($baseclass);
				include_once($nailsclass);

				$this->_ci_loaded_files[] = $nailsclass;

				return $this->_ci_init_class($class, 'CORE_'.config_item('subclass_prefix'), $params, $object_name);
			}


			// Lets search for the requested library file and load it.
			$is_duplicate = FALSE;
			foreach ($this->_ci_library_paths as $path)
			{
				$filepath = $path.'libraries/'.$subdir.$class.'.php';

				// Does the file exist?  No?  Bummer...
				if ( ! file_exists($filepath))
				{
					continue;
				}

				// Safety:  Was the class already loaded by a previous call?
				if (in_array($filepath, $this->_ci_loaded_files))
				{
					// Before we deem this to be a duplicate request, let's see
					// if a custom object name is being supplied.  If so, we'll
					// return a new instance of the object
					if ( NULL !== $object_name )
					{
						$CI =& get_instance();
						if ( ! isset($CI->$object_name))
						{
							return $this->_ci_init_class($class, '', $params, $object_name);
						}
					}

					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}

				include_once($filepath);
				$this->_ci_loaded_files[] = $filepath;
				return $this->_ci_init_class($class, '', $params, $object_name);
			}

		} // END FOREACH

		// One last attempt.  Maybe the library is in a subdirectory, but it wasn't specified?
		if ($subdir == '')
		{
			$path = strtolower($class).'/'.$class;
			return $this->_ci_load_class($path, $params);
		}

		// If we got this far we were unable to find the requested class.
		// We do not issue errors if the load call failed due to a duplicate request
		if ($is_duplicate == FALSE)
		{
			log_message('error', "Unable to load the requested class: ".$class);
			show_error("Unable to load the requested class: ".$class);
		}
	}
}

/* End of file CORE_NAILS_Loader.php */
/* Location: NAILS/core/CORE_NAILS_Loader.php */