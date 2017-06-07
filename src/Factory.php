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
use Nails\Common\Exception\FactoryException;

class Factory
{
    /**
     * Contains an array of containers; each component gets its own element so as
     * to avoid naming collisions.
     * @var array
     */
    private static $aContainers;
    private static $aLoadedHelpers;

    // --------------------------------------------------------------------------

    /**
     * Look for services from available components and configure into the dependency container
     * @return void
     */
    public static function setup()
    {
        $aComponents          = _NAILS_GET_COMPONENTS();
        self::$aContainers    = [];
        self::$aLoadedHelpers = [];
        $aDiscoveredServices  = [
            'nailsapp/common' => self::findServicesForComponent('nailsapp/common'),
        ];

        foreach ($aComponents as $oComponent) {
            $aDiscoveredServices[$oComponent->slug] = self::findServicesForComponent($oComponent->slug);
        }

        $aDiscoveredServices['app'] = self::findServicesForApp();
        $aDiscoveredServices        = array_filter($aDiscoveredServices);

        foreach ($aDiscoveredServices as $ComponentName => $aComponentServices) {

            //  Properties
            if (!empty($aComponentServices['properties'])) {
                if (empty(self::$aContainers[$ComponentName]['properties'])) {
                    self::$aContainers[$ComponentName]['properties'] = new Container();
                }
                foreach ($aComponentServices['properties'] as $sKey => $mValue) {
                    self::$aContainers[$ComponentName]['properties'][$sKey] = $mValue;
                }
            }

            // --------------------------------------------------------------------------

            //  Services
            if (!empty($aComponentServices['services'])) {
                if (empty(self::$aContainers[$ComponentName]['services'])) {
                    self::$aContainers[$ComponentName]['services'] = new Container();
                }
                foreach ($aComponentServices['services'] as $sKey => $oCallable) {
                    self::$aContainers[$ComponentName]['services'][$sKey] = $oCallable;
                }
            }

            // --------------------------------------------------------------------------

            //  Models
            if (!empty($aComponentServices['models'])) {
                if (empty(self::$aContainers[$ComponentName]['models'])) {
                    self::$aContainers[$ComponentName]['models'] = new Container();
                }
                foreach ($aComponentServices['models'] as $sKey => $oCallable) {
                    self::$aContainers[$ComponentName]['models'][$sKey] = $oCallable;
                }
            }

            // --------------------------------------------------------------------------

            //  Factories
            if (!empty($aComponentServices['factories'])) {
                if (empty(self::$aContainers[$ComponentName]['factories'])) {
                    self::$aContainers[$ComponentName]['factories'] = new Container();
                }
                foreach ($aComponentServices['factories'] as $sKey => $oCallable) {
                    self::$aContainers[$ComponentName]['factories'][$sKey] = self::$aContainers[$ComponentName]['factories']->factory($oCallable);
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Look for a components's services.php file, allowing for app and/or environment overrides
     *
     * @param  string $sComponentName The component name to search for
     *
     * @return array
     */
    private static function findServicesForComponent($sComponentName)
    {
        $aPaths = [
            //  App overrides
            FCPATH . 'application/services/' . Environment::get() . '/' . $sComponentName . '/services.php',
            FCPATH . 'application/services/' . $sComponentName . '/services.php',
            //  Default locations
            FCPATH . 'vendor/' . $sComponentName . '/services/' . Environment::get() . '/services.php',
            FCPATH . 'vendor/' . $sComponentName . '/services/services.php',
        ];

        return self::findServicesAtPaths($aPaths);
    }

    // --------------------------------------------------------------------------

    /**
     * Look for the app's services.php file, allowing for environment overrides
     * @return array
     */
    private static function findServicesForApp()
    {
        $aPaths = [
            'application/services/' . Environment::get() . '/services.php',
            'application/services/services.php',
        ];

        return self::findServicesAtPaths($aPaths);
    }

    // --------------------------------------------------------------------------

    /**
     * Traverses an array of paths until one exits
     *
     * @param  array $aPaths An array of paths to look for
     *
     * @return array
     */
    private static function findServicesAtPaths($aPaths)
    {
        foreach ($aPaths as $sPath) {
            if (file_exists($sPath)) {
                return require $sPath;
            }
        }

        return [];
    }

    // --------------------------------------------------------------------------

    /**
     * Return a property from the container.
     *
     * @param  string $sPropertyName  The property name
     * @param  string $sComponentName The name of the component which provides the property
     *
     * @return mixed
     */
    public static function property($sPropertyName, $sComponentName = '')
    {
        return self::getService('properties', $sPropertyName, $sComponentName);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a new value for a property
     *
     * @param  string $sPropertyName  The property name
     * @param  mixed  $mPropertyValue The new property value
     * @param  string $sComponentName The name of the component which provides the property
     *
     * @throws FactoryException
     * @return void
     */
    public static function setProperty($sPropertyName, $mPropertyValue, $sComponentName = '')
    {
        $sComponentName = empty($sComponentName) ? 'nailsapp/common' : $sComponentName;

        if (empty(self::$aContainers[$sComponentName]['properties'][$sPropertyName])) {
            throw new FactoryException(
                'Property "' . $sPropertyName . '" is not provided by component "' . $sComponentName . '"',
                0
            );
        }

        self::$aContainers[$sComponentName]['properties'][$sPropertyName] = $mPropertyValue;
    }

    // --------------------------------------------------------------------------

    /**
     * Return a service from the container.
     *
     * @param  string $sServiceName   The service name
     * @param  string $sComponentName The name of the component which provides the service
     *
     * @return mixed
     */
    public static function service($sServiceName, $sComponentName = '')
    {
        return self::getService('services', $sServiceName, $sComponentName);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a model from the container.
     *
     * @param  string $sModelName     The model name
     * @param  string $sComponentName The name of the component which provides the model
     *
     * @return mixed
     */
    public static function model($sModelName, $sComponentName = '')
    {
        return self::getService('models', $sModelName, $sComponentName);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a factory from the container.
     *
     * @param  string $sFactoryName   The factory name
     * @param  string $sComponentName The name of the component which provides the factory
     *
     * @return mixed
     */
    public static function factory($sFactoryName, $sComponentName = '')
    {
        return self::getService('factories', $sFactoryName, $sComponentName);
    }

    // --------------------------------------------------------------------------

    /**
     * Load a helper file
     *
     * @param  string $sHelperName    The helper name
     * @param  string $sComponentName The name of the component which provides the factory
     *
     * @throws FactoryException
     * @return void
     */
    public static function helper($sHelperName, $sComponentName = '')
    {
        $sComponentName = empty($sComponentName) ? 'nailsapp/common' : $sComponentName;

        if (empty(self::$aLoadedHelpers[$sComponentName][$sHelperName])) {

            if (empty(self::$aLoadedHelpers[$sComponentName])) {
                self::$aLoadedHelpers[$sComponentName] = [];
            }

            /**
             * If we're only interested in the app version of the helper then we change things
             * around a little as the paths and reliance of a "component" based helper aren't the same
             */
            if ($sComponentName == 'app') {

                $sAppPath = FCPATH . 'application/helpers/' . $sHelperName . '.php';

                if (!file_exists($sAppPath)) {
                    throw new FactoryException(
                        'Helper "' . $sComponentName . '/' . $sHelperName . '" does not exist.',
                        1
                    );
                }

                require_once $sAppPath;

            } else {

                $sComponentPath = FCPATH . 'vendor/' . $sComponentName . '/helpers/' . $sHelperName . '.php';
                $sAppPath       = FCPATH . 'application/helpers/' . $sComponentName . '/' . $sHelperName . '.php';

                if (!file_exists($sComponentPath)) {
                    throw new FactoryException(
                        'Helper "' . $sComponentName . '/' . $sHelperName . '" does not exist.',
                        1
                    );
                }

                if (file_exists($sAppPath)) {
                    require_once $sAppPath;
                }

                require_once $sComponentPath;
            }

            self::$aLoadedHelpers[$sComponentName][$sHelperName] = true;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a service from the namespaced container
     *
     * @param  string $sServiceType   The type of the service to return
     * @param  string $sServiceName   The name of the service to return
     * @param  string $sComponentName The name of the module which defined it
     *
     * @throws FactoryException
     * @return mixed
     */
    private static function getService($sServiceType, $sServiceName, $sComponentName = '')
    {
        $sComponentName = empty($sComponentName) ? 'nailsapp/common' : $sComponentName;

        if (empty(self::$aContainers[$sComponentName][$sServiceType][$sServiceName])) {
            throw new FactoryException(
                ucfirst($sServiceType) . ' "' . $sServiceName . '" is not provided by component "' . $sComponentName . '"',
                0
            );
        }

        return self::$aContainers[$sComponentName][$sServiceType][$sServiceName];
    }

    // --------------------------------------------------------------------------

    /**
     * Auto-loads items at startup
     */
    public static function autoload()
    {
        //  CI base helpers
        require_once BASEPATH . 'core/Common.php';

        //  Common helpers
        self::helper('string');
        self::helper('app_setting');
        self::helper('app_notification');
        self::helper('date');
        self::helper('url');
        self::helper('cookie');
        self::helper('form');
        self::helper('html');
        self::helper('tools');
        self::helper('debug');
        self::helper('language');
        self::helper('text');
        self::helper('exception');
        self::helper('typography');
        self::helper('log');

        //  Module items
        foreach (_NAILS_GET_MODULES() as $oModule) {
            //  Helpers
            if (!empty($oModule->autoload->helpers)) {
                foreach ($oModule->autoload->helpers as $sHelper) {
                    self::helper($sHelper, $oModule->slug);
                }
            }
            //  Services
            if (!empty($oModule->autoload->services)) {
                foreach ($oModule->autoload->services as $sService) {
                   self::service($sService, $oModule->slug);
                }
            }
        }
    }
}
