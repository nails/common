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

use Nails\Common\Exception\NailsException;
use Nails\Common\Service\ErrorHandler;
use Nails\Common\Service\Input;

class Functions
{
    /**
     * Define a constant if it is not already defined
     *
     * @param string $sConstantName The constant to define
     * @param mixed  $mValue        The constant's value
     */
    public static function define(string $sConstantName, $mValue): void
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
    public static function isPageSecure(): bool
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
     * @param string $sUrl The URL to analyse
     *
     * @return mixed        string on success, false on failure
     * @todo: Try and fix this bug
     *
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
     * @param string $sFrom Path 1
     * @param string $sTo   Path 2
     *
     * @return string
     */
    public static function getRelativePath($sFrom, $sTo): string
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
     * Throw an error
     *
     * @param string $sMessage    The error message
     * @param string $sSubject    The error subject
     * @param int    $iStatusCode The status code
     */
    public static function showError(
        $sMessage = '',
        $sSubject = '',
        $iStatusCode = 500,
        $bUseException = true
    ): void {

        if (is_array($sMessage)) {
            $sMessage = implode('<br>', $sMessage);
        }

        if ($bUseException) {
            throw new NailsException($sMessage, $iStatusCode);
        } else {
            /** @var ErrorHandler $oErrorHandler */
            $oErrorHandler = Factory::service('ErrorHandler');
            $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 401 page, optionally logging the error to the database.
     * If a user is not logged in they are directed to the login page.
     *
     * @param string $sReturnUrl    The URL to return to after logging in
     * @param string $sFlashMessage The flashmessage to display to the user
     * @param bool   $bLogError     Whether to log the error or not
     */
    public static function show401(
        string $sReturnUrl = null,
        string $sFlashMessage = null,
        bool $bLogError = true
    ): void {

        /** @var ErrorHandler $oErrorHandler */
        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->show401($sReturnUrl, $sFlashMessage, $bLogError);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 404 page, logging disabled by default.
     *
     * @param bool $bLogError Whether to log the error or not
     */
    public static function show404($bLogError = true): void
    {
        /** @var ErrorHandler $oErrorHandler */
        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->show404($bLogError);
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the current request is being executed on the CLI
     *
     * @return bool
     */
    public static function isCli(): bool
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        return $oInput::isCli();
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the current request is an Ajax request
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        return $oInput::isAjax();
    }
}
