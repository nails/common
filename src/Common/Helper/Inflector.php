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

use Nails\Factory;

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
     * @deprecated
     * @see Inflector::possessive()
     *
     * @param  string $sString The word to which to add a possessive apostrophe
     *
     * @return string
     */
    public static function possessionise($sString)
    {
        trigger_error('Function ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);
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
            Factory::helper('inflector');
            return $sSpecified ?: plural($sSingular);
        }
    }
}
