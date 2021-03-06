<?php

namespace Nails\Common\Resource;

use DateInterval;
use DateTimeInterface;
use Exception;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource;
use Nails\Factory;

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
     * The formatted date (as per active user's settings)
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
     *
     * @throws Exception
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);
        $this->oDateObj = $this->raw ? new \DateTime($this->raw) : null;
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
     * @return \DateTime|null
     */
    public function getDateTimeObject(): ?\DateTime
    {
        return $this->oDateObj;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the difference between two DateTime objects
     *
     * @param DateTimeInterface $oDateTime The date to compare to.
     * @param bool              $bAbsolute Should the interval be forced to be positive?
     *
     * @return DateInterval|false
     */
    public function diff(DateTimeInterface $oDateTime, bool $bAbsolute = false)
    {
        return $this->getDateTimeObject()
            ? $this->getDateTimeObject()->diff($oDateTime, $bAbsolute)
            : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns date formatted according to given format
     *
     * @param string $sFormat The format to use
     *
     * @return string|null
     */
    public function format(string $sFormat): ?string
    {
        return $this->getDateTimeObject()
            ? $this->getDateTimeObject()->format($sFormat)
            : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the timezone offset
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->getDateTimeObject()
            ? $this->getDateTimeObject()->getOffset()
            : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the Unix timestamp
     *
     * @return int|null
     */
    public function getTimestamp(): ?int
    {
        return $this->getDateTimeObject()
            ? $this->getDateTimeObject()->getTimestamp()
            : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Return time zone relative to given DateTime
     *
     * @return \DateTimeZone|null
     */
    public function getTimezone(): ?\DateTimeZone
    {
        return $this->getDateTimeObject()
            ? $this->getDateTimeObject()->getTimezone()
            : null;
    }

    // --------------------------------------------------------------------------

    /**
     * The __wakeup handler
     */
    public function __wakeup()
    {
        $this->oDateObj->__wakeup();
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the date is before the supplied date
     *
     * @param \DateTime|self $oCompareWith The date to compare with
     *
     * @return bool
     */
    public function isBefore($oCompareWith): bool
    {
        if (!$this->raw) {
            return true;
        }

        $oCompareWith = $this->inferDateTimeObject($oCompareWith);

        return $this->getDateTimeObject() < $oCompareWith;

    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the date is after the supplied date
     *
     * @param \DateTime|self $oCompareWith The date to compare with
     *
     * @return bool
     */
    public function isAfter($oCompareWith): bool
    {
        if (!$this->raw) {
            return false;
        }

        $oCompareWith = $this->inferDateTimeObject($oCompareWith);

        return $this->getDateTimeObject() > $oCompareWith;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the date is between two supplied dates
     *
     * @param \DateTime|self|null $oCompareWithLower The lower bound
     * @param \DateTime|self|null $oCompareWithUpper The upper bound
     * @param bool                $bInclusive        Whether to include the date bounds
     *
     * @return bool
     */
    public function isBetween($oCompareWithLower, $oCompareWithUpper, bool $bInclusive = true): bool
    {
        if (!$this->raw) {
            return false;
        }

        $oCompareWithLower = $oCompareWithLower ? $this->inferDateTimeObject($oCompareWithLower) : null;
        $oCompareWithUpper = $oCompareWithUpper ? $this->inferDateTimeObject($oCompareWithUpper) : null;

        if ($oCompareWithLower) {
            $bIsAfter = $bInclusive
                ? $this->getDateTimeObject() >= $oCompareWithLower
                : $this->getDateTimeObject() > $oCompareWithLower;
        } else {
            $bIsAfter = true;
        }

        if ($oCompareWithUpper) {
            $bIsBefore = $bInclusive
                ? $this->getDateTimeObject() <= $oCompareWithUpper
                : $this->getDateTimeObject() < $oCompareWithUpper;
        } else {
            $bIsBefore = true;
        }

        return $bIsAfter && $bIsBefore;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the date time is in the past
     *
     * @param \DateTime|self|null $oCompareWith The date to compare with
     *
     * @return bool
     * @throws FactoryException
     */
    public function isPast($oCompareWith = null): bool
    {
        /** @var \DateTime|self $oCompareWith */
        $oCompareWith = $oCompareWith ?? Factory::factory('DateTime');
        return $this->isBefore($oCompareWith);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the date time is in the future
     *
     * @param \DateTime|self|null $oCompareWith The date to compare with
     *
     * @return bool
     * @throws FactoryException
     */
    public function isFuture($oCompareWith = null): bool
    {
        /** @var \DateTime|self $oCompareWith */
        $oCompareWith = $oCompareWith ?? Factory::factory('DateTime');
        return $this->isAfter($oCompareWith);
    }

    // --------------------------------------------------------------------------

    /**
     * Infers the date time object from the supplied object
     *
     * @param \Datetime|self $oDateTime The date to inspect
     *
     * @return \DateTime
     */
    protected function inferDateTimeObject($oDateTime): \DateTime
    {
        if ($oDateTime instanceof \DateTime) {
            return $oDateTime;

        } elseif ($oDateTime instanceof self) {
            return $oDateTime->getDateTimeObject();
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Expected object of type %s or %s, got %s',
                \DateTime::class,
                self::class,
                gettype($oDateTime)
            )
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a human-friendly relative time string (e.g. 3 minutes ago)
     *
     * @param bool $bIncludeTense
     * @param null $sMessageBadDate
     * @param null $sMessageGreaterOneWeek
     * @param null $sMessageLessTenMinutes
     * @param null $oCompareWith
     *
     * @return string
     * @throws FactoryException
     */
    public function relative(
        $bIncludeTense = true,
        $sMessageBadDate = null,
        $sMessageGreaterOneWeek = null,
        $sMessageLessTenMinutes = null,
        $oCompareWith = null
    ): string {

        /** @var \Nails\Common\Service\DateTime $oDateTimeService */
        $oDateTimeService = Factory::service('DateTime');

        return $oDateTimeService->niceTime(
            $this,
            $bIncludeTense,
            $sMessageBadDate,
            $sMessageGreaterOneWeek,
            $sMessageLessTenMinutes,
            $oCompareWith
        );
    }
}
