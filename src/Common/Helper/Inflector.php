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
     * Correctly adds possession to a word
     *
     * @param  string $sString The word to possesionise
     *
     * @return string
     */
    public static function possessionise($sString)
    {
        //  Attempt to match the case
        $sLastChar       = substr($sString, -1);
        $bIsLowerCase    = strtolower($sLastChar) === $sLastChar;
        $sPossessionChar = $bIsLowerCase ? 's' : 'S';

        return substr($sString, -1) == $sPossessionChar ? $sString . '\'' : $sString . '\'' . $sPossessionChar;
    }

    // --------------------------------------------------------------------------

    /**
     * Pluralises english words if a value is greater than 1
     *
     * @param integer $iValue    The number to compare against
     * @param string  $sSingular The word to pluralise
     * @param string  $sPlural   The plural of the word (skips auto-detection)
     *
     * @return string
     */
    public static function pluralise($iValue, $sSingular, $sPlural = null)
    {
        $sSingular = trim($sSingular);

        if ($iValue == 1) {

            return $sSingular;

        } elseif (substr($sSingular, -1) == 's') {

            return $sSingular;

        } else {

            //  If a plural is defined, then just use it (allows dev to provide alternative if the
            //  automatic pluralisation misbehaves).
            if (!is_null($sPlural)) {
                return (string) $sPlural;
            }

            //  Attempt to match case
            $sLastChar    = substr($sSingular, -1);
            $bIsLowerCase = strtolower($sLastChar) === $sLastChar;
            $sPluralChar  = $bIsLowerCase ? 's' : 'S';

            if (strtolower(substr($sSingular, -1)) !== 'y') {
                return $sSingular . $sPluralChar;
            }

            //  In English, if a word ends in a consonant + y then the y is
            //  replaced with ies, otherwise an s is simply appended.
            $aVowelCombos = ['ay', 'ey', 'iy', 'oy', 'uy'];
            if (in_array(substr($sSingular, -2), $aVowelCombos)) {

                return $sSingular . $sPluralChar;

            } else {

                $sPluralChar = $bIsLowerCase ? 'ies' : 'IES';
                return substr($sSingular, 0, -1) . $sPluralChar;
            }
        }
    }
}
