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
    protected $oDateObj;

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
     * Returns the internal date object
     *
     * @return \DateTime
     */
    public function getDateObject(): \DateTime
    {
        return $this->oDateObj;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the difference between two DateTime objects
     *
     * @param \DateTimeInterface $oDateTime The date to compare to.
     * @param bool               $bAbsolute Should the interval be forced to be positive?
     *
     * @return \DateInterval|false
     */
    public function diff(\DateTimeInterface $oDateTime, bool $bAbsolute = false)
    {
        $this->getDateObject()->diff($oDateTime, $bAbsolute);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns date formatted according to given format
     *
     * @param string $sFormat The format to use
     *
     * @return string
     */
    public function format(string $sFormat): string
    {
        $this->getDateObject()->format($sFormat);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the timezone offset
     *
     * @return int
     */
    public function getOffset(): int
    {
        $this->getDateObject()->getOffset();
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the Unix timestamp
     *
     * @return int
     */
    public function getTimestamp(): int
    {
        $this->getDateObject()->getTimestamp();
    }

    // --------------------------------------------------------------------------

    /**
     * Return time zone relative to given DateTime
     *
     * @return \DateTimeZone
     */
    public function getTimezone(): \DateTimeZone
    {
        $this->getDateObject()->getTimezone();
    }

    // --------------------------------------------------------------------------

    /**
     * The __wakeup handler
     */
    public function __wakeup()
    {
        $this->oDateObj->__wakeup();
    }
}
