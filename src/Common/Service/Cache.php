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

use Nails\Common\Interfaces;

/**
 * Class Cache
 *
 * @package Nails\Common\Service
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
        static::$oPrivate = $oPrivate;
        static::$oPublic  = $oPublic;
    }
}
