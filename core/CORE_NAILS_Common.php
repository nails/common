<?php

/**
 * This file provides functions used internally by Nails
 *
 * @package     Nails
 * @subpackage  common
 * @category    helper
 * @author      Nails Dev Team
 * @link
 */

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

// --------------------------------------------------------------------------

if (!function_exists('isModuleEnabled')) {

    /**
     * Handy way of determining whether a module is enabled or not in the app's config
     * @param  string $module the name of the module to check
     * @return boolean
     */
    function isModuleEnabled($module)
    {
        $potential = _NAILS_GET_AVAILABLE_MODULES();

        if (array_search('nailsapp/module-' . $module, $potential) !== false) {

            return true;
        }

        return false;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getControllerData')) {

    /**
     * $NAILS_CONTROLLER_DATA is an array populated by $this->data in controllers,
     * this function provides an easy interface to this array when it's not in scope.
     * @return  array   A reference to $NAILS_CONTROLLER_DATA
     **/
    function &getControllerData()
    {
        global $NAILS_CONTROLLER_DATA;
        return $NAILS_CONTROLLER_DATA;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('setControllerData')) {

    /**
     * $NAILS_CONTROLLER_DATA is an array populated by $this->data in controllers,
     * this function provides an easy interface to populate this array when it's not
     * in scope.
     * @param string $key The key to populate
     * @param mixed $value The value to assign
     * @return  void
     **/
    function setControllerData($key, $value)
    {
        global $NAILS_CONTROLLER_DATA;
        $NAILS_CONTROLLER_DATA[$key] = $value;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getDomainFromUrl')) {

    /**
     * Attempts to get the top level part of a URL (i.e example.tld from sub.domains.example.tld).
     * Hat tip: http://uk1.php.net/parse_url#104874
     * BUG: 2 character TLD's break this
     * @TODO: Try and fix this bug
     * @param  string $url The URL to analyse
     * @return mixed       string on success, false on failure
     */
    function getDomainFromUrl($url)
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

if (!function_exists('getRelativePath')) {

    /**
     * Fetches the relative path between two directories
     * Hat tip: Thanks to Gordon for this one; http://stackoverflow.com/a/2638272/789224
     * @param  string $from Path 1
     * @param  string $to   Path 2
     * @return string
     */
    function getRelativePath($from, $to)
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

if (!function_exists('isPageSecure')) {

    /**
     * Detects whether the current page is secure or not
     *
     * @access  public
     * @param   string
     * @return  bool
     */
    function isPageSecure()
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
