<?php

namespace Nails\Common\Traits\Database\Seeder;

use Nails\Common\Exception\FactoryException;
use Nails\Factory;

/**
 * Trait DateTime
 *
 * @package Nails\Common\Traits\Database\Seeder
 */
trait DateTime
{
    /**
     * Return a random datetime, optionally restricted between bounds
     *
     * @param string $sLow    The lowest possible datetime to return
     * @param string $sHigh   The highest possible datetime to return
     * @param string $sFormat The format to return the datetime value in
     *
     * @return string
     */
    protected function randomDateTime($sLow = null, $sHigh = null, $sFormat = 'Y-m-d H:i:s')
    {
        $iLow  = $sLow ? strtotime($sLow) : strtotime('last year');
        $iHigh = $sHigh ? strtotime($sHigh) : strtotime('next year');
        return date($sFormat, rand($iLow, $iHigh));
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random datetime from the future, optionally restricted to a upper bound
     *
     * @param string $sHigh The highest possible datetime to return
     *
     * @return string
     * @throws FactoryException
     */
    protected function randomFutureDateTime($sHigh = null)
    {
        /** @var \DateTime $oNow */
        $oNow = Factory::factory('DateTime');
        return $this->randomDateTime($oNow->format('Y-m-d H:i:s'), $sHigh);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random datetime from the past, optionally restricted to a lower bound
     *
     * @param string $sLow The lowest possible datetime to return
     *
     * @return string
     * @throws FactoryException
     */
    protected function randomPastDateTime($sLow = null)
    {
        /** @var \DateTime $oNow */
        $oNow = Factory::factory('DateTime');
        return $this->randomDateTime($sLow, $oNow->format('Y-m-d H:i:s'));
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random date, optionally restricted between bounds
     *
     * @param string $sLow    The lowest possible date to return
     * @param string $sHigh   The highest possible date to return
     * @param string $sFormat The format to return the datetime value in
     *
     * @return string
     */
    protected function randomDate($sLow = null, $sHigh = null, $sFormat = 'Y-m-d')
    {
        $iLow  = $sLow ? strtotime($sLow) : strtotime('last year');
        $iHigh = $sHigh ? strtotime($sHigh) : strtotime('next year');
        return date($sFormat, rand($iLow, $iHigh));
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random date from the future, optionally restricted to a upper bound
     *
     * @param string $sHigh The highest possible date to return
     *
     * @return string
     * @throws FactoryException
     */
    protected function randomFutureDate($sHigh = null)
    {
        /** @var \DateTime $oNow */
        $oNow = Factory::factory('DateTime');
        return $this->randomDateTime($oNow->format('Y-m-d'), $sHigh);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random date from the past, optionally restricted to a lower bound
     *
     * @param string $sLow The lowest possible date to return
     *
     * @return string
     * @throws FactoryException
     */
    protected function randomPastDate($sLow = null)
    {
        /** @var \DateTime $oNow */
        $oNow = Factory::factory('DateTime');
        return $this->randomDateTime($sLow, $oNow->format('Y-m-d'));
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a random timestamp
     *
     * @param int    $iLowHour  The low value for the hour
     * @param int    $iHighHour The high value for the hour
     * @param int    $iLowMin   The low value for the minute
     * @param int    $iHighMin  The high value for the minute
     * @param bool   $bPadHour  Whether to pad the hour segment with $sPad
     * @param bool   $bPadMin   Whether to pad the minute segment with $sPad
     * @param string $sPad      The padding string to use
     *
     * @return string
     */
    protected function randomTime(
        int $iLowHour = 0,
        int $iHighHour = 23,
        int $iLowMin = 0,
        int $iHighMin = 59,
        bool $bPadHour = true,
        bool $bPadMin = true,
        string $sPad = '0'
    ): string {

        $iHour = rand($iLowHour, $iHighHour);
        $iMin  = rand($iLowMin, $iHighMin);

        return sprintf(
            '%s:%s',
            $bPadHour ? str_pad($iHour, 2, $sPad, STR_PAD_LEFT) : $iHour,
            $bPadMin ? str_pad($iMin, 2, $sPad, STR_PAD_LEFT) : $iMin
        );
    }
}
