<?php

/**
 * Tools helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

/**
 * Class Tools
 *
 * @package Nails\Common\Helper
 */
class Tools
{
    /**
     * Re-maps a number from one range to another
     * See http://www.arduino.cc/en/Reference/Map
     *
     * @param int|float $fX      Number to map
     * @param int|float $iInMin  Current low
     * @param int|float $iInMax  Current high
     * @param int|float $iOutMin New low
     * @param int|float $iOutMax New high
     *
     * @return  float
     */
    public static function map($fX, $iInMin, $iInMax, $iOutMin, $iOutMax): float
    {
        return ($fX - $iInMin) * ($iOutMax - $iOutMin) / ($iInMax - $iInMin) + $iOutMin;
    }

    // --------------------------------------------------------------------------

    /**
     * Replaces special chars with their HTML counterpart
     *
     * @param string $sString String to parse
     *
     * @return string
     */
    public static function specialChars($sString)
    {
        /* Only do the slow convert if there are 8-bit characters */
        /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
        if (!preg_match("/[\200-\237]/", $sString) and !preg_match("/[\241-\377]/", $sString)) {
            return $sString;
        }

        // decode three byte unicode characters
        $sString = preg_replace(
            "/([\340-\357])([\200-\277])([\200-\277])/e",
            "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",
            $sString
        );

        // decode two byte unicode characters
        $sString = preg_replace(
            "/([\300-\337])([\200-\277])/e",
            "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
            $sString
        );

        return $sString;
    }

    // --------------------------------------------------------------------------

    /**
     * Converts a string to a boolean
     *
     * @param string $sString The string to parse
     *
     * @return  bool
     */
    public static function stringToBoolean($sString): bool
    {
        return $sString && strtolower($sString) !== 'false';
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether an IP Address matches any of a set of addresses or CIDR ranges
     *
     * Supports IPv4 and IPv6, both as exact addresses and as CIDR ranges. An address
     * never matches a range of the other family, and invalid addresses or ranges
     * never match.
     *
     * @param $sIp    string The IP Address to test
     * @param $mRange mixed  The addresses/CIDR ranges, either as a comma/newline separated string, or an array
     *
     * @return bool
     */
    public static function isIpInRange(string $sIp, $mRange): bool
    {
        if (!is_array($mRange)) {

            //  Prepare the range
            $mRangeRaw = $mRange;
            $mRangeRaw = str_replace("\n\r", "\n", $mRangeRaw);
            $aRangeRaw = explode("\n", $mRangeRaw);
            $aRange    = [];

            foreach ($aRangeRaw as $line) {
                $aRange = array_merge(explode(',', $line), $aRange);
            }

            $aRange = array_unique($aRange);
            $aRange = array_filter($aRange);
            $aRange = array_map('trim', $aRange);
            $aRange = array_values($aRange);

        } else {
            $aRange = $mRange;
        }

        $sIpBinary = static::ipToBinary($sIp);
        if ($sIpBinary === null) {
            return false;
        }

        foreach ($aRange as $sRange) {

            $sRange = trim((string) $sRange);

            if (strpos($sRange, '/') !== false) {

                [$sSubnet, $sBits] = explode('/', $sRange, 2);

                $sSubnetBinary = static::ipToBinary($sSubnet);

                //  Only compare addresses of the same family (4 bytes for IPv4, 16 for IPv6)
                if ($sSubnetBinary === null || strlen($sSubnetBinary) !== strlen($sIpBinary) || !ctype_digit($sBits)) {
                    continue;
                }

                $iBits = (int) $sBits;
                if ($iBits > strlen($sIpBinary) * 8) {
                    continue;
                }

                //  Compare the whole bytes covered by the prefix, then the remaining bits
                $iBytes     = intdiv($iBits, 8);
                $iRemainder = $iBits % 8;

                if (substr($sIpBinary, 0, $iBytes) !== substr($sSubnetBinary, 0, $iBytes)) {
                    continue;
                }

                if ($iRemainder === 0) {
                    return true;
                }

                $iMask = (0xFF << (8 - $iRemainder)) & 0xFF;
                if ((ord($sIpBinary[$iBytes]) & $iMask) === (ord($sSubnetBinary[$iBytes]) & $iMask)) {
                    return true;
                }

            } elseif (static::ipToBinary($sRange) === $sIpBinary) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Converts an IPv4 or IPv6 address to its packed binary form
     *
     * @param string $sIp The IP Address to convert
     *
     * @return string|null Null if the address is invalid
     */
    protected static function ipToBinary(string $sIp): ?string
    {
        $sIp = trim($sIp);

        if (filter_var($sIp, FILTER_VALIDATE_IP) === false) {
            return null;
        }

        $sBinary = inet_pton($sIp);

        return $sBinary === false ? null : $sBinary;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns null if the input is empty, or the input if not
     *
     * @param mixed $mVal The input to check
     *
     * @return  mixed|null
     */
    public static function nullIfEmpty($mVal)
    {
        return empty($mVal) ? null : $mVal;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a class implements a particular interface
     *
     * @param object|string $mClass     The class to test, either as an object or a string
     * @param string        $sInterface The interface to look for
     *
     * @return bool
     */
    public static function classImplements($mClass, string $sInterface): bool
    {
        return in_array(
            ltrim($sInterface, '\\'),
            class_implements($mClass)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a class uses a particular trait
     *
     * @param object|string $mClass The class to test, either as an object or a string
     * @param string        $sTrait The trait to look for
     * @param bool          $bDeep  Wether to test parent classes too
     *
     * @return bool
     */
    public static function classUses($mClass, string $sTrait, bool $bDeep = true): bool
    {
        $aTraits = class_uses($mClass);

        if ($bDeep) {
            do {

                $aTraits = array_merge(class_uses($mClass), $aTraits);

            } while ($mClass = get_parent_class($mClass));

            foreach ($aTraits as $sTestTrait) {
                $aTraits = array_merge(class_uses($sTestTrait), $aTraits);
            }

            $aTraits = array_unique($aTraits);
            $aTraits = array_filter($aTraits);
        }

        return in_array(
            ltrim($sTrait, '\\'),
            $aTraits
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a class extends a particular class
     *
     * @param object|string $mClass  The class to test, either as an object or a string
     * @param string        $sParent The parent to look for
     *
     * @return bool
     */
    public static function classExtends($mClass, string $sParent): bool
    {
        return in_array(
            ltrim($sParent, '\\'),
            class_parents($mClass)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a class can be instantiated
     *
     * @param object|string $mClass The class to test, either as an object or a string
     *
     * @return bool
     * @throws \ReflectionException
     */
    public static function classCanBeInstantiated($mClass): bool
    {
        $oReflectionClass = new \ReflectionClass($mClass);
        return $oReflectionClass->isInstantiable();
    }
}
