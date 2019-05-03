<?php

namespace Nails\Common\Resource\FileCache;

use Nails\Common\Resource;

/**
 * Class Item
 *
 * @package Nails\Common\Resource\FileCache
 */
class Item extends Resource
{
    /**
     * The item's key
     *
     * @var string
     */
    protected $sKey;

    /**
     * The item's path
     *
     * @var string
     */
    protected $sPath;

    // --------------------------------------------------------------------------

    /**
     * Return the item's key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->sKey;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the item's path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->sPath;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the contents of the file
     *
     * @return string
     */
    public function __toString(): string
    {
        return file_get_contents($this->getPath());
    }
}
