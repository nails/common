<?php

namespace Nails\Common\Service\FileCache;

use Nails\Common\Exception\Directory\DirectoryDoesNotExistException;
use Nails\Common\Exception\Directory\DirectoryIsNotWritableException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Helper\Strings;
use Nails\Common\Interfaces;
use Nails\Common\Resource\FileCache\Item;
use Nails\Factory;

/**
 * Class Driver
 *
 * @package Nails\Common\Service\Cache
 */
class Driver implements Interfaces\Service\FileCache\Driver
{
    /**
     * The directory to use for the cache
     *
     * @var string
     */
    protected $sDir = NAILS_APP_PATH . 'cache' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR;

    // --------------------------------------------------------------------------

    /**
     * Cache constructor.
     *
     * @param string|null $sDir
     *
     * @throws DirectoryDoesNotExistException
     * @throws DirectoryIsNotWritableException
     */
    public function __construct(string $sDir = null)
    {
        if (!is_null($sDir)) {
            $this->sDir = Strings::addTrailingSlash($sDir);
        }

        if (!is_dir($this->getDir())) {
            throw new DirectoryDoesNotExistException(
                'Cache directory "' . $this->getDir() . '" does not exist'
            );
        } elseif (!is_writable($this->getDir())) {
            throw new DirectoryIsNotWritableException(
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
        //  Generate a key if one isn't explicitly specified
        if (is_null($sKey)) {
            $sKey = md5(microtime(true));
        }

        $sPath = $this->prepKey($sKey);

        file_put_contents($sPath, $mData);

        return $this->newItem($sKey);
    }

    // --------------------------------------------------------------------------

    /**
     * Reads a file from the cache
     *
     * @param string $sKey The key of the item
     *
     * @return Item
     * @throws FactoryException
     */
    public function read(string $sKey): Item
    {
        return $this->exists($sKey) ? $this->newItem($sKey) : null;
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
        if ($this->exists($sKey)) {
            return @unlink($this->prepKey($sKey));
        } else {
            return false;
        }
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
        return file_exists($this->prepKey($sKey));
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
     * @param string $sKey The item's key
     *
     * @return Item
     * @throws FactoryException
     */
    protected function newItem(string $sKey): Item
    {
        $oObj = (object) [
            'sKey'  => $sKey,
            'sPath' => $this->prepKey($sKey),
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
