<?php

namespace Nails\Common;

use stdClass;

/**
 * Class Resource
 *
 * @package Nails\Common
 */
#[\AllowDynamicProperties]
class Resource
{
    /**
     * Resource constructor.
     *
     * @param self|stdClass|array $resource The data to populate the resource with
     */
    public function __construct(self|stdClass|array $resource = [])
    {
        foreach ($resource as $sProperty => $mValue) {
            $this->{$sProperty} = $mValue;
        }
    }
}
