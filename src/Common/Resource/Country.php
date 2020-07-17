<?php

namespace Nails\Common\Resource;

use Nails\Common\Resource;

/**
 * Class Country
 *
 * @package Nails\Common\Resource
 */
class Country extends Resource
{
    /** @var string */
    public $name;

    /** @var string */
    public $iso;

    /** @var string */
    public $native;

    /** @var string */
    public $phone;

    /** @var Resource\Country\Continent */
    public $continent;

    /** @var string */
    public $capital;

    /** @var string */
    public $currency;

    /** @var Resource\Country\Language[] */
    public $languages;

    // --------------------------------------------------------------------------

    public function __toString()
    {
        return $this->iso;
    }
}
