<?php

use Nails\Common\Helper\Timecode;

if (!function_exists('timecodeToSeconds')) {
    function timecodeToSeconds(string $sTimecode): int
    {
        return Timecode::toSeconds($isTimecode);
    }
}

if (!function_exists('secondsToTimecode')) {
    function secondsToTimecode(int $iSeconds, string $sSeparator = null): string
    {
        return Timecode::toTimecode($iSeconds, $sSeparator);
    }
}
