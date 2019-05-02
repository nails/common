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
     * @var Interfaces\Service\Cache
     */
    protected $oPrivate;

    /**
     * The Public cache driver
     *
     * @var Interfaces\Service\Cache
     */
    protected $oPublic;

    // --------------------------------------------------------------------------

    /**
     * Cache constructor.
     *
     * @param Interfaces\Service\Cache $oPrivate The private cache
     * @param Interfaces\Service\Cache $oPublic  The public cache
     */
    public function __construct(
        Interfaces\Service\Cache $oPrivate,
        Interfaces\Service\Cache $oPublic
    ) {
        $this->oPrivate = $oPrivate;
        $this->oPublic  = $oPublic;
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
        if (method_exists($this->oPrivate, $sMethod)) {
            return call_user_func_array([$this->oPrivate, $sMethod], $aArguments);
        } else {
            throw new CacheException('"' . static::class . ':' . $sMethod . '" is not a valid method');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an interface to the public cache
     *
     * @return Interfaces\Service\Cache
     */
    public function public(): Interfaces\Service\Cache
    {
        return $this->oPublic;
    }
}
