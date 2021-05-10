<?php

/**
 * The class provides a convenient way to load assets
 *
 * @package    Nails
 * @subpackage common
 * @category   Service
 * @author     Nails Dev Team
 */

namespace Nails\Common\Service;

use Nails\Asset\Constants;
use Nails\Common\Exception\AssetException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Factory\Asset\CriticalCss;
use Nails\Common\Helper\Strings;
use Nails\Config;
use Nails\Environment;
use Nails\Factory;
use Nails\Functions;

/**
 * Class Asset
 *
 * @package Nails\Common\Service
 */
class Asset
{
    /**
     * The various types of asset
     */
    const TYPE_ALL              = 'ALL';
    const TYPE_JS               = 'JS';
    const TYPE_JS_HEADER        = 'JS-HEADER';
    const TYPE_JS_FOOTER        = 'JS-FOOTER';
    const TYPE_JS_INLINE        = 'JS-INLINE';
    const TYPE_JS_INLINE_HEADER = 'JS-INLINE-HEADER';
    const TYPE_JS_INLINE_FOOTER = 'JS-INLINE-FOOTER';
    const TYPE_CSS              = 'CSS';
    const TYPE_CSS_INLINE       = 'CSS-INLINE';

    /**
     * The supported locations for JS
     */
    const JS_LOCATION_HEADER = 'HEADER';
    const JS_LOCATION_FOOTER = 'FOOTER';

    // --------------------------------------------------------------------------

    /**
     * The cachebuster string
     *
     * @var string
     */
    protected $sCacheBuster;

    /**
     * The base URL where assets are stored
     *
     * @var string
     */
    protected $sBaseUrl;

    /**
     * The secure version of the base URL
     *
     * @var string
     */
    protected $sBaseUrlSecure;

    /**
     * The base URL where module assets are stored
     *
     * @var string
     */
    protected $sBaseModuleUrl;

    /**
     * The secure version of the base module URL
     *
     * @var string
     */
    protected $sBaseModuleUrlSecure;

    /**
     * Where compiled CSS is stored
     *
     * @var string
     */
    protected $sCssDir;

    /**
     * Where compiled JS is stored
     *
     * @var string
     */
    protected $sJsDir;

    /**
     * Loaded CSS files
     *
     * @var array
     */
    protected $aCss = [];

    /**
     * Loaded inline CSS
     *
     * @var array
     */
    protected $aCssInline = [];

    /**
     * Loaded JS files
     *
     * @var array
     */
    protected $aJs = [];

    /**
     * Loades JS files (for the header)
     *
     * @var array
     */
    protected $aJsHeader = [];

    /**
     * Loaded inline JS for the header
     *
     * @var array
     */
    protected $aJsInlineHeader = [];

    /**
     * Loaded inline JS for the footer
     *
     * @var array
     */
    protected $aJsInlineFooter = [];

    /** @var CriticalCss */
    protected $oCriticalCss;

    /**
     * @var array[]
     */
    protected $aLibraries = [];

    // --------------------------------------------------------------------------

