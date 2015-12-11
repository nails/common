<?php

/**
 * The class provides a convinient way to load assets
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Library;

use Nails\Factory;

class Asset
{
    protected $oCi;
    protected $aCcss;
    protected $aCcssInline;
    protected $aJs;
    protected $aJsInline;
    protected $sCacheBuster;
    protected $sBaseUrl;
    protected $sBaseUrlSecure;
    protected $sBowerDir;
    protected $sCssDir;
    protected $sJsDir;

    // --------------------------------------------------------------------------

    /**
     * Construct the library
     * @return  void
     **/
    public function __construct()
    {
        $this->oCi =& get_instance();
        Factory::helper('string');

        $this->aCss           = array();
        $this->aCssInline     = array();
        $this->aJs            = array();
        $this->aJsInline      = array();
        $this->sCacheBuster   = defined('DEPLOY_REVISION') ? DEPLOY_REVISION : '';
        $this->sBaseUrl       = defined('DEPLOY_ASSET_BASE_URL') ? DEPLOY_ASSET_BASE_URL : 'assets';
        $this->sBaseUrl       = site_url($this->sBaseUrl);
        $this->sBaseUrl       = addTrailingSlash($this->sBaseUrl);
        $this->sBaseUrlSecure = defined('DEPLOY_ASSET_BASE_URL_SECURE') ? DEPLOY_ASSET_BASE_URL_SECURE : 'assets';
        $this->sBaseUrlSecure = secure_site_url($this->sBaseUrlSecure);
        $this->sBaseUrlSecure = addTrailingSlash($this->sBaseUrlSecure);
        $this->sBowerDir      = defined('DEPLOY_ASSET_BOWER_DIR') ? DEPLOY_ASSET_BOWER_DIR : 'bower_components';
        $this->sBowerDir      = addTrailingSlash($this->sBowerDir);
        $this->sCssDir        = defined('DEPLOY_ASSET_CSS_DIR') ? DEPLOY_ASSET_CSS_DIR : 'css';
        $this->sCssDir        = addTrailingSlash($this->sCssDir);
        $this->sJsDir         = defined('DEPLOY_ASSET_JS_DIR') ? DEPLOY_ASSET_CJS_DIR : 'js';
        $this->sJsDir         = addTrailingSlash($this->sJsDir);

    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset
     * @param  mixed  $mAssets        The asset to load, can be an array or a string
     * @param  string $sAssetLocation The asset's location
     * @param  string $sForceType     The asset's file type (e.g., JS or CSS)
     * @return object
     */
    public function load($mAssets, $sAssetLocation = 'APP', $sForceType = null)
    {
        //  Cast as an array
        $aAssets = (array) $mAssets;

        // --------------------------------------------------------------------------

        //  Backwards compatibility
        $sAssetLocation = $sAssetLocation === true ? 'NAILS' : $sAssetLocation;

        // --------------------------------------------------------------------------

        switch (strtoupper($sAssetLocation)) {

            case 'NAILS-BOWER':

                $sAssetTypeMethod = 'loadNailsBower';
                break;

            case 'NAILS-PACKAGE':

                $sAssetTypeMethod = 'loadNailsPackage';
                break;

            case 'NAILS':

                $sAssetTypeMethod = 'loadNails';
                break;

            case 'APP-BOWER':
            case 'BOWER':

                $sAssetTypeMethod = 'loadAppBower';
                break;

            case 'APP-PACKAGE':
            case 'PACKAGE':

                $sAssetTypeMethod = 'loadAppPackage';
                break;

            case 'APP':
            default:

                $sAssetTypeMethod = 'loadApp';
                break;
        }

        // --------------------------------------------------------------------------

        foreach ($aAssets as $sAsset) {

            if (preg_match('#^https?://#', $sAsset)) {

                $this->loadUrl($sAsset, $sForceType);

            } elseif (substr($sAsset, 0, 0) == '/') {

                $this->loadAbsolute(substr($sAsset, 1), $sForceType);

            } else {

                $this->{$sAssetTypeMethod}($sAsset, $sForceType);
            }
        }

        // --------------------------------------------------------------------------

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset supplied as a URL
     * @param  string $sAsset     The asset to load
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
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
     * @param  string $sAsset     The asset to load
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function loadAbsolute($sAsset, $sForceType)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':

                $this->aCss['ABSOLUTE-' . $sAsset] = $this->sBaseUrl . $sAsset;
                break;

            case 'JS':

                $this->aJs['ABSOLUTE-' . $sAsset] = $this->sBaseUrl . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset from the Nails asset module
     * @param  string $sAsset     The asset to load
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function loadNails($sAsset, $sForceType = null)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':

                $this->aCss['NAILS-' . $sAsset] = NAILS_ASSETS_URL . $this->sCssDir . $sAsset;
                break;

            case 'JS':

                $this->aJs['NAILS-' . $sAsset] = NAILS_ASSETS_URL . $this->sJsDir . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a Bower asset from the NAils asset module's bower_components directory
     * @param  string $sAsset     The asset to load
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function loadNailsBower($sAsset, $sForceType = null)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':

                $this->aCss['NAILS-BOWER-' . $sAsset] = NAILS_ASSETS_URL . $this->sBowerDir . $sAsset;
                break;

            case 'JS':

                $this->aJs['NAILS-BOWER-' . $sAsset] = NAILS_ASSETS_URL . $this->sBowerDir . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a Nails package asset (as a relative url from NAILS_ASSETS_URL . 'packages/')
     * @param  string $sAsset     The asset to load
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function loadNailsPackage($sAsset, $sForceType = null)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':

                $this->aCss['NAILS-PACKAGE-' . $sAsset] = NAILS_ASSETS_URL . 'packages/' . $sAsset;
                break;

            case 'JS':

                $this->aJs['NAILS-PACKAGE-' . $sAsset] = NAILS_ASSETS_URL . 'packages/' . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a Bower asset from the app's bower_components directory
     * @param  string $sAsset     The asset to load
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function loadAppBower($sAsset, $sForceType = null)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':

                $this->aCss['APP-BOWER-' . $sAsset] = $this->sBaseUrl . $this->sBowerDir . $sAsset;
                break;

            case 'JS':

                $this->aJs['APP-BOWER-' . $sAsset] = $this->sBaseUrl . $this->sBowerDir . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an App package asset (as a relative url from 'packages/')
     * @param  string $sAsset     The asset to load
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function loadAppPackage($sAsset, $sForceType = null)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':

                $this->aCss['APP-PACKAGE-' . $sAsset] = $this->sBaseUrl . 'packages/' . $sAsset;
                break;

            case 'JS':

                $this->aJs['APP-PACKAGE-' . $sAsset] = $this->sBaseUrl . 'packages/' . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads an asset from the app's asset directory
     * @param  string $sAsset     The asset to load
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function loadApp($sAsset, $sForceType = null)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':

                $this->aCss['APP-' . $sAsset] = $this->sBaseUrl . $this->sCssDir . $sAsset;
                break;

            case 'JS':

                $this->aJs['APP-' . $sAsset] = $this->sBaseUrl . $this->sJsDir . $sAsset;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an asset
     * @param  mixed  $mAssets    The asset to unload, can be an array or a string
     * @param  string $sAssetType The asset's type
     * @param  string $sForceType The asset's file type (e.g., JS or CSS)
     * @return object
     */
    public function unload($mAssets, $sAssetType = 'APP', $sForceType = null)
    {
        //  Cast as an array
        $aAssets = (array) $mAssets;

        // --------------------------------------------------------------------------

        //  Backwards compatibility
        $sAssetType = $sAssetType === true ? 'NAILS' : $sAssetType;

        // --------------------------------------------------------------------------

        switch (strtoupper($sAssetType)) {

            case 'NAILS-BOWER':

                $sAssetTypeMethod = 'unloadNailsBower';
                break;

            case 'NAILS-PACKAGE':

                $sAssetTypeMethod = 'unloadNailsPackage';
                break;

            case 'NAILS':

                $sAssetTypeMethod = 'unloadNails';
                break;

            case 'APP-BOWER':
            case 'BOWER':

                $sAssetTypeMethod = 'unloadAppBower';
                break;

            case 'APP-PACKAGE':
            case 'PACKAGE':

                $sAssetTypeMethod = 'unloadAppPackage';
                break;

            case 'APP':
            default:

                $sAssetTypeMethod = 'unloadApp';
                break;
        }

        // --------------------------------------------------------------------------

        foreach ($aAssets as $sAsset) {

            if (preg_match('#^https?://#', $sAsset)) {

                $this->unloadUrl($sAsset, $sForceType);

            } elseif (substr($sAsset, 0, 0) == '/') {

                $this->unloadAbsolute($sAsset, $sForceType);

            } else {

                $this->{$sAssetTypeMethod}($sAsset, $sForceType);
            }
        }

        // --------------------------------------------------------------------------

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an asset supplied as a URL
     * @param  string $sAsset     The asset to unload
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function unloadUrl($sAsset, $sForceType = null)
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
     * @param  string $sAsset     The asset to unload
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function unloadAbsolute($sAsset, $sForceType = null)
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
     * @param  string $sAsset     The asset to unload
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function unloadNails($sAsset, $sForceType = null)
    {
        $sType = $this->determineType($sAsset, $sForceType);

        switch ($sType) {

            case 'CSS':

                unset($this->aCss['NAILS-' . $sAsset]);
                break;

            case 'JS':

                unset($this->aJs['NAILS-' . $sAsset]);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a Bower asset from the Nails asset module's bower_components directory
     * @param  string $sAsset     The asset to unload
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function unloadNailsBower($sAsset, $sForceType = null)
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
     * Unloads a Nails package asset (as a relative url from NAILS_ASSETS_URL . 'packages/')
     * @param  string $sAsset     The asset to unload
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function unloadNailsPackage($sAsset, $sForceType = null)
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
     * @param  string $sAsset     The asset to unload
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function unloadAppBower($sAsset, $sForceType = null)
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
     * @param  string $sAsset     The asset to load
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function unloadAppPackage($sAsset, $sForceType = null)
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
     * @param  string $sAsset     The asset to unload
     * @param  string $sForceType Force a particular type of asset (i.e. JS or CSS)
     * @return void
     */
    protected function unloadApp($sAsset, $sForceType = null)
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
     * Loads an inline asset
     * @param  string $sScript    The inline asset to load, wrap in <script> tags for JS, or <style> tags for CSS
     * @param  string $sForceType Force a particular type of asset (i.e. JS-INLINE or CSS-INLINE)
     * @return object
     */
    public function inline($sScript = null, $sForceType = null)
    {
        if (!empty($sScript)) {

            $sType = $this->determineType($sScript, $sForceType);

            switch ($sType) {

                case 'CSS-INLINE':
                case 'CSS':

                    $this->aCssInline['INLINE-CSS-' . md5($sScript)] = $sScript;
                    break;

                case 'JS-INLINE':
                case 'JS':

                    $this->aJsInline['INLINE-JS-' . md5($sScript)] = $sScript;
                    break;
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Unloads an inline asset
     * @param  string $sScript    The inline asset to load, wrap in <script> tags for JS, or <style> tags for CSS
     * @param  string $sForceType Force a particular type of asset (i.e. JS-INLINE or CSS-INLINE)
     * @return void
     */
    public function unloadInline($sScript = null, $sForceType = null)
    {
        if (!empty($sScript)) {

            $sType = $this->determineType($sScript, $sForceType);

            switch ($sType) {

                case 'CSS-INLINE':
                case 'CSS':

                    unset($this->aCssInline['INLINE-CSS-' . md5($sScript)]);
                    break;

                case 'JS-INLINE':
                case 'JS':

                    unset($this->aJsInline['INLINE-JS-' . md5($sScript)]);
                    break;
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a set of assets
     * @param  string $sLibrary The library to load
     * @return object
     */
    public function library($sLibrary)
    {
        switch (strtoupper($sLibrary)) {

            case 'CKEDITOR':

                $this->load(
                    array(
                        'ckeditor/ckeditor.js',
                        'ckeditor/adapters/jquery.js'
                    ),
                    'NAILS-BOWER'
                );
                break;

            case 'JQUERYUI':

                $this->load(
                    array(
                        'jquery-ui/jquery-ui.min.js',
                        'jquery-ui/themes/smoothness/jquery-ui.min.css',
                        'jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
                        'jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.css'
                    ),
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
                    array(
                        'mustache.js/mustache.js',
                        'jquery-serialize-object/dist/jquery.serialize-object.min.js'
                    ),
                    'NAILS-BOWER'
                );
                $this->load(
                    array(
                        'nails.admin.module.cms.css',
                        'nails.admin.cms.widgeteditor.min.js'
                    ),
                    'NAILS'
                );
                break;

            case 'UPLOADIFY':

                $this->load(
                    array(
                        'uploadify/uploadify.css',
                        'uploadify/jquery.uploadify.min.js'
                    ),
                    'NAILS-PACKAGE'
                );
                break;

            case 'CHOSEN':

                $this->load(
                    array(
                        'chosen/chosen.min.css',
                        'chosen/chosen.jquery.min.js'
                    ),
                    'NAILS-BOWER'
                );
                break;

            case 'SELECT2':

                $this->load(
                    array(
                        'select2/select2.css',
                        'select2/select2.min.js'
                    ),
                    'NAILS-BOWER'
                );
                break;

            case 'ZEROCLIPBOARD':

                $this->load(
                    array(
                        'zeroclipboard/dist/ZeroClipboard.min.js',
                    ),
                    'NAILS-BOWER'
                );
                break;

            case 'KNOCKOUT':

                $this->load(
                    array(
                        'knockout/dist/knockout.js',
                    ),
                    'NAILS-BOWER'
                );
                break;

            case 'MUSTACHE':

                $this->load(
                    array(
                        'mustache.js/mustache.js',
                    ),
                    'NAILS-BOWER'
                );
                break;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Clears all loaded assets
     * @return object
     */
    public function clear()
    {
        $this->aCss       = array();
        $this->aCssInline = array();
        $this->aJs        = array();
        $this->aJsInline  = array();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an object containing all loaded assets, useful for debugging.
     * @return stdClass
     */
    public function getLoaded()
    {
        $oLoaded            = new \stdClass();
        $oLoaded->css       = $this->aCss;
        $oLoaded->cssInline = $this->aCssInline;
        $oLoaded->js        = $this->aJs;
        $oLoaded->jsInline  = $this->aJsInline;

        return $oLoaded;
    }

    // --------------------------------------------------------------------------

    /**
     * Output the assets for HTML
     * @param  string  $sType   The type of asset to output
     * @param  boolean $bOutput Whether to output to the browser or to return as a string
     * @return string
     */
    public function output($sType = 'ALL', $bOutput = true)
    {
        $aOut  = array();
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
                $aOut[] = '<script type="text/javascript" src="' . $sAsset . '"></script>';
            }
        }

        // --------------------------------------------------------------------------

        //  Inline CSS
        if (!empty($this->aCssInline) && ($sType == 'CSS-INLINE' || $sType == 'ALL')) {

            $aOut[] = '<style type="text/css">';
            foreach ($this->aCssInline as $sAsset) {

                $aOut[] = preg_replace('/<\/?style.*?>/si', '', $sAsset);
            }
            $aOut[] = '</style>';
        }

        // --------------------------------------------------------------------------

        //  Inline JS
        if (!empty($this->aJsInline) && ($sType == 'JS-INLINE' || $sType == 'ALL')) {

            $aOut[] = '<script type="text/javascript">';
            foreach ($this->aJsInline as $sAsset) {

                $aOut[] = preg_replace('/<\/?script.*?>/si', '', $sAsset);
            }
            $aOut[] = '</script>';
        }

        // --------------------------------------------------------------------------

        //  Force SSL for assets if page is secure
        if (isPageSecure()) {
            foreach ($aOut as &$sLine) {
                $sLine = str_replace($this->sBaseUrl, $this->sBaseUrlSecure, $sLine);
            }
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
     * @param  string $sAsset     The asset being loaded
     * @param  string $sForceType Forces a particular type (accepts values CSS, JS, CSS-INLINE or JS-INLINE)
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
