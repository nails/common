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
    const URL = '';

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
        if (static::URL) {
            $sUrl = rtrim(static::URL, '/');
        } else {
            $sUrl = Config::siteUrl('cache/' . static::DIR);
        }
        $sUrl .= $sKey ? '/' . $sKey : '';
        return $sUrl;
    }
}
