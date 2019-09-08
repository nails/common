<?php

namespace Nails\Common\Resource;

/**
 * Class DateTime
 *
 * @package Nails\Common\Resource
 */
class DateTime extends Date
{
    /**
     * DateTime constructor.
     *
     * @param array $mObj
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);
        if (!empty($this->raw)) {
            $this->formatted = toUserDateTime($this->raw);
        }
    }
}
