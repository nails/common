<?php

/**
 * The class provides a convenient way to load assets
 *
 * @package    Nails
 * @subpackage common
 * @category   Service
 * @author     Nails Dev Team
 */

//  @todo (Pablo - 2020-03-08) - Remove support for Bower

namespace Nails\Common\Service;

use Nails\Common\Exception\AssetException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Helper\Strings;
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
     * Where bower assets are stored
     *
     * @var string
     */
    protected $sBowerDir;

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
     * @param string $sBowerDir            Where bower assets are stored
     */
    public function __construct(
        string $sCacheBuster,
        string $sBaseUrl,
        string $sBaseUrlSecure,
        string $sBaseModuleUrl,
        string $sBaseModuleUrlSecure,
        string $sCssDir,
        string $sJsDir,
        string $sBowerDir
    ) {

        $this->sCacheBuster = $sCacheBuster;

        $this->setBaseUrls(
            $sBaseUrl,
            $sBaseModuleUrl,
            $sBaseUrlSecure,
            $sBaseModuleUrlSecure
        );

        $this->sCssDir   = Strings::addTrailingSlash($sCssDir);
        $this->sJsDir    = Strings::addTrailingSlash($sJsDir);
        $this->sBowerDir = Strings::addTrailingSlash($sBowerDir);
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

        $this->sBaseUrlSecure = $sBaseUrlSecure ?? preg_replace('/^http/', 'https', $this->sBaseUrl);
        $this->sBaseUrlSecure = Strings::addTrailingSlash($this->sBaseUrlSecure);

        $this->sBaseModuleUrlSecure = $sBaseModuleUrlSecure ?? preg_replace('/^http/', 'https', $this->sBaseModuleUrl);
        $this->sBaseModuleUrlSecure = Strings::addTrailingSlash($this->sBaseModuleUrlSecure);

        return $this;
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
     * Loads an asset
     *
     * @param mixed  $mAssets        The asset to load, can be an array or a string
     * @param string $sAssetLocation The asset's location
     * @param string $sForceType     The asset's file type (e.g., JS or CSS)
     *
     * @return $this
     */
    public function load($mAssets, $sAssetLocation = 'APP', $sForceType = null): self
    {
        //  Cast as an array
        $aAssets = (array) $mAssets;

        // --------------------------------------------------------------------------

        //  Backwards compatibility
        $sAssetLocation = $sAssetLocation === true ? 'NAILS' : $sAssetLocation;

        // --------------------------------------------------------------------------

        switch (strtoupper($sAssetLocation)) {

            case 'NAILS-BOWER':
                $sAssetLocationMethod = 'loadNailsBower';
                break;

            case 'NAILS-PACKAGE':
                $sAssetLocationMethod = 'loadNailsPackage';
                break;

            case 'NAILS':
                $sAssetLocationMethod = 'loadNails';
                break;

            case 'APP-BOWER':
            case 'BOWER':
                $sAssetLocationMethod = 'loadAppBower';
                break;

            case 'APP-PACKAGE':
            case 'PACKAGE':
                $sAssetLocationMethod = 'loadAppPackage';
                break;

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
                $this->loadUrl($sAsset, $sForceType);
            } elseif (substr($sAsset, 0, 0) == '/') {
                $this->loadAbsolute(substr($sAsset, 1), $sForceType);
            } else {
                $this->{$sAssetLocationMethod}($sAsset, $sForceType, $sAssetLocation);
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
     *
     * @return void
     */
    protected function loadUrl($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                $this->aCss['URL-' . $sAsset] = $sAsset;
                break;

            case 'JS':
                $this->aJs['URL-' . $sAsset] = $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset supplied as an absolute URL
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function loadAbsolute($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                $this->aCss['ABSOLUTE-' . $sAsset] = $this->getBaseUrl() . $sAsset;
                break;

            case 'JS':
                $this->aJs['ABSOLUTE-' . $sAsset] = $this->getBaseUrl() . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset from the Nails asset module
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function loadNails($sAsset, $sForceType)
    {
        $this->loadModule($sAsset, $sForceType, 'nails/module-asset');
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a Bower asset from the Nails asset module's bower_components directory
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function loadNailsBower($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                $this->aCss['NAILS-BOWER-' . $sAsset] = $this->getNailsAssetUrl() . $this->sBowerDir . $sAsset;
                break;

            case 'JS':
                $this->aJs['NAILS-BOWER-' . $sAsset] = $this->getNailsAssetUrl() . $this->sBowerDir . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a Nails package asset (as a relative url from $this->getNailsAssetUrl() . 'packages/')
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function loadNailsPackage($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                $this->aCss['NAILS-PACKAGE-' . $sAsset] = $this->getNailsAssetUrl() . 'packages/' . $sAsset;
                break;

            case 'JS':
                $this->aJs['NAILS-PACKAGE-' . $sAsset] = $this->getNailsAssetUrl() . 'packages/' . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a Bower asset from the app's bower_components directory
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function loadAppBower($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                $this->aCss['APP-BOWER-' . $sAsset] = $this->getBaseUrl() . $this->sBowerDir . $sAsset;
                break;

            case 'JS':
                $this->aJs['APP-BOWER-' . $sAsset] = $this->getBaseUrl() . $this->sBowerDir . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an App package asset (as a relative url from 'packages/')
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function loadAppPackage($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                $this->aCss['APP-PACKAGE-' . $sAsset] = $this->getBaseUrl() . 'packages/' . $sAsset;
                break;

            case 'JS':
                $this->aJs['APP-PACKAGE-' . $sAsset] = $this->getBaseUrl() . 'packages/' . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset from the app's asset directory
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function loadApp($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                $this->aCss['APP-' . $sAsset] = $this->getBaseUrl() . $this->sCssDir . $sAsset;
                break;

            case 'JS':
                $this->aJs['APP-' . $sAsset] = $this->getBaseUrl() . $this->sJsDir . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset from a module's asset directory
     *
     * @param string $sAsset     The asset to load
     * @param mixed  $mModule    The module to load from
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function loadModule($sAsset, $sForceType, $mModule)
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

            case 'CSS':
                if ($sLocation == 'BOWER') {
                    $this->aCss[$sKey] = $this->sBaseModuleUrl . $sModule . '/assets/bower_components/' . $sAsset;
                } else {
                    $this->aCss[$sKey] = $this->sBaseModuleUrl . $sModule . '/assets/css/' . $sAsset;
                }
                break;

            case 'JS':
                if ($sLocation == 'BOWER') {
                    $this->aJs[$sKey] = $this->sBaseModuleUrl . $sModule . '/assets/bower_components/' . $sAsset;
                } else {
                    $this->aJs[$sKey] = $this->sBaseModuleUrl . $sModule . '/assets/js/' . $sAsset;
                }
                break;
        }
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

        // --------------------------------------------------------------------------

        switch (strtoupper($sAssetLocation)) {

            case 'NAILS-BOWER':
                $sAssetLocationMethod = 'unloadNailsBower';
                break;

            case 'NAILS-PACKAGE':
                $sAssetLocationMethod = 'unloadNailsPackage';
                break;

            case 'NAILS':
                $sAssetLocationMethod = 'unloadNails';
                break;

            case 'APP-BOWER':
            case 'BOWER':
                $sAssetLocationMethod = 'unloadAppBower';
                break;

            case 'APP-PACKAGE':
            case 'PACKAGE':
                $sAssetLocationMethod = 'unloadAppPackage';
                break;

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
            } elseif (substr($sAsset, 0, 0) == '/') {
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

            case 'CSS':
                unset($this->aCss['URL-' . $sAsset]);
                break;

            case 'JS':
                unset($this->aJs['URL-' . $sAsset]);
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

            case 'CSS':
                unset($this->aCss['ABSOLUTE-' . $sAsset]);
                break;

            case 'JS':
                unset($this->aJs['ABSOLUTE-' . $sAsset]);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an asset from the Nails asset module
     *
     * @param string $sAsset     The asset to unload
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function unloadNails($sAsset, $sForceType)
    {
        $this->unloadModule($sAsset, $sForceType, 'nails/module-asset');
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a Bower asset from the Nails asset module's bower_components directory
     *
     * @param string $sAsset     The asset to unload
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function unloadNailsBower($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                unset($this->aCss['NAILS-BOWER-' . $sAsset]);
                break;

            case 'JS':
                unset($this->aJs['NAILS-BOWER-' . $sAsset]);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads a Nails package asset (as a relative url from $this->getNailsAssetUrl() . 'packages/')
     *
     * @param string $sAsset     The asset to unload
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function unloadNailsPackage($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                unset($this->aCss['NAILS-PACKAGE-' . $sAsset]);
                break;

            case 'JS':
                unset($this->aJs['NAILS-PACKAGE-' . $sAsset]);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads a Bower asset from the app's bower_components directory
     *
     * @param string $sAsset     The asset to unload
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function unloadAppBower($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                unset($this->aCss['APP-BOWER-' . $sAsset]);
                break;

            case 'JS':
                unset($this->aJs['APP-BOWER-' . $sAsset]);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an App package asset (as a relative url from 'packages/')
     *
     * @param string $sAsset     The asset to load
     * @param string $sForceType Force a particular type of asset (i.e. JS or CSS)
     *
     * @return void
     */
    protected function unloadAppPackage($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':
                unset($this->aCss['APP-PACKAGE-' . $sAsset]);
                break;

            case 'JS':
                unset($this->aJs['APP-PACKAGE-' . $sAsset]);
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

            case 'CSS':
                unset($this->aCss['APP-' . $sAsset]);
                break;

            case 'JS':
                unset($this->aJs['APP-' . $sAsset]);
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

            case 'CSS':
                unset($this->aCss['MODULE-' . $sModule . '-' . $sAsset]);
                break;

            case 'JS':
                unset($this->aJs['MODULE-' . $sModule . '-' . $sAsset]);
                break;
        }
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
    public function inline($sScript = null, $sForceType = null, $sJsLocation = 'FOOTER')
    {
        if (!empty($sScript)) {

            $sJsLocation = strtoupper($sJsLocation);
            if ($sJsLocation != 'FOOTER' && $sJsLocation != 'HEADER') {
                throw new AssetException(
                    '"' . $sJsLocation . '" is not a valid inline JS location value.',
                    1
                );
            }

            if ($sScript instanceof \Closure && empty($sForceType)) {
                throw new NailsException(
                    'Type must be specified when passing a closure.'
                );
            }

            $sHash = $sScript instanceof \Closure ? md5(uniqid('inline-closure-')) : md5($sScript);
            $sType = $this->determineType($sScript, $sForceType);

            switch ($sType) {

                case 'CSS-INLINE':
                case 'CSS':
                    $this->aCssInline['INLINE-CSS-' . $sHash] = $sScript;
                    break;

                case 'JS-INLINE':
                case 'JS':
                    if ($sJsLocation == 'FOOTER') {
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
     * Unloads an inline asset
     *
     * @param string $sScript     The inline asset to load, wrap in script tags for JS, or style tags for CSS
     * @param string $sForceType  Force a particular type of asset (i.e. JS-INLINE or CSS-INLINE)
     * @param string $sJsLocation Where the inline JS should appear, accepts FOOTER or HEADER
     *
     * @return void
     */
    public function unloadInline($sScript = null, $sForceType = null, $sJsLocation = 'FOOTER')
    {
        if (!empty($sScript)) {

            $sJsLocation = strtoupper($sJsLocation);
            if ($sJsLocation != 'FOOTER' && $sJsLocation != 'HEADER') {
                throw new AssetException(
                    '"' . $sJsLocation . '" is not a valid inline asset location value.',
                    1
                );
            }

            $sType = $this->determineType($sScript, $sForceType);

            switch ($sType) {

                case 'CSS-INLINE':
                case 'CSS':
                    unset($this->aCssInline['INLINE-CSS-' . md5($sScript)]);
                    break;

                case 'JS-INLINE':
                case 'JS':
                    if ($sJsLocation == 'FOOTER') {
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
     * @param string $sLibrary The library to load
     *
     * @return object
     */
    public function library($sLibrary)
    {
        switch (strtoupper($sLibrary)) {

            case 'CKEDITOR':
                $this->load(
                    [
                        'ckeditor/ckeditor.js',
                        'ckeditor/adapters/jquery.js',
                    ],
                    'NAILS-BOWER'
                );
                break;

            case 'JQUERYUI':
                $this->load(
                    [
                        'jquery-ui/jquery-ui.min.js',
                        'jquery-ui/themes/smoothness/jquery-ui.min.css',
                        'jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
                        'jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.css',
                    ],
                    'NAILS-BOWER'
                );
                $this->load(
                    'jquery.ui.extra.css',
                    'NAILS'
                );
                break;

            case 'CMSWIDGETEDITOR':
                $this->library('JQUERYUI');
                $this->load(
                    [
                        'mustache.js/mustache.js',
                        'jquery-serialize-object/dist/jquery.serialize-object.min.js',
                    ],
                    'NAILS-BOWER'
                );
                break;

            case 'UPLOADIFY':
                $this->load(
                    [
                        'uploadify/uploadify.css',
                        'uploadify/jquery.uploadify.min.js',
                    ],
                    'NAILS-PACKAGE'
                );
                break;

            case 'CHOSEN':
                $this->load(
                    [
                        'chosen/chosen.min.css',
                        'chosen/chosen.jquery.min.js',
                    ],
                    'NAILS-BOWER'
                );
                break;

            case 'SELECT2':
                $this->load(
                    [
                        'select2/select2.css',
                        'select2/select2.min.js',
                    ],
                    'NAILS-BOWER'
                );
                break;

            case 'ZEROCLIPBOARD':
                $this->load(
                    [
                        'zeroclipboard/dist/ZeroClipboard.min.js',
                    ],
                    'NAILS-BOWER'
                );
                break;

            case 'KNOCKOUT':
                $this->load(
                    [
                        'knockout/dist/knockout.js',
                    ],
                    'NAILS-BOWER'
                );
                break;

            case 'MUSTACHE':
                $this->load(
                    [
                        'mustache.js/mustache.js',
                    ],
                    'NAILS-BOWER'
                );
                break;

            case 'MOMENT':
                $this->load(
                    [
                        'moment/moment.js',
                    ],
                    'NAILS-BOWER'
                );
                break;
        }

        return $this;
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
        $oLoaded = (object) [
            'css'            => $this->aCss,
            'cssInline'      => $this->aCssInline,
            'js'             => $this->aJs,
            'jsInlineHeader' => $this->aJsInlineHeader,
            'jsInlineFooter' => $this->aJsInlineFooter,
        ];

        return $oLoaded;
    }

    // --------------------------------------------------------------------------

    /**
     * Output the assets for HTML
     *
     * @param string  $sType   The type of asset to output
     * @param boolean $bOutput Whether to output to the browser or to return as a string
     *
     * @return string
     */
    public function output($sType = 'ALL', $bOutput = true)
    {
        $aOut  = [];
        $sType = strtoupper($sType);

        //  Linked Stylesheets
        if (!empty($this->aCss) && ($sType == 'CSS' || $sType == 'ALL')) {
            foreach ($this->aCss as $sAsset) {
                $sAsset = $this->addCacheBuster($sAsset);
                $aOut[] = link_tag($sAsset);
            }
        }

        // --------------------------------------------------------------------------

        //  Linked JS
        if (!empty($this->aJs) && ($sType == 'JS' || $sType == 'ALL')) {
            foreach ($this->aJs as $sAsset) {
                $sAsset = $this->addCacheBuster($sAsset);
                $aOut[] = '<script src="' . $sAsset . '"></script>';
            }
        }

        // --------------------------------------------------------------------------

        //  Inline CSS
        if (!empty($this->aCssInline) && ($sType == 'CSS-INLINE' || $sType == 'ALL')) {

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
        if (!empty($this->aJsInlineHeader) && ($sType == 'JS-INLINE-HEADER' || $sType == 'ALL')) {
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
        if (!empty($this->aJsInlineFooter) && ($sType == 'JS-INLINE-FOOTER' || $sType == 'ALL')) {
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
     * Appends the cacheBuster string to the asset name, accounts for existing query strings
     *
     * @param string $sAsset The asset's url to append
     */
    protected function addCacheBuster($sAsset)
    {
        if ($this->sCacheBuster) {

            $aParsedUrl = parse_url($sAsset);

            if (empty($aParsedUrl['query'])) {
                $sAsset .= '?';
            } else {
                $sAsset .= '&';
            }

            $sAsset .= 'revision=' . $this->sCacheBuster;
        }

        return $sAsset;
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
            return 'CSS-INLINE';
        }

        // --------------------------------------------------------------------------

        //  Look for <script></script>
        if (preg_match('/^<script.*?>.*?<\/script>$/si', $sAsset)) {
            return 'JS-INLINE';
        }

        // --------------------------------------------------------------------------

        //  Look for .css
        if (substr($sAsset, strrpos($sAsset, '.')) == '.css') {
            return 'CSS';
        }

        // --------------------------------------------------------------------------

        //  Look for .js
        if (substr($sAsset, strrpos($sAsset, '.')) == '.js') {
            return 'JS';
        }
    }
}
