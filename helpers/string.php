<?php

use Nails\Factory;

/**
 * This file provides string related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('str_lreplace')) {
    /**
     * Replace the last occurance of a string within a string with a string
     * @param  string $search  The substring to replace
     * @param  string $replace The string to replace the substring with
     * @param  string $subject The string to search
     * @return string
     */
    function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {

            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('underscoreToCamelcase')) {

    /**
     * Transforms a string with underscores into a camelcased string
     * @param  string  $str     The string to transform
     * @param  boolean $lcfirst Whether or not to lowercase the first letter of the transformed string or not
     * @return string
     */
    function underscoreToCamelcase($str, $lcfirst = true)
    {
        $str = explode('_', $str);
        $str = array_map('strtolower', $str);
        $str = array_map('ucfirst', $str);
        $str = implode($str);
        $str = $lcfirst ? lcfirst($str) : $str;
        return $str;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('camelcase_to_underscore')) {

    /**
     * Transforms a camelcased string to underscores
     * @param  string $str The string to transform
     * @return string
     */
    function camelcase_to_underscore($str)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $str));
    }
}

// --------------------------------------------------------------------------

if (!function_exists('addTrailingSlash')) {

    /**
     * Adds a trailing slash to the input string if there isn't already one there
     * @param   string The string to add a trailing shash to.
     * @return  string
     **/
    function addTrailingSlash($str)
    {
        return rtrim($str, '/') . '/';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('removeStopWords')) {

    /**
     * Removes stop and other common words from a string
     * @param   string The string to filter
     * @return  string
     **/
    function removeStopWords($str)
    {
        $stopWords = lang('string_helper_stop_words');
        $stopWords = explode(',', $stopWords);
        $stopWords = array_unique($stopWords);
        $stopWords = array_filter($stopWords);

        $str = preg_replace('/(\b(' . implode('|', $stopWords) . ')\b)/i', '', $str);
        $str = preg_replace('/ {2,}/', ' ', $str);

        return trim($str);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('generateToken')) {

    /**
     * Generates a token string using a specific mask
     * @param  string $sMask    The mask to use; A = Any, C = Character, D = digit, S = Symbol
     * @param  array  $aChars   The array of characters to use
     * @param  array  $aDigits  The array of digits to use
     * @return string
     */
    function generateToken($sMask = null, $aChars = array(), $aDigits = array())
    {
        $sMask    = empty($sMask)    ? 'AAAA-AAAA-AAAA-AAAA-AAAA-AAAA' : $sMask;
        $aChars   = empty($aChars)   ? str_split('abcdefghijklmnopqrstuvwxyz') : $aChars;
        $aDigits  = empty($aDigits)  ? str_split('0123456789') : $aDigits;

        $aMask    = str_split(strtoupper($sMask));
        $aOut     = array();
        $iMaskLen = count($aMask);

        Factory::helper('array');

        for ($i=0; $i < $iMaskLen; $i++) {

            if ($aMask[$i] === 'A') {

                if (mt_rand(0, 1)) {
                    $aOut[] = random_element($aChars);
                } else {
                    $aOut[] = random_element($aDigits);
                }

            } else if ($aMask[$i] === 'C') {

                $aOut[] = random_element($aChars);

            } else if ($aMask[$i] === 'D') {

                $aOut[] = random_element($aDigits);

            } else {

                $aOut[] = $aMask[$i];
            }
        }

        return implode($aOut);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include FCPATH . 'vendor/rogeriopradoj/codeigniter/system/helpers/string_helper.php';
