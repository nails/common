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
     * @param self|\stdClass|array $mObj The data to populate the resource with
     */
    public function __construct($mObj = [])
    {
        foreach ($mObj as $sProperty => $mValue) {
            $this->{$sProperty} = $mValue;
        }
    }
}
