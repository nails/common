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
     * Sets a config value
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
