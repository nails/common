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

use Nails\Common\Helper\Url;

if (!function_exists('siteUrl')) {
    function siteUrl(string $sUrl = null, bool $bForceSecure = false): string
    {
        return Url::siteUrl($sUrl, $bForceSecure);
    }
}

if (!function_exists('site_url')) {
    function site_url(string $sUrl = null, bool $bForceSecure = false): string
    {
        return siteUrl($sUrl, $bForceSecure);
    }
}

if (!function_exists('secure_site_url')) {
    function secure_site_url(string $sUrl = null): string
    {
        return siteUrl($sUrl, true);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $sUrl = null, string $sMethod = 'location', int $iHttpResponseCode = 302): void
    {
        Url::redirect($sUrl, $sMethod, $iHttpResponseCode);
    }
}

if (!function_exists('tel')) {
    function tel(string $sUrl = null, string $sTitle = '', string $sAttributes = ''): string
    {
        return Url::tel($sUrl, $sTitle, $sAttributes);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/url_helper.php';
