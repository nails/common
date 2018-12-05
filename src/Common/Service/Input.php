<?php

/**
 * The class provides an interface to some key input elements
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 */

namespace Nails\Common\Service;

use Nails\Factory;

class Input
{
    /**
     * Returns keys from the global $_GET array
     *
     * @param string|array $mKeys     The key(s) to return
     * @param bool         $bXssClean Whether to run the result through the XSS filter
     *
     * @return array|mixed
     */
    public static function get($mKeys = null, $bXssClean = false)
    {
        $aArray = isset($_GET) ? $_GET : [];
        return static::getItemsFromArray($mKeys, $bXssClean, $aArray);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns keys from the global $_POST array
     *
     * @param string|array $mKeys     The key(s) to return
     * @param bool         $bXssClean Whether to run the result through the XSS filter
     *
     * @return array|mixed
     */
    public static function post($mKeys = null, $bXssClean = false)
    {
        $aArray = isset($_POST) ? $_POST : [];
        return static::getItemsFromArray($mKeys, $bXssClean, $aArray);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns keys from the global $_SERVER array
     *
     * @param string|array $mKeys     The key(s) to return
     * @param bool         $bXssClean Whether to run the result through the XSS filter
     *
     * @return array|mixed
     */
    public static function server($mKeys = null, $bXssClean = false)
    {
        $aArray = isset($_SERVER) ? $_SERVER : [];
        return static::getItemsFromArray($mKeys, $bXssClean, $aArray);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns keys from the global $_COOKIE array
     *
     * @param string|array $mKeys     The key(s) to return
     * @param bool         $bXssClean Whether to run the result through the XSS filter
     *
     * @return array|mixed
     */
    public static function cookie($mKeys = null, $bXssClean = false)
    {
        $aArray = isset($_COOKIE) ? $_COOKIE : [];
        return static::getItemsFromArray($mKeys, $bXssClean, $aArray);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns keys from the global $_FILES array
     *
     * @param string|array $mKeys The key(s) to return
     *
     * @return array|mixed
     */
    public static function file($mKeys = null)
    {
        $aArray = isset($_FILES) ? $_FILES : [];
        return static::getItemsFromArray($mKeys, false, $aArray);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns keys from the request headers
     *
     * @param string|array $mKeys     The key(s) to return
     * @param bool         $bXssClean Whether to run the result through the XSS filter
     *
     * @return array|mixed
     */
    public static function header($mKeys = null, $bXssClean = false)
    {
        return static::getItemsFromArray($mKeys, $bXssClean, getallheaders());
    }

    // --------------------------------------------------------------------------

    /**
     * Returns keys from the supplied $aArray array
     *
     * @param string|array $mKeys     The key(s) to return
     * @param bool         $bXssClean Whether to run the result through the XSS filter
     * @param array        $aArray    The array to inspect
     *
     * @return array|mixed
     */
    protected static function getItemsFromArray($mKeys = null, $bXssClean = false, $aArray = [])
    {
        $aOut  = [];
        $aKeys = $mKeys !== null ? (array) $mKeys : array_keys($aArray);

        foreach ($aKeys as $sKey) {
            $aOut[$sKey] = getFromArray($sKey, $aArray);
            if ($bXssClean) {
                $oSecurity   = Factory::service('Security');
                $aOut[$sKey] = $oSecurity->xss_clean($aOut[$sKey]);
            }
        }

        return is_string($mKeys) ? reset($aOut) : $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the user's IP Address
     * @return string
     */
    public static function ipAddress()
    {
        if (static::isCli()) {
            return '0.0.0.0';
        } else {
            return static::server('REMOTE_ADDR');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Validate an IP address
     *
     * @param string $sIp   The IP to validate
     * @param string $sType The type of IP (IPV4 or IPV6)
     *
     * @return mixed
     */
    public static function isValidIp($sIp, $sType = null)
    {
        switch (strtoupper($sType)) {
            case 'IPV4':
                return filter_var($sIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
                break;
            case 'IPV6':
                return filter_var($sIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
                break;
            default:
                return filter_var($sIp, FILTER_VALIDATE_IP);
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the current request is being executed on the CLI
     * @return bool
     */
    public static function isCli()
    {
        return php_sapi_name() === 'cli' || defined('STDIN');
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the current request is an Ajax request
     * @return bool
     */
    public static function isAjax()
    {
        return static::server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
    }
}
