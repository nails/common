<?php

namespace Nails\Common\Interfaces\Service\Cache\Driver;

/**
 * Interface Cache
 *
 * @package Nails\Common\Interfaces\Service
 */
interface AccessibleByUrl extends Cache
{
    /**
     * Return the URL for the public cache
     *
     * @param string|null $sKey The cache key
     *
     * @return string
     */
    public function getUrl(string $sKey = null): string;
}
