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
use Nails\Common\Factory\Component;
use Nails\Common\Helper\File;
use Nails\Common\Model\Base;
use Nails\Common\Resource;
use Pimple\Container;

class Factory
{
    /**
     * The slug to use for app services
     *
     * @var string
     */
    public static $oAppSlug = 'app';

    /**
     * Contains an array of containers; each component gets its own element so as
     * to avoid naming collisions.
     *
     * @var array
     */
    private static $aContainers = [];

    /**
     * Tracks which items have been loaded
     *
     * @var array
     */
    private static $aLoadedItems = ['SERVICES' => [], 'MODELS' => []];

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
    public static function setup(): void
    {
        $aComponents          = Components::available();
        self::$aContainers    = [];
        self::$aLoadedHelpers = [];
        $aDiscoveredServices  = [];

        foreach ($aComponents as $oComponent) {
            $aDiscoveredServices[$oComponent->slug] = self::findServicesForComponent($oComponent);
        }
        $aDiscoveredServices = array_filter($aDiscoveredServices);

        foreach ($aDiscoveredServices as $sComponentName => $aComponentServices) {

            //  Properties
            if (!empty($aComponentServices['properties'])) {
                if (empty(self::$aContainers[$sComponentName]['properties'])) {
                    self::$aContainers[$sComponentName]['properties'] = new Container();
                }
                foreach ($aComponentServices['properties'] as $sKey => $mValue) {
                    self::$aContainers[$sComponentName]['properties'][$sKey] = $mValue;
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
                if (empty(self::$aContainers[$sComponentName]['services'])) {
                    self::$aContainers[$sComponentName]['services'] = new Container();
                }
                foreach ($aComponentServices['services'] as $sKey => $cCallable) {
                    self::$aContainers[$sComponentName]['services'][$sKey] = function () use ($sKey, $cCallable) {
                        return $cCallable;
                    };
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
                if (empty(self::$aContainers[$sComponentName]['models'])) {
                    self::$aContainers[$sComponentName]['models'] = new Container();
                }
                foreach ($aComponentServices['models'] as $sKey => $cCallable) {
                    self::$aContainers[$sComponentName]['models'][$sKey] = function () use ($cCallable) {
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
                if (empty(self::$aContainers[$sComponentName]['factories'])) {
                    self::$aContainers[$sComponentName]['factories'] = new Container();
                }
                foreach ($aComponentServices['factories'] as $sKey => $cCallable) {

                    $cWrapper = function () use ($cCallable) {
                        return $cCallable;
                    };

                    self::$aContainers[$sComponentName]['factories'][$sKey] = self::$aContainers[$sComponentName]['factories']
                        ->factory($cWrapper);
                }
            }

            // --------------------------------------------------------------------------

            /**
             * Resources
             * All resources are wrapped in a closure, this is so that on fetch we can
             * pass in any additional arguments as parameters which can be used in the
             * item's constructor
             */
            if (!empty($aComponentServices['resources'])) {
                if (empty(self::$aContainers[$sComponentName]['resources'])) {
                    self::$aContainers[$sComponentName]['resources'] = new Container();
                }
                foreach ($aComponentServices['resources'] as $sKey => $cCallable) {

                    $cWrapper = function () use ($cCallable) {
                        return $cCallable;
                    };

                    self::$aContainers[$sComponentName]['resources'][$sKey] = self::$aContainers[$sComponentName]['resources']
                        ->factory($cWrapper);
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Look for a components's services.php file, allowing for app and/or environment overrides
     *
     * @param Component $oComponent The component name to search for
     *
     * @return array
     */
    private static function findServicesForComponent(Component $oComponent): array
    {
        $sPath = $oComponent->path;
        $sSlug = $oComponent->slug;

        return self::findServicesAtPaths(
            array_filter([
                $sPath . static::compilePath(['services', 'services.php']),
                $oComponent->fromApp
                    ? $sPath . static::compilePath(['application', 'services', 'services.php'])
                    : null,
                $oComponent->fromApp
                    ? $sPath . static::compilePath(['vendor', $sSlug, 'services', 'services.php'])
                    : null,
                $oComponent->fromApp
                    ? $sPath . static::compilePath(['application', 'services', $sSlug, 'services.php'])
                    : null,
            ])
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles an array into a path
     *
     * @param string[] $aPath An array of path segments
     *
     * @return string
     */
    private static function compilePath(array $aPath): string
    {
        return implode(DIRECTORY_SEPARATOR, $aPath);
    }

    // --------------------------------------------------------------------------

    /**
     * Look for the app's services.php file, allowing for environment overrides
     *
     * @return array
     */
    private static function findServicesForApp(): array
    {
        return self::findServicesAtPaths([
            'application/services/services.php',
            'services/services.php',
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Traverses an array of paths until one exits
     *
     * @param array $aPaths An array of paths to look for
     *
     * @return array
     */
    private static function findServicesAtPaths(array $aPaths): array
    {
        foreach ($aPaths as $sPath) {
            if (File::fileExistsCS(realpath($sPath))) {
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
    public static function property(string $sPropertyName, ?string $sComponentName = '')
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
    public static function setProperty(string $sPropertyName, $mPropertyValue, ?string $sComponentName = ''): void
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
     * @return object
     * @throws FactoryException
     * @todo (Pablo - 2019-03-22) - Consider forcing all servcies to extend a base class (and add a typehint)
     */
    public static function service(string $sServiceName, ?string $sComponentName = ''): object
    {
        return static::getServiceOrModel(
            static::$aLoadedItems['SERVICES'],
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
     * @return Base
     * @throws FactoryException
     */
    public static function model(string $sModelName, ?string $sComponentName = ''): Base
    {
        return static::getServiceOrModel(
            static::$aLoadedItems['MODELS'],
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
    private static function getServiceOrModel(
        array &$aTrackerArray,
        string $sType,
        string $sName,
        string $sComponent,
        array $aParamaters
    ): object {
        /**
         * We track them like this because we need to return the instance of the
         * item, not the closure. If we don't do this then we will always get
         * a new instance of the item, which is undesireable.
         */

        $sKey = md5($sComponent . $sName);

        if (!array_key_exists($sKey, $aTrackerArray)) {

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
     * @return object
     * @throws FactoryException
     */
    public static function factory(string $sFactoryName, ?string $sComponentName = ''): object
    {
        return call_user_func_array(
            self::getService('factories', $sFactoryName, $sComponentName),
            array_slice(func_get_args(), 2)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Return a resource from the container.
     *
     * @param string $sResourceName  The resource name
     * @param string $sComponentName The name of the component which provides the resource
     *
     * @return Resource
     * @throws FactoryException
     */
    public static function resource(string $sResourceName, ?string $sComponentName = ''): Resource
    {
        return call_user_func_array(
            self::getService('resources', $sResourceName, $sComponentName),
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
     * @return void
     * @throws FactoryException
     */
    public static function helper(string $sHelperName, ?string $sComponentName = ''): void
    {
        $sComponentName = empty($sComponentName) ? 'nails/common' : $sComponentName;
        $oComponent     = Components::getBySlug($sComponentName);

        if (empty(self::$aLoadedHelpers[$sComponentName][$sHelperName])) {

            if (empty(self::$aLoadedHelpers[$sComponentName])) {
                self::$aLoadedHelpers[$sComponentName] = [];
            }

            $aPaths = [
                $oComponent->path . static::compilePath(['application', 'helpers', $sHelperName . '.php']),
                $oComponent->path . static::compilePath(['helpers', $sHelperName . '.php']),
            ];

            $iNumLoaded = 0;
            foreach ($aPaths as $sPath) {
                if (File::fileExistsCS($sPath)) {
                    $iNumLoaded++;
                    require_once $sPath;
                }
            }

            if (!$iNumLoaded) {
                throw new FactoryException(
                    'Helper "' . $sComponentName . '/' . $sHelperName . '" does not exist.'
                );
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
     * @return mixed
     * @throws FactoryException
     */
    private static function getService(string $sServiceType, string $sServiceName, ?string $sComponentName = '')
    {
        $sComponentName = empty($sComponentName) ? 'nails/common' : $sComponentName;

        if (!array_key_exists($sComponentName, self::$aContainers)) {
            throw new FactoryException(
                'No containers registered for ' . $sComponentName
            );
        } elseif (!array_key_exists($sServiceType, self::$aContainers[$sComponentName])) {
            throw new FactoryException(
                'No ' . $sServiceType . ' containers registered for ' . $sComponentName
            );
        } elseif (!self::$aContainers[$sComponentName][$sServiceType]->offsetExists($sServiceName)) {
            throw new FactoryException(
                ucfirst($sServiceType) . '::' . $sServiceName . ' is not provided by ' . $sComponentName
            );
        }

        return self::$aContainers[$sComponentName][$sServiceType][$sServiceName];
    }

    // --------------------------------------------------------------------------

    /**
     * Auto-loads items at startup
     */
    public static function autoload(): void
    {
        //  CI base helpers
        require_once BASEPATH . 'core/Common.php';

        //  Cherry pick the app, autoload it's items last, we do this so common's
        //  helpers are available early as they're most liekly to be used by the
        //  app or modules
        $aComponents = Components::available();
        $oApp        = array_shift($aComponents);
        array_push($aComponents, $oApp);

        foreach ($aComponents as $oModule) {
            //  Helpers
            if (!empty($oModule->autoload->helpers)) {
                foreach ($oModule->autoload->helpers as $sHelper) {
                    if (is_array($sHelper)) {
                        [$sName, $sProvider] = $sHelper;
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
                        [$sName, $sProvider] = $sService;
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
     * @return \stdClass
     */
    protected static function extractAutoLoadItemsFromComposerJson(string $sPath): \stdClass
    {
        $oOut = (object) ['helpers' => [], 'services' => []];
        if (File::fileExistsCS($sPath)) {
            $oAppComposer = json_decode(file_get_contents($sPath));
            if (!empty($oAppComposer->extra->nails->autoload->helpers)) {
                foreach ($oAppComposer->extra->nails->autoload->helpers as $sHelper) {
                    $oOut->helpers[] = $sHelper;
                }
            }
            if (!empty($oAppComposer->extra->nails->autoload->services)) {
                foreach ($oAppComposer->extra->nails->autoload->services as $sService) {
                    $oOut->services[] = $sService;
                }
            }
        }

        return $oOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Allows for a service to be destroyed so that a subsequent request will yeild a new instance
     *
     * @param string $sServiceName   The name of the service to destroy
     * @param string $sComponentName The name of the component which provides the service
     *
     * @return bool
     */
    public static function destroyService(string $sServiceName, ?string $sComponentName = ''): bool
    {
        return static::destroyServiceOrModel(
            static::$aLoadedItems['SERVICES'],
            null,
            $sServiceName,
            $sComponentName
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Allows for a model to be destroyed so that a subsequent request will yeild a new instance
     *
     * @param string $sModelName     The name of the model to destroy
     * @param string $sComponentName The name of the component which provides the model
     *
     * @return bool
     */
    public static function destroyModel(string $sModelName, ?string $sComponentName = ''): bool
    {
        return static::destroyServiceOrModel(
            static::$aLoadedItems['MODELS'],
            null,
            $sModelName,
            $sComponentName
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Destroys an object by its instance
     *
     * @param object $oInstance The instance to destroy
     *
     * @return bool
     */
    public static function destroy(object $oInstance): bool
    {
        foreach (static::$aLoadedItems['SERVICES'] as $sKey => $oItem) {
            if ($oItem === $oInstance) {
                return static::destroyServiceOrModel(static::$aLoadedItems['SERVICES'], $sKey);
            }
        }

        foreach (static::$aLoadedItems['MODELS'] as $sKey => $oItem) {
            if ($oItem === $oInstance) {
                return static::destroyServiceOrModel(static::$aLoadedItems['MODELS'], $sKey);
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Destroys an item in the tracker array
     *
     * @param array  $aTrackerArray The tracker array to destroy from
     * @param string $sKey          The key to destroy, if known
     * @param string $sName         The name of the item being destroyed, used to generate the key
     * @param string $sComponent    The name of the component which provides the item, used to generate the key
     *
     * @return bool
     */
    private static function destroyServiceOrModel(
        array &$aTrackerArray,
        string $sKey,
        string $sName = null,
        string $sComponent = null
    ): bool {
        if (!$sKey) {
            $sKey = md5($sComponent . $sName);
        }

        if (array_key_exists($sKey, $aTrackerArray)) {
            unset($aTrackerArray[$sKey]);
            return true;
        } else {
            return false;
        }
    }
}
