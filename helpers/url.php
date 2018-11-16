<?php

/**
 * This file provides url related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('site_url')) {
    /**
     * Create a local URL based on your basepath. Segments can be passed via the
     * first parameter either as a string or an array.
     *
     * @param  mixed   $sUri         URI segments, either as a string or an array
     * @param  boolean $bForceSecure Whether to force the url to be secure or not
     *
     * @return string
     */
    function site_url($sUri = '', $bForceSecure = false)
    {
        $oConfig = \Nails\Factory::service('Config');
        return $oConfig::siteUrl($sUri, $bForceSecure);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('secure_site_url')) {

    /**
     * Create a secure local URL based on your basepath. Segments can be passed via the
     * first parameter either as a string or an array.
     *
     * @param  mixed $sUri URI segments, either as a string or an array
     *
     * @return string
     */
    function secure_site_url($sUri = '')
    {
        $oConfig = \Nails\Factory::service('Config');
        return $oConfig::siteUrl($sUri, true);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('redirect')) {

    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's set_header() function.
     *
     * Overriding so as to call the post_system hook before exit()'ing
     *
     * @param  string  $sUri              The uri to redirect to
     * @param  string  $sMethod           The redirect method
     * @param  integer $sHttpResponseCode The response code to send
     *
     * @return void
     */
    function redirect($sUri = '', $sMethod = 'location', $sHttpResponseCode = 302)
    {
        /**
         * Call the post_system hook, the system will be killed in approximately 13
         * lines so this is the last chance to cleanup.
         */

        $oHook =& load_class('Hooks', 'core');
        $oHook->call_hook('post_system');

        // --------------------------------------------------------------------------

        if (!preg_match('#^https?://#i', $sUri)) {
            $sUri = site_url($sUri);
        }

        switch ($sMethod) {
            case 'refresh':
                header("Refresh:0;url=" . $sUri);
                break;

            default:
                header('Location: ' . $sUri, true, $sHttpResponseCode);
                break;
        }

        exit;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('tel')) {

    /**
     * Generates a hyperlink using the tel: scheme
     *
     * @param  string $sUri        The phone number to link
     * @param  string $sTitle      The title to give the hyperlink
     * @param  string $aAttributes Any attributes to give the hyperlink
     *
     * @return string
     */
    function tel($sUri = '', $sTitle = '', $aAttributes = '')
    {
        $sTitle = empty($sTitle) ? $sUri : $sTitle;
        $sUri   = preg_replace('/[^\+0-9]/', '', $sUri);
        $sUri   = 'tel://' . $sUri;

        return anchor($sUri, $sTitle, $aAttributes);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/url_helper.php';
