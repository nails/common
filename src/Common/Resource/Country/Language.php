<?php

namespace Nails\Common\Resource\Country;

use Nails\Common\Resource;

/**
 * Class Language
 *
 * @package Nails\Common\Resource\Country
 */
class Language extends Resource
{
    /** @var string */
    public $name;

    /** @var string */
    public $iso;

    /** @var string */
    public $native;

    // --------------------------------------------------------------------------

    public function __toString()
    {
        return $this->iso;
    }
}
