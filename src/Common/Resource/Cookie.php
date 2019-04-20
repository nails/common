<?php

namespace Nails\Common\Resource;

use Nails\Common\Resource;

class Cookie extends Resource
{
    /**
     * The cookie's key
     *
     * @var string
     */
    public $key;

    /**
     * The cookie's value
     *
     * @var string
     */
    public $value;

    // --------------------------------------------------------------------------

    /**
     * Returns the cookie's value when cast as a string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
