<?php

/**
 * This file provides functions used internally by Nails
 *
 * @package     Nails
 * @subpackage  common
 * @category    helper
 * @author      Nails Dev Team
 * @link
 */

//  @todo move these into the factory
$GLOBALS['NAILS'] = array();

if (!function_exists('_NAILS_GET_COMPONENTS')) {

    /**
     * Fetch all the Nails components which are installed
     * @return array
     */
    function _NAILS_GET_COMPONENTS()
    {
        /**
         * If we already know which Nails components are available then return that, save
         * the [small] overhead of working out the modules again and again.
         */

        if (isset($GLOBALS['NAILS']['COMPONENTS'])) {

            return $GLOBALS['NAILS']['COMPONENTS'];
        }

        // --------------------------------------------------------------------------

        $sComposer = @file_get_contents(FCPATH . 'vendor/composer/installed.json');

        if (empty($sComposer)) {

            $sMessage = 'Failed to discover potential modules; could not load composer/installed.json';

            if (function_exists('_NAILS_ERROR')) {

                _NAILS_ERROR($sMessage);

            } else {

                echo 'ERROR: ' . $sMessage;
                exit(0);
            }
        }

        $aComposer = @json_decode($sComposer);

        if (empty($aComposer)) {

            $sMessage = 'Failed to discover potential modules; could not decode composer/installed.json';

            if (function_exists('_NAILS_ERROR')) {

                _NAILS_ERROR($sMessage);

            } else {

                echo 'ERROR: ' . $sMessage;
                exit(0);
            }
        }

        $aOut = array();

        foreach ($aComposer as $oPackage) {

            if (isset($oPackage->extra->nails)) {

                $oTemp              = new stdClass();
                $oTemp->name        = $oPackage->name;
                $oTemp->description = $oPackage->description;
                $oTemp->homepage    = $oPackage->homepage;
                $oTemp->authors     = $oPackage->authors;
                $oTemp->path        = FCPATH . 'vendor/' . $oPackage->name . '/';
                $oTemp->moduleName  = !empty($oPackage->extra->nails->moduleName) ? $oPackage->extra->nails->moduleName : null;
                $oTemp->moduleData  = !empty($oPackage->extra->nails->moduleData) ? $oPackage->extra->nails->moduleData : null;
                $oTemp->type        = !empty($oPackage->extra->nails->type) ? $oPackage->extra->nails->type : null;
                $oTemp->subType     = !empty($oPackage->extra->nails->subType) ? $oPackage->extra->nails->subType : null;
                $oTemp->forModule   = !empty($oPackage->extra->nails->forModule) ? $oPackage->extra->nails->forModule : null;
                $oTemp->autoload    = !empty($oPackage->extra->nails->autoload) ? $oPackage->extra->nails->autoload : null;
                $oTemp->fromApp     = false;

                $aOut[] = $oTemp;
            }
        }

        // --------------------------------------------------------------------------

        //  Get App components, too
        $sAppPath = FCPATH . 'application/components/';

        if (is_dir($sAppPath)) {
            $aDirs = scandir($sAppPath);
            foreach ($aDirs as $sDirName) {
                if ($sDirName == '.' || $sDirName == '..') {
                    continue;
                }

                $sConfigPath = $sAppPath . $sDirName . '/config.json';
                if (is_file($sConfigPath)) {
                    $sConfig = file_get_contents($sConfigPath);
                    $oConfig = json_decode($sConfig);

                    if (!empty($oConfig)) {

                        $oTemp              = new stdClass();
                        $oTemp->name        = 'app/' . $sDirName;
                        $oTemp->description = !empty($oConfig->description) ? $oConfig->description : null;
                        $oTemp->homepage    = !empty($oConfig->homepage) ? $oConfig->homepage : null;
                        $oTemp->authors     = !empty($oConfig->authors) ? $oConfig->authors : null;
                        $oTemp->path        = $sAppPath . $sDirName;
                        $oTemp->moduleName  = null;
                        $oTemp->moduleData  = !empty($oConfig->moduleData) ? $oConfig->moduleData : null;
                        $oTemp->type        = !empty($oConfig->type) ? $oConfig->type : null;
                        $oTemp->subType     = !empty($oConfig->subType) ? $oConfig->subType : null;
                        $oTemp->forModule   = !empty($oConfig->forModule) ? $oConfig->forModule : null;
                        $oTemp->autoload    = !empty($oConfig->autoload) ? $oConfig->autoload : null;
                        $oTemp->fromApp     = true;

                        $aOut[] = $oTemp;
                    }
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Save as a $GLOBAL for next time
        $GLOBALS['NAILS']['COMPONENTS'] = $aOut;

        // --------------------------------------------------------------------------

        return $aOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_COMPONENTS_OF_TYPE')) {

    /**
     * Fetches a type of component (e.e., modules, drivers or skins)
     * @return array
     */
    function _NAILS_GET_COMPONENTS_OF_TYPE($sType)
    {
        if (isset($GLOBALS['NAILS'][$sType])) {

            $aOut = $GLOBALS['NAILS'][$sType];

        } else {

            $aComponents = _NAILS_GET_COMPONENTS();
            $aOut        = array();

            // --------------------------------------------------------------------------

            foreach ($aComponents as $oComponent) {
                if ($oComponent->type == $sType) {
                    $aOut[] = $oComponent;
                }
            }

            // --------------------------------------------------------------------------

            $GLOBALS['NAILS'][$sType] = $aOut;
        }

        return $aOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_MODULES')) {

    /**
     * Fetch all the Nails modules which are installed
     * @return array
     */
    function _NAILS_GET_MODULES()
    {
        return _NAILS_GET_COMPONENTS_OF_TYPE('module');
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_SKINS')) {

    /**
     * Fetch Skins for a module, optionally filtered by subtype
     * @return array
     */
    function _NAILS_GET_SKINS($sModule, $sSubType = '')
    {
        $aSkins = _NAILS_GET_COMPONENTS_OF_TYPE('skin');
        $aOut   = array();

        foreach ($aSkins as $oSkin) {
            if ($oSkin->forModule == $sModule) {
                if (!empty($sSubType) && $sSubType == $oSkin->subType) {
                    $aOut[] = $oSkin;
                } elseif (empty($sSubType)) {
                    $aOut[] = $oSkin;
                }
            }
        }

        return $aOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_DRIVERS')) {

    /**
     * Fetch drivers for a module, optionally filtered by subtype
     * @return array
     */
    function _NAILS_GET_DRIVERS($sModule, $sSubType = '')
    {
        $aDrivers = _NAILS_GET_COMPONENTS_OF_TYPE('driver');
        $aOut     = array();

        foreach ($aDrivers as $oDriver) {
            if ($oDriver->forModule == $sModule) {
                if (!empty($sSubType) && $sSubType == $oDriver->subType) {
                    $aOut[] = $oDriver;
                } elseif (empty($sSubType)) {
                    $aOut[] = $oDriver;
                }
            }
        }

        return $aOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_MIN_PHP_VERSION')) {

    /**
     * Determines the minimum supported PHP version as per enabled modules
     * @return string
     */
    function _NAILS_MIN_PHP_VERSION()
    {
        //  First check nailsapp/nails
        $composer   = @file_get_contents('vendor/nailsapp/nails/composer.json');
        $composer   = @json_decode($composer);
        $minVersion = isset($composer->extra->nails->minPhpVersion) ? $composer->extra->nails->minPhpVersion : 0;

        //  Next, check nailsapp/common
        $composer         = @file_get_contents('vendor/nailsapp/nails/composer.json');
        $composer   = @json_decode($composer);
        $minVersionCommon = isset($composer->extra->nails->minPhpVersion) ? $composer->extra->nails->minPhpVersion : 0;

        if (version_compare($minVersionCommon, $minVersion, '>')) {

            $minVersion = $minVersionCommon;
        }

        //  Now we check all the components
        $components = _NAILS_GET_COMPONENTS();

        foreach ($components as $component) {

            $composer            = @file_get_contents($component->path . 'composer.json');
            $composer            = @json_decode($composer);
            $minVersionComponent = isset($composer->extra->nails->minPhpVersion) ? $composer->extra->nails->minPhpVersion : 0;

            if (version_compare($minVersionComponent, $minVersion, '>')) {

                $minVersion = $minVersionComponent;
            }
        }

        return $minVersion;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('nailsFactory')) {

    /**
     * A route to the Nails Factory
     * @return miixed
     */
    function nailsFactory($sType, $sKey, $sModuleName = '')
    {
        switch (strtoupper($sType)) {

            case 'PROPERTY':

                return \Nails\Factory::property($sKey, $sModuleName);
                break;

            case 'SERVICE':

                return \Nails\Factory::service($sKey, $sModuleName);
                break;

            case 'MODEL':

                return \Nails\Factory::model($sKey, $sModuleName);
                break;

            case 'FACTORY':

                return \Nails\Factory::factory($sKey, $sModuleName);
                break;

            case 'HELPER':

                return \Nails\Factory::helper($sKey, $sModuleName);
                break;

            default:

                throw new \NailsCommon\Exception\FactoryException('"' . $sType . '" is not valid', 1);
                break;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isModuleEnabled')) {

    /**
     * Handy way of determining whether a module is available
     * @param  string $moduleName The name of the module to check
     * @return boolean
     */
    function isModuleEnabled($moduleName)
    {
        $modules = _NAILS_GET_MODULES();

        foreach ($modules as $module) {

            if ($moduleName == $module->name) {

                return true;
            }
        }

        return false;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getControllerData')) {

    /**
     * $NAILS_CONTROLLER_DATA is an array populated by $this->data in controllers,
     * this function provides an easy interface to this array when it's not in scope.
     * @return  array   A reference to $NAILS_CONTROLLER_DATA
     **/
    function &getControllerData()
    {
        global $NAILS_CONTROLLER_DATA;
        return $NAILS_CONTROLLER_DATA;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('setControllerData')) {

    /**
     * $NAILS_CONTROLLER_DATA is an array populated by $this->data in controllers,
     * this function provides an easy interface to populate this array when it's not
     * in scope.
     * @param string $key The key to populate
     * @param mixed $value The value to assign
     * @return  void
     **/
    function setControllerData($key, $value)
    {
        global $NAILS_CONTROLLER_DATA;
        $NAILS_CONTROLLER_DATA[$key] = $value;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getDomainFromUrl')) {

    /**
     * Attempts to get the top level part of a URL (i.e example.tld from sub.domains.example.tld).
     * Hat tip: http://uk1.php.net/parse_url#104874
     * BUG: 2 character TLD's break this
     * @todo: Try and fix this bug
     * @param  string $sUrl The URL to analyse
     * @return mixed        string on success, false on failure
     */
    function getDomainFromUrl($sUrl)
    {
        $sDomain  = parse_url($sUrl, PHP_URL_HOST);
        $bits = explode('.', $sDomain);
        $idz = count($bits);
        $idz -=3;

        if (!isset($bits[($idz+2)])) {

            $out = false;

        } elseif (strlen($bits[($idz+2)]) == 2 && isset($bits[($idz+2)])) {

            $out   = array();
            $out[] = !empty($bits[$idz])   ? $bits[$idz]   : false;
            $out[] = !empty($bits[$idz+1]) ? $bits[$idz+1] : false;
            $out[] = !empty($bits[$idz+2]) ? $bits[$idz+2] : false;

            $out = implode('.', array_filter($out));

        } elseif (strlen($bits[($idz+2)]) == 0) {

            $out   = array();
            $out[] = !empty($bits[$idz])   ? $bits[$idz]   : false;
            $out[] = !empty($bits[$idz+1]) ? $bits[$idz+1] : false;

            $out = implode('.', array_filter($out));

        } elseif (isset($bits[($idz+1)])) {

            $out   = array();
            $out[] = !empty($bits[$idz+1]) ? $bits[$idz+1] : false;
            $out[] = !empty($bits[$idz+2]) ? $bits[$idz+2] : false;

            $out = implode('.', array_filter($out));

        } else {

            $out = false;
        }

        return $out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getRelativePath')) {

    /**
     * Fetches the relative path between two directories
     * Hat tip: Thanks to Gordon for this one; http://stackoverflow.com/a/2638272/789224
     * @param  string $from Path 1
     * @param  string $to   Path 2
     * @return string
     */
    function getRelativePath($from, $to)
    {
        $from    = explode('/', $from);
        $to      = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {

            //  Find first non-matching dir
            if ($dir === $to[$depth]) {

                //  Ignore this directory
                array_shift($relPath);

            } else {

                //  Get number of remaining dirs to $from
                $remaining = count($from) - $depth;

                if ($remaining > 1) {

                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath   = array_pad($relPath, $padLength, '..');
                    break;

                } else {

                    $relPath[0] = './' . $relPath[0];
                }
            }
        }

        return implode('/', $relPath);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isPageSecure')) {

    /**
     * Detects whether the current page is secure or not
     *
     * @access  public
     * @param   string
     * @return  bool
     */
    function isPageSecure()
    {
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {

            //  Page is being served through HTTPS
            return true;

        } elseif (isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REQUEST_URI']) && SECURE_BASE_URL != BASE_URL) {

            //  Not being served through HTTPS, but does the URL of the page begin
            //  with SECURE_BASE_URL (when BASE_URL is different)

            $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

            if (preg_match('#^' . SECURE_BASE_URL . '.*#', $url)) {

                return true;

            } else {

                return false;
            }
        }

        // --------------------------------------------------------------------------

        //  Unknown, assume not
        return false;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('show_404')) {

    /**
     * Renders the 404 page, logging disabled by default.
     *
     * Note that the Exception class does log by default. Manual 404's are probably
     * a result of some other checking and not technically a 404 so shouldn't be
     * logged as one. )Actual_ 404's should continue to be logged however.
     *
     * @param  string  $page     The page which 404'd
     * @param  boolean $logError whether to log the error or not
     * @return void
     */
    function show_404($page = '', $logError = false)
    {
        $_error =& load_class('Exceptions', 'core');
        $_error->show_404($page, $logError);
        exit;
    }
}
