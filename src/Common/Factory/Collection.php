<?php

/**
 * This class contains a collection of items
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Factory;

use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Tools;

/**
 * Class Collection
 *
 * @package Nails\Common\Factory
 */
abstract class Collection implements \Iterator, \Countable
{
    /**
     * The collection
     *
     * @var array
     */
    protected $aCollection = [];

    /**
     * The current index
     *
     * @var int
     */
    protected $iIndex = 0;

    // --------------------------------------------------------------------------

    /**
     * Collection constructor.
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
     * @param mixed $mItem The item to add to the collection
     *
     * @return $this
     */
    public function add($mItem): self
    {
        $this->aCollection[] = $mItem;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Filters the collection
     *
     * @param callable $cFilter The callback to filter by
     *
     * @return Collection
     */
    public function filter(callable $cFilter): self
    {
        return new static(
            array_values(
                array_filter(
                    $this->aCollection,
                    $cFilter
                ) ?? []
            )
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->aCollection);
    }
}
