<?php

/**
 * Implements a common API for caching in classes
 *
 * @package     Nails
 * @subpackage  common
 * @category    traits
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Traits;

trait Caching
{
    protected $aCacheValues = array();
    protected $aCacheKeys   = array();
    protected $sCacheMethod = 'LOCAL';

    // --------------------------------------------------------------------------

    /**
     * Saves an item to the cache
     * @param string $sKey   The cache key
     * @param mixed  $mValue The data to be cached
     */
    protected function setCache($sKey, $mValue)
    {
        if (empty($sKey)) {
            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $sPrefix = $this->getCachePrefix();

        // --------------------------------------------------------------------------

        switch ($this->sCacheMethod) {

            case 'MEMCACHED':

                //  @todo
                break;

            case 'LOCAL':
            default:

                $this->aCacheValues[md5($sPrefix . $sKey)] = serialize($mValue);
                $this->aCacheKeys[] = $sKey;
                break;
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches an item from the cache
     * @param  string $sKey The cache key
     * @return mixed
     */
    protected function getCache($sKey)
    {
        if (empty($sKey)) {
            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $sPrefix = $this->getCachePrefix();

        // --------------------------------------------------------------------------

        switch ($this->sCacheMethod) {

            case 'MEMCACHED':

                //  @TODO
                $mReturn = false;
                break;

            case 'LOCAL':
            default:

                if (isset($this->aCacheValues[md5($sPrefix . $sKey)])) {

                    $mReturn = unserialize($this->aCacheValues[md5($sPrefix . $sKey)]);

                } else {

                    $mReturn = false;

                }
                break;
        }

        return $mReturn;
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes an item from the cache
     * @param  string $sKey The cache key
     * @return boolean
     */
    protected function unsetCache($sKey)
    {
        if (empty($sKey)) {
            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $sPrefix = $this->getCachePrefix();

        // --------------------------------------------------------------------------

        switch ($this->sCacheMethod) {

            case 'MEMCACHED':

                //  @TODO
                break;

            case 'LOCAL':
            default:

                unset($this->aCacheValues[md5($sPrefix . $sKey)]);

                $sIndex = array_search($sKey, $this->aCacheKeys);

                if ($sIndex !== false) {

                    unset($this->aCacheKeys[$sIndex]);
                }
                break;
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Unsets all defined cache keys
     * @return void
     */
    public function clearCache()
    {
        if (!empty($this->aCacheKeys)) {
            foreach ($this->aCacheKeys as $sKey) {
                $this->unsetCache($sKey);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * In order to avoid collisions between classes a prefix is used; this method
     * defines the cache key prefix using the calling class' name.
     * @return string
     */
    protected function getCachePrefix()
    {
        return get_called_class();
    }
}
