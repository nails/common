<?php

/**
 * Nails helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
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
}
