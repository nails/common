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

use Nails\Common\Helper\ArrayHelper;

final class Component
{
    private $slug;
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

        $this->slug         = ArrayHelper::getFromArray('name', $aPackage);
        $this->namespace    = ArrayHelper::getFromArray('namespace', $aNailsData);
        $this->name         = ArrayHelper::getFromArray('name', $aNailsData, $this->slug);
        $this->description  = ArrayHelper::getFromArray('description', $aNailsData, ArrayHelper::getFromArray('description', $aPackage));
        $this->homepage     = ArrayHelper::getFromArray('homepage', $aNailsData, ArrayHelper::getFromArray('homepage', $aPackage));
        $this->authors      = ArrayHelper::getFromArray('authors', $aNailsData, ArrayHelper::getFromArray('authors', $aPackage));
        $this->path         = rtrim($sAbsolutePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->relativePath = rtrim($sRelativePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->moduleName   = ArrayHelper::getFromArray('moduleName', $aNailsData, '');
        $this->data         = ArrayHelper::getFromArray('data', $aNailsData, null);
        $this->type         = ArrayHelper::getFromArray('type', $aNailsData, '');
        $this->subType      = ArrayHelper::getFromArray('subType', $aNailsData, '');
        $this->forModule    = ArrayHelper::getFromArray('forModule', $aNailsData, '');
        $this->autoload     = ArrayHelper::getFromArray('autoload', $aNailsData, null);
        $this->scripts      = ArrayHelper::getFromArray('scripts', $aNailsData, (object) []);
        $this->fromApp      = $bIsApp;
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
}
