<?php

/**
 * This class handles dependency injection throughout Nails.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 * @link
 */

namespace Nails;

use Pimple\Container;
use Nails\Environment;

class Factory
{
    /**
     * Contains an array of containers; each module gets its own element so as
     * to avoid naming collisions.
     * @var array
     */
    private static $aContainers;
    private static $aLoadedHelpers;

    // --------------------------------------------------------------------------

    /**
     * Look for services from available modules and configure into the dependency container
     * @return void
     */
    public static function setup()
    {
        $aModules             = _NAILS_GET_MODULES();
        self::$aContainers    = array();
        self::$aLoadedHelpers = array();
        $aDiscoveredServices  = array(
            'nailsapp/common' => self::findServicesForModule('nailsapp/common')
        );

        foreach ($aModules as $oModule) {

            $aDiscoveredServices[$oModule->name] = self::findServicesForModule($oModule->name);
        }

        $aDiscoveredServices['app'] = self::findServicesForApp();

        $aDiscoveredServices = array_filter($aDiscoveredServices);

        foreach ($aDiscoveredServices as $sModuleName => $aModuleServices) {

            //  Properties
            if (!empty($aModuleServices['properties'])) {

                if (empty(self::$aContainers[$sModuleName]['properties'])) {
                    self::$aContainers[$sModuleName]['properties'] = new Container();
                }

                foreach ($aModuleServices['properties'] as $sKey => $mValue) {
                    self::$aContainers[$sModuleName]['properties'][$sKey] = $mValue;
                }
            }

            // --------------------------------------------------------------------------

            //  Services
            if (!empty($aModuleServices['services'])) {

                if (empty(self::$aContainers[$sModuleName]['services'])) {
                    self::$aContainers[$sModuleName]['services'] = new Container();
                }

                foreach ($aModuleServices['services'] as $sKey => $oCallable) {
                    self::$aContainers[$sModuleName]['services'][$sKey] = $oCallable;
                }
            }

            // --------------------------------------------------------------------------

            //  Models
            if (!empty($aModuleServices['models'])) {

                if (empty(self::$aContainers[$sModuleName]['models'])) {
                    self::$aContainers[$sModuleName]['models'] = new Container();
                }

                foreach ($aModuleServices['models'] as $sKey => $oCallable) {
                    self::$aContainers[$sModuleName]['models'][$sKey] = $oCallable;
                }
            }

            // --------------------------------------------------------------------------

            //  Factories
            if (!empty($aModuleServices['factories'])) {

                if (empty(self::$aContainers[$sModuleName]['factories'])) {
                    self::$aContainers[$sModuleName]['factories'] = new Container();
                }

                foreach ($aModuleServices['factories'] as $sKey => $oCallable) {
                    self::$aContainers[$sModuleName]['factories'][$sKey] = self::$aContainers[$sModuleName]['factories']->factory($oCallable);
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Look for a module's services.php file, allowing for app and/or environment overrides
     * @param  string $sModuleName The module name to search for
     * @return array
     */
    private static function findServicesForModule($sModuleName)
    {
        $aPaths = array(

            //  App overrides
            FCPATH . 'application/services/' . Environment::get() . '/' . $sModuleName . '/services.php',
            FCPATH . 'application/services/' . $sModuleName . '/services.php',

            //  Default locations
            FCPATH . 'vendor/' . $sModuleName . '/services/' . Environment::get() . '/services.php',
            FCPATH . 'vendor/' . $sModuleName . '/services/services.php'
        );

        return self::findServicesAtPaths($aPaths);
    }

    // --------------------------------------------------------------------------

    /**
     * Look for the app's services.php file, allowing for environment overrides
     * @return array
     */
    private static function findServicesForApp()
    {
        $aPaths = array(
            'application/services/' . Environment::get() . '/services.php',
            'application/services/services.php',
        );

        return self::findServicesAtPaths($aPaths);
    }

    // --------------------------------------------------------------------------

    /**
     * Traverses an array of paths until one exits
     * @param  array $aPaths An array of paths to look for
     * @return array
     */
    private static function findServicesAtPaths($aPaths)
    {
        $aModuleServices = array();

        foreach ($aPaths as $sPath) {
            if (file_exists($sPath)) {
                return require $sPath;
            }
        }

        return array();
    }

    // --------------------------------------------------------------------------

    /**
     * Return a property from the container.
     * @param  string $sPropertyName The property name
     * @param  string $sModuleName   The name of the module which provides the property
     * @return mixed
     */
    public static function property($sPropertyName, $sModuleName = '')
    {
        return self::getService('properties', $sPropertyName, $sModuleName);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a new value for a property
     * @param  string $sPropertyName  The property name
     * @param  mixed  $mPropertyValue The new property value
     * @param  string $sModuleName    The name of the module which provides the property
     * @return void
     */
    public static function setProperty($sPropertyName, $mPropertyValue, $sModuleName = '')
    {
        $sModuleName = empty($sModuleName) ? 'nailsapp/common' : $sModuleName;

        if (empty(self::$aContainers[$sModuleName]['properties'][$sPropertyName])) {
            throw new Common\Exception\FactoryException(
                'Property "' . $sPropertyName . '" is not provided by module "' . $sModuleName . '"',
                0
            );
        }

        self::$aContainers[$sModuleName]['properties'][$sPropertyName] = $mPropertyValue;
    }

    // --------------------------------------------------------------------------

    /**
     * Return a service from the container.
     * @param  string $sServiceName The service name
     * @param  string $sModuleName  The name of the module which provides the service
     * @return mixed
     */
    public static function service($sServiceName, $sModuleName = '')
    {
        return self::getService('services', $sServiceName, $sModuleName);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a model from the container.
     * @param  string $sModelName  The model name
     * @param  string $sModuleName The name of the module which provides the model
     * @return mixed
     */
    public static function model($sModelName, $sModuleName = '')
    {
        return self::getService('models', $sModelName, $sModuleName);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a factory from the container.
     * @param  string $sFactoryName The factory name
     * @param  string $sModuleName  The name of the module which provides the factory
     * @return mixed
     */
    public static function factory($sFactoryName, $sModuleName = '')
    {
        return self::getService('factories', $sFactoryName, $sModuleName);
    }

    // --------------------------------------------------------------------------

    /**
     * Load a helper file
     * @param  string $sHelperName The helper name
     * @param  string $sModuleName The name of the module which provides the factory
     * @return void
     */
    public static function helper($sHelperName, $sModuleName = '')
    {
        $sModuleName = empty($sModuleName) ? 'nailsapp/common' : $sModuleName;

        if (empty(self::$aLoadedHelpers[$sModuleName][$sHelperName])) {

            if (empty(self::$aLoadedHelpers[$sModuleName])) {
                self::$aLoadedHelpers[$sModuleName] = array();
            }

            /**
             * If we're only interested in the app version of the helper then we change things
             * around a little as the paths and reliance of a "module" based helper aren't the same
             */
            if ($sModuleName == 'app') {

                $sAppPath = FCPATH . 'application/helpers/' . $sHelperName . '.php';

                if (!file_exists($sAppPath)) {
                    throw new Common\Exception\FactoryException(
                        'Helper "' . $sModuleName . '/' . $sHelperName . '" does not exist.',
                        1
                    );
                }

                require_once $sAppPath;

            } else {

                $sModulePath = FCPATH . 'vendor/' . $sModuleName . '/helpers/' . $sHelperName . '.php';
                $sAppPath    = FCPATH . 'application/helpers/' . $sModuleName . '/' . $sHelperName . '.php';

                if (!file_exists($sModulePath)) {
                    throw new Common\Exception\FactoryException(
                        'Helper "' . $sModuleName . '/' . $sHelperName . '" does not exist.',
                        1
                    );
                }

                if (file_exists($sAppPath)) {
                    require_once $sAppPath;
                }

                require_once $sModulePath;
            }

            self::$aLoadedHelpers[$sModuleName][$sHelperName] = true;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a service from the namespaced container
     * @param  string $sServiceType The type of the service to return
     * @param  string $sServiceName The name of the service to return
     * @param  string $sModuleName  The name of the mdoule which defined it
     * @return mixed
     */
    private static function getService($sServiceType, $sServiceName, $sModuleName = '')
    {
        $sModuleName = empty($sModuleName) ? 'nailsapp/common' : $sModuleName;

        if (empty(self::$aContainers[$sModuleName][$sServiceType][$sServiceName])) {
            throw new Common\Exception\FactoryException(
                ucfirst($sServiceType) . ' "' . $sServiceName . '" is not provided by module "' . $sModuleName . '"',
                0
            );
        }

        return self::$aContainers[$sModuleName][$sServiceType][$sServiceName];
    }
}
