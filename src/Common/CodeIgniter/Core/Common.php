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

use Nails\Common\Exception\NailsException;
use Nails\Common\Service\ErrorHandler;
use Nails\Factory;
use Nails\Functions;

//  @todo move these into the factory
$GLOBALS['NAILS'] = [];

if (!function_exists('_NAILS_GET_APP')) {
    function _NAILS_GET_APP($bUseCache = true)
    {
        /**
         * If we have already fetched this data then don't get it again
         */

        if ($bUseCache && isset($GLOBALS['NAILS']['APP'])) {
            return $GLOBALS['NAILS']['APP'];
        }

        // --------------------------------------------------------------------------

        $sComposer = @file_get_contents(NAILS_APP_PATH . 'composer.json');

        if (empty($sComposer)) {
            ErrorHandler::die('Failed to get app configuration; could not load composer.json');
        }

        $oComposer = @json_decode($sComposer);

        if (empty($oComposer)) {
            ErrorHandler::die('Failed to get app configuration; could not decode composer.json');
        }

        $aComposer = (array) $oComposer;
        $aNails    = !empty($oComposer->extra->nails) ? (array) $oComposer->extra->nails : [];

        $oOut = (object) [
            'slug'          => getFromArray('name', $aComposer),
            'namespace'     => '\\App',
            'name'          => getFromArray('name', $aNails, getFromArray('name', $aComposer)),
            'description'   => getFromArray('description', $aNails, getFromArray('description', $aComposer)),
            'homepage'      => getFromArray('homepage', $aNails, getFromArray('homepage', $aComposer)),
            'authors'       => getFromArray('authors', $aNails, getFromArray('authors', $aComposer)),
            'path'          => NAILS_APP_PATH,
            'relativePath'  => './',
            'moduleName'    => getFromArray('moduleName', $aNails, ''),
            'data'          => getFromArray('data', $aNails, null),
            'autoload'      => getFromArray('autoload', $aNails, null),
            'minPhpVersion' => getFromArray('minPhpVersion', $aNails, null),
            'fromApp'       => true,
        ];

        // --------------------------------------------------------------------------

        //  Save as a $GLOBAL for next time
        if ($bUseCache) {
            $GLOBALS['NAILS']['APP'] = $oOut;
        }

        return $oOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_COMPONENTS')) {

    /**
     * Fetch all the Nails components which are installed
     *
     * @param boolean $bUseCache Whether to cache the result of the search
     *
     * @return array
     */
    function _NAILS_GET_COMPONENTS($bUseCache = true)
    {
        /**
         * If we already know which Nails components are available then return that, save
         * the [small] overhead of working out the modules again and again.
         */

        if ($bUseCache && isset($GLOBALS['NAILS']['COMPONENTS'])) {
            return $GLOBALS['NAILS']['COMPONENTS'];
        }

        // --------------------------------------------------------------------------

        $sComposer = @file_get_contents(NAILS_APP_PATH . 'vendor/composer/installed.json');

        if (empty($sComposer)) {
            ErrorHandler::die('Failed to discover potential modules; could not load composer/installed.json');
        }

        $aComposer = @json_decode($sComposer);

        if (empty($aComposer)) {
            ErrorHandler::die('Failed to discover potential modules; could not decode composer/installed.json');
        }

        $aOut = [];
        foreach ($aComposer as $oPackage) {
            if (isset($oPackage->extra->nails)) {
                $aPackage = (array) $oPackage;
                $aNails   = !empty($oPackage->extra->nails) ? (array) $oPackage->extra->nails : [];
                $aOut[]   = (object) [
                    'slug'          => getFromArray('name', $aPackage),
                    'namespace'     => getFromArray('namespace', $aNails),
                    'name'          => getFromArray('name', $aNails, getFromArray('name', $aPackage)),
                    'description'   => getFromArray('description', $aNails, getFromArray('description', $aPackage)),
                    'homepage'      => getFromArray('homepage', $aNails, getFromArray('homepage', $aPackage)),
                    'authors'       => getFromArray('authors', $aNails, getFromArray('authors', $aPackage)),
                    'path'          => NAILS_APP_PATH . 'vendor/' . $oPackage->name . '/',
                    'relativePath'  => 'vendor/' . $oPackage->name . '/',
                    'moduleName'    => getFromArray('moduleName', $aNails, ''),
                    'data'          => getFromArray('data', $aNails, null),
                    'type'          => getFromArray('type', $aNails, ''),
                    'subType'       => getFromArray('subType', $aNails, ''),
                    'forModule'     => getFromArray('forModule', $aNails, ''),
                    'autoload'      => getFromArray('autoload', $aNails, null),
                    'minPhpVersion' => getFromArray('minPhpVersion', $aNails, null),
                    'fromApp'       => false,
                ];
            }
        }

        // --------------------------------------------------------------------------

        //  Get App components, too
        $sAppPath = NAILS_APP_PATH . 'application/components/';

        if (is_dir($sAppPath)) {
            $aDirs = scandir($sAppPath);
            foreach ($aDirs as $sDirName) {
                if ($sDirName == '.' || $sDirName == '..') {
                    continue;
                }

                /**
                 * Load up config.json, This is basically exactly like composer.json, but
                 * the bit contained within extra->nails.
                 */

                $sConfigPath = $sAppPath . $sDirName . '/config.json';

                if (is_file($sConfigPath)) {

                    $sConfig = file_get_contents($sConfigPath);
                    $oConfig = json_decode($sConfig);

                    if (!empty($oConfig)) {
                        $aConfig = (array) $oConfig;
                        $aOut[]  = (object) [
                            'slug'          => 'app/' . $sDirName,
                            'namespace'     => getFromArray('namespace', $aConfig, null),
                            'name'          => getFromArray('name', $aConfig, 'app/' . $sDirName),
                            'description'   => getFromArray('description', $aConfig, ''),
                            'homepage'      => getFromArray('homepage', $aConfig, ''),
                            'authors'       => getFromArray('authors', $aConfig, []),
                            'path'          => $sAppPath . $sDirName . '/',
                            'relativePath'  => 'application/components/' . $sDirName . '/',
                            'moduleName'    => '',
                            'data'          => getFromArray('data', $aConfig, null),
                            'type'          => getFromArray('type', $aConfig, ''),
                            'subType'       => getFromArray('subType', $aConfig, ''),
                            'forModule'     => getFromArray('forModule', $aConfig, ''),
                            'autoload'      => getFromArray('autoload', $aConfig, null),
                            'minPhpVersion' => getFromArray('minPhpVersion', $aConfig, null),
                            'fromApp'       => true,
                        ];
                    }
                }
            }
        }

        // --------------------------------------------------------------------------

        uasort($aOut, function ($a, $b) {

            $a = (object) $a;
            $b = (object) $b;

            //  Equal?
            if ($a->slug == $b->slug) {
                return 0;
            }

            //  If $a is a prefix of $b then $a comes first
            $sPattern = '/^' . preg_quote($a->slug, '/') . '/';
            if (preg_match($sPattern, $b->slug)) {
                return -1;
            }

            //  Not equal, work out which takes precedence
            $_sort = [$a->slug, $b->slug];
            sort($_sort);

            return $_sort[0] == $a->slug ? -1 : 1;
        });

        $aOut = array_values($aOut);

        //  Pluck common out so it is always the first item
        for ($i = 0; $i < count($aOut); $i++) {
            if ($aOut[$i]->slug === 'nails/common') {
                break;
            }
        }

        $aExtracted = array_splice($aOut, $i, 1);
        $aOut       = array_values(array_merge($aExtracted, $aOut));

        // --------------------------------------------------------------------------

        //  Save as a $GLOBAL for next time
        if ($bUseCache) {
            $GLOBALS['NAILS']['COMPONENTS'] = $aOut;
        }

        // --------------------------------------------------------------------------

        return $aOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_COMPONENTS_BY_SLUG')) {

    /**
     * Fetches a component by it's slug
     *
     * @param string $sSlug The component's slug
     *
     * @return array
     */
    function _NAILS_GET_COMPONENTS_BY_SLUG($sSlug)
    {
        $aComponents = _NAILS_GET_COMPONENTS();

        foreach ($aComponents as $oComponent) {
            if ($oComponent->slug == $sSlug) {
                return $oComponent;
            }
        }

        return null;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_COMPONENTS_BY_TYPE')) {

    /**
     * Fetches a type of component (e.g., modules, drivers or skins)
     *
     * @param string $sType The component's type
     *
     * @return array
     */
    function _NAILS_GET_COMPONENTS_BY_TYPE($sType)
    {
        if (isset($GLOBALS['NAILS'][$sType])) {
            $aOut = $GLOBALS['NAILS'][$sType];
        } else {

            $aComponents = _NAILS_GET_COMPONENTS();
            $aOut        = [];

            foreach ($aComponents as $oComponent) {
                if ($oComponent->type == $sType) {
                    $aOut[] = $oComponent;
                }
            }

            $GLOBALS['NAILS'][$sType] = $aOut;
        }

        return $aOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_MODULES')) {

    /**
     * Fetch all the Nails modules which are installed
     *
     * @return array
     */
    function _NAILS_GET_MODULES()
    {
        return _NAILS_GET_COMPONENTS_BY_TYPE('module');
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_GET_SKINS')) {

    /**
     * Fetch Skins for a module, optionally filtered by subtype
     *
     * @param string $sModule  Restrict to particular component
     * @param string $sSubType Restrict by skin sub type
     *
     * @return array
     */
    function _NAILS_GET_SKINS($sModule, $sSubType = '')
    {
        $aSkins = _NAILS_GET_COMPONENTS_BY_TYPE('skin');
        $aOut   = [];

        foreach ($aSkins as $oSkin) {

            //  Provide a url field for the skin
            if (Functions::isPageSecure()) {
                $oSkin->url = SECURE_BASE_URL . $oSkin->relativePath;
            } else {
                $oSkin->url = BASE_URL . $oSkin->relativePath;
            }

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
     *
     * @param string $sModule  Restrict to particular component
     * @param string $sSubType Restrict by driver sub type
     *
     * @return array
     */
    function _NAILS_GET_DRIVERS($sModule, $sSubType = '')
    {
        $aDrivers = _NAILS_GET_COMPONENTS_BY_TYPE('driver');
        $aOut     = [];

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

if (!function_exists('_NAILS_GET_DRIVER_INSTANCE')) {

    /**
     * Returns an instance of a single driver
     *
     * @param  object $oDriver The Driver definition
     *
     * @throws NailsException
     * @return object
     */
    function _NAILS_GET_DRIVER_INSTANCE($oDriver)
    {
        //  Allow for driver requesting as a string
        if (is_string($oDriver)) {
            $oDriver = _NAILS_GET_COMPONENTS_BY_SLUG($oDriver);
        }

        if (isset($GLOBALS['NAILS']['DRIVER_INSTANCE'][$oDriver->slug])) {
            return $GLOBALS['NAILS']['DRIVER_INSTANCE'][$oDriver->slug];
        }

        //  Test driver
        if (!empty($oDriver->data->namespace)) {
            $sNamespace = $oDriver->data->namespace;
        } else {
            throw new NailsException('Driver Namespace missing from driver "' . $oDriver->slug . '"', 1);
        }

        if (!empty($oDriver->data->class)) {
            $sClassName = $oDriver->data->class;
        } else {
            throw new NailsException('Driver ClassName missing from driver "' . $oDriver->slug . '"', 2);
        }

        //  Load the driver file
        $sDriverPath = $oDriver->path . 'src/' . $oDriver->data->class . '.php';
        if (!file_exists($sDriverPath)) {
            throw new NailsException(
                'Driver file for "' . $oDriver->slug . '" does not exist at "' . $sDriverPath . '"',
                3
            );
        }

        require_once $sDriverPath;

        //  Check if the class exists
        $sDriverClass = $sNamespace . $sClassName;

        if (!class_exists($sDriverClass)) {
            throw new NailsException('Driver class does not exist "' . $sDriverClass . '"', 4);
        }

        //  Save for later
        $GLOBALS['NAILS']['DRIVER_INSTANCE'][$oDriver->slug] = new $sDriverClass();

        return $GLOBALS['NAILS']['DRIVER_INSTANCE'][$oDriver->slug];
    }
}

// --------------------------------------------------------------------------

if (!function_exists('_NAILS_MIN_PHP_VERSION')) {

    /**
     * Determines the minimum supported PHP version as per enabled modules
     *
     * @return string
     */
    function _NAILS_MIN_PHP_VERSION()
    {
        //  First check nails/nails
        $sComposer   = @file_get_contents('vendor/nails/nails/composer.json');
        $oComposer   = @json_decode($sComposer);
        $sMinVersion = isset($oComposer->extra->nails->minPhpVersion) ? $oComposer->extra->nails->minPhpVersion : 0;

        //  Next, check nails/common
        $sComposer         = @file_get_contents('vendor/nails/nails/composer.json');
        $oComposer         = @json_decode($sComposer);
        $sMinVersionCommon = isset($oComposer->extra->nails->minPhpVersion) ? $oComposer->extra->nails->minPhpVersion : 0;

        if (version_compare($sMinVersionCommon, $sMinVersion, '>')) {
            $sMinVersion = $sMinVersionCommon;
        }

        //  Now we check all the components
        $aComponents = _NAILS_GET_COMPONENTS();
        foreach ($aComponents as $cComponent) {
            if (version_compare($cComponent->minPhpVersion, $sMinVersion, '>')) {
                $sMinVersion = $cComponent->minPhpVersion;
            }
        }

        return $sMinVersion;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('defineConst')) {

    /**
     * Defines a constant if it is not already defined
     *
     * @param  string $sConstant The name of the constant to define
     * @param  string $mValue    The value to give the constant
     *
     * @return void
     */
    function defineConst($sConstant, $mValue)
    {
        if (!defined($sConstant)) {
            define($sConstant, $mValue);
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isModuleEnabled')) {

    /**
     * Handy way of determining whether a module is available
     *
     * @param  string $sModuleName The name of the module to check
     *
     * @return boolean
     */
    function isModuleEnabled($sModuleName)
    {
        $aModules = _NAILS_GET_MODULES();

        foreach ($aModules as $oModule) {
            if ($sModuleName == $oModule->name) {
                return true;
            }
        }

        return false;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getControllerData')) {

    /**
     * Nails\Bootstrap::$aNailsControllerData is an array populated by $this->data in controllers,
     * this function provides an easy interface to this array when it's not in scope.
     *
     * @return  array   A reference to Nails\Bootstrap::$aNailsControllerData
     **/
    function &getControllerData()
    {
        return Nails\Bootstrap::$aNailsControllerData;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('setControllerData')) {

    /**
     * Nails\Bootstrap::$aNailsControllerData is an array populated by $this->data
     * in controllers, this function provides an easy interface to populate this
     * array when it's not in scope.
     *
     * @param string $sKey   The key to populate
     * @param mixed  $mValue The value to assign
     *
     * @return  void
     **/
    function setControllerData($sKey, $mValue)
    {
        Nails\Bootstrap::$aNailsControllerData[$sKey] = $mValue;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getDomainFromUrl')) {

    /**
     * Attempts to get the top level part of a URL (i.e example.tld from sub.domains.example.tld).
     * Hat tip: http://uk1.php.net/parse_url#104874
     * BUG: 2 character TLD's break this
     *
     * @todo: Try and fix this bug
     *
     * @param  string $sUrl The URL to analyse
     *
     * @return mixed        string on success, false on failure
     */
    function getDomainFromUrl($sUrl)
    {
        $sDomain = parse_url($sUrl, PHP_URL_HOST);
        $bits    = explode('.', $sDomain);
        $idz     = count($bits);
        $idz     -= 3;

        if (!isset($bits[($idz + 2)])) {

            $aOut = false;

        } elseif (strlen($bits[($idz + 2)]) == 2 && isset($bits[($idz + 2)])) {

            $aOut = [
                !empty($bits[$idz]) ? $bits[$idz] : false,
                !empty($bits[$idz + 1]) ? $bits[$idz + 1] : false,
                !empty($bits[$idz + 2]) ? $bits[$idz + 2] : false,
            ];

            $aOut = implode('.', array_filter($aOut));

        } elseif (strlen($bits[($idz + 2)]) == 0) {

            $aOut = [
                !empty($bits[$idz]) ? $bits[$idz] : false,
                !empty($bits[$idz + 1]) ? $bits[$idz + 1] : false,
            ];

            $aOut = implode('.', array_filter($aOut));

        } elseif (isset($bits[($idz + 1)])) {

            $aOut = [
                !empty($bits[$idz + 1]) ? $bits[$idz + 1] : false,
                !empty($bits[$idz + 2]) ? $bits[$idz + 2] : false,
            ];
            $aOut = implode('.', array_filter($aOut));

        } else {
            $aOut = false;
        }

        return $aOut;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getRelativePath')) {

    /**
     * Fetches the relative path between two directories
     * Hat tip: Thanks to Gordon for this one; http://stackoverflow.com/a/2638272/789224
     *
     * @param  string $sForm Path 1
     * @param  string $sTo   Path 2
     *
     * @return string
     */
    function getRelativePath($sForm, $sTo)
    {
        $aFrom    = explode('/', $sForm);
        $aTo      = explode('/', $sTo);
        $aRelPath = $aTo;

        foreach ($aFrom as $iDepth => $sDir) {

            //  Find first non-matching dir
            if ($sDir === $aTo[$iDepth]) {
                //  Ignore this directory
                array_shift($aRelPath);
            } else {

                //  Get number of remaining dirs to $aFrom
                $remaining = count($aFrom) - $iDepth;

                if ($remaining > 1) {

                    // add traversals up to first matching dir
                    $padLength = (count($aRelPath) + $remaining - 1) * -1;
                    $aRelPath  = array_pad($aRelPath, $padLength, '..');
                    break;

                } else {
                    $aRelPath[0] = './' . $aRelPath[0];
                }
            }
        }

        return implode('/', $aRelPath);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('show_error')) {

    function show_error($sMessage = '', $sSubject = '', $iStatusCode = 500)
    {
        $oError =& load_class('Exceptions', 'core');
        $oError->show_error($sSubject, $sMessage, $iStatusCode, $iStatusCode);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('show_401')) {

    /**
     * Renders the 401 page, optionally logging the error to the database.
     * If a user is not logged in they are directed to the login page.
     *
     * @param  boolean $bLogError Whether to log the error or not
     *
     * @return void
     */
    function show_401($bLogError = true)
    {
        $oError =& load_class('Exceptions', 'core');
        $oError->show_401($bLogError);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('show401')) {

    /**
     * Alias of show_401
     *
     * @param  boolean $bLogError Whether to log the error or not
     *
     * @return void
     */
    function show401($bLogError = true)
    {
        show_401($bLogError);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('unauthorised')) {

    /**
     * Alias of show_401
     *
     * @param  boolean $bLogError Whether to log the error or not
     *
     * @return void
     */
    function unauthorised($bLogError = true)
    {
        show_401($bLogError);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('show_404')) {

    /**
     * Renders the 404 page, logging disabled by default.
     *
     * Note that the Exception class does log by default. Manual 404's are probably
     * a result of some other checking and not technically a 404 so should not be
     * logged as one. _Actual_ 404's should continue to be logged however.
     *
     * @param  boolean $bLogError Whether to log the error or not
     *
     * @return void
     */
    function show_404($bLogError = true)
    {
        $oError =& load_class('Exceptions', 'core');
        $oError->show_404('', $bLogError);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('show404')) {

    /**
     * Alias to show_404()
     *
     * @param  boolean $bLogError Whether to log the error or not
     *
     * @return void
     */
    function show404($bLogError = true)
    {
        show_404($bLogError);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getFromArray')) {

    /**
     * Retrieve a value from $sArray at $sKey, if it exists
     *
     * @param  string $sKey     The key to get
     * @param  array  $aArray   The array to look in
     * @param  mixed  $mDefault What to return if $sKey doesn't exist in $aArray
     *
     * @return mixed
     */
    function getFromArray($sKey, $aArray, $mDefault = null)
    {
        return array_key_exists($sKey, $aArray) ? $aArray[$sKey] : $mDefault;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isCli')) {

    /**
     * Whether the current request is being executed on the CLI
     *
     * @return bool
     */
    function isCli()
    {
        $oInput = Factory::service('Input');
        return $oInput::isCli();
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isAjax')) {

    /**
     * Whether the current request is an Ajax request
     *
     * @return bool
     */
    function isAjax()
    {
        $oInput = Factory::service('Input');
        return $oInput::isAjax();
    }
}
