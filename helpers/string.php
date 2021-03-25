<?php

use Nails\Common\Helper\Strings;

if (!function_exists('str_lreplace')) {
    function str_lreplace($sSearch, $sReplace, $sSubject)
    {
        return Strings::replaceLastOccurrence($sSearch, $sReplace, $sSubject);
    }
}

if (!function_exists('replaceLastOccurrence')) {
    function replaceLastOccurrence($sSearch, $sReplace, $sSubject)
    {
        return Strings::replaceLastOccurrence($sSearch, $sReplace, $sSubject);
    }
}

if (!function_exists('underscoreToCamelcase')) {
    function underscoreToCamelcase($sString, $bLowerFirst = true)
    {
        return Strings::underscoreToCamelcase($sString, $bLowerFirst);
    }
}

if (!function_exists('camelcase_to_underscore')) {
    function camelcase_to_underscore($sString)
    {
        return Strings::camelcase_to_underscore($sString);
    }
}

if (!function_exists('addTrailingSlash')) {
    function addTrailingSlash($sString)
    {
        return Strings::addTrailingSlash($sString);
    }
}

if (!function_exists('removeStopWords')) {
    function removeStopWords($sString)
    {
        return Strings::removeStopWords($sString);
    }
}

if (!function_exists('generateToken')) {
    function generateToken($sMask = null, $aChars = [], $aDigits = [])
    {
        return Strings::generateToken($sMask, $aChars, $aDigits);
    }
}

if (!function_exists('prosaicList')) {
    function prosaicList(array $aArray, $sSeparator = ', ', $sConjunctive = ' and ', $bOxfordComma = true)
    {
        return Strings::prosaicList($aArray, $sSeparator, $sConjunctive, $bOxfordComma);
    }
}

if (!function_exists('mask')) {
    function mask(string $sInput, $iMaskLength = null, string $sMask = null): string
    {
        return Strings::mask($sInput, $iMaskLength, $sMask);
    }
}

if (!function_exists('toArray')) {
    function toArray(string $sInput, array $aDeliminators = null, array $aFormatters = null): array
    {
        return Strings::toArray($sInput, $aDeliminators, $aFormatters);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/string_helper.php';
