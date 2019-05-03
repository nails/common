<?php

/**
 * This class provides a consistent API for querying the cache
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\CacheException;
use Nails\Common\Interfaces;
use Nails\Common\Resource;

/**
 * Class Cache
 *
 * @package Nails\Common\Service
 *
 * @method string getDir()
 * @method Resource\Cache\Item read(string $sKey)
 * @method Resource\Cache\Item write($mData, string $sKey = null)
 * @method bool delete(string $sKey)
 * @method bool exists(string $sKey)
 */
class Cache
{
    /**
     * The Private cache driver
     *
     * @var Interfaces\Service\Cache\Driver\Cache
     */
    protected $oDriver;

    /**
     * The Public cache driver
     *
     * @var Interfaces\Service\Cache\Driver\AccessibleByUrl
     */
    protected $oAccessibleDriver;

    // --------------------------------------------------------------------------

    /**
     * Cache constructor.
     *
     * @param Interfaces\Service\Cache\Driver\Cache           $oDriver           The private cache
     * @param Interfaces\Service\Cache\Driver\AccessibleByUrl $oAccessibleDriver The public cache
     */
    public function __construct(
        Interfaces\Service\Cache\Driver\Cache $oDriver,
        Interfaces\Service\Cache\Driver\AccessibleByUrl $oAccessibleDriver
    ) {
        $this->oDriver           = $oDriver;
        $this->oAccessibleDriver = $oAccessibleDriver;
    }

    // --------------------------------------------------------------------------

    /**
     * Routes calls to the private cache
     *
     * @param string $sMethod    The method being called
     * @param array  $aArguments Any arguments to pass
     *
     * @return mixed
     * @throws CacheException
     */
    public function __call(string $sMethod, array $aArguments)
    {
        if (method_exists($this->oDriver, $sMethod)) {
            return call_user_func_array([$this->oDriver, $sMethod], $aArguments);
        } else {
            throw new CacheException('"' . static::class . ':' . $sMethod . '" is not a valid method');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an interface to the public cache
     *
     * @return Interfaces\Service\Cache\Driver\AccessibleByUrl
     */
    public function public(): Interfaces\Service\Cache\Driver\AccessibleByUrl
    {
        return $this->oAccessibleDriver;
    }
}
