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
     * @param string   $sKey   The cookie's key
     * @param string   $sValue The cookie's value
     * @param int|null $iTTL   The cookie's TTL in seconds
     *
     * @return bool
     */
    public function write(
        string $sKey,
        string $sValue,
        int $iTTL = null,
        string $sPath = '',
        string $sDomain = '',
        bool $bSecure = false,
        bool $bHttpOnly = false
    ): bool {
        if (setcookie($sKey, $sValue, $iTTL ? time() + $iTTL : 0, $sPath, $sDomain, $bSecure, $bHttpOnly)) {
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
     * @param string $sKey The cookie to delete, or an array of cookies
     *
     * @return bool
     */
    public function delete(string $sKey): bool
    {
        if (array_key_exists($sKey, $this->aCookies) && setcookie($sKey, '', 1)) {
            unset($this->aCookies[$sKey]);
            return true;
        }

        return false;
    }
}
