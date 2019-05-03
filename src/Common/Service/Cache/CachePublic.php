<?php

namespace Nails\Common\Service\Cache;

use Nails\Common\Interfaces\Service\Cache;
use Nails\Common\Service\Config;

/**
 * Class CachePublic
 *
 * @package Nails\Common\Service\Cache
 */
class CachePublic extends CachePrivate implements Cache\CachePublic
{
    const DIR = 'public';

    // --------------------------------------------------------------------------

    /**
     * Return the URL for the public cache
     *
     * @param string|null $sKey The cache key
     *
     * @return string
     */
    public function getUrl(string $sKey = null): string
    {
        $sUrl = Config::siteUrl('cache/' . static::DIR);
        return $sUrl;
    }
}
