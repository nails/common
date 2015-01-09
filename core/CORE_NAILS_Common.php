<?php

/**
 * The following functions are used internally by Nails
 */

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_POTENTIAL_MODULES')) {

    /**
     * Fetch all the potentially available modules for this app
     * @return array
     */
    function _NAILS_GET_POTENTIAL_MODULES()
    {
        /**
         * If we already know which modules are available then return that, save
         * the [small] overhead of working out the modules again and again.
         */

        if (isset($GLOBALS['NAILS_POTENTIAL_MODULES'])) {

            return $GLOBALS['NAILS_POTENTIAL_MODULES'];
        }

        // --------------------------------------------------------------------------

        $composer = @file_get_contents(NAILS_PATH . 'nails/composer.json');

        if (empty($composer)) {

            $message = 'Failed to discover potential modules; could not load composer.json';

            if (function_exists('_NAILS_ERROR')) {

                _NAILS_ERROR($message);

            } else {

                echo 'ERROR: ' . $message;
                exit(0);
            }
        }

        $composer = json_decode($composer);

        if (empty($composer->extra->nails->modules)) {

            $message = 'Failed to discover potential modules; could not decode composer.json';

            if (function_exists('_NAILS_ERROR')) {

                _NAILS_ERROR($message);

            } else {

                echo 'ERROR: ' . $message;
                exit(0);
            }
        }

        $out = array();

        foreach ($composer->extra->nails->modules as $vendor => $modules) {

            foreach ($modules as $module) {

                $out[] = $vendor . '/' . $module;
            }
        }

        //  Save as a $GLOBAL for next time
        $GLOBALS['NAILS_POTENTIAL_MODULES'] = $out;

        // --------------------------------------------------------------------------

        return $out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_AVAILABLE_MODULES')) {

    /**
     * Fetch the available modules for this app
     * @return array
     */
    function _NAILS_GET_AVAILABLE_MODULES()
    {
        /**
         * If we already know which modules are available then return that, save
         * the [small] overhead of working out the modules again and again.
         */

        if (isset($GLOBALS['NAILS_AVAILABLE_MODULES'])) {

            return $GLOBALS['NAILS_AVAILABLE_MODULES'];
        }

        // --------------------------------------------------------------------------

        $potential = _NAILS_GET_POTENTIAL_MODULES();
        $out       = array();

        foreach ($potential as $module) {

            if (is_dir('vendor/' . $module)) {

                $out[] = $module;
            }
        }

        //  Save as a $GLOBAL for next time
        $GLOBALS['NAILS_AVAILABLE_MODULES'] = $out;

        // --------------------------------------------------------------------------

        return $out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_UNAVAILABLE_MODULES')) {

    /**
     * Fetch the unavailable modules for this app
     * @return array
     */
    function _NAILS_GET_UNAVAILABLE_MODULES()
    {
        /**
         * If we already know which modules are available then return that, save
         * the [small] overhead of working out the modules again and again.
         */

        if (isset($GLOBALS['NAILS_UNAVAILABLE_MODULES'])) {

            return $GLOBALS['NAILS_UNAVAILABLE_MODULES'];
        }

        // --------------------------------------------------------------------------

        $potential = _NAILS_GET_POTENTIAL_MODULES();
        $out       = array();

        foreach ($potential as $module) {

            if (!is_dir('vendor/' . $module)) {

                $out[] = $module;
            }
        }

        //  Save as a $GLOBAL for next time
        $GLOBALS['NAILS_UNAVAILABLE_MODULES'] = $out;

        // --------------------------------------------------------------------------

        return $out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('module_is_enabled')) {

    /**
     * Handy way of determining whether a module is enabled or not in the app's config
     * @param  string $module the name of the module to check
     * @return boolean
     */
    function module_is_enabled($module)
    {
        $potential = _NAILS_GET_AVAILABLE_MODULES();

        if (array_search('nailsapp/module-' . $module, $potential) !== false) {

            return true;
        }

        return false;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('get_controller_data')) {

    /**
     * $NAILS_CONTROLLER_DATA is an array populated by $this->data in controllers,
     * this function provides an easy interface to this array when it's not in scope.
     * @return  array   A reference to $NAILS_CONTROLLER_DATA
     **/
    function &get_controller_data()
    {
        global $NAILS_CONTROLLER_DATA;
        return $NAILS_CONTROLLER_DATA;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('set_controller_data')) {

    /**
     * $NAILS_CONTROLLER_DATA is an array populated by $this->data in controllers,
     * this function provides an easy interface to populate this array when it's not
     * in scope.
     * @param string $key The key to populate
     * @param mixed $value The value to assign
     * @return  void
     **/
    function set_controller_data($key, $value)
    {
        global $NAILS_CONTROLLER_DATA;
        $NAILS_CONTROLLER_DATA[$key] = $value;
    }
}


// --------------------------------------------------------------------------


/**
 * PHP Version Check
 * =================
 *
 * We need to loop through all available modules and have a look at what version
 * of PHP they require, we'll then take the highest version and set that as our
 * minimum supported value.
 *
 * To set a requirement, within the module's nails object in composer.json,
 * specify the minPhpVersion value. You should also specify the appropriate
 * constraint for composer in the "require" section of composer.json.
 *
 * e.g:
 *
 *  "extra":
 *  {
 *      "nails" :
 *      {
 *          "minPhpVersion": "5.4.0"
 *      }
 *  }
 */

if (!function_exists('_NAILS_MIN_PHP_VERSION')) {

    /**
     * Determines the minimum supported PHP version as per enabled modules
     * @return string
     */
    function _NAILS_MIN_PHP_VERSION()
    {
        $modules    = array('nailsapp/common') + _NAILS_GET_AVAILABLE_MODULES();
        $minVersion = 0;

        foreach ($modules as $m) {

            $composer = @file_get_contents('vendor/' . $m . '/composer.json');

            if (!empty($composer)) {

                $composer = json_decode($composer);

                if (!empty($composer->extra->nails->minPhpVersion)) {

                    if (version_compare($composer->extra->nails->minPhpVersion, $minVersion, '>')) {

                        $minVersion = $composer->extra->nails->minPhpVersion;
                    }
                }
            }
        }

        return $minVersion;
    }
}

define('NAILS_MIN_PHP_VERSION', _NAILS_MIN_PHP_VERSION());

if (version_compare(PHP_VERSION, NAILS_MIN_PHP_VERSION, '<')) {

    $subject  = 'PHP Version ' . PHP_VERSION . ' is not supported by Nails';
    $message  = 'The version of PHP you are running is not supported. Nails requires at least ';
    $message .= 'PHP version ' . NAILS_MIN_PHP_VERSION;

    if (function_exists('_NAILS_ERROR')) {

        _NAILS_ERROR($message, $subject);

    } else {

        echo '<h1>ERROR: ' . $subject . '</h1>';
        echo '<h2>' . $message . '</h2>';
        exit(0);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('get_domain_from_url')) {

    /**
     * Attempts to get the top level part of a URL (i.e example.tld from sub.domains.example.tld).
     * Hat tip: http://uk1.php.net/parse_url#104874
     * BUG: 2 character TLD's break this
     * @TODO: Try and fix this bug
     * @param  string $url The URL to analyse
     * @return mixed       string on success, false on failure
     */
    function get_domain_from_url($url)
    {
        $bits = explode('/', $url);

        if ($bits[0] == 'http:' || $bits[0] == 'https:') {

            $_domain = $bits[2];

        } else {

            $_domain = $bits[0];
        }

        unset($bits);

        $bits = explode('.', $_domain);
        $idz = count($bits);
        $idz -=3;

        if (!isset($bits[($idz+2)])) {

            $out = false;

        } elseif (strlen($bits[($idz+2)]) == 2 && isset($bits[($idz+2)])) {

            $out   = array();
            $out[] = !empty($bits[$idz])   ? $bits[$idz]   : false;
            $out[] = !empty($bits[$idz+1]) ? $bits[$idz+1] : false;
            $out[] = !empty($bits[$idz+2]) ? $bits[$idz+2] : false;

            $out = implode('.', array_filter($out));

        } elseif (strlen($bits[($idz+2)]) == 0) {

            $out   = array();
            $out[] = !empty($bits[$idz])   ? $bits[$idz]   : false;
            $out[] = !empty($bits[$idz+1]) ? $bits[$idz+1] : false;

            $out = implode('.', array_filter($out));

        } elseif (isset($bits[($idz+1)])) {

            $out   = array();
            $out[] = !empty($bits[$idz+1]) ? $bits[$idz+1] : false;
            $out[] = !empty($bits[$idz+2]) ? $bits[$idz+2] : false;

            $out = implode('.', array_filter($out));

        } else {

            $out = false;
        }

        return $out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('get_relative_path')) {

    /**
     * Fetches the relative path between two directories
     * Hat tip: Thanks to Gordon for this one; http://stackoverflow.com/a/2638272/789224
     * @param  string $from Path 1
     * @param  string $to   Path 2
     * @return string
     */
    function get_relative_path($from, $to)
    {
        $from    = explode('/', $from);
        $to      = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {

            //  Find first non-matching dir
            if ($dir === $to[$depth]) {

                //  Ignore this directory
                array_shift($relPath);

            } else {

                //  Get number of remaining dirs to $from
                $remaining = count($from) - $depth;

                if ($remaining > 1) {

                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath   = array_pad($relPath, $padLength, '..');
                    break;

                } else {

                    $relPath[0] = './' . $relPath[0];
                }
            }
        }

        return implode('/', $relPath);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('add_trailing_slash')) {

    /**
     * Adds a trailing slash to the input string if there isn't already one there
     * @param   string The string to add a trailing shash to.
     * @return  string
     **/
    function add_trailing_slash($str)
    {
        return rtrim($str, '/') . '/';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('page_is_secure')) {

    /**
     * Detects whether the current page is secure or not
     *
     * @access  public
     * @param   string
     * @return  bool
     */
    function page_is_secure()
    {
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {

            //  Page is being served through HTTPS
            return true;

        } elseif (isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REQUEST_URI']) && SECURE_BASE_URL != BASE_URL) {

            //  Not being served through HTTPS, but does the URL of the page begin
            //  with SECURE_BASE_URL (when BASE_URL is different)

            $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

            if (preg_match('#^' . SECURE_BASE_URL . '.*#', $url)) {

                return true;

            } else {

                return false;
            }
        }

        // --------------------------------------------------------------------------

        //  Unknown, assume not
        return false;
    }
}

// --------------------------------------------------------------------------

/**
 *
 * The following class traits are used throughout Nails
 *
 */

/**
 * Implements a common API for error handling in classes
 */
trait NAILS_COMMON_TRAIT_ERROR_HANDLING
{
    protected $_errors = array();

    // --------------------------------------------------------------------------

    /**
     * Set a generic error
     * @param string $error The error message
     */
    protected function _set_error($error)
    {
        $this->_errors[] = $error;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the error array
     * @return array
     */
    public function get_errors()
    {
        return $this->_errors;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the last error
     * @return string
     */
    public function last_error()
    {
        return end($this->_errors);
    }

    // --------------------------------------------------------------------------

    /**
     * Clears the last error
     * @return mixed
     */
    public function clear_last_error()
    {
        return array_pop($this->_errors);
    }

    // --------------------------------------------------------------------------

    /**
     * Clears all errors
     * @return void
     */
    public function clear_errors()
    {
        $this->_errors = array();
    }
}

// --------------------------------------------------------------------------

/**
 * Implements a common API for caching in classes
 */
trait NAILS_COMMON_TRAIT_CACHING
{
    protected $_cache_values    = array();
    protected $_cache_keys      = array();
    protected $_cache_method    = 'LOCAL';

    // --------------------------------------------------------------------------

    /**
     * Saves an item to the cache
     * @param string $key   The cache key
     * @param mixed  $value The data to be cached
     */
    protected function _set_cache($key, $value)
    {
        if (empty($key)) {

            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $_prefix = $this->_cache_prefix();

        // --------------------------------------------------------------------------

        switch ($this->_cache_method) {

            case 'MEMCACHED':

                //  @TODO
                break;

            case 'LOCAL':
            default:

                $this->_cache_values[md5($_prefix . $key)] = serialize($value);
                $this->_cache_keys[] = $key;
                break;
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches an item from the cache
     * @param  string $key The cache key
     * @return mixed
     */
    protected function _get_cache($key)
    {
        if (empty($key)) {

            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $_prefix = $this->_cache_prefix();

        // --------------------------------------------------------------------------

        switch ($this->_cache_method) {

            case 'MEMCACHED':

                //  @TODO
                break;

            case 'LOCAL':
            default:

                if (isset($this->_cache_values[md5($_prefix . $key)])) {

                    return unserialize($this->_cache_values[md5($_prefix . $key)]);

                } else {

                    return false;

                }
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes an item from the cache
     * @param  string $key The cache key
     * @return boolean
     */
    protected function _unset_cache($key)
    {
        if (empty($key)) {

            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $_prefix = $this->_cache_prefix();

        // --------------------------------------------------------------------------

        switch ($this->_cache_method) {

            case 'MEMCACHED':

                //  @TODO
                break;

            case 'LOCAL':
            default:

                unset($this->_cache_values[md5($_prefix . $key)]);

                $_key = array_search($key, $this->_cache_keys);

                if ($_key !== false) {

                    unset($this->_cache_keys[$_key]);
                }
                break;
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * In order to avoid collission between classes a prefix is used; this method
     * defines the cache key prefix using the calling class' name.
     * @return string
     */
    protected function _cache_prefix()
    {
        return get_called_class();
    }
}

/**
 * Implements the common getcount_common() and _getcount_common_parse_sort() methods
 */
trait NAILS_COMMON_TRAIT_GETCOUNT_COMMON
{
    /**
     * Applies common conditionals
     *
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     * @param string $data Data passed from the calling method
     * @param string $_caller The name of the calling method
     * @return void
     **/
    protected function _getcount_common($data = array(), $_caller = null)
    {
        //  Handle wheres
        $_wheres = array('where', 'where_in', 'or_where_in', 'where_not_in', 'or_where_not_in');

        foreach ($_wheres as $where_type) {

            if (!empty($data[$where_type])) {

                if (is_array($data[$where_type])) {

                    /**
                     * If it's a single dimensional array then just bung that into
                     * the db->where(). If not, loop it and parse.
                     */

                    $_first = reset($data[$where_type]);

                    if (is_string($_first)) {

                        $this->db->$where_type($data[$where_type]);

                    } else {

                        foreach ($data[$where_type] as $where) {

                            //  Work out column
                            $_column = !empty($where['column']) ? $where['column'] : null;

                            if ($_column === null) {

                                $_column = !empty($where[0]) && is_string($where[0]) ? $where[0] : null;
                            }

                            //  Work out value
                            $_value = isset($where['value']) ? $where['value'] : null;

                            if ($_value === null) {

                                $_value = !empty($where[1]) ? $where[1] : null;
                            }

                            //  Escaped?
                            $_escape = isset($where['escape']) ? (bool) $where['escape'] : true;

                            if ($_column) {

                                $this->db->$where_type($_column, $_value, $_escape);
                            }
                        }
                    }

                } elseif (is_string($data[$where_type])) {

                    $this->db->$where_type($data[$where_type]);
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Handle Likes
        //  @TODO

        // --------------------------------------------------------------------------

        //  Handle sorting
        if (!empty($data['sort'])) {

            /**
             * How we handle sorting
             * =====================
             *
             * - If $data['sort'] is a string assume it's the field to sort on, use the default order
             * - If $data['sort'] is a single dimension array then assume the first element (or the element
             *   named 'column') is the column; and the second element (or the element named 'order') is the
             *   direction to sort in
             * - If $data['sort'] is a multidimensional array then loop each element and test as above.
             *
             **/


            if (is_string($data['sort'])) {

                //  String
                $this->db->order_by($data['sort']);

            } elseif (is_array($data['sort'])) {

                $_first = reset($data['sort']);

                if (is_string($_first)) {

                    //  Single dimension array
                    $_sort = $this->_getcount_common_parse_sort($data['sort']);

                    if (!empty($_sort['column'])) {

                        $this->db->order_by($_sort['column'], $_sort['order']);

                    }

                } else {

                    //  Multi dimension array
                    foreach ($data['sort'] as $sort) {

                        $_sort = $this->_getcount_common_parse_sort($sort);

                        if (!empty($_sort['column'])) {

                            $this->db->order_by($_sort['column'], $_sort['order']);
                        }
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    protected function _getcount_common_parse_sort($sort)
    {
        $_out = array('column' => null, 'order' => null);

        // --------------------------------------------------------------------------

        if (is_string($sort)) {

            $_out['column'] = $sort;
            return $_out;

        } elseif (isset($sort['column'])) {

            $_out['column'] = $sort['column'];

        } else {

            //  Take the first element
            $_out['column'] = reset($sort);
            $_out['column'] = is_string($_out['column']) ? $_out['column'] : null;
        }

        if ($_out['column']) {

            //  Determine order
            if (isset($sort['order'])) {

                $_out['order'] = $sort['order'];

            } elseif(count($sort) > 1) {

                //  Take the last element
                $_out['order'] = end($sort);
                $_out['order'] = is_string($_out['order']) ? $_out['order'] : null;
            }
        }

        // --------------------------------------------------------------------------

        return $_out;
    }
}
