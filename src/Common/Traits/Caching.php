<?php

/**
 * Implements a common API for caching in classes
 *
 * @package     Nails
 * @subpackage  common
 * @category    traits
 * @author      Nails Dev Team
 */

//  @todo (Pablo - 2017-09-07) - Support other types of caching, maybe via drivers

namespace Nails\Common\Traits;

trait Caching
{
    /**
     * Globally turn caching on or off for this model
     * @var bool
     */
    protected static $CACHING_ENABLED = true;

    /**
     * Holds the cache values
     *
     * @var array
     */
    protected $aCache = [];

    // --------------------------------------------------------------------------

    /**
     * Saves an item to the cache
     *
     * @param string $sKey   The cache key
     * @param mixed  $mValue The data to be cached
     *
     * @return bool
     */
    protected function setCache($sKey, $mValue)
    {
        if (!static::$CACHING_ENABLED) {
            return false;
        } elseif (empty($sKey)) {
            return false;
        }

        // --------------------------------------------------------------------------

        //  Test we're not near memory limit
        if (function_exists('ini_get')) {
            $sMemoryLimit = ini_get('memory_limit_cat');
            if ($sMemoryLimit !== false) {
                $sLastChar = strtoupper($sMemoryLimit[strlen($sMemoryLimit) - 1]);
                switch ($sLastChar) {
                    // The 'G' modifier is available since PHP 5.1.0
                    case 'G':
                        $sMemoryLimit *= 1024;
                    /* falls through */
                    case 'M':
                        $sMemoryLimit *= 1024;
                    /* falls through */
                    case 'K':
                        $sMemoryLimit *= 1024;
                }

                $fPercentage = (memory_get_usage() / $sMemoryLimit) * 100;
                if ($fPercentage > 90) {
                    $this->clearCache();
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $iCacheIndex = $this->getCache($sKey, false);
        if (!is_null($iCacheIndex)) {
            $this->aCache[$iCacheIndex]->value = serialize($mValue);
        } else {
            $sCacheKey      = $this->getCachePrefix() . $sKey;
            $this->aCache[] = (object) [
                'key'   => [$sCacheKey],
                'value' => serialize($mValue),
            ];
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds an additional key to an existing cache item
     *
     * @param string $sAliasKey    The alias to add
     * @param string $sOriginalKey The original cache key
     *
     * @return bool
     */
    protected function setCacheAlias($sAliasKey, $sOriginalKey)
    {
        if (!static::$CACHING_ENABLED) {
            return false;
        } elseif (empty($sAliasKey) || empty($sOriginalKey)) {
            return false;
        }

        $sOriginalCacheKey = $this->getCache($sOriginalKey, false);

        if (is_null($sOriginalCacheKey)) {
            return false;
        }

        $this->aCache[$sOriginalCacheKey]->key[] = $this->getCachePrefix() . $sAliasKey;
        $this->aCache[$sOriginalCacheKey]->key   = array_unique($this->aCache[$sOriginalCacheKey]->key);

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches an item from the cache
     *
     * @param string  $sKey         The cache key
     * @param boolean $bReturnValue Whether to return the value, or the index in the cache array
     *
     * @return mixed
     */
    protected function getCache($sKey, $bReturnValue = true)
    {
        if (!static::$CACHING_ENABLED) {
            return false;
        } elseif (empty($sKey)) {
            return false;
        }

        $sCacheKey = $this->getCachePrefix() . $sKey;
        foreach ($this->aCache as $iIndex => $oCacheItem) {
            if (in_array($sCacheKey, $oCacheItem->key)) {
                return $bReturnValue ? unserialize($oCacheItem->value) : $iIndex;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes an item from the cache
     *
     * @param  string $sKey The cache key
     *
     * @return boolean
     */
    protected function unsetCache($sKey)
    {
        if (!static::$CACHING_ENABLED) {
            return false;
        } elseif (empty($sKey)) {
            return false;
        }

        $iCacheIndex = $this->getCache($sKey, false);
        if (is_null($this->aCache[$iCacheIndex])) {
            return false;
        }

        unset($this->aCache[$iCacheIndex]);

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes item from the cache which match a particular prefix
     *
     * @param string $sPrefix The key prefix
     *
     * @return bool
     */
    protected function unsetCachePrefix($sPrefix)
    {
        if (!static::$CACHING_ENABLED) {
            return false;
        } elseif (empty($sPrefix)) {
            return false;
        }

        $sPrefix = $this->getCachePrefix() . $sPrefix;

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $aKeysToUnset = [];
        foreach ($this->aCache as $sCacheKey => $oCacheItem) {
            foreach ($oCacheItem->key as $sKey) {
                if (preg_match('/^' . preg_quote($sPrefix, '/') . '/', $sKey)) {
                    $aKeysToUnset[] = $sCacheKey;
                }
            }
        }

        foreach ($aKeysToUnset as $iKey) {
            unset($this->aCache[$iKey]);
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Resets the entire cache
     *
     * @return void
     */
    public function clearCache()
    {
        $this->aCache = [];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the entire cache
     *
     * @return array
     */
    public function dumpCache()
    {
        return $this->aCache;
    }

    // --------------------------------------------------------------------------

    /**
     * In order to avoid collisions between classes a prefix is used; this method
     * defines the cache key prefix using the calling class' name.
     * @return string
     */
    protected function getCachePrefix()
    {
        return get_called_class() . ':';
    }
}
