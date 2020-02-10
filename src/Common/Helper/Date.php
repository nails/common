<?php

/**
 * Date helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

use DateTime;
use DateInterval;
use InvalidArgumentException;

/**
 * Class Date
 *
 * @package Nails\Common\Helper
 */
class Date
{
    /**
     * The various periods supported
     *
     * @var string
     */
    const PERIOD_DAY   = 'DAY';
    const PERIOD_MONTH = 'MONTH';
    const PERIOD_YEAR  = 'YEAR';

    // --------------------------------------------------------------------------

    /**
     * Adds a period to a date time, adjusting for variances of month lengths and leap years
     *
     * @param DateTime $oStart  The date time to adjust
     * @param string   $sPeriod The period to add
     * @param int      $iNum    The number of periods to add
     *
     * @return DateTime
     * @throws Exception
     */
    public static function addPeriod(DateTime $oStart, string $sPeriod, int $iNum): DateTime
    {
        if ($iNum === 0) {

            return $oStart;

        } elseif ($sPeriod === static::PERIOD_DAY) {

            return $oStart->add(new DateInterval(sprintf('P%sD', $iNum)));

        } elseif (in_array($sPeriod, [static::PERIOD_MONTH, static::PERIOD_YEAR])) {

            $sPeriod = $sPeriod === static::PERIOD_MONTH ? 'M' : 'Y';

            $oFuture = clone $oStart;
            $oFuture->modify('first day of this month');
            $oFuture->add(
                new DateInterval(
                    sprintf(
                        'P%s%s',
                        $iNum,
                        $sPeriod
                    )
                )
            );
            $oFuture->modify('last day of this month');

            return $oStart
                ->add(
                    (int) $oStart->format('j') > (int) $oFuture->format('j')
                        ? new DateInterval(sprintf('P%sD', $oStart->diff($oFuture)->days))
                        : new DateInterval(sprintf('P%s%s', $iNum, $sPeriod))
                );
        }

        throw new InvalidArgumentException(
            sprintf('"%s" is not a supported date period', $sPeriod)
        );
    }
}
