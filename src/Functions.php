<?php

/**
 * Nails helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 */

namespace Nails;

class Functions
{
    /**
     * Define a constant if it is not already defined
     *
     * @param string $sConstantName The constant to define
     * @param mixed  $mValue        The constant's value
     */
    public static function define($sConstantName, $mValue)
    {
        if (!defined($sConstantName)) {
            define($sConstantName, $mValue);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Detects whether the current page is secure or not
     *
     * @return bool
     */
    public static function isPageSecure()
    {
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') {

            //  Page is being served through HTTPS
            return true;

        } elseif (isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REQUEST_URI']) && SECURE_BASE_URL != BASE_URL) {

            //  Not being served through HTTPS, but does the URL of the page begin
            //  with SECURE_BASE_URL (when BASE_URL is different)

            $sUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            return (bool) preg_match('#^' . SECURE_BASE_URL . '.*#', $sUrl);
        }

        //  Unknown, assume not
        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Attempts to get the top level part of a URL (i.e example.tld from sub.domains.example.tld).
     * Hat tip: http://uk1.php.net/parse_url#104874
     * BUG: 2 character TLD's break this
     *
     * @todo: Try and fix this bug
     *
     * @param  string $sUrl The URL to analyse
     *
     * @return mixed        string on success, false on failure
     */
    public static function getDomainFromUrl($sUrl)
    {
        $sDomain = parse_url($sUrl, PHP_URL_HOST);
        $bits    = explode('.', $sDomain);
        $idz     = count($bits);
        $idz     -= 3;

        if (!isset($bits[($idz + 2)])) {

            $aOut = false;

        } elseif (strlen($bits[($idz + 2)]) == 2 && isset($bits[($idz + 2)])) {

            $aOut = [
                !empty($bits[$idz]) ? $bits[$idz] : false,
                !empty($bits[$idz + 1]) ? $bits[$idz + 1] : false,
                !empty($bits[$idz + 2]) ? $bits[$idz + 2] : false,
            ];

            $aOut = implode('.', array_filter($aOut));

        } elseif (strlen($bits[($idz + 2)]) == 0) {

            $aOut = [
                !empty($bits[$idz]) ? $bits[$idz] : false,
                !empty($bits[$idz + 1]) ? $bits[$idz + 1] : false,
            ];

            $aOut = implode('.', array_filter($aOut));

        } elseif (isset($bits[($idz + 1)])) {

            $aOut = [
                !empty($bits[$idz + 1]) ? $bits[$idz + 1] : false,
                !empty($bits[$idz + 2]) ? $bits[$idz + 2] : false,
            ];
            $aOut = implode('.', array_filter($aOut));

        } else {
            $aOut = false;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches the relative path between two directories
     * Hat tip: Thanks to Gordon for this one; http://stackoverflow.com/a/2638272/789224
     *
     * @param  string $sFrom Path 1
     * @param  string $sTo   Path 2
     *
     * @return string
     */
    public static function getRelativePath($sFrom, $sTo)
    {
        $aFrom    = explode('/', $sFrom);
        $aTo      = explode('/', $sTo);
        $aRelPath = $aTo;

        foreach ($aFrom as $iDepth => $sDir) {

            //  Find first non-matching dir
            if ($sDir === $aTo[$iDepth]) {
                //  Ignore this directory
                array_shift($aRelPath);
            } else {

                //  Get number of remaining dirs to $aFrom
                $remaining = count($aFrom) - $iDepth;

                if ($remaining > 1) {

                    // add traversals up to first matching dir
                    $padLength = (count($aRelPath) + $remaining - 1) * -1;
                    $aRelPath  = array_pad($aRelPath, $padLength, '..');
                    break;

                } else {
                    $aRelPath[0] = './' . $aRelPath[0];
                }
            }
        }

        return implode('/', $aRelPath);
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve a value from $sArray at $sKey, if it exists
     *
     * @param  string $sKey     The key to get
     * @param  array  $aArray   The array to look in
     * @param  mixed  $mDefault What to return if $sKey doesn't exist in $aArray
     *
     * @return mixed
     */
    public static function getFromArray($sKey, $aArray, $mDefault = null)
    {
        return array_key_exists($sKey, $aArray) ? $aArray[$sKey] : $mDefault;
    }

    // --------------------------------------------------------------------------

    /**
     * Throw an error
     *
     * @param string $sMessage    The error message
     * @param string $sSubject    The error subject
     * @param int    $iStatusCode The status code
     */
    public static function showError($sMessage = '', $sSubject = '', $iStatusCode = 500)
    {
        $oError =& load_class('Exceptions', 'core');
        $oError->show_error($sSubject, $sMessage, $iStatusCode, $iStatusCode);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 401 page, optionally logging the error to the database.
     * If a user is not logged in they are directed to the login page.
     *
     * @param  boolean $bLogError Whether to log the error or not
     *
     * @return void
     */
    public static function show401($bLogError = true)
    {
        $oError =& load_class('Exceptions', 'core');
        $oError->show_401($bLogError);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 404 page, logging disabled by default.
     *
     * Note that the Exception class does log by default. Manual 404's are probably
     * a result of some other checking and not technically a 404 so should not be
     * logged as one. _Actual_ 404's should continue to be logged however.
     *
     * @param  boolean $bLogError Whether to log the error or not
     *
     * @return void
     */
    public static function show404($bLogError = true)
    {
        $oError =& load_class('Exceptions', 'core');
        $oError->show_404('', $bLogError);
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the current request is being executed on the CLI
     *
     * @return bool
     */
    public static function isCli()
    {
        $oInput = Factory::service('Input');
        return $oInput::isCli();
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the current request is an Ajax request
     *
     * @return bool
     */
    public static function isAjax()
    {
        $oInput = Factory::service('Input');
        return $oInput::isAjax();
    }
}