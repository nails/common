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

use Nails\Common\Exception\FileCacheException;
use Nails\Common\Interfaces;
use Nails\Common\Resource;

/**
 * Class FileCache
 *
 * @package Nails\Common\Service
 *
 * @method string getDir()
 * @method Resource\FileCache\Item read(string $sKey)
 * @method Resource\FileCache\Item write($mData, string $sKey = null)
 * @method bool delete(string $sKey)
 * @method bool exists(string $sKey)
 */
class FileCache
{
    /**
     * The Private cache driver
     *
     * @var Interfaces\Service\FileCache\Driver
     */
    protected $oDriver;

    /**
     * The Public cache driver
     *
     * @var Interfaces\Service\FileCache\Driver\AccessibleByUrl
     */
    protected $oAccessibleDriver;

    // --------------------------------------------------------------------------

    /**
     * Cache constructor.
     *
     * @param Interfaces\Service\FileCache\Driver                 $oDriver           The private cache
     * @param Interfaces\Service\FileCache\Driver\AccessibleByUrl $oAccessibleDriver The public cache
     */
    public function __construct(
        Interfaces\Service\FileCache\Driver $oDriver,
        Interfaces\Service\FileCache\Driver\AccessibleByUrl $oAccessibleDriver
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
     * @throws FileCacheException
     */
    public function __call(string $sMethod, array $aArguments)
    {
        if (method_exists($this->oDriver, $sMethod)) {
            return call_user_func_array([$this->oDriver, $sMethod], $aArguments);
        } else {
            throw new FileCacheException('"' . static::class . ':' . $sMethod . '" is not a valid method');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an interface to the public cache
     *
     * @return Interfaces\Service\FileCache\Driver\AccessibleByUrl
     */
    public function public(): Interfaces\Service\FileCache\Driver\AccessibleByUrl
    {
        return $this->oAccessibleDriver;
    }
}
