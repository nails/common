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
        if (array_key_exists($sKey, static::$aConfig)) {
            return static::$aConfig[$sKey];
        } elseif (defined($sKey)) {
            return constant($sKey);
        } elseif (array_key_exists($sKey, $_ENV)) {
            return $_ENV[$sKey];
        } else {
            return $mDefault;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a config value if it is not already set
     *
     * @param string $sKey     The key to set
     * @param null   $mDefault The value to default to
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
        if (array_key_exists($sKey, static::$aConfig)) {
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
        static::$aConfig[$sKey] = $mValue;

        //  (Pablo - 2020-03-02) - Set as a constant as well, for backwards compatability
        Functions::define($sKey, $mValue);
    }
}
