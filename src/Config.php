<?php

namespace Nails;

/**
 * Class Config
 *
 * @package Nails
 */
final class Config
{
    /**
     * The defined configurations
     *
     * @var array
     */
    private static $aConfig = [];

    // --------------------------------------------------------------------------

    /**
     * Returns a config value
     *
     * @param string $sKey     The key to get
     * @param mixed  $mDefault The default value to return if not set
     */
    public static function get(string $sKey, $mDefault = null)
    {
        if (array_key_exists($sKey, self::$aConfig)) {
            $sValue = self::$aConfig[$sKey];
        } elseif (defined($sKey)) {
            $sValue = constant($sKey);
        } elseif (array_key_exists($sKey, $_ENV)) {
            $sValue = $_ENV[$sKey];
        } elseif (array_key_exists($sKey, $_SERVER)) {
            $sValue = $_SERVER[$sKey];
        } else {
            $sValue = $mDefault;
        }

        return is_string($sValue) && static::isJson($sValue)
            ? json_decode($sValue)
            : $sValue;
    }

    // --------------------------------------------------------------------------

    /**
     * Detects if a value is JSON
     *
     * @param mixed $mValue The value to test
     *
     * @return bool
     */
    private static function isJson($mValue): bool
    {
        if (!is_string($mValue)) {
            return false;
        }

        json_decode($mValue);
        return json_last_error() === JSON_ERROR_NONE;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a config value if it is not already set
     *
     * @param string $sKey     The key to set
     * @param mixed  $mDefault The value to default to
     */
    public static function default(string $sKey, $mDefault = null): void
    {
        if (!static::isSet($sKey)) {
            static::set($sKey, $mDefault);
        }

        //  (Pablo - 2020-03-02) - Set as a constant as well, for backwards compatability
        Functions::define($sKey, static::get($sKey));
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether a config value has been set
     *
     * @param string $sKey
     *
     * @return bool
     */
    public static function isSet(string $sKey): bool
    {
        if (array_key_exists($sKey, self::$aConfig)) {
            return true;
        } elseif (defined($sKey)) {
            return true;
        } elseif (array_key_exists($sKey, $_ENV)) {
            return true;
        } else {
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Forcibly sets a config value
     *
     * @param string $sKey   The key to set
     * @param mixed  $mValue The value to set
     */
    public static function set(string $sKey, $mValue): void
    {
        self::$aConfig[$sKey] = $mValue;

        //  (Pablo - 2020-03-02) - Set as a constant as well, for backwards compatability
        Functions::define($sKey, $mValue);
    }
}
