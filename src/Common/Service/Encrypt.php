<?php

/**
 * The class abstracts CI's Encryption class.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use \Defuse\Crypto\Crypto;
use Nails\Common\Exception\EnvironmentException;
use Nails\Common\Exception\Encrypt\DecodeException;

class Encrypt
{
    /**
     * Encodes a given value using the supplied key
     *
     * @param  mixed   $mValue The value to encode
     * @param  boolean $sKey   The key to use for encryption
     *
     * @throws EnvironmentException
     * @return string
     */
    public static function encode($mValue, $sKey = false)
    {
        try {
            return Crypto::encryptWithPassword(
                $mValue,
                $sKey ?: static::getKey()
            );
        } catch (\Defuse\Crypto\Exception\EnvironmentIsBrokenException $e) {
            throw new EnvironmentException($e->getMessage(), $e->getCode());
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Decodes a given value using the supplied key
     *
     * @param  mixed   $mValue The value to decode
     * @param  boolean $sKey   The key to use for encryption
     *
     * @throws EnvironmentException
     * @throws DecodeException
     * @return string
     */
    public static function decode($sCipher, $sKey = false)
    {
        try {
            return Crypto::decryptWithPassword(
                $sCipher,
                $sKey ?: static::getKey()
            );
        } catch (\Defuse\Crypto\Exception\EnvironmentIsBrokenException $e) {
            throw new EnvironmentException($e->getMessage(), $e->getCode());
        } catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $e) {
            throw new DecodeException($e->getMessage(), $e->getCode());
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default key to use
     * @return string
     */
    public static function getKey()
    {
        return APP_PRIVATE_KEY;
    }
}
