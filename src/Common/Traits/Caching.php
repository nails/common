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
    protected $_cache_values    = array();
    protected $_cache_keys      = array();
    protected $_cache_method    = 'LOCAL';

    // --------------------------------------------------------------------------

    /**
     * Saves an item to the cache
     * @param string $key   The cache key
     * @param mixed  $value The data to be cached
     */
    protected function _set_cache($key, $value)
    {
        if (empty($key)) {

            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $_prefix = $this->_cache_prefix();

        // --------------------------------------------------------------------------

        switch ($this->_cache_method) {

            case 'MEMCACHED':

                //  @TODO
                break;

            case 'LOCAL':
            default:

                $this->_cache_values[md5($_prefix . $key)] = serialize($value);
                $this->_cache_keys[] = $key;
                break;
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches an item from the cache
     * @param  string $key The cache key
     * @return mixed
     */
    protected function _get_cache($key)
    {
        if (empty($key)) {

            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $prefix = $this->_cache_prefix();

        // --------------------------------------------------------------------------

        switch ($this->_cache_method) {

            case 'MEMCACHED':

                //  @TODO
                $return = false;
                break;

            case 'LOCAL':
            default:

                if (isset($this->_cache_values[md5($prefix . $key)])) {

                    $return = unserialize($this->_cache_values[md5($prefix . $key)]);

                } else {

                    $return = false;

                }
                break;
        }

        return $return;
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes an item from the cache
     * @param  string $key The cache key
     * @return boolean
     */
    protected function _unset_cache($key)
    {
        if (empty($key)) {

            return false;
        }

        // --------------------------------------------------------------------------

        //  Prep the key, the key should have a prefix unique to this model
        $prefix = $this->_cache_prefix();

        // --------------------------------------------------------------------------

        switch ($this->_cache_method) {

            case 'MEMCACHED':

                //  @TODO
                break;

            case 'LOCAL':
            default:

                unset($this->_cache_values[md5($prefix . $key)]);

                $_key = array_search($key, $this->_cache_keys);

                if ($_key !== false) {

                    unset($this->_cache_keys[$_key]);
                }
                break;
        }

        // --------------------------------------------------------------------------

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * In order to avoid collission between classes a prefix is used; this method
     * defines the cache key prefix using the calling class' name.
     * @return string
     */
    protected function _cache_prefix()
    {
        return get_called_class();
    }
}