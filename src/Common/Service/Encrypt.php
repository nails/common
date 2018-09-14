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

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Nails\Common\Exception\Encrypt\DecodeException;
use Nails\Common\Exception\EnvironmentException;
use Nails\Factory;

class Encrypt
{
    /**
     * Encodes a given value using the supplied key
     *
     * @param  mixed  $mValue The value to encode
     * @param  string $sSalt  The salt to add to the key
     *
     * @throws EnvironmentException
     * @return string
     */
    public static function encode($mValue, $sSalt = '')
    {
        try {
            return Crypto::encryptWithPassword(
                $mValue,
                static::getKey($sSalt)
            );
        } catch (EnvironmentIsBrokenException $e) {
            throw new EnvironmentException(
                $e->getMessage(),
                $e->getCode()
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Decodes a given value using the supplied key
     *
     * @param  mixed  $sCipher The value to decode
     * @param  string $sSalt   The salt to add to the key
     *
     * @throws EnvironmentException
     * @throws DecodeException
     * @return string
     */
    public static function decode($sCipher, $sSalt = '')
    {
        try {
            return Crypto::decryptWithPassword(
                $sCipher,
                static::getKey($sSalt)
            );
        } catch (EnvironmentIsBrokenException $e) {
            throw new EnvironmentException(
                $e->getMessage(),
                $e->getCode()
            );
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            throw new DecodeException(
                $e->getMessage(),
                $e->getCode()
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the key to use, salted
     *
     * @param  string $sSalt The salt to add to the key
     *
     * @return string
     */
    public static function getKey($sSalt = '')
    {
        return APP_PRIVATE_KEY . $sSalt;
    }

    // --------------------------------------------------------------------------

    /**
     * Migrates a cipher from CI encrypt library, to Defuse\Crypto library
     *
     * @param string $sCipher  The cipher to migrate
     * @param string $sOldKey  The key used to encode originally
     * @param string $sNewSalt The salt to add to the new key
     *
     * @return string
     */
    public static function migrate($sCipher, $sOldKey, $sNewSalt = '')
    {
        require_once FCPATH . 'vendor/codeigniter/framework/system/libraries/Encrypt.php';

        $oEncryptCi = new \CI_Encrypt();
        $oEncrypt   = Factory::service('Encrypt');

        return $oEncrypt::encode(
            $oEncryptCi->decode(
                $sCipher,
                $sOldKey
            ),
            $sNewSalt
        );
    }
}
