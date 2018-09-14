<?php

/**
 * Inflector helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

class Inflector
{
    /**
     * Correctly adds a possessive apostrophe to a word
     *
     * @param  string $sString The word to which to add a possessive apostrophe
     *
     * @return string
     */
    public static function possessive($sString)
    {
        //  Attempt to match the case
        $sLastChar       = substr($sString, -1);
        $bIsLowerCase    = strtolower($sLastChar) === $sLastChar;
        $sPossessionChar = $bIsLowerCase ? 's' : 'S';

        return substr($sString, -1) == $sPossessionChar ? $sString . '\'' : $sString . '\'' . $sPossessionChar;
    }

    // --------------------------------------------------------------------------

    /**
     * Alias to possessive()
     *
     * @see Inflector::possessive()
     *
     * @param  string $sString The word to which to add a possessive apostrophe
     *
     * @return string
     */
    public function possessionise($sString)
    {
        return static::possessive($sString);
    }

    // --------------------------------------------------------------------------

    /**
     * Pluralises english words if a value is greater than 1
     *
     * @param integer $iValue     The number to compare against
     * @param string  $sSingular  The word to pluralise
     * @param string  $sSpecified A specific word to use for the plural
     *
     * @return string
     */
    public static function pluralise($iValue, $sSingular, $sSpecified = null)
    {
        $sSingular = trim($sSingular);

        if ($iValue == 1) {
            return $sSingular;
        } else {
            return $sSpecified ?: plural($sSingular);
        }
    }
}
