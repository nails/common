<?php

/**
 * This class handles the environment variables
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 */

namespace Nails;

class Environment
{
    /**
     * The default environments
     */
    const ENV_PROD  = 'PRODUCTION';
    const ENV_STAGE = 'STAGING';
    const ENV_DEV   = 'DEVELOPMENT';
    const ENV_TEST  = 'TESTING';

    // --------------------------------------------------------------------------

    /**
     * The current environment
     *
     * @var string
     */
    protected static $sEnvironment;

    // --------------------------------------------------------------------------

    /**
     * Get the current environment
     *
     * @return string
     */
    public static function get()
    {
        if (empty(static::$sEnvironment)) {
            $oInput = Factory::service('Input');
            if ($oInput->header(Testing::TEST_HEADER_NAME) === Testing::TEST_HEADER_VALUE) {
                static::set(static::ENV_TEST);
            } elseif (!empty($_ENV['ENVIRONMENT'])) {
                static::set($_ENV['ENVIRONMENT']);
            } else {
                static::set(ENVIRONMENT);
            }
        }

        return static::$sEnvironment;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the active environment
     *
     * @param string $sEnvironment The environment to set
     */
    private static function set($sEnvironment)
    {
        static::$sEnvironment = trim(strtoupper($sEnvironment));
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the environment is the supplied environment
     *
     * @param  string $sEnvironment The environment to query
     *
     * @return boolean
     */
    public static function is($sEnvironment)
    {
        return self::get() === strtoupper($sEnvironment);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the environment is not the supplied environment
     *
     * @param  string $sEnvironment The environment to query
     *
     * @return boolean
     */
    public static function not($sEnvironment)
    {
        return self::get() !== strtoupper($sEnvironment);
    }

    // --------------------------------------------------------------------------

    /**
     * Lists the available environments
     *
     * @return array
     */
    public static function list()
    {
        return [
            ENV_PROD,
            ENV_STAGE,
            ENV_DEV,
            ENV_TEST,
        ];
    }
}
