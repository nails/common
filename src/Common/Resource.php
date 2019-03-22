<?php

namespace Nails\Common;

/**
 * Class Resource
 *
 * @package Nails\Common
 */
class Resource
{
    /**
     * Resource constructor.
     *
     * @param Resource|\stdClass|array $mObj The database row
     */
    public function __construct($mObj = [])
    {
        foreach ($mObj as $sProperty => $mValue) {
            $this->{$sProperty} = $mValue;
        }
    }
}
