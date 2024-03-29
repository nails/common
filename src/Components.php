<?php

/**
 * This class handles component registration
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 */

namespace Nails;

use Nails\Common\Driver\Base;
use Nails\Common\Exception\NailsException;
use Nails\Common\Factory\Component;
use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Service\ErrorHandler;

/**
 * Class Components
 *
 * @package Nails
 */
final class Components
{
    /**
     * The slug to use for App components
     *
     * @var string
     */
    public static $sAppSlug = 'app';

    /**
     * The namespace to use for App components
     *
     * @var string
     */
    static $oAppNamespace = '\\App\\';

    /**
     * The component cache
     *
     * @var Component[]
     */
    private static $aCache = [];

    /**
     * Stores the paths used for components
     *
     * @var string[]
     */
    private static $aComponentPaths = [];

    // --------------------------------------------------------------------------

    /**
     * Returns all detected Nails components
     *
     * @param bool $bUseCache Whether to use the cache or not
     *
     * @return Component[]
     */
    public static function available($bUseCache = true): array
    {
        /**
         * If we already know which Nails components are available then return that, save
         * the [small] overhead of working out the modules again and again.
         */

        if ($bUseCache && isset(static::$aCache['COMPONENTS'])) {
            return static::$aCache['COMPONENTS'];
        }

        // --------------------------------------------------------------------------

        $sComposer = @file_get_contents(NAILS_APP_PATH . 'vendor/composer/installed.json');

        if (empty($sComposer)) {
            ErrorHandler::halt('Failed to discover potential modules; could not load composer/installed.json');
        }

        $mComposer = @json_decode($sComposer);

        if (empty($mComposer)) {
            ErrorHandler::halt('Failed to discover potential modules; could not decode composer/installed.json');
        }

        if (is_array($mComposer)) {
            $aPackages = $mComposer;

        } elseif (is_object($mComposer) && property_exists($mComposer, 'packages')) {
            $aPackages = $mComposer->packages;

        } else {
            ErrorHandler::halt('composer/installed.json is not in a valid format');
        }

        $aOut                  = [];
        self::$aComponentPaths = [];

        foreach ($aPackages as $oPackage) {
            if (isset($oPackage->extra->nails)) {

                $sAbsolutePath = realpath(NAILS_APP_PATH . 'vendor' . DIRECTORY_SEPARATOR . $oPackage->name);
                $sRelativePath = preg_replace('/^' . preg_quote(getcwd(), '/') . '/', '.', $sAbsolutePath);

                $aOut[] = new Component(
                    $oPackage,
                    $sAbsolutePath,
                    $sRelativePath,
                    false
                );

                self::$aComponentPaths[] = $sAbsolutePath;
            }
        }

        static::$aComponentPaths = array_values(
            array_unique(
                array_map(fn($sPath) => dirname($sPath), static::$aComponentPaths)
            )
        );

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

        // --------------------------------------------------------------------------

        /**
         * Sort the components into a known order: app, nails/common, modules, drivers, skins
         */

        $aCommon  = [];
        $aModules = [];
        $aDrivers = [];
        $aSkins   = [];

        foreach ($aOut as $oComponent) {
            if ($oComponent->slug === \Nails\Common\Constants::MODULE_SLUG) {
                $aCommon[] = $oComponent;
            } elseif ($oComponent->type === 'module') {
                $aModules[] = $oComponent;
            } elseif ($oComponent->type === 'driver') {
                $aDrivers[] = $oComponent;
            } elseif ($oComponent->type === 'skin') {
                $aSkins[] = $oComponent;
            } else {
                throw new NailsException(
                    sprintf(
                        'Unsupported component type: %s',
                        $oComponent->type
                    )
                );
            }
        }

        $aOut = array_merge($aCommon, $aModules, $aDrivers, $aSkins);

        // --------------------------------------------------------------------------

        //  And then glue the app's definition onto the front
        array_unshift($aOut, static::getApp());

        // --------------------------------------------------------------------------

        if ($bUseCache) {
            static::$aCache['COMPONENTS'] = $aOut;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a filtered array of components
     *
     * @param string $sType The type of component to return
     *
     * @return Component[]
     */
    public static function filter($sType): array
    {
        if (!isset(static::$aCache[$sType])) {

            $aComponents            = static::available();
            static::$aCache[$sType] = [];

            foreach ($aComponents as $oComponent) {
                if ($oComponent->type === $sType) {
                    static::$aCache[$sType][] = $oComponent;
                }
            }
        }

        return static::$aCache[$sType];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a component by its slug
     *
     * @param string $sSlug The component's slug
     *
     * @return Component
     */
    public static function getBySlug($sSlug): ?Component
    {
        $aComponents = static::available();

        foreach ($aComponents as $oComponent) {
            if ($oComponent->slug === $sSlug) {
                return $oComponent;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a component by its URL slug
     *
     * @param string $sSlug The component's URL slug
     *
     * @return Component
     */
    public static function getByUrlSlug($sSlug): ?Component
    {
        $aComponents = static::available();

        foreach ($aComponents as $oComponent) {
            if ($oComponent->slugUrl === $sSlug) {
                return $oComponent;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an instance of the app as a component
     *
     * @param bool $bUseCache Whether to use the cache or not
     *
     * @return Component
     */
    public static function getApp($bUseCache = true): Component
    {
        //  If we have already fetched this data then don't get it again
        if ($bUseCache && isset(static::$aCache['APP'])) {
            return static::$aCache['APP'];
        }

        // --------------------------------------------------------------------------

        $sComposer = @file_get_contents(NAILS_APP_PATH . 'composer.json');

        if (empty($sComposer)) {
            ErrorHandler::halt('Failed to get app configuration; could not load composer.json');
        }

        $oComposer = @json_decode($sComposer);

        if (empty($oComposer)) {
            ErrorHandler::halt('Failed to get app configuration; could not decode composer.json');
        }

        $aComposer = (array) $oComposer;
        $aNails    = !empty($oComposer->extra->nails) ? (array) $oComposer->extra->nails : [];

        $oOut = new Component(
            (object) [
                /**
                 * When a module is the App (e.g. in tests) then this ensures that the relationship
                 * in the factory is set properly, so loading from the factory does what you'd expect.
                 */
                'slug' => array_key_exists('moduleName', $aNails)
                    ? $aComposer['name']
                    : static::$sAppSlug,
                'name' => array_key_exists('moduleName', $aNails)
                    ? $aComposer['name']
                    : static::$sAppSlug,

                'description' => ArrayHelper::get('description', $aNails, ArrayHelper::get('description', $aComposer)),
                'homepage'    => ArrayHelper::get('homepage', $aNails, ArrayHelper::get('homepage', $aComposer)),
                'authors'     => ArrayHelper::get('authors', $aNails, ArrayHelper::get('authors', $aComposer)),
                'extra'       => (object) [
                    'nails' => (object) [
                        'namespace'  => $aNails['namespace'] ?? static::$oAppNamespace,
                        'moduleName' => ArrayHelper::get('moduleName', $aNails, ''),
                        'data'       => ArrayHelper::get('data', $aNails, (object) []),
                        'autoload'   => ArrayHelper::get('autoload', $aNails, (object) []),
                    ],
                ],
            ],
            NAILS_APP_PATH,
            '.' . DIRECTORY_SEPARATOR,
            true
        );

        // --------------------------------------------------------------------------

        if ($bUseCache) {
            static::$aCache['APP'] = $oOut;
        }

        return $oOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Test whether a component is installed
     *
     * @param string $sSlug The component's slug
     *
     * @return bool
     */
    public static function exists($sSlug): bool
    {
        $aModules = static::modules();

        foreach ($aModules as $oModule) {
            if ($sSlug === $oModule->slug) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all registered modules
     *
     * @return Component[]
     */
    public static function modules(): array
    {
        return static::filter('module');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns registered skins, optionally filtered
     *
     * @param string $sModule The module to filter for
     * @param string $sSubTyp The sub-type to filter by
     *
     * @return Component[]
     */
    public static function skins($sModule, $sSubType = ''): array
    {
        $aSkins = static::filter('skin');
        $aOut   = [];

        foreach ($aSkins as $oSkin) {

            //  Provide a url field for the skin
            if (Functions::isPageSecure()) {
                $oSkin->url = Config::get('SECURE_BASE_URL') . $oSkin->relativePath;
            } else {
                $oSkin->url = Config::get('BASE_URL') . $oSkin->relativePath;
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

    // --------------------------------------------------------------------------

    /**
     * Returns registered drivers, optionally filtered
     *
     * @param string $sModule The module to filter for
     * @param string $sSubTyp The sub-type to filter by
     *
     * @return Component[]
     */
    public static function drivers($sModule, $sSubType = ''): array
    {
        $aDrivers = static::filter('driver');
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

    // --------------------------------------------------------------------------

    /**
     * Returns an instance of a single driver
     *
     * @param object $oDriver The Driver definition
     *
     * @return Base
     * @throws NailsException
     */
    public static function getDriverInstance($oDriver): Base
    {
        //  Allow for driver requesting as a string
        if (is_string($oDriver)) {
            $oDriver = \Nails\Components::getBySlug($oDriver);
        }

        if (isset(static::$aCache['DRIVER_INSTANCE'][$oDriver->slug])) {
            return static::$aCache['DRIVER_INSTANCE'][$oDriver->slug];
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
        static::$aCache['DRIVER_INSTANCE'][$oDriver->slug] = new $sDriverClass();

        return static::$aCache['DRIVER_INSTANCE'][$oDriver->slug];
    }

    // --------------------------------------------------------------------------

    /**
     * Attempt to detect which component a class belongs to
     *
     * @param mixed $mClass A class as a string or an object
     *
     * @return Component|null
     * @throws \ReflectionException
     */
    public static function detectClassComponent($mClass): ?Component
    {
        $oReflect  = new \ReflectionClass($mClass);
        $sPath     = $oReflect->getFileName();
        $bIsVendor = (bool) preg_match(
            sprintf(
                '/^(%s)/',
                implode('|', array_map(fn($sPath) => preg_quote($sPath, '/'), static::$aComponentPaths))
            ),
            $sPath
        );

        if (!$bIsVendor) {
            return static::getApp();
        }

        foreach (static::available() as $oComponent) {
            if ($oComponent->slug === static::$sAppSlug) {
                continue;

            } elseif (preg_match('/^' . preg_quote($oComponent->path, '/') . '/', $sPath)) {
                return $oComponent;
            }
        }

        return null;
    }
}
