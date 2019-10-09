<?php

namespace Nails\Common\Resource;

use Nails\Common\Resource;

/**
 * Class Date
 *
 * @package Nails\Common\Resource
 */
class Date extends Resource
{
    /**
     * The raw date
     *
     * @var string
     */
    public $raw = '';

    /**
     * The formatted date (as per actvie user's settings)
     *
     * @var string
     */
    public $formatted = '';

    /**
     * The internal date object
     *
     * @var \DateTime
     */
    public $oDateObj;

    // --------------------------------------------------------------------------

    /**
     * Date constructor.
     *
     * @param array $mObj
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);
        $this->oDateObj = new \DateTime($this->raw);
        if (!empty($this->raw)) {
            $this->formatted = toUserDate($this->raw);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the raw value when cast as a string
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->raw;
    }

    // --------------------------------------------------------------------------

    /**
     * Formats the date
     *
     * @param string $sFormat The format to use
     */
    public function format(string $sFormat)
    {
        return $this->oDateObj->format($sFormat);
    }
}
