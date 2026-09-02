<?php

/**
 * Manage cookies
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Resource;
use Nails\Factory;

/**
 * Class Cookie
 *
 * @package Nails\Common\Service
 */
class Cookie
{
    /**
     * The cookie array
     *
     * @var Resource\Cookie[]
     */
    protected $aCookies = [];

    // --------------------------------------------------------------------------

    /**
     * Cookie constructor.
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function __construct()
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        foreach ($oInput->cookie() as $sKey => $sValue) {
            $this->aCookies[$sKey] = Factory::resource(
                'Cookie',
                null,
                (object) [
                    'key'   => $sKey,
                    'value' => $sValue,
                ]
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Lists all cookies
     *
     * @return Resource\Cookie[]
     */
    public function list(): array
    {
        return $this->aCookies;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a specific cookie
     *
     * @param string $sKey The cookie's key
     *
     * @return Resource\Cookie|null
     */
    public function read(string $sKey): ?Resource\Cookie
    {
        return ArrayHelper::get($sKey, $this->aCookies, null);
    }

    // --------------------------------------------------------------------------

    /**
     * Writes a cookie, or overwrites an existing one
     *
     * @param string      $sKey      The cookie's key
     * @param string      $sValue    The cookie's value
     * @param int|null    $iTTL      The cookie's TTL in seconds
     * @param string      $sPath     The path the cookie is valid for
     * @param string      $sDomain   The domain the cookie is valid for
     * @param bool        $bSecure   Whether the cookie is restricted to HTTPS
     * @param bool        $bHttpOnly Whether the cookie is hidden from JavaScript
     * @param string|null $sSameSite The cookie's SameSite policy; Strict, Lax, or None
     *
     * @return bool
     */
    public function write(
        string $sKey,
        string $sValue,
        ?int $iTTL = null,
        string $sPath = '',
        string $sDomain = '',
        bool $bSecure = false,
        bool $bHttpOnly = false,
        ?string $sSameSite = null
    ): bool {
        $iExpires = $iTTL ? time() + $iTTL : 0;

        /**
         * SameSite can only be expressed using setcookie()'s options array, but
         * the positional form is retained when it is not in play so that the
         * behaviour of existing calls is untouched.
         */
        $bResult = $sSameSite === null
            ? setcookie($sKey, $sValue, $iExpires, $sPath, $sDomain, $bSecure, $bHttpOnly)
            : setcookie($sKey, $sValue, [
                'expires'  => $iExpires,
                'path'     => $sPath,
                'domain'   => $sDomain,
                'secure'   => $bSecure,
                'httponly' => $bHttpOnly,
                'samesite' => $sSameSite,
            ]);

        if ($bResult) {
            $this->aCookies[$sKey] = Factory::resource(
                'Cookie',
                null,
                (object) [
                    'key'   => $sKey,
                    'value' => $sValue,
                ]
            );
            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Delete a cookie
     *
     * @param string $sKey    The cookie to delete
     * @param string $sPath   The path the cookie was written with
     * @param string $sDomain The domain the cookie was written with
     *
     * @return bool
     */
    public function delete(string $sKey, string $sPath = '', string $sDomain = ''): bool
    {
        if (array_key_exists($sKey, $this->aCookies) && setcookie($sKey, '', 1, $sPath, $sDomain)) {
            unset($this->aCookies[$sKey]);
            return true;
        }

        return false;
    }
}
