<?php

use Nails\Common\Helper\Strings;

if (!function_exists('str_lreplace')) {
    function str_lreplace($sSearch, $sReplace, $sSubject)
    {
        return Strings::replaceLastOccurance($sSearch, $sReplace, $sSubject);
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

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/string_helper.php';
