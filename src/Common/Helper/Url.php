<?php

/**
 * URL helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

use Nails\Bootstrap;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Service\FileCache;
use Nails\Factory;
use Pdp;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class Url
 *
 * @package Nails\Common\Helper
 */
class Url
{
    const PUBLIC_SUFFIX_LIST = 'https://publicsuffix.org/list/public_suffix_list.dat';

    // --------------------------------------------------------------------------

    /**
     * Create a local URL based on your base path. Segments can be passed via the
     * first parameter either as a string or an array.
     *
     * @param mixed $sUrl         URI segments, either as a string or an array
     * @param bool  $bForceSecure Whether to force the url to be secure or not
     *
     * @return string
     * @throws FactoryException
     */
    public static function siteUrl(string $sUrl = null, bool $bForceSecure = false): string
    {
        $oConfig = Factory::service('Config');
        return $oConfig::siteUrl($sUrl, $bForceSecure);
    }

    // --------------------------------------------------------------------------

    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's setHeader() function.
     *
     * Overriding so as to call the post_system hook before exit()'ing
     *
     * @param string $sUrl    The uri to redirect to
     * @param string $sMethod The redirect method
     * @param int    $iHttpResponseCode
     *
     * @return void
     * @throws FactoryException
     * @throws NailsException
     */
    public static function redirect(string $sUrl = null, string $sMethod = 'location', int $iHttpResponseCode = 302): void
    {
        /**
         * Call the Bootstrap::shutdown method, the system will be killed in approximately 13
         * lines so this is the last chance to cleanup.
         */
        Bootstrap::shutdown();

        // --------------------------------------------------------------------------

        /** @var \Nails\Common\Service\UserFeedback $oUserFeedback */
        $oUserFeedback = Factory::service('UserFeedback');
        $oUserFeedback->persist();

        // --------------------------------------------------------------------------

        if (!preg_match('#^https?://#i', $sUrl)) {
            $sUrl = siteUrl($sUrl);
        }

        switch ($sMethod) {
            case 'refresh':
                header('Refresh:0;url=' . $sUrl);
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
    public static function tel(string $sUrl = null, string $sTitle = '', string $sAttributes = ''): string
    {
        $sTitle = empty($sTitle) ? $sUrl : $sTitle;
        $sUrl   = preg_replace('/[^+0-9]/', '', $sUrl);
        $sUrl   = 'tel://' . $sUrl;

        return anchor($sUrl, $sTitle, $sAttributes);
    }

    // --------------------------------------------------------------------------

    /**
     * Extracts the registrable portion of a domain
     *
     * @param string|null $sUrl The URL to parse
     *
     * @return string|null
     * @throws InvalidArgumentException
     */
    public static function extractRegistrableDomain(?string $sUrl): ?string
    {
        /** @var FileCache $oFileCache */
        $oFileCache = Factory::service('FileCache');
        $sCacheDir  = $oFileCache->getDir() . 'php-domain-parser' . DIRECTORY_SEPARATOR;

        $oManager = new Pdp\Manager(
            new Pdp\Cache($sCacheDir),
            new Pdp\CurlHttpClient()
        );

        try {

            $oRules = $oManager->getRules();

        } catch (Pdp\Exception\CouldNotLoadRules $e) {
            $oManager->refreshRules(static::PUBLIC_SUFFIX_LIST);
            $oRules = $oManager->getRules();
        }

        $sUrl    = preg_replace('/^(?:https?|ftp):\/\//', '', $sUrl);
        $oDomain = $oRules->resolve($sUrl);

        return $oDomain->getRegistrableDomain();
    }
}
