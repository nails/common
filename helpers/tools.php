<?php

/**
 * This file provides miscallaeneous related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 */

use Nails\Common\Helper\Tools;

if (!function_exists('map')) {
    function map($x, $in_min, $in_max, $out_min, $out_max): float
    {
        return Tools::map($x, $in_min, $in_max, $out_min, $out_max);
    }
}

if (!function_exists('special_chars')) {
    function special_chars($sString): string
    {
        deprecatedError('special_chars', 'specialChars');
        return Tools::specialChars($sString);
    }
}

if (!function_exists('specialChars')) {
    function specialChars($sString): string
    {
        return Tools::specialChars($sString);
    }
}

if (!function_exists('stringToBoolean')) {
    function stringToBoolean($sString): bool
    {
        return Tools::stringToBoolean($sString);
    }
}

if (!function_exists('isIpInRange')) {
    function isIpInRange(string $sIp, $mRange): bool
    {
        return Tools::isIpInRange($sIp, $mRange);
    }
}

if (!function_exists('nullIfEmpty')) {
    function nullIfEmpty($mVal)
    {
        return Tools::nullIfEmpty($mVal);
    }
}

if (!function_exists('classImplements')) {
    function classImplements($mClass, string $sInterface): bool
    {
        return Tools::classImplements($mClass, $sInterface);
    }
}

if (!function_exists('classUses')) {
    function classUses($mClass, string $sTrait): bool
    {
        return Tools::classUses($mClass, $sTrait);
    }
}

if (!function_exists('classExtends')) {
    function classExtends($mClass, string $sParent): bool
    {
        return Tools::classExtends($mClass, $sParent);
    }
}
