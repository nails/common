<?php

namespace Nails\Common\Interfaces\Service;

/**
 * Interface Cache
 *
 * @package Nails\Common\Interfaces\Service
 */
interface Cache
{
    /**
     * Return the absolute path for the cache
     *
     * @return string
     */
    public function getDir(): string;
}
