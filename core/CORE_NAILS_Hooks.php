<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MY_Hooks Class
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Miscallenous
 * @author        dazoe
 * @link        http://codeigniter.com/wiki/Dynamic_Hooking
 */

class CORE_NAILS_Hooks extends CI_Hooks
{
	var $myhooks = array();
	var $my_in_progress = false;
	function MY_Hooks() {
		parent::CI_Hooks();
	}

	/**
	 * --Add Hook--
	 * Adds a particular hook
	 * @access    public
	 * @param    string the hook name
	 * @param    array(classref, method, params)
	 * @return    mixed
	 */
	function add_hook($hookwhere, $hook) {
		if (is_array($hook)) {
			if (isset($hook['classref']) AND isset($hook['method']) AND isset($hook['params'])) {
				if (is_object($hook['classref']) AND method_exists($hook['classref'], $hook['method'])) {
					$this->myhooks[$hookwhere][] = $hook;
					return true;
				}
			}
		}
		return false;
	}


	function _call_hook($which = '') {
		if (!isset($this->myhooks[$which])) {
			return parent::_call_hook($which);
		}
		if (isset($this->myhooks[$which][0]) AND is_array($this->myhooks[$which][0])) {
			foreach ($this->myhooks[$which] as $val) {
				$this->_my_run_hook($val);
			}
		} else {
			$this->_my_run_hook($this->myhooks[$which]);
		}
		return parent::_call_hook($which);
	}

	function _my_run_hook($data)
	{
		if ( ! is_array($data)) {
			return FALSE;
		}

		// -----------------------------------
		// Safety - Prevents run-away loops
		// -----------------------------------
		// If the script being called happens to have the same
		// hook call within it a loop can happen
		if ($this->my_in_progress == TRUE) {
			return;
		}

		// -----------------------------------
		// Set class/method name
		// -----------------------------------
		$class        = NULL;
		$method        = NULL;
		$params        = '';

		if (isset($data['classref'])) {
			$class =& $data['classref'];
		}

		if (isset($data['method'])) {
			$method = $data['method'];
		}
		if (isset($data['params'])) {
			$params = $data['params'];
		}

		if (!is_object($class) OR !method_exists($class, $method)) {
			return FALSE;
		}

		// -----------------------------------
		// Set the in_progress flag
		// -----------------------------------
		$this->my_in_progress = TRUE;

		// -----------------------------------
		// Call the requested class and/or function
		// -----------------------------------
		$class->$method($params);
		$this->my_in_progress = FALSE;
		return TRUE;
	}
}
