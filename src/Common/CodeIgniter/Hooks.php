<?php

/**
 * MY_Hooks Class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Miscallenous
 * @author      dazoe
 * @link        https://github.com/bcit-ci/CodeIgniter/wiki/Dynamic-Hooking
 */

namespace Nails\Common\CodeIgniter;

use CI_Hooks;

class Hooks extends CI_Hooks
{
    public $myhooks        = [];
    public $my_in_progress = false;

    public function MY_Hooks()
    {
        parent::CI_Hooks();
    }

    /**
     * --Add Hook--
     * Adds a particular hook
     * @access    public
     *
     * @param    string the hook name
     * @param    array  (classref, method, params)
     *
     * @return    mixed
     */
    public function add_hook($hookwhere, $hook)
    {
        if (is_array($hook)) {
            if (isset($hook['classref']) && isset($hook['method']) && isset($hook['params'])) {
                if (is_object($hook['classref']) && method_exists($hook['classref'], $hook['method'])) {
                    $this->myhooks[$hookwhere][] = $hook;
                    return true;
                }
            }
        }
        return false;
    }

    // --------------------------------------------------------------------------

    public function call_hook($which = '')
    {
        if (!isset($this->myhooks[$which])) {
            return parent::call_hook($which);
        }
        if (isset($this->myhooks[$which][0]) && is_array($this->myhooks[$which][0])) {
            foreach ($this->myhooks[$which] as $val) {
                $this->_my_run_hook($val);
            }
        } else {
            $this->_my_run_hook($this->myhooks[$which]);
        }
        return parent::call_hook($which);
    }

    // --------------------------------------------------------------------------

    public function _my_run_hook($data)
    {
        if (!is_array($data)) {
            return false;
        }

        // -----------------------------------
        // Safety - Prevents run-away loops
        // -----------------------------------
        // If the script being called happens to have the same
        // hook call within it a loop can happen
        if ($this->my_in_progress == true) {
            return;
        }

        // -----------------------------------
        // Set class/method name
        // -----------------------------------
        $class  = null;
        $method = null;
        $params = '';

        if (isset($data['classref'])) {
            $class =& $data['classref'];
        }

        if (isset($data['method'])) {
            $method = $data['method'];
        }
        if (isset($data['params'])) {
            $params = $data['params'];
        }

        if (!is_object($class) || !method_exists($class, $method)) {
            return false;
        }

        // -----------------------------------
        // Set the in_progress flag
        // -----------------------------------
        $this->my_in_progress = true;

        // -----------------------------------
        // Call the requested class and/or function
        // -----------------------------------
        $class->$method($params);
        $this->my_in_progress = false;
        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Run Hook
     *
     * Runs a particular hook
     *
     * @access  private
     *
     * @param   array   the hook details
     *
     * @return  bool
     */
    public function _run_hook($data)
    {
        if (!is_array($data)) {
            return false;
        }

        // -----------------------------------
        // Safety - Prevents run-away loops
        // -----------------------------------

        // If the script being called happens to have the same
        // hook call within it a loop can happen

        if ($this->my_in_progress == true) {
            return;
        }

        // -----------------------------------
        // Set file path
        // -----------------------------------

        if (!isset($data['filepath']) || !isset($data['filename'])) {
            return false;
        }

        //  Using absolute filepath?
        if (substr($data['filepath'], 0, 1) == '/') {

            $filepath = rtrim($data['filepath'], '/') . '/';
            $filepath .= $data['filename'];

        } else {

            $filepath = APPPATH;
            $filepath .= rtrim($data['filepath'], '/') . '/';
            $filepath .= $data['filename'];
        }

        if (!file_exists($filepath)) {
            return false;
        }

        // -----------------------------------
        // Set class/function name
        // -----------------------------------

        $class    = false;
        $function = false;
        $params   = '';

        if (isset($data['class']) && $data['class'] != '') {
            $class = $data['class'];
        }

        if (isset($data['function'])) {
            $function = $data['function'];
        }

        if (isset($data['params'])) {
            $params = $data['params'];
        }

        if ($class === false && $function === false) {
            return false;
        }

        // -----------------------------------
        // Set the in_progress flag
        // -----------------------------------

        $this->in_progress = true;

        // -----------------------------------
        // Call the requested class and/or function
        // -----------------------------------

        if ($class !== false) {

            if (!class_exists($class)) {
                require($filepath);
            }

            $HOOK = new $class();
            $HOOK->$function($params);

        } else {

            if (!function_exists($function)) {
                require($filepath);
            }

            $function($params);
        }

        $this->in_progress = false;
        return true;
    }
}
