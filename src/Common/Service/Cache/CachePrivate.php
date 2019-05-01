<?php

namespace Nails\Common\Service\Cache;

use Nails\Common\Interfaces\Service\Cache;

/**
 * Class CachePrivate
 *
 * @package Nails\Common\Service\Cache
 */
class CachePrivate implements Cache
{
    /**
     * Return the absolute path for the cache
     *
     * @return string
     */
    public function getDir(): string
    {
        return NAILS_APP_PATH . 'cache' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR;
    }
}
