<?php

/**
 * Url helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

class Url
{
    /**
     * Create a local URL based on your basepath. Segments can be passed via the
     * first parameter either as a string or an array.
     *
     * @param mixed $sUrl         URI segments, either as a string or an array
     * @param bool  $bForceSecure Whether to force the url to be secure or not
     *
     * @return string
     */
    public static function siteUrl(string $sUrl = '', bool $bForceSecure = false): string
    {
        $oConfig = \Nails\Factory::service('Config');
        return $oConfig::siteUrl($sUrl, $bForceSecure);
    }

    // --------------------------------------------------------------------------

    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's set_header() function.
     *
     * Overriding so as to call the post_system hook before exit()'ing
     *
     * @param string  $sUrl              The uri to redirect to
     * @param string  $sMethod           The redirect method
     * @param integer $sHttpResponseCode The response code to send
     *
     * @return void
     */
    public static function redirect(string $sUrl = '', string $sMethod = 'location', int $iHttpResponseCode = 302): void
    {
        /**
         * Call the post_system hook, the system will be killed in approximately 13
         * lines so this is the last chance to cleanup.
         */
        $oHook =& load_class('Hooks', 'core');
        $oHook->call_hook('post_system');

        // --------------------------------------------------------------------------

        if (!preg_match('#^https?://#i', $sUrl)) {
            $sUrl = site_url($sUrl);
        }

        switch ($sMethod) {
            case 'refresh':
                header("Refresh:0;url=" . $sUrl);
                break;

            default:
                header('Location: ' . $sUrl, true, $iHttpResponseCode);
                break;
        }

        exit;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a hyperlink using the tel: scheme
     *
     * @param string $sUrl        The phone number to link
     * @param string $sTitle      The title to give the hyperlink
     * @param string $sAttributes Any attributes to give the hyperlink
     *
     * @return string
     */
    public static function tel(string $sUrl = '', string $sTitle = '', string $sAttributes = ''): string
    {
        $sTitle = empty($sTitle) ? $sUrl : $sTitle;
        $sUrl   = preg_replace('/[^\+0-9]/', '', $sUrl);
        $sUrl   = 'tel://' . $sUrl;

        return anchor($sUrl, $sTitle, $sAttributes);
    }
}
