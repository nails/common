<?php

namespace Nails\Common\Service\Cache;

use Nails\Common\Exception\CacheException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Helper\Strings;
use Nails\Common\Interfaces\Service\Cache;
use Nails\Common\Resource\Cache\Item;
use Nails\Factory;

/**
 * Class CachePrivate
 *
 * @package Nails\Common\Service\Cache
 */
class CachePrivate implements Cache
{
    /**
     * The directory to use for the cache
     *
     * @var string
     */
    protected $sDir = NAILS_APP_PATH . 'cache' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR;

    // --------------------------------------------------------------------------

    /**
     * CachePrivate constructor.
     *
     * @param string|null $sDir
     *
     * @throws CacheException
     */
    public function __construct(string $sDir = null)
    {
        if (!is_null($sDir)) {
            $this->sDir = Strings::addTrailingSlash($sDir);
        }

        if (!is_writable($this->getDir())) {
            throw new CacheException(
                'Cache directory "' . $this->getDir() . '" is not writable'
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Return the absolute path for the cache
     *
     * @return string
     */
    public function getDir(): string
    {
        return $this->sDir;
    }

    // --------------------------------------------------------------------------

    /**
     * Writes to the cache
     *
     * @param mixed       $mData The data to write
     * @param string|null $sKey  The key of the item
     *
     * @return Item
     * @throws FactoryException
     */
    public function write($mData, string $sKey = null): Item
    {
        $sPath = $this->prepKey($sKey);

        file_put_contents($sPath, $mData);

        return $this->newItem($sKey, $sPath);
    }

    // --------------------------------------------------------------------------

    /**
     * Reads a file from the cache
     *
     * @param string $sKey The key of the item
     *
     * @return Item
     */
    public function read(string $sKey): Item
    {

    }

    // --------------------------------------------------------------------------

    /**
     * Delete a cache item
     *
     * @param string $sKey The key of the item
     *
     * @return bool
     */
    public function delete(string $sKey): bool
    {

    }

    // --------------------------------------------------------------------------

    /**
     * Test whether an item exists in the cache
     *
     * @param string $sKey
     *
     * @return bool
     */
    public function exists(string $sKey): bool
    {

    }

    // --------------------------------------------------------------------------

    /**
     * Prepares the cache key
     *
     * @param string $sKey The cache key to prepare
     *
     * @return string
     */
    protected function prepKey(string $sKey): string
    {
        return $this->getDir() . $sKey;
    }

    // --------------------------------------------------------------------------

    /**
     * Configures a new item object
     *
     * @param string $sKey  the item's key
     * @param string $sPath the item's path
     *
     * @return Item
     * @throws FactoryException
     */
    protected function newItem(string $sKey, string $sPath): Item
    {
        $oObj = (object) [
            'sKey'  => $sKey,
            'sPath' => $sPath,
        ];

        //  When testing the Factory isn't available
        if (defined('PHPUNIT_NAILS_COMMON_TEST_SUITE')) {
            $oItem = new Item($oObj);
        } else {
            /** @var Item $oItem */
            $oItem = Factory::resource('CacheItem', null, $oObj);
        }

        return $oItem;
    }
}
