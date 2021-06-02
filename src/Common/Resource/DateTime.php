<?php

namespace Nails\Common\Resource;

use Nails\Common\Exception\FactoryException;

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

    // --------------------------------------------------------------------------

    /**
     * Returns the date component, formatted for the user
     *
     * @return string|null
     * @throws FactoryException
     */
    public function formatAsUserDate(): ?string
    {
        /** @var \Nails\Common\Service\DateTime $oDateTimeService */
        $oDateTimeService = \Nails\Factory::service('DateTime');

        return $this->getDateTimeObject()
            ? $this->getDateTimeObject()->format($oDateTimeService->getUserDateFormat())
            : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the time component, formatted for the user
     *
     * @return string|null
     * @throws FactoryException
     */
    public function formatAsUserTime(): ?string
    {
        /** @var \Nails\Common\Service\DateTime $oDateTimeService */
        $oDateTimeService = \Nails\Factory::service('DateTime');

        return $this->getDateTimeObject()
            ? $this->getDateTimeObject()->format($oDateTimeService->getUserTimeFormat())
            : null;
    }
}
