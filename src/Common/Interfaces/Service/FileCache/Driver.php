<?php

namespace Nails\Common\Interfaces\Service\FileCache;

use Nails\Common\Resource\FileCache\Item;

/**
 * Interface Driver
 *
 * @package Nails\Common\Interfaces\Service
 */
interface Driver
{
    /**
     * Return the absolute path for the cache
     *
     * @return string
     */
    public function getDir(): string;

    // --------------------------------------------------------------------------

    /**
     * Writes to the cache
     *
     * @param mixed       $mData The data to write
     * @param string|null $sKey  The key of the item
     *
     * @return Item
     */
    public function write($mData, string $sKey = null): Item;

    // --------------------------------------------------------------------------

    /**
     * Reads a file from the cache
     *
     * @param string $sKey The key of the item
     *
     * @return Item
     */
    public function read(string $sKey): Item;

    // --------------------------------------------------------------------------

    /**
     * Delete a cache item
     *
     * @param string $sKey The key of the item
     *
     * @return bool
     */
    public function delete(string $sKey): bool;

    // --------------------------------------------------------------------------

    /**
     * Test whether an item exists in the cache
     *
     * @param string $sKey
     *
     * @return bool
     */
    public function exists(string $sKey): bool;
}
