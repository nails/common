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

class Strings
{
    /**
     * Replace the last occurance of a string within a string with a string
     *
     * @param string $search  The substring to replace
     * @param string $replace The string to replace the substring with
     * @param string $subject The string to search
     *
     * @return string
     */
    public static function replaceLastOccurance($sString, $sReplace, $sSubject)
    {
        $iPos = strrpos($sSubject, $sString);

        if ($iPos !== false) {
            $sSubject = substr_replace($sSubject, $sReplace, $pos, strlen($sString));
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
    public static function underscoreToCamelcase($sString, $bLowerFirst = true)
    {
        $sString = explode('_', $sString);
        $sString = array_map('strtolower', $sString);
        $sString = array_map('ucfirst', $sString);
        $sString = implode($sString);
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
    public static function camelcase_to_underscore($sString)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $sString));
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a trailing slash to the input string if there isn't already one there
     *
     * @param string The string to add a trailing shash to.
     *
     * @return  string
     **/
    public static function addTrailingSlash($sString)
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
    public static function removeStopWords($sString)
    {
        $oFilter = new \Axisofstevil\StopWords\Filter();
        return $oFilter->cleanText($sString);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a token string using a specific mask
     *
     * @param string $sMask   The mask to use; A = Any, C = Character, D = digit, S = Symbol
     * @param array  $aChars  The array of characters to use
     * @param array  $aDigits The array of digits to use
     *
     * @return string
     */
    public static function generateToken($sMask = null, $aChars = [], $aDigits = [])
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

        return implode($aOut);
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
    public static function prosaicList(array $aArray, $sSeparator = ', ', $sConjunctive = ' and ', $bOxfordComma = true)
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
}
