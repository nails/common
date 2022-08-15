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

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Exception\Redirect\RedirectException;
use Nails\Common\Factory\Redirect;
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
     * @param string|null $sUrl         URI segments, either as a string or an array
     * @param bool        $bForceSecure Whether to force the url to be secure or not
     *
     * @return string
     * @throws FactoryException
     */
    public static function siteUrl(string $sUrl = null, bool $bForceSecure = false): string
    {
        /** @var \Nails\Common\Service\Config $oConfig */
        $oConfig = Factory::service('Config');
        return $oConfig::siteUrl($sUrl, $bForceSecure);
    }

    // --------------------------------------------------------------------------

    /**
     * Header Redirect
     *
     * @param string $sUrl                      The uri to redirect to
     * @param string $sMethod                   The redirect method
     * @param int    $iLocationHttpResponseCode The status code to give refresh redirects
     * @param bool   $bAllowExternal            Whether to allow external redirects
     *
     * @return void
     * @throws FactoryException
     * @throws NailsException
     * @throws RedirectException
     */
    public static function redirect(
        string $sUrl = '',
        string $sMethod = Redirect::METHOD_LOCATION,
        int $iLocationHttpResponseCode = Redirect::HTTP_CODE_TEMPORARY,
        bool $bAllowExternal = false
    ): void {
        /** @var Redirect $oRedirect */
        $oRedirect = Factory::factory('Redirect');
        $oRedirect
            ->setUrl($sUrl)
            ->setMethod($sMethod)
            ->setLocationHttpResponseCode($iLocationHttpResponseCode)
            ->allowExternal($bAllowExternal)
            ->execute();
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a hyperlink using the tel: scheme
     *
     * @param string      $sUrl        The phone number to link
     * @param string|null $sTitle      The title to give the hyperlink
     * @param string      $sAttributes Any attributes to give the hyperlink
     *
     * @return string
     */
    public static function tel(string $sUrl = null, string $sTitle = null, string $sAttributes = ''): string
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
