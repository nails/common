<?php

namespace Nails\Common\Service\Cache;

/**
 * Class CachePublic
 *
 * @package Nails\Common\Service\Cache
 */
class CachePublic extends CachePrivate
{
    const DIR = 'public';

    // --------------------------------------------------------------------------

    /**
     * Returns the public URL to access the cache
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        //  @todo (Pablo - 2019-05-02) - comile this
        return siteUrl();
    }
}
