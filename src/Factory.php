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

use Nails\Common\Exception\FactoryException;
use Pimple\Container;

class Factory
{
    /**
     * Contains an array of containers; each component gets its own element so as
     * to avoid naming collisions.
     *
     * @var array
     */
    private static $aContainers = [];

    /**
     * Tracks which services have been loaded
     *
     * @var array
     */
    private static $aLoadedServices = [];

    /**
     * Tracks which models have been loaded
     *
     * @var array
     */
    private static $aLoadedModels = [];

    /**
     * Tracks which helpers have been loaded
     *
     * @var array
     */
    private static $aLoadedHelpers = [];

    // --------------------------------------------------------------------------

    /**
     * Look for services from available components and configure into the dependency container
     *
     * @return void
     */
    public static function setup()
    {
        $aComponents          = _NAILS_GET_COMPONENTS();
        self::$aContainers    = [];
        self::$aLoadedHelpers = [];
        $aDiscoveredServices  = [
            'nails/common' => self::findServicesForComponent('nails/common'),
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

            /**
             * SERVICES
             * All services are wrapped in a closure, this is so that on fetch we can
             * pass in any additional arguments as parameters which can be used in the
             * item's constructor
             */
            if (!empty($aComponentServices['services'])) {
                if (empty(self::$aContainers[$ComponentName]['services'])) {
                    self::$aContainers[$ComponentName]['services'] = new Container();
                }
                foreach ($aComponentServices['services'] as $sKey => $cCallable) {
                    self::$aContainers[$ComponentName]['services'][$sKey] = function () use ($sKey, $cCallable) {
                        return $cCallable;
                    };;
                }
            }

            // --------------------------------------------------------------------------

            /**
             * MODELS
             * All models are wrapped in a closure, this is so that on fetch we can
             * pass in any additional arguments as parameters which can be used in the
             * item's constructor
             */
            if (!empty($aComponentServices['models'])) {
                if (empty(self::$aContainers[$ComponentName]['models'])) {
                    self::$aContainers[$ComponentName]['models'] = new Container();
                }
                foreach ($aComponentServices['models'] as $sKey => $cCallable) {
                    self::$aContainers[$ComponentName]['models'][$sKey] = function () use ($cCallable) {
                        return $cCallable;
                    };
                }
            }

            // --------------------------------------------------------------------------

            /**
             * FACTORIES
             * All factories are wrapped in a closure, this is so that on fetch we can
             * pass in any additional arguments as parameters which can be used in the
             * item's constructor
             */
            if (!empty($aComponentServices['factories'])) {
                if (empty(self::$aContainers[$ComponentName]['factories'])) {
                    self::$aContainers[$ComponentName]['factories'] = new Container();
                }
                foreach ($aComponentServices['factories'] as $sKey => $cCallable) {

                    $cWrapper = function () use ($cCallable) {
                        return $cCallable;
                    };

                    self::$aContainers[$ComponentName]['factories'][$sKey] = self::$aContainers[$ComponentName]['factories']
                        ->factory($cWrapper);
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Look for a components's services.php file, allowing for app and/or environment overrides
     *
     * @param string $sComponentName The component name to search for
     *
     * @return array
     */
    private static function findServicesForComponent($sComponentName)
    {
        $aPaths = [
            //  App overrides
            NAILS_APP_PATH . 'application/services/' . Environment::get() . '/' . $sComponentName . '/services.php',
            NAILS_APP_PATH . 'application/services/' . $sComponentName . '/services.php',
            //  Default locations
            NAILS_APP_PATH . 'vendor/' . $sComponentName . '/services/' . Environment::get() . '/services.php',
            NAILS_APP_PATH . 'vendor/' . $sComponentName . '/services/services.php',
        ];

        return self::findServicesAtPaths($aPaths);
    }

    // --------------------------------------------------------------------------

    /**
     * Look for the app's services.php file, allowing for environment overrides
     *
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
     * @param array $aPaths An array of paths to look for
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
     * @param string $sPropertyName  The property name
     * @param string $sComponentName The name of the component which provides the property
     *
     * @return mixed
     * @throws FactoryException
     */
    public static function property($sPropertyName, $sComponentName = '')
    {
        $mProperty = self::getService('properties', $sPropertyName, $sComponentName);

        if (is_callable($mProperty)) {
            return $mProperty();
        } else {
            return $mProperty;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a new value for a property
     *
     * @param string $sPropertyName  The property name
     * @param mixed  $mPropertyValue The new property value
     * @param string $sComponentName The name of the component which provides the property
     *
     * @return void
     * @throws FactoryException
     */
    public static function setProperty($sPropertyName, $mPropertyValue, $sComponentName = '')
    {
        $sComponentName = empty($sComponentName) ? 'nails/common' : $sComponentName;

        if (!self::$aContainers[$sComponentName]['properties']->offsetExists($sPropertyName)) {
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
     * @param string $sServiceName   The service name
     * @param string $sComponentName The name of the component which provides the service
     *
     * @return mixed
     * @throws FactoryException
     */
    public static function service($sServiceName, $sComponentName = '')
    {
        return static::getServiceOrModel(
            static::$aLoadedServices,
            'services',
            $sServiceName,
            $sComponentName,
            array_slice(func_get_args(), 2)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Return a model from the container.
     *
     * @param string $sModelName     The model name
     * @param string $sComponentName The name of the component which provides the model
     *
     * @return mixed
     * @throws FactoryException
     */
    public static function model($sModelName, $sComponentName = '')
    {
        return static::getServiceOrModel(
            static::$aLoadedModels,
            'models',
            $sModelName,
            $sComponentName,
            array_slice(func_get_args(), 2)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a servie or a model from the tracker array
     *
     * @param array  $aTrackerArray The tracker array to load from
     * @param string $sType         The type of item being loaded
     * @param string $sName         The name of the item being loaded
     * @param string $sComponent    The name of the component which provides the item
     * @param array  $aParamaters   Any paramters to pass to the constructor
     *
     * @return object
     * @throws FactoryException
     */
    private static function getServiceOrModel(array &$aTrackerArray, $sType, $sName, $sComponent, array $aParamaters)
    {
        /**
         * We track them like this because we need to return the instance of the
         * item, not the closure. If we don't do this then we will always get
         * a new instance of the item, which is undesireable.
         */

        $sKey = md5($sComponent . $sName);

        if (!array_key_exists($sKey, static::$aLoadedServices)) {

            $aTrackerArray[$sKey] = call_user_func_array(
                self::getService($sType, $sName, $sComponent),
                $aParamaters
            );

        } elseif (!empty($aParamaters)) {
            trigger_error(
                'A call to Factory::' . $sType . '(' . $sName . ') passed construction paramaters, but the object has already been constructed'
            );
        }

        return $aTrackerArray[$sKey];
    }

    // --------------------------------------------------------------------------

    /**
     * Return a factory from the container.
     *
     * @param string $sFactoryName   The factory name
     * @param string $sComponentName The name of the component which provides the factory
     *
     * @return mixed
     * @throws FactoryException
     */
    public static function factory($sFactoryName, $sComponentName = '')
    {
        return call_user_func_array(
            self::getService('factories', $sFactoryName, $sComponentName),
            array_slice(func_get_args(), 2)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Load a helper file
     *
     * @param string $sHelperName    The helper name
     * @param string $sComponentName The name of the component which provides the factory
     *
     * @throws FactoryException
     * @return void
     */
    public static function helper($sHelperName, $sComponentName = '')
    {
        $sComponentName = empty($sComponentName) ? 'nails/common' : $sComponentName;

        if (empty(self::$aLoadedHelpers[$sComponentName][$sHelperName])) {

            if (empty(self::$aLoadedHelpers[$sComponentName])) {
                self::$aLoadedHelpers[$sComponentName] = [];
            }

            /**
             * If we're only interested in the app version of the helper then we change things
             * around a little as the paths and reliance of a "component" based helper aren't the same
             */
            if ($sComponentName == 'app') {

                $sAppPath = NAILS_APP_PATH . 'application/helpers/' . $sHelperName . '.php';

                if (!file_exists($sAppPath)) {
                    throw new FactoryException(
                        'Helper "' . $sComponentName . '/' . $sHelperName . '" does not exist.',
                        1
                    );
                }

                require_once $sAppPath;

            } else {

                $sComponentPath = NAILS_APP_PATH . 'vendor/' . $sComponentName . '/helpers/' . $sHelperName . '.php';
                $sAppPath       = NAILS_APP_PATH . 'application/helpers/' . $sComponentName . '/' . $sHelperName . '.php';

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
     * @param string $sServiceType   The type of the service to return
     * @param string $sServiceName   The name of the service to return
     * @param string $sComponentName The name of the module which defined it
     *
     * @throws FactoryException
     * @return mixed
     */
    private static function getService($sServiceType, $sServiceName, $sComponentName = '')
    {
        $sComponentName = empty($sComponentName) ? 'nails/common' : $sComponentName;

        if (!array_key_exists($sComponentName, self::$aContainers)) {
            throw new FactoryException(
                'No containers registered for "' . $sComponentName . '"'
            );
        } elseif (!array_key_exists($sServiceType, self::$aContainers[$sComponentName])) {
            throw new FactoryException(
                'No "' . $sServiceType . '" containers registered for "' . $sComponentName . '"'
            );
        } elseif (!self::$aContainers[$sComponentName][$sServiceType]->offsetExists($sServiceName)) {
            throw new FactoryException(
                ucfirst($sServiceType) . ' "' . $sServiceName . '" is not provided by component "' . $sComponentName . '"'
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

        $aComponents = [];

        //  App
        $aComponents[] = (object) [
            'slug'     => 'app',
            'autoload' => static::extractAutoLoadItemsFromComposerJson(NAILS_APP_PATH . 'composer.json'),
        ];

        //  Modules
        foreach (_NAILS_GET_COMPONENTS() as $oModule) {
            $aComponents[] = (object) [
                'slug'     => $oModule->slug,
                'autoload' => !empty($oModule->autoload) ? $oModule->autoload : [],
            ];
        }

        //  Module items
        foreach ($aComponents as $oModule) {
            //  Helpers
            if (!empty($oModule->autoload->helpers)) {
                foreach ($oModule->autoload->helpers as $sHelper) {
                    if (is_array($sHelper)) {
                        list($sName, $sProvider) = $sHelper;
                        self::helper($sName, $sProvider);
                    } else {
                        self::helper($sHelper, $oModule->slug);
                    }
                }
            }
            //  Services
            if (!empty($oModule->autoload->services)) {
                foreach ($oModule->autoload->services as $sService) {
                    if (is_array($sHelper)) {
                        list($sName, $sProvider) = $sService;
                        self::service($sName, $sProvider);
                    } else {
                        self::service($sService, $oModule->slug);
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Extracts the autoload elements from a composer.json file
     *
     * @param string $sPath The path to the composer.json file
     *
     * @return object
     */
    protected static function extractAutoLoadItemsFromComposerJson($sPath)
    {
        $aOut = (object) ['helpers' => [], 'services' => []];
        if (file_exists($sPath)) {
            $oAppComposer = json_decode(file_get_contents($sPath));
            if (!empty($oAppComposer->extra->nails->autoload->helpers)) {
                foreach ($oAppComposer->extra->nails->autoload->helpers as $sHelper) {
                    $aOut->helpers[] = $sHelper;
                }
            }
            if (!empty($oAppComposer->extra->nails->autoload->services)) {
                foreach ($oAppComposer->extra->nails->autoload->services as $sService) {
                    $aOut->services[] = $sService;
                }
            }
        }

        return $aOut;
    }
}
