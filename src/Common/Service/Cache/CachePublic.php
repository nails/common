<?php

namespace Nails\Common\Service\Cache;

use Nails\Common\Interfaces\Service\Cache;

/**
 * Class CachePublic
 *
 * @package Nails\Common\Service\Cache
 */
class CachePublic implements Cache
{
    /**
     * Return the absolute path for the cache
     *
     * @return string
     */
    public function getDir(): string
    {
        return NAILS_APP_PATH . 'cache' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
    }
}