    /**
     * Asset constructor.
     *
     * @param string $sCacheBuster         The cachebuster string
     * @param string $sBaseUrl             The base URL where assets are stored
     * @param string $sBaseUrlSecure       The secure base URL where assets are stored
     * @param string $sBaseModuleUrl       The base URL where modules are
     * @param string $sBaseModuleUrlSecure The secure base URL where modules are
     * @param string $sCssDir              Where compiled CSS is stored
     * @param string $sJsDir               Where compiled JS is stored
     */
    public function __construct(
        ?string $sCacheBuster,
        string $sBaseUrl,
        string $sBaseUrlSecure,
        string $sBaseModuleUrl,
        string $sBaseModuleUrlSecure,
        string $sCssDir,
        string $sJsDir
    ) {

        $this->sCacheBuster = $sCacheBuster;
        $this->sCssDir      = Strings::addTrailingSlash($sCssDir);
        $this->sJsDir       = Strings::addTrailingSlash($sJsDir);

        $this->setBaseUrls(
            $sBaseUrl,
            $sBaseModuleUrl,
            $sBaseUrlSecure,
            $sBaseModuleUrlSecure
        );

        $this
            ->addLibrary('CKEDITOR', [
                'https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.16.0/ckeditor.js',
                'https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.16.0/adapters/jquery.min.js',
            ])
            ->addLibrary('JQUERYUI', [
                'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css',
            ])
            ->addLibrary('SELECT2', [
                'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2.min.css',
            ])
            ->addLibrary('KNOCKOUT', [
                'https://cdnjs.cloudflare.com/ajax/libs/knockout/3.5.1/knockout-latest.min.js',
            ])
            ->addLibrary('MUSTACHE', [
                'https://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.8.2/mustache.min.js',
            ])
            ->addLibrary('MOMENT', [
                'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
            ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the base URLs
     *
     * @param string      $sBaseUrl             The base URL
     * @param string      $sBaseModuleUrl       The base Module URL
     * @param string|null $sBaseUrlSecure       The secure base URL
     * @param string|null $sBaseModuleUrlSecure The secure base module URL
     *
     * @return $this
     */
    public function setBaseUrls(
        string $sBaseUrl,
        string $sBaseModuleUrl,
        string $sBaseUrlSecure = null,
        string $sBaseModuleUrlSecure = null
    ): self {

        $this->sBaseUrl = siteUrl($sBaseUrl);
        $this->sBaseUrl = Strings::addTrailingSlash($this->sBaseUrl);

        $this->sBaseModuleUrl = siteUrl($sBaseModuleUrl);
        $this->sBaseModuleUrl = Strings::addTrailingSlash($this->sBaseModuleUrl);

        $this->sBaseUrlSecure = site_url($sBaseUrlSecure);
        $this->sBaseUrlSecure = Strings::addTrailingSlash($this->sBaseUrlSecure);

        $this->sBaseModuleUrlSecure = siteUrl($sBaseModuleUrlSecure);
        $this->sBaseModuleUrlSecure = Strings::addTrailingSlash($this->sBaseModuleUrlSecure);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the base module URL appropriate for the page
     *
     * @return string
     */
    public function getBaseModuleUrl(): string
    {
        return Functions::isPageSecure()
            ? $this->sBaseModuleUrlSecure
            : $this->sBaseModuleUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an asset
     *
     * @param mixed  $mAssets        The asset to unload, can be an array or a string
     * @param string $sAssetLocation The asset's location
     * @param string $sForceType     The asset's file type (e.g., JS or CSS)
     *
     * @return object
     */
    public function unload($mAssets, $sAssetLocation = 'APP', $sForceType = null)
    {
        //  Cast as an array
        $aAssets = (array) $mAssets;

        // --------------------------------------------------------------------------

        //  Backwards compatibility
        $sAssetLocation = $sAssetLocation === true ? 'NAILS' : $sAssetLocation;
        $sAssetLocation = $sAssetLocation === 'NAILS' ? Constants::MODULE_SLUG : $sAssetLocation;

        // --------------------------------------------------------------------------

        switch (strtoupper($sAssetLocation)) {
            case 'APP':
                $sAssetLocationMethod = 'unloadApp';
                break;

            default:
                $sAssetLocationMethod = 'unloadModule';
                break;
        }

        // --------------------------------------------------------------------------

        foreach ($aAssets as $sAsset) {
            if (preg_match('#^https?://#', $sAsset)) {
                $this->unloadUrl($sAsset, $sForceType);
            } elseif (substr($sAsset, 0, 0) === '/') {
                $this->unloadAbsolute($sAsset, $sForceType);
            } else {
                $this->{$sAssetLocationMethod}($sAsset, $sForceType, $sAssetLocation);
            }
        }

        // --------------------------------------------------------------------------

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an asset supplied as a URL
     *
     * @param string $sAsset     The asset to unload
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function unloadUrl($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case static::TYPE_CSS:
                unset($this->aCss['URL-' . $sAsset]);
                break;

            case static::TYPE_JS:
            case static::TYPE_JS_FOOTER:
                unset($this->aJs['URL-' . $sAsset]);
                break;

            case static::TYPE_JS_HEADER:
                unset($this->aJsHeader['URL-' . $sAsset]);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an asset supplied as an absolute URL
     *
     * @param string $sAsset     The asset to unload
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function unloadAbsolute($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case static::TYPE_CSS:
                unset($this->aCss['ABSOLUTE-' . $sAsset]);
                break;

            case static::TYPE_JS:
            case static::TYPE_JS_FOOTER:
                unset($this->aJs['ABSOLUTE-' . $sAsset]);
                break;

            case static::TYPE_JS_HEADER:
                unset($this->aJsHeader['ABSOLUTE-' . $sAsset]);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an inline asset
     *
     * @param string $sScript     The inline asset to load, wrap in script tags for JS, or style tags for CSS
     * @param string $sForceType  Force a particular type of asset (i.e. JS-INLINE or CSS-INLINE)
     * @param string $sJsLocation Where the inline JS should appear, accepts FOOTER or HEADER
     *
     * @return void
     */
    public function unloadInline($sScript = null, $sForceType = null, $sJsLocation = self::JS_LOCATION_FOOTER)
    {
        if (!empty($sScript)) {

            $sJsLocation = strtoupper($sJsLocation);
            if (!in_array($sJsLocation, [static::JS_LOCATION_FOOTER, static::JS_LOCATION_HEADER])) {
                throw new AssetException(sprintf(
                    '"%s" is not a valid inline asset location value.',
                    $sJsLocation
                ));
            }

            $sType = $this->determineType($sScript, $sForceType);

            switch ($sType) {

                case static::TYPE_CSS_INLINE:
                case static::TYPE_CSS:
                    unset($this->aCssInline['INLINE-CSS-' . md5($sScript)]);
                    break;

                case static::TYPE_JS_INLINE:
                case static::TYPE_JS:
                    if ($sJsLocation === static::JS_LOCATION_FOOTER) {
                        unset($this->aJsInlineFooter['INLINE-JS-' . md5($sScript)]);
                    } else {
                        unset($this->aJsInlineHeader['INLINE-JS-' . md5($sScript)]);
                    }
                    break;
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a set of assets
     *
     * @param string $sKey The library to load
     *
     * @return $this
     * @throws AssetException
     */
    public function library($sKey): self
    {
        if (!array_key_exists($sKey, $this->aLibraries)) {
            throw new AssetException(sprintf(
                '"%s" is not a valid asset library',
                $sKey
            ));
        }

        foreach ($this->aLibraries as $sUrls) {
            $this->load($sUrls);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a new library
     *
     * @param string $sKey  The library key
     * @param array  $aUrls The URLs the library loads
     *
     * @return $this
     */
    public function addLibrary(string $sKey, array $aUrls): self
    {
        $this->aLibraries[strtoupper($sKey)] = $aUrls;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset
     *
     * @param mixed  $mAssets        The asset to load, can be an array or a string
     * @param string $sAssetLocation The asset's location
     * @param string $sForceType     The asset's file type (e.g., JS or CSS)
     * @param bool   $bAsync         Whether to load the asset asynchronously
     *
     * @return $this
     */
    public function load($mAssets, $sAssetLocation = 'APP', $sForceType = null, bool $bAsync = false): self
    {
        //  Cast as an array
        $aAssets = (array) $mAssets;

        // --------------------------------------------------------------------------

        //  Backwards compatibility
        $sAssetLocation = $sAssetLocation === true ? 'NAILS' : $sAssetLocation;
        $sAssetLocation = $sAssetLocation === 'NAILS' ? Constants::MODULE_SLUG : $sAssetLocation;

        // --------------------------------------------------------------------------

        switch (strtoupper($sAssetLocation)) {
            case 'APP':
                $sAssetLocationMethod = 'loadApp';
                break;

            default:
                $sAssetLocationMethod = 'loadModule';
                break;
        }

        // --------------------------------------------------------------------------

        foreach ($aAssets as $sAsset) {
            if (preg_match('#^https?://#', $sAsset)) {
                $this->loadUrl($sAsset, $sForceType, $bAsync);

            } elseif (substr($sAsset, 0, 0) === '/') {
                $this->loadAbsolute(substr($sAsset, 1), $sForceType, $bAsync);

            } else {
                $this->{$sAssetLocationMethod}($sAsset, $sForceType, $bAsync, $sAssetLocation);
            }
        }

        // --------------------------------------------------------------------------

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset supplied as a URL
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @param bool   $bAsync     Whether to load the asset asynchronously
     *
     * @return void
     */
    protected function loadUrl($sAsset, $sForceType, bool $bAsync)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case static::TYPE_CSS:
                $this->aCss['URL-' . $sAsset] = $sAsset;
                break;

            case static::TYPE_JS:
            case static::TYPE_JS_FOOTER:
                $this->aJs['URL-' . $sAsset] = [$sAsset, $bAsync];
                break;

            case static::TYPE_JS_HEADER:
                $this->aJsHeader['URL-' . $sAsset] = [$sAsset, $bAsync];
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset supplied as an absolute URL
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @param bool   $bAsync     Whether to load the asset asynchronously
     *
     * @return void
     */
    protected function loadAbsolute($sAsset, $sForceType, bool $bAsync)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case static::TYPE_CSS:
                $this->aCss['ABSOLUTE-' . $sAsset] = $this->buildUrl($sAsset);
                break;

            case static::TYPE_JS:
            case static::TYPE_JS_FOOTER:
                $this->aJs['ABSOLUTE-' . $sAsset] = [$this->buildUrl($sAsset), $bAsync];
                break;

            case static::TYPE_JS_HEADER:
                $this->aJsHeader['ABSOLUTE-' . $sAsset] = [$this->buildUrl($sAsset), $bAsync];
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Clears all loaded assets
     *
     * @return object
     */
    public function clear()
    {
        $this->aCss            = [];
        $this->aCssInline      = [];
        $this->aJs             = [];
        $this->aJsHeader       = [];
        $this->aJsInlineHeader = [];
        $this->aJsInlineFooter = [];
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an object containing all loaded assets, useful for debugging.
     *
     * @return stdClass
     */
    public function getLoaded()
    {
        return (object) [
            'css'            => $this->aCss,
            'cssInline'      => $this->aCssInline,
            'js'             => $this->aJs,
            'jsHeader'       => $this->aJsHeader,
            'jsInlineHeader' => $this->aJsInlineHeader,
            'jsInlineFooter' => $this->aJsInlineFooter,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Output the assets for HTML
     *
     * @param string $sType   The type of asset to output
     * @param bool   $bOutput Whether to output to the browser or to return as a string
     *
     * @return array
     */
    public function output(string $sType = self::TYPE_ALL, bool $bOutput = true): array
    {
        $aOut  = [];
        $sType = strtoupper($sType);

        //  Linked Stylesheets
        if (!empty($this->aCss) && ($sType === static::TYPE_CSS || $sType === static::TYPE_ALL)) {
            foreach ($this->aCss as $sAsset) {
                $aOut[] = link_tag($sAsset);
            }
        }

        // --------------------------------------------------------------------------

        //  Linked JS
        if (!empty($this->aJs) && ($sType === static::TYPE_JS || $sType === static::TYPE_ALL)) {
            foreach ($this->aJs as $aAsset) {
                [$sAsset, $bAsync] = $aAsset;
                $aOut[] = '<script ' . ($bAsync ? 'async ' : '') . 'src="' . $sAsset . '"></script>';
            }
        }

        if (!empty($this->aJsHeader) && ($sType === static::TYPE_JS_HEADER || $sType === static::TYPE_ALL)) {
            foreach ($this->aJsHeader as $aAsset) {
                [$sAsset, $bAsync] = $aAsset;
                $aOut[] = '<script ' . ($bAsync ? 'async ' : '') . 'src="' . $sAsset . '"></script>';
            }
        }

        // --------------------------------------------------------------------------

        //  Inline CSS
        if (!empty($this->aCssInline) && ($sType === static::TYPE_CSS_INLINE || $sType === static::TYPE_ALL)) {

            $aOut[] = '<style type="text/css">';
            foreach ($this->aCssInline as $sAsset) {
                if ($sAsset instanceof \Closure) {
                    $aOut[] = $sAsset();
                } else {
                    $aOut[] = preg_replace('/<\/?style.*?>/si', '', $sAsset);
                }
            }
            $aOut[] = '</style>';
        }

        // --------------------------------------------------------------------------

        //  Inline JS (Header)
        if (!empty($this->aJsInlineHeader) && ($sType === static::TYPE_JS_INLINE_HEADER || $sType === static::TYPE_ALL)) {
            $aOut[] = '<script>';
            foreach ($this->aJsInlineHeader as $sAsset) {
                if ($sAsset instanceof \Closure) {
                    $aOut[] = $sAsset();
                } else {
                    $aOut[] = preg_replace('/<\/?script.*?>/si', '', $sAsset);
                }
            }
            $aOut[] = '</script>';
        }

        // --------------------------------------------------------------------------

        //  Inline JS (Footer)
        if (!empty($this->aJsInlineFooter) && ($sType === static::TYPE_JS_INLINE_FOOTER || $sType === static::TYPE_ALL)) {
            $aOut[] = '<script>';
            foreach ($this->aJsInlineFooter as $sAsset) {
                if ($sAsset instanceof \Closure) {
                    $aOut[] = $sAsset();
                } else {
                    $aOut[] = preg_replace('/<\/?script.*?>/si', '', $sAsset);
                }
            }
            $aOut[] = '</script>';
        }

        // --------------------------------------------------------------------------

        if ($bOutput) {
            echo implode("\n", $aOut);
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles global assets
     *
     * @return $this
     * @throws AssetException
     * @throws NailsException
     */
    public function compileGlobalData(): self
    {
        $aVariables = [
            'ENVIRONMENT' => Environment::get(),
            'SITE_URL'    => siteUrl('', Functions::isPageSecure()),
            'NAILS'       => (object) [
                'URL'  => Config::get('NAILS_ASSETS_URL'),
                'LANG' => (object) [],
                'USER' => (object) [
                    'ID'    => function_exists('activeUser') ? ((int) activeUser('id') ?: null) : null,
                    'FNAME' => function_exists('activeUser') ? activeUser('first_name') : null,
                    'LNAME' => function_exists('activeUser') ? activeUser('last_name') : null,
                    'EMAIL' => function_exists('activeUser') ? activeUser('email') : null,
                ],
            ],
        ];

        foreach ($aVariables as $sKey => $mValue) {
            $this->inline('window.' . $sKey . ' = ' . json_encode($mValue) . ';', static::TYPE_JS, static::JS_LOCATION_HEADER);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an inline asset
     *
     * @param string|\Closure $sScript     The inline asset to load, wrap in script tags for JS, or style tags for CSS
     * @param string          $sForceType  Force a particular type of asset (i.e. JS-INLINE or CSS-INLINE)
     * @param string          $sJsLocation Where the inline JS should appear, accepts FOOTER or HEADER
     *
     * @return object
     */
    public function inline($sScript = null, $sForceType = null, $sJsLocation = self::JS_LOCATION_FOOTER)
    {
        if (!empty($sScript)) {

            $sJsLocation = strtoupper($sJsLocation);
            if (!in_array($sJsLocation, [static::JS_LOCATION_FOOTER, static::JS_LOCATION_HEADER])) {
                throw new AssetException(sprintf(
                    '"%s" is not a valid inline asset location value.',
                    $sJsLocation
                ));
            }

            if ($sScript instanceof \Closure && empty($sForceType)) {
                throw new NailsException(
                    'Type must be specified when passing a closure.'
                );
            }

            $sHash = $sScript instanceof \Closure ? md5(uniqid('inline-closure-')) : md5($sScript);
            $sType = $this->determineType($sScript, $sForceType);

            switch ($sType) {

                case static::TYPE_CSS_INLINE:
                case static::TYPE_CSS:
                    $this->aCssInline['INLINE-CSS-' . $sHash] = $sScript;
                    break;

                case static::TYPE_JS_INLINE:
                case static::TYPE_JS:
                    if ($sJsLocation === static::JS_LOCATION_FOOTER) {
                        $this->aJsInlineFooter['INLINE-JS-' . $sHash] = $sScript;
                    } else {
                        $this->aJsInlineHeader['INLINE-JS-' . $sHash] = $sScript;
                    }
                    break;

                case static::TYPE_JS_INLINE_HEADER:
                case static::TYPE_JS_INLINE_FOOTER:
                    if ($sType === static::TYPE_JS_INLINE_FOOTER) {
                        $this->aJsInlineFooter['INLINE-JS-' . $sHash] = $sScript;
                    } else {
                        $this->aJsInlineHeader['INLINE-JS-' . $sHash] = $sScript;
                    }
                    break;
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset from a module's asset directory
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @param bool   $bAsync     Whether to load the asset asynchronously
     * @param mixed  $mModule    The module to load from
     *
     * @return void
     */
    protected function loadModule($sAsset, $sForceType, bool $bAsync, $mModule)
    {
        if (is_array($mModule)) {
            $sModule   = !empty($mModule[0]) ? $mModule[0] : null;
            $sLocation = !empty($mModule[1]) ? $mModule[1] : null;
        } else {
            $sModule   = $mModule;
            $sLocation = null;
        }

        $sType = $this->determineType($sAsset, $sForceType);
        $sKey  = 'MODULE-' . $sModule . '-' . $sAsset;

        switch ($sType) {

            case static::TYPE_CSS:
                $this->aCss[$sKey] = $this->addCacheBuster($this->sBaseModuleUrl . $sModule . '/assets/css/' . $sAsset);
                break;

            case static::TYPE_JS:
            case static::TYPE_JS_FOOTER:
                $this->aJs[$sKey] = [
                    $this->addCacheBuster($this->sBaseModuleUrl . $sModule . '/assets/js/' . $sAsset),
                    $bAsync,
                ];
                break;

            case static::TYPE_JS_HEADER:
                $this->aJsHeader[$sKey] = [
                    $this->addCacheBuster($this->sBaseModuleUrl . $sModule . '/assets/js/' . $sAsset),
                    $bAsync,
                ];
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Determines the type of asset being loaded
     *
     * @param string $sAsset     The asset being loaded
     * @param string $sForceType Forces a particular type (accepts values CSS, JS, CSS-INLINE or JS-INLINE)
     *
     * @return string
     */
    protected function determineType($sAsset, $sForceType = null)
    {
        //  Override if nessecary
        if (!empty($sForceType)) {
            return $sForceType;
        }

        // --------------------------------------------------------------------------

        //  Look for <style></style>
        if (preg_match('/^<style.*?>.*?<\/style>$/si', $sAsset)) {
            return static::TYPE_CSS_INLINE;
        }

        // --------------------------------------------------------------------------

        //  Look for <script></script>
        if (preg_match('/^<script.*?>.*?<\/script>$/si', $sAsset)) {
            return static::TYPE_JS_INLINE;
        }

        // --------------------------------------------------------------------------

        //  Look for .css
        if (substr($sAsset, strrpos($sAsset, '.')) === '.css') {
            return static::TYPE_CSS;
        }

        // --------------------------------------------------------------------------

        //  Look for .js
        if (substr($sAsset, strrpos($sAsset, '.')) === '.js') {
            return static::TYPE_JS;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the URL for the Nails assets module
     *
     * @return string
     */
    public function getNailsAssetUrl(): string
    {
        return \Nails\Config::get('NAILS_ASSETS_URL');
    }

    // --------------------------------------------------------------------------

    /**
     * Builds the URL, with base URL and cachebuster
     *
     * @param string $sItem The item being loaded
     *
     * @return string
     */
    public function buildUrl(string $sItem): string
    {
        return $this->addCacheBuster($this->getBaseUrl() . $sItem);
    }

    // --------------------------------------------------------------------------

    /**
     * Appends the cacheBuster string to the asset name, accounts for existing query strings
     *
     * @param string $sAsset The asset's url to append
     */
    protected function addCacheBuster(string $sAsset): string
    {
        $sCacheBuster = $this->getCacheBuster();

        if ($sCacheBuster) {

            $aParsedUrl = parse_url($sAsset);

            return empty($aParsedUrl['query'])
                ? $sAsset . '?' . $this->getCacheBuster()
                : $sAsset . '&' . $this->getCacheBuster();
        }

        return $sAsset;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the base URL appropriate for the page
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return Functions::isPageSecure()
            ? $this->sBaseUrlSecure
            : $this->sBaseUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current cachebuster
     *
     * @return string|null
     */
    public function getCacheBuster(): ?string
    {
        return trim($this->sCacheBuster)
            ? 'revision=' . trim($this->sCacheBuster)
            : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset from the app's asset directory
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @param bool   $bAsync     Whether to load the asset asynchronously
     *
     * @return void
     */
    protected function loadApp($sAsset, $sForceType, bool $bAsync)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case static::TYPE_CSS:
                $this->aCss['APP-' . $sAsset] = $this->buildUrl($this->sCssDir . $sAsset);
                break;

            case static::TYPE_JS:
            case static::TYPE_JS_FOOTER:
                $this->aJs['APP-' . $sAsset] = [$this->buildUrl($this->sJsDir . $sAsset), $bAsync];
                break;

            case static::TYPE_JS_HEADER:
                $this->aJsHeader['APP-' . $sAsset] = [$this->buildUrl($this->sJsDir . $sAsset), $bAsync];
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an asset from the app's asset directory
     *
     * @param string $sAsset     The asset to unload
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function unloadModule($sAsset, $sForceType, $sModule)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case static::TYPE_CSS:
                unset($this->aCss['MODULE-' . $sModule . '-' . $sAsset]);
                break;

            case static::TYPE_JS:
            case static::TYPE_JS_FOOTER:
                unset($this->aJs['MODULE-' . $sModule . '-' . $sAsset]);
                break;

            case static::TYPE_JS_HEADER:
                unset($this->aJsHeader['MODULE-' . $sModule . '-' . $sAsset]);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an asset from the app's asset directory
     *
     * @param string $sAsset     The asset to unload
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function unloadApp($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case static::TYPE_CSS:
                unset($this->aCss['APP-' . $sAsset]);
                break;

            case static::TYPE_JS:
            case static::TYPE_JS_FOOTER:
                unset($this->aJs['APP-' . $sAsset]);
                break;

            case static::TYPE_JS_HEADER:
                unset($this->aJsHeader['APP-' . $sAsset]);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the CSS directory
     *
     * @return string
     */
    public function getCssDir(): string
    {
        return $this->sCssDir;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the JS directory
     *
     * @return string
     */
    public function getJsDir(): string
    {
        return $this->sJsDir;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the CriticalCss object
     *
     * @return CriticalCss
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function criticalCss(): CriticalCss
    {
        if (empty($this->oCriticalCss)) {
            $this->oCriticalCss = Factory::factory('AssetCriticalCss', null, $this);
        }

        return $this->oCriticalCss;
    }
}
