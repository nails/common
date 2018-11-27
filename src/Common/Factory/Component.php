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

        $this->slug         = getFromArray('name', $aPackage);
        $this->namespace    = getFromArray('namespace', $aNailsData);
        $this->name         = getFromArray('name', $aNailsData, $this->slug);
        $this->description  = getFromArray('description', $aNailsData, getFromArray('description', $aPackage));
        $this->homepage     = getFromArray('homepage', $aNailsData, getFromArray('homepage', $aPackage));
        $this->authors      = getFromArray('authors', $aNailsData, getFromArray('authors', $aPackage));
        $this->path         = rtrim($sAbsolutePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->relativePath = rtrim($sRelativePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->moduleName   = getFromArray('moduleName', $aNailsData, '');
        $this->data         = getFromArray('data', $aNailsData, null);
        $this->type         = getFromArray('type', $aNailsData, '');
        $this->subType      = getFromArray('subType', $aNailsData, '');
        $this->forModule    = getFromArray('forModule', $aNailsData, '');
        $this->autoload     = getFromArray('autoload', $aNailsData, null);
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
