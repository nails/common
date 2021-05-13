<?php

/**
 * Timecode helper
 *
 * @package    Nails
 * @subpackage common
 * @category   Helper
 * @author     Nails Dev Team
 */

namespace Nails\Common\Helper;

use Nails\Common\Exception\ValidationException;

/**
 * Class Timecode
 *
 * @package Nails\Common\Helper
 */
class Timecode
{
    /**
     * Converts a timecode to seconds
     *
     * @param string $sTimecode The timecode to convert, in format hh:mm:ss
     *
     * @return int
     * @throws ValidationException
     */
    public static function toSeconds(string $sTimecode): int
    {
        $sTimecode = trim($sTimecode);

        if (!static::isValid($sTimecode)) {
            throw new ValidationException(sprintf(
                '"%s" is not a valid timecode.',
                $sTimecode
            ));
        }

        $aSegments = explode(':', $sTimecode);
        $iHours    = intval($aSegments[0]);
        $iMinutes  = intval($aSegments[1]);
        $iSeconds  = intval($aSegments[2]);

        return ($iHours * 60 * 60) + ($iMinutes * 60) + $iSeconds;
    }

    // --------------------------------------------------------------------------

    /**
     * Converts seconds to a timecode
     *
     * @param int $iSeconds The value to convert
     *
     * @return string
     * @throws ValidationException
     */
    public static function toTimecode(int $iSeconds, string $sSeparator = null): string
    {
        $sSeparator = $sSeparator ?? ':';

        if ($iSeconds < 0) {
            throw new ValidationException('Seconds must be a positive integer.');
        }

        $aSegments = [
            'hours'   => 0,
            'minutes' => 0,
            'seconds' => 0,
        ];

        $aSegments['seconds'] = $iSeconds % 60;
        $aSegments['minutes'] = ($iSeconds - $aSegments['seconds']) / 60;

        if ($aSegments['minutes'] > 59) {
            $iTemp                = $aSegments['minutes'] % 60;
            $aSegments['hours']   = floor($aSegments['minutes'] / 60);
            $aSegments['minutes'] = $iTemp;
        }

        return implode($sSeparator, array_map(function(int $iSegment) {
            return str_pad($iSegment, 2, 0, STR_PAD_LEFT);
        }, $aSegments));
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether a given string is a valid timecode
     *
     * @param string $sTimecode The string to test
     *
     * @return bool
     */
    public static function isValid(string $sTimecode): bool
    {
        return preg_match('/\d{2,}:[0-5]\d:[0-5]\d/', trim($sTimecode));
    }
}
