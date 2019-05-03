<?php

namespace Nails\Common\Service\Cache;

use Nails\Common\Interfaces\Service\Cache;

/**
 * Class CachePublic
 *
 * @package Nails\Common\Service\Cache
 */
class CachePublic extends CachePrivate implements Cache\CachePublic
{
    /**
     * The directory to use for the cache
     *
     * @var string
     */
    protected $sDir = NAILS_APP_PATH . 'cache' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

    /**
     * The URL for accessing the public cache
     *
     * @var string
     */
    protected $sUrl = BASE_URL . 'cache/public';

    // --------------------------------------------------------------------------

    public function __construct(string $sDir = null, string $sUrl = null)
    {
        parent::__construct($sDir);

        if (!is_null($sUrl)) {
            $this->sUrl = $sUrl;
        }
    }

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
        $sUrl = rtrim($this->sUrl, '/');
        $sUrl .= $sKey ? '/' . $sKey : '';
        return $sUrl;
    }
}
