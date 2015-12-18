<?php

/**
 * Provides additional Loading functionality as well as bringing support for Nails.
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 * @link
 */

/* load the MX Loader class */
require NAILS_COMMON_PATH . 'MX/Loader.php';

use Nails\Common\Exception\NailsException;

class CORE_NAILS_Loader extends MX_Loader
{
    /**
     * Determines whether a model is loaded or not
     * @param  string $model The model to check
     * @return boolean
     */
    public function isModelLoaded($model)
    {
        return array_search($model, $this->_ci_models) !== false;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a view. If an absolute path is provided then that view will be used,
     * otherwise the system will search the modules
     * @param  string  $view   The view to load
     * @param  array   $vars   An array of data to pass to the view
     * @param  boolean $return Whether or not to return then view, or output it
     * @return mixed
     */
    public function view($view, $vars = array(), $return = false)
    {

        if (strpos($view, '/') === 0) {

            //  The supplied view is an absolute path, so use it.

            //  Add on EXT if it's not there (so pathinfo() works as expected)
            if (substr($view, strlen(EXT)*-1) != EXT) {

                $view .= EXT;
            }

            //  Get path information about the view
            $pathInfo = pathinfo($view);
            $path     = $pathInfo['dirname'] . '/';
            $view     = $pathInfo['filename'];

            //  Set the view path so the loader knows where to look
            $this->_ci_view_paths = array($path => true) + $this->_ci_view_paths;

            //  Load the view
            return $this->_ci_load(array(
                '_ci_view'   => $view,
                '_ci_vars'   => $this->_ci_object_to_array($vars),
                '_ci_return' => $return
            ));

        } else {

            /**
             * Try looking in the application folder second - prevents Nails views
             * being loaded over an application view.
             */

            $absoluteView = FCPATH . APPPATH . 'views/' . $view;

            if (substr($absoluteView, strlen(EXT)*-1) != EXT) {

                $absoluteView .= EXT;
            }

            if (file_exists($absoluteView)) {

                //  Try again with this view
                return $this->view($absoluteView, $vars, $return);

            } else {

                //  Fall back to the old method
                return parent::view($view, $vars, $return);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether or not a view exists
     * @param  string $view The view to look for
     * @return boolean
     */
    public function viewExists($view)
    {
        list($path, $view) = Modules::find($view, $this->_module, 'views/');
        return (bool) trim($path);
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a helper. Overloading this method so that extended helpers in NAILS are
     * correctly loaded. Slightly more complex as the helper() method for
     * ModuleExtensions also needs to be fired (i.e it's functionality needs to exist
     * in here too).
     * @param  array  $helpers The helpers to load
     * @return void
     */
    public function helper($helpers = array())
    {
        /**
         * Need to make the $helpers variable into an array immediately and loop through
         * it so MX knows what to do. Also specify a to_load variable which will contain
         * helpers which the fallback, CI, method will attempt to load.
         */

        $helpers = $this->_ci_prep_filename($helpers, '_helper');
        $toLoad  = array();

        //  Modded MX Loader:
        foreach ($helpers as $helper) {

            if (isset($this->_ci_helpers[$helper])) {

                return;
            }

            list($path, $_helper) = Modules::find($helper . '_helper', $this->_module, 'helpers/');

            if ($path === false) {

                $toLoad[] = $helper;

            } else {

                Modules::load_file($_helper, $path);
                $this->_ci_helpers[$_helper] = true;
            }
        }

        // --------------------------------------------------------------------------

        //  CI Loader
        foreach ($toLoad as $helper) {
            if (isset($this->_ci_helpers[$helper])) {
                continue;
            }

            $classPrefix          = config_item('subclass_prefix');
            $appExtHelper         = APPPATH . 'helpers/' . $classPrefix . $helper . EXT;
            $nailsExtHelper       = NAILS_COMMON_PATH . 'helpers/' . $classPrefix . $helper . EXT;
            $sHelperPath          = NAILS_COMMON_PATH . 'modules/' . $this->router->current_module() . '/helpers/';
            $nailsExtModuleHelper = $this->router->current_module() ? $sHelperPath . $classPrefix . $helper . EXT : null;
            $nailsModuleHelper    = $this->router->current_module() ? $sHelperPath . $helper . EXT : null;

            // Is this a helper extension request?
            if (file_exists($appExtHelper)) {

                $baseHelper = BASEPATH . 'helpers/' . $helper . EXT;

                if (!file_exists($baseHelper)) {
                    throw new NailsException(
                        'Unable to load the requested file: helpers/' . $helper . EXT,
                        1
                    );
                }

                include_once($appExtHelper);

                /**
                 * If a Nails version exists, load that too; allows the app to overload
                 * the nails version but also allows the app to extend the nails version
                 * without destorying existing functions
                 */

                if (file_exists($nailsExtHelper)) {

                    include_once($nailsExtHelper);

                //  If there isn't an explicit Nails version, check the current module for one
                } elseif ($nailsExtModuleHelper && file_exists($nailsExtModuleHelper)) {

                    include_once($nailsExtModuleHelper);
                }

                include_once($baseHelper);

                $this->_ci_helpers[$helper] = true;
                log_message('debug', 'Helper loaded: ' . $helper);
                continue;
            }

            //  App version didn't exist, see if a Nails version does
            if (file_exists($nailsExtHelper)) {

                $baseHelper = BASEPATH . 'helpers/' . $helper . EXT;

                if (!file_exists($baseHelper)) {
                    throw new NailsException(
                        'Unable to load the requested file: helpers/' . $helper . EXT,
                        1
                    );
                }

                include_once($nailsExtHelper);
                include_once($baseHelper);

                $this->_ci_helpers[$helper] = true;
                log_message('debug', 'Helper loaded: ' . $helper);
                continue;
            }

            //  See if the helper resides within the current Nails module
            if (file_exists($nailsModuleHelper)) {

                include_once($nailsModuleHelper);

                $this->_ci_helpers[$helper] = true;
                log_message('debug', 'Helper loaded: ' . $helper);
                continue;
            }

            // Try to load the helper
            foreach ($this->_ci_helper_paths as $path) {

                if (file_exists($path . 'helpers/' . $helper . EXT)) {

                    include_once($path . 'helpers/' . $helper . EXT);

                    $this->_ci_helpers[$helper] = true;
                    log_message('debug', 'Helper loaded: ' . $helper);
                    break;
                }
            }

            // Unable to load the helper
            if (!isset($this->_ci_helpers[$helper])) {
                throw new NailsException(
                    'Unable to load the requested file: helpers/' . $helper . EXT,
                    1
                );
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * This function loads the requested class. Overloading this method to include
     * a lookup for a Nails version of the class if it exists.
     *
     * @param   string  the item that is being loaded
     * @param   mixed   any additional parameters
     * @param   string  an optional object name
     * @return  void
     */
    protected function _ci_load_class($class, $params = null, $objectName = null)
    {
        /**
         * Get the class name, and while we're at it trim any slashes. The
         * directory path can be included as part of the class name, but we
         * don't want a leading slash
         */

        $class = str_replace(EXT, '', trim($class, '/'));

        /**
         * Was the path included with the class name? We look for a slash to
         * determine this
         */

        $subdir = '';
        if (($last_slash = strrpos($class, '/')) !== false) {

            // Extract the path
            $subdir = substr($class, 0, $last_slash + 1);

            // Get the filename from the path
            $class = substr($class, $last_slash + 1);
        }

        $classPrefix = config_item('subclass_prefix');

        // We'll test for both lowercase and capitalized versions of the file name
        foreach (array(ucfirst($class), strtolower($class)) as $class) {

            $subClass   = APPPATH . 'libraries/' . $subdir . $classPrefix . $class . EXT;
            $nailsClass = NAILS_COMMON_PATH . 'libraries/' . $subdir . 'CORE_' . $classPrefix . $class . EXT;

            // Is this a class extension request?
            if (file_exists($subClass)) {

                $baseClass = BASEPATH . 'libraries/' . ucfirst($class) . EXT;

                if (!file_exists($baseClass)) {

                    log_message('error', 'Unable to load the requested class: ' . $class);
                    throw new NailsException(
                        'Unable to load the requested class: ' . $class,
                        1
                    );
                }

                // Safety:  Was the class already loaded by a previous call?
                if (in_array($subClass, $this->_ci_loaded_files)) {

                    /**
                     * Before we deem this to be a duplicate request, let's see if
                     * a custom object name is being supplied.  If so, we'll return
                     * a new instance of the object
                     */

                    if (!is_null($objectName)) {

                        $CI =& get_instance();
                        if (!isset($CI->$objectName)) {

                            return $this->_ci_init_class($class, $classPrefix, $params, $objectName);
                        }
                    }

                    $isDuplicate = true;
                    log_message('debug', $class . ' class already loaded. Second attempt ignored.');
                    return;
                }

                include_once($baseClass);

                /**
                 * If a Nails version exists, load that too; allows the app to overload the Nails
                 * version but also allows the app to extend the nails version without destorying
                 * existing functions
                 */

                if (file_exists($nailsClass)) {

                    include_once($nailsClass);
                    $this->_ci_loaded_files[] = $nailsClass;
                }

                include_once($subClass);
                $this->_ci_loaded_files[] = $subClass;

                return $this->_ci_init_class($class, $classPrefix, $params, $objectName);
            }

            // Ok, so it wasn't a subClass request, but does the subClass exist within Nails?
            if (file_exists($nailsClass)) {

                $baseClass = BASEPATH . 'libraries/' . ucfirst($class) . EXT;

                if (!file_exists($baseClass)) {

                    log_message('error', 'Unable to load the requested class: ' . $class);
                    throw new NailsException(
                        'Unable to load the requested class: ' . $class,
                        1
                    );
                }

                // Safety:  Was the class already loaded by a previous call?
                if (in_array($nailsClass, $this->_ci_loaded_files)) {

                    /**
                     * Before we deem this to be a duplicate request, let's see if
                     * a custom object name is being supplied.  If so, we'll return
                     * a new instance of the object
                     */

                    if (!is_null($objectName)) {

                        $CI =& get_instance();
                        if (!isset($CI->$objectName)) {

                            return $this->_ci_init_class($class, $classPrefix, $params, $objectName);
                        }
                    }

                    $isDuplicate = true;
                    log_message('debug', $class." class already loaded. Second attempt ignored.");
                    return;
                }

                include_once($baseClass);
                include_once($nailsClass);

                $this->_ci_loaded_files[] = $nailsClass;

                return $this->_ci_init_class($class, 'CORE_' . $classPrefix, $params, $objectName);
            }

            // Lets search for the requested library file and load it.
            $isDuplicate = false;
            foreach ($this->_ci_library_paths as $path) {

                $filepath = $path . 'libraries/' . $subdir . $class . EXT;

                // Does the file exist?  No?  Bummer...
                if (!file_exists($filepath)) {

                    continue;
                }

                // Safety:  Was the class already loaded by a previous call?
                if (in_array($filepath, $this->_ci_loaded_files)) {

                    /**
                     * Before we deem this to be a duplicate request, let's see if
                     * a custom object name is being supplied.  If so, we'll return
                     * a new instance of the object
                     */

                    if (!is_null($objectName)) {

                        $CI =& get_instance();
                        if (!isset($CI->$objectName)) {

                            return $this->_ci_init_class($class, '', $params, $objectName);
                        }
                    }

                    $isDuplicate = true;
                    log_message('debug', $class . ' class already loaded. Second attempt ignored.');
                    return;
                }

                include_once($filepath);
                $this->_ci_loaded_files[] = $filepath;
                return $this->_ci_init_class($class, '', $params, $objectName);
            }
        }

        // One last attempt.  Maybe the library is in a subdirectory, but it wasn't specified?
        if ($subdir == '') {

            $path = strtolower($class) . '/' . $class;
            return $this->_ci_load_class($path, $params);
        }

        /**
         * If we got this far we were unable to find the requested class. We do not issue
         * errors if the load call failed due to a duplicate request
         */

        if ($isDuplicate == false) {
            log_message('error', 'Unable to load the requested class: ' . $class);
            throw new NailsException(
                'Unable to load the requested class: ' . $class,
                1
            );
        }
    }
}
