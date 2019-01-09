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

namespace Nails\Common\CodeIgniter\Core;

use CI_Hooks;

class Hooks extends CI_Hooks
{
    /**
     * Custom hooks
     *
     * @var array
     */
    public $aCustomHooks = [];

    /**
     * Whether a custom hook is in progress
     *
     * @var bool
     */
    public $bCustomHooksInProgress = false;

    // --------------------------------------------------------------------------

    /**
     * Adds a particular hook
     *
     * @param string The hook name
     * @param array  The hook configuration (classref, method, params)
     *
     * @return bool
     */
    public function addHook($hookwhere, $hook)
    {
        if (is_array($hook)) {
            if (isset($hook['classref']) && isset($hook['method']) && isset($hook['params'])) {
                if (is_object($hook['classref']) && method_exists($hook['classref'], $hook['method'])) {
                    $this->aCustomHooks[$hookwhere][] = $hook;
                    return true;
                }
            }
        }
        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Alias of addHook
     *
     * @param string The hook name
     * @param array  The hook configuration (classref, method, params)
     *
     * @return bool
     */
    public function add_hook($hookwhere, $hook)
    {
        deprecatedError('add_hook', 'addHook');
        return $this->addHook($hookwhere, $hook);
    }

    // --------------------------------------------------------------------------

    /**
     * Call a particular hook
     *
     * @param string $sWhich The hook to cal
     *
     * @return bool
     */
    public function call_hook($sWhich = '')
    {
        if (!isset($this->aCustomHooks[$sWhich])) {
            return parent::call_hook($sWhich);
        }

        if (isset($this->aCustomHooks[$sWhich][0]) && is_array($this->aCustomHooks[$sWhich][0])) {
            foreach ($this->aCustomHooks[$sWhich] as $aCustomHook) {
                $this->runCustomHook($aCustomHook);
            }
        } else {
            $this->runCustomHook($this->aCustomHooks[$sWhich]);
        }

        return parent::call_hook($sWhich);
    }

    // --------------------------------------------------------------------------

    /**
     * Execute a cusotm hook
     *
     * @param array $aData The hook data
     *
     * @return bool|void
     */
    protected function runCustomHook($aData)
    {
        if (!is_array($aData)) {
            return false;
        }

        /** -----------------------------------
         * Safety - Prevents run-away loops
         * ------------------------------------
         * If the script being called happens to have the same
         * hook call within it a loop can happen
         */
        if ($this->bCustomHooksInProgress == true) {
            return;
        }

        // Set class/method name
        $oClass  = null;
        $sMethod = null;
        $aParams = [];

        if (isset($aData['classref'])) {
            $oClass =& $aData['classref'];
        }

        if (isset($aData['method'])) {
            $sMethod = $aData['method'];
        }
        if (isset($aData['params'])) {
            $aParams = $aData['params'];
        }

        if (!is_object($oClass) || !method_exists($oClass, $sMethod)) {
            return false;
        }

        // Set the in_progress flag
        $this->bCustomHooksInProgress = true;

        // Call the requested class and/or function
        $oClass->$sMethod($aParams);
        $this->bCustomHooksInProgress = false;
        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Run Hook
     *
     * Runs a particular hook
     *
     * @param array The hook details
     *
     * @return bool
     */
    public function _run_hook($aData)
    {
        if (!is_array($aData)) {
            return false;
        }

        /** -----------------------------------
         * Safety - Prevents run-away loops
         * ------------------------------------
         * If the script being called happens to have the same
         * hook call within it a loop can happen
         */
        if ($this->bCustomHooksInProgress == true) {
            return;
        }

        // Set file path
        if (!isset($aData['filepath']) || !isset($aData['filename'])) {
            return false;
        }

        //  Using absolute filepath?
        if (substr($aData['filepath'], 0, 1) == '/') {

            $sFilePath = rtrim($aData['filepath'], '/') . '/';
            $sFilePath .= $aData['filename'];

        } else {

            $sFilePath = APPPATH;
            $sFilePath .= rtrim($aData['filepath'], '/') . '/';
            $sFilePath .= $aData['filename'];
        }

        if (!file_exists($sFilePath)) {
            return false;
        }

        // Set class/function name
        $sClass    = false;
        $sFunction = false;
        $sParams   = '';

        if (isset($aData['class']) && $aData['class'] != '') {
            $sClass = $aData['class'];
        }

        if (isset($aData['function'])) {
            $sFunction = $aData['function'];
        }

        if (isset($aData['params'])) {
            $sParams = $aData['params'];
        }

        if ($sClass === false && $sFunction === false) {
            return false;
        }

        // Set the in_progress flag
        $this->in_progress = true;

        // Call the requested class and/or function
        if ($sClass !== false) {

            if (!class_exists($sClass)) {
                require($sFilePath);
            }

            $HOOK = new $sClass();
            $HOOK->$sFunction($sParams);

        } else {

            if (!function_exists($sFunction)) {
                require($sFilePath);
            }

            $sFunction($sParams);
        }

        $this->in_progress = false;
        return true;
    }
}
