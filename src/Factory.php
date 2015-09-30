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
use \Pimple\Container;

class Factory
{
    /**
     * Contains an array of containers; each module gets its own element so as
     * to avoid naming collisions.
     * @var array
     */
    private static $aContainers;

    // --------------------------------------------------------------------------

    /**
     * Look for services from available modules and configure into the dependency container
     * @return void
     */
    public static function setup()
    {
        self::$aContainers = array();

        $aDiscoveredServices = array();
        $aDiscoveredServices['nailsapp/common'] = self::findServicesAtPath('vendor/nailsapp/common/');

        $aModules = _NAILS_GET_MODULES();

        foreach ($aModules as $oModule) {

            $aDiscoveredServices[$oModule->name] = self::findServicesAtPath($oModule->path);
        }

        $aDiscoveredServices = array_filter($aDiscoveredServices);

        foreach ($aDiscoveredServices as $sModuleName => $aModuleServices) {

            if (empty(self::$aContainers[$sModuleName])) {
                self::$aContainers[$sModuleName] = new Container();
            }

            if (!empty($aModuleServices['properties'])) {
                foreach ($aModuleServices['properties'] as $sKey => $mValue) {
                    self::$aContainers[$sModuleName][$sKey] = $mValue;
                }
            }

            if (!empty($aModuleServices['services'])) {
                foreach ($aModuleServices['services'] as $sKey => $oCallable) {
                    self::$aContainers[$sModuleName][$sKey] = $oCallable;
                }
            }

            if (!empty($aModuleServices['factories'])) {
                foreach ($aModuleServices['factories'] as $sKey => $oCallable) {
                    self::$aContainers[$sModuleName][$sKey] = self::$aContainers[$sModuleName]->factory($oCallable);
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for a services.php file at various possible places
     * @param  string $sPath The base path to search
     * @return array
     */
    private static function findServicesAtPath($sPath)
    {
        $aPaths = array(
            $sPath . 'services/' . strtolower(ENVIRONMENT) . '/services.php',
            $sPath . 'services/' . strtolower(ENVIRONMENT) . '.services.php',
            $sPath . 'services/services.' . strtolower(ENVIRONMENT) . '.php',
            $sPath . 'services/services.php'
        );

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
     * @param  string $sModuleName  The name of the module which provides the property
     * @return mixed
     */
    public static function property($sPropertyName, $sModuleName = null)
    {
        return self::getService($sPropertyName, $sModuleName);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a service from the container.
     * @param  string $sServiceName The service name
     * @param  string $sModuleName  The name of the module which provides the service
     * @return mixed
     */
    public static function service($sServiceName, $sModuleName = null)
    {
        return self::getService($sServiceName, $sModuleName);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a factory from the container.
     * @param  string $sFactoryName The factory name
     * @param  string $sModuleName  The name of the module which provides the factory
     * @return mixed
     */
    public static function factory($sFactoryName, $sModuleName = null)
    {
        return self::getService($sFactoryName, $sModuleName);
    }

    // --------------------------------------------------------------------------

    private static function getService($sServiceName, $sModuleName = null)
    {
        $sModuleName = empty($sModuleName) ? 'nailsapp/common' : $sModuleName;

        if (empty(self::$aContainers[$sModuleName])) {
            throw new Common\Exception\FactoryException(
                'Service "' . $sServiceName . '"  is not provided by module "' . $sModuleName . '"',
                0
            );
        }

        return self::$aContainers[$sModuleName][$sServiceName];
    }
}
