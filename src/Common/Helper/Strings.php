<?php

/**
 * String helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

use Axisofstevil\StopWords\Filter;

class Strings
{
    /**
     * Replace the last occurrence of a string within a string with a string
     *
     * @param string $sString  The substring to replace
     * @param string $sReplace The string to replace the substring with
     * @param string $sSubject The string to search
     *
     * @return string
     */
    public static function replaceLastOccurrence(string $sString, string $sReplace, string $sSubject): string
    {
        $iPos = strrpos($sSubject, $sString);

        if ($iPos !== false) {
            $sSubject = substr_replace($sSubject, $sReplace, $iPos, strlen($sString));
        }

        return $sSubject;
    }

    // --------------------------------------------------------------------------

    /**
     * Transforms a string with underscores into a camelcased string
     *
     * @param string  $sString     The string to transform
     * @param boolean $bLowerFirst Whether or not to lowercase the first letter of the transformed string or not
     *
     * @return string
     */
    public static function underscoreToCamelcase(string $sString, bool $bLowerFirst = true): string
    {
        $sString = explode('_', $sString);
        $sString = array_map('strtolower', $sString);
        $sString = array_map('ucfirst', $sString);
        $sString = implode('', $sString);
        $sString = $bLowerFirst ? lcfirst($sString) : $sString;
        return $sString;
    }

    // --------------------------------------------------------------------------

    /**
     * Transforms a camelcased string to underscores
     *
     * @param string $sString The string to transform
     *
     * @return string
     */
    public static function camelcase_to_underscore(string $sString): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $sString));
    }

    // --------------------------------------------------------------------------

    /**
     * Transforms a camelcased string to dashes
     *
     * @param string $sString The string to transform
     *
     * @return string
     */
    public static function camelcase_to_dash(string $sString): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $sString));
    }

    // --------------------------------------------------------------------------

    /**
     * Transforms a dash string to camelCase
     *
     * @param string $sString The string to transform
     *
     * @return string
     */
    public static function dashToCamelCase(string $sString): string
    {
        return preg_replace_callback('/\-(.)?/', function ($aMatches) {
            return strtoupper($aMatches[1]);
        }, $sString);
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a trailing slash to the input string if there isn't already one there
     *
     * @param string The string to add a trailing slash to.
     *
     * @return  string
     **/
    public static function addTrailingSlash(string $sString): string
    {
        return rtrim($sString, '/') . '/';
    }

    // --------------------------------------------------------------------------

    /**
     * Removes stop and other common words from a string
     *
     * @param string The string to filter
     *
     * @return  string
     **/
    public static function removeStopWords(string $sString): string
    {
        $oFilter = new Filter();
        return $oFilter->cleanText($sString);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a token string using a specific mask
     *
     * @param string   $sMask   The mask to use; A = Any, C = Character, D = digit, S = Symbol
     * @param string[] $aChars  The array of characters to use
     * @param int[]    $aDigits The array of digits to use
     *
     * @return string
     */
    public static function generateToken(string $sMask = null, array $aChars = [], array $aDigits = []): string
    {
        $sMask   = empty($sMask) ? 'AAAA-AAAA-AAAA-AAAA-AAAA-AAAA' : $sMask;
        $aChars  = empty($aChars) ? str_split('abcdefghijklmnopqrstuvwxyz') : $aChars;
        $aDigits = empty($aDigits) ? str_split('0123456789') : $aDigits;

        $aMask    = str_split(strtoupper($sMask));
        $aOut     = [];
        $iMaskLen = count($aMask);

        for ($i = 0; $i < $iMaskLen; $i++) {

            if ($aMask[$i] === 'A') {

                if (mt_rand(0, 1)) {
                    $aOut[] = random_element($aChars);
                } else {
                    $aOut[] = random_element($aDigits);
                }

            } else {
                if ($aMask[$i] === 'C') {

                    $aOut[] = random_element($aChars);

                } else {
                    if ($aMask[$i] === 'D') {

                        $aOut[] = random_element($aDigits);

                    } else {

                        $aOut[] = $aMask[$i];
                    }
                }
            }
        }

        return implode('', $aOut);
    }

    // --------------------------------------------------------------------------

    /**
     * Takes an array of strings and returns as a comma separated string using a terminal conjunctive,
     * optionally using an Oxford Comma.
     *
     * @param array  $aArray       The array to implode
     * @param string $sSeparator   The string to use to separate the strings
     * @param string $sConjunctive The conjunctive to use
     * @param bool   $bOxfordComma Whether to use an Oxford comma, or not.
     *
     * @return string
     */
    public static function prosaicList(array $aArray, string $sSeparator = ', ', string $sConjunctive = ' and ', bool $bOxfordComma = true): string
    {
        $iCount = count($aArray);
        if ($iCount <= 1) {
            return implode('', $aArray);

        } elseif ($iCount == 2) {
            return implode($sConjunctive, $aArray);

        } else {
            $aOut = [];
            for ($i = 0; $i < $iCount; $i++) {
                $sTemp = $aArray[$i];
                if ($i == ($iCount - 2) && $bOxfordComma) {
                    //  Second last item, and using Oxford comma
                    $sTemp .= $sSeparator . $sConjunctive;

                } elseif ($i == ($iCount - 2) && !$bOxfordComma) {
                    $sTemp .= $sConjunctive;

                } elseif ($i != ($iCount - 1)) {
                    $sTemp .= $sSeparator;
                }
                $aOut[] = $sTemp;
            }

            return implode('', $aOut);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Masks a string
     *
     * @param string         $sInput      The string to mask
     * @param float|int|null $iMaskLength The length of the mask, calculated as a percentage if the value is < 1
     * @param string|null    $sMask       The masking character
     *
     * @return string
     */
    public static function mask(string $sInput, $iMaskLength = null, string $sMask = null): string
    {
        $iMaskLength = $iMaskLength ?? 0.75;
        $sMask       = $sMask ?? '*';

        $iMaskLength = $iMaskLength < 1
            ? ceil(strlen($sInput) * $iMaskLength)
            : floor($iMaskLength);

        return str_repeat($sMask, $iMaskLength) . substr($sInput, $iMaskLength);
    }

    // --------------------------------------------------------------------------

    /**
     * Takes a string and transforms it into an array
     *
     * @param string     $sInput        The string to transform
     * @param array|null $aDeliminators What characters should be considered deliminator (default: "\r", "\n", ';')
     * @param array|null $aFormatters   Any post-processing functions to perform on the array elements (default: trim)
     *
     * @return string[]
     */
    public static function toArray(string $sInput, array $aDeliminators = null, array $aFormatters = null): array
    {
        foreach ($aDeliminators ?? ["\r", "\n", ';'] as $sDeliminator) {
            $sInput = str_replace($sDeliminator, ',', $sInput);
        }

        $aInput = explode(',', $sInput);

        foreach ($aFormatters ?? ['trim'] as $cFormatter) {
            $aInput = array_map($cFormatter, $aInput);
        }

        $aInput = array_filter($aInput);
        $aInput = array_unique($aInput);

        return array_values($aInput);
    }
}
