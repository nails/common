<?php

/**
 * This class represents Nails components
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Factory;

use Nails\Common\Factory\Component\ClassCollection;
use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Directory;

/**
 * Class Component
 *
 * @package Nails\Common\Factory
 *
 * @property string    $slug
 * @property string    $slugUrl
 * @property string    $namespace
 * @property string    $name
 * @property string    $description
 * @property string    $homepage
 * @property array     $authors
 * @property string    $path
 * @property string    $relativePath
 * @property string    $moduleName
 * @property \stdClass $data
 * @property string    $type
 * @property string    $subType
 * @property string    $forModule
 * @property \stdClass $autoload
 * @property \stdClass $scripts
 * @property bool      $fromApp
 */
final class Component
{
    private $slug;
    private $slugUrl;
    private $namespace;
    private $name;
    private $description;
    private $homepage;
    private $authors;
    private $path;
    private $relativePath;
    private $moduleName;
    private $data;
    private $type;
    private $subType;
    private $forModule;
    private $autoload;
    private $scripts;
    private $fromApp;

    // --------------------------------------------------------------------------

    /**
     * Component constructor.
     *
     * @param stdClass $oPackage      The package definition
     * @param string   $sAbsolutePath The absolute path to the package
     * @param string   $sRelativePath The relative path to the package
     * @param boolean  $bIsApp        Whether this is a component supplied by the app
     */
    public function __construct($oPackage, $sAbsolutePath, $sRelativePath, $bIsApp)
    {
        $aPackage   = (array) $oPackage;
        $aNailsData = !empty($aPackage['extra']->nails) ? (array) $aPackage['extra']->nails : [];

        $this->slug         = ArrayHelper::get(['slug', 'name'], $aPackage);
        $this->slugUrl      = str_replace('/', '-', $this->slug);
        $this->namespace    = ArrayHelper::get('namespace', $aNailsData);
        $this->name         = ArrayHelper::get('name', $aNailsData, $this->slug);
        $this->description  = ArrayHelper::get('description', $aNailsData, ArrayHelper::get('description', $aPackage));
        $this->homepage     = ArrayHelper::get('homepage', $aNailsData, ArrayHelper::get('homepage', $aPackage));
        $this->authors      = ArrayHelper::get('authors', $aNailsData, ArrayHelper::get('authors', $aPackage));
        $this->path         = rtrim($sAbsolutePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->relativePath = rtrim($sRelativePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->moduleName   = ArrayHelper::get('moduleName', $aNailsData, '');
        $this->data         = ArrayHelper::get('data', $aNailsData, (object) []);
        $this->type         = ArrayHelper::get('type', $aNailsData, '');
        $this->subType      = ArrayHelper::get('subType', $aNailsData, '');
        $this->forModule    = ArrayHelper::get('forModule', $aNailsData, '');
        $this->autoload     = ArrayHelper::get('autoload', $aNailsData, (object) []);
        $this->scripts      = ArrayHelper::get('scripts', $aNailsData, (object) []);
        $this->fromApp      = $bIsApp;

        if (!empty($this->namespace)) {
            $this->namespace = '\\' . ltrim($this->namespace, '\\');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value of private properties
     *
     * @param string $sProperty The property being called
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        if (property_exists($this, $sProperty)) {
            return $this->{$sProperty};
        } else {
            trigger_error('Undefined property ' . get_class() . '::$' . $sProperty);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether a property isset, or not
     *
     * @param string $sProperty The property being checked
     *
     * @return bool
     */
    public function __isset($sProperty)
    {
        return property_exists($this, $sProperty);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the root paths used by a namespace
     *
     * @return string[]
     */
    public function getNamespaceRootPaths(): array
    {
        $aPsr4NameSpaces = require(NAILS_APP_PATH . 'vendor/composer/autoload_psr4.php');
        return getFromArray(ltrim($this->namespace, '\\'), $aPsr4NameSpaces, []);
    }

    // --------------------------------------------------------------------------

    /**
     * Find classes in this component's namespace
     *
     * @param string $sNamespace The namespace to filter by
     *
     * @return ClassCollection
     */
    public function findClasses(string $sNamespace = ''): ClassCollection
    {
        $oCollection = new ClassCollection();
        $aNamespace  = explode('\\', $sNamespace);
        $aPaths      = $this->getNamespaceRootPaths();

        foreach ($aPaths as $sPath) {

            $sPath  = $sPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $aNamespace);
            $aFiles = Directory::map($sPath, null, false);

            foreach ($aFiles as $sFile) {

                $sClass = $this->namespace . implode(
                        '\\',
                        array_merge(
                            $aNamespace,
                            explode(DIRECTORY_SEPARATOR, preg_replace('/\.php$/', '', $sFile))
                        )
                    );

                $oCollection->add($sClass);
            }
        }

        return $oCollection;
    }
}
