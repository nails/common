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

use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Tools;

/**
 * Class Component
 *
 * @package Nails\Common\Factory\Component
 */
final class ClassCollection implements \Iterator
{
    /**
     * The collection
     *
     * @var array
     */
    private $aCollection = [];

    /**
     * The current index
     *
     * @var int
     */
    private $iIndex = 0;

    // --------------------------------------------------------------------------

    /**
     * ClassCollection constructor.
     *
     * @param array $aCollection
     */
    public function __construct(array $aCollection = [])
    {
        $this->aCollection = $aCollection;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the current element
     *
     * @return mixed
     */
    public function current()
    {
        return $this->aCollection[$this->iIndex];
    }

    // --------------------------------------------------------------------------

    /**
     * Move forward to the next element
     */
    public function next(): void
    {
        $this->iIndex++;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the key of the current element
     *
     * @return bool|float|int|string|null
     */
    public function key()
    {
        return $this->iIndex;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->aCollection[$this->key()]);
    }

    // --------------------------------------------------------------------------

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind(): void
    {
        $this->iIndex = 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Reverse the collection
     */
    public function reverse(): void
    {
        $this->aCollection = array_reverse($this->aCollection);
        $this->rewind();
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a new item to the collection
     *
     * @param string $sItem
     *
     * @return $this
     */
    public function add(string $sItem): self
    {
        if (class_exists($sItem)) {
            $this->aCollection[] = $sItem;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Filters the collection
     *
     * @param callable $cFilter The callback to filter by
     *
     * @return $this
     */
    public function filter(callable $cFilter): self
    {
        return new static(
            array_filter(
                $this->aCollection,
                $cFilter
            ) ?? []
        );
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
}
