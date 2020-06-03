<?php

/**
 * This class contains a collection of component classes
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Factory\Component;

use Nails\Common\Factory\Collection;
use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Tools;

/**
 * Class Component
 *
 * @package Nails\Common\Factory\Component
 */
class ClassCollection extends Collection
{
    /**
     * Adds a new item to the collection
     *
     * @param mixed $mItem The item to add to the collection
     *
     * @return $this
     */
    public function add($mItem): Collection
    {
        if (class_exists($mItem)) {
            parent::add($mItem);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Filters the collection by interface
     *
     * @param string $sInterface The interface the class must implement
     *
     * @return $this
     */
    public function whichImplement(string $sInterface): self
    {
        return $this->filter(function ($sItem) use ($sInterface) {
            return Tools::classImplements($sItem, $sInterface);
        });
    }

    // --------------------------------------------------------------------------

    /**
     * Filters the collection by trait
     *
     * @param string $sTrait The trait the class must use
     *
     * @return $this
     */
    public function whichUse(string $sTrait): self
    {
        return $this->filter(function ($sItem) use ($sTrait) {
            return Tools::classUses($sItem, $sTrait);
        });
    }

    // --------------------------------------------------------------------------

    /**
     * Filters the collection by parent
     *
     * @param string $sParent The parent the class must extend
     *
     * @return $this
     */
    public function whichExtend(string $sParent): self
    {
        return $this->filter(function ($sItem) use ($sParent) {
            return Tools::classExtends($sItem, $sParent);
        });
    }

    // --------------------------------------------------------------------------

    /**
     * Filters the collection by classes which can be instantiated
     *
     * @return $this
     */
    public function whichCanBeInstantiated(): self
    {
        return $this->filter(function ($sItem) {
            return Tools::classCanBeInstantiated($sItem);
        });
    }
}
