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
    const ENV_PROD      = 'PRODUCTION';
    const ENV_STAGE     = 'STAGING';
    const ENV_DEV       = 'DEVELOPMENT';
    const ENV_TEST      = 'TESTING';
    const ENV_HTTP_TEST = 'HTTP_TEST';

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

            if (!empty($_ENV['ENVIRONMENT'])) {
                static::set($_ENV['ENVIRONMENT']);
            } elseif (defined('ENVIRONMENT')) {
                static::set(ENVIRONMENT);
            } else {
                static::set(static::ENV_DEV);
            }

            try {

                $oInput = Factory::service('Input');
                if (static::not(static::ENV_PROD) && $oInput->header(Testing::TEST_HEADER_NAME) === Testing::TEST_HEADER_VALUE) {
                    static::set(static::ENV_HTTP_TEST);
                    //  @todo (Pablo - 2018-11-21) - Consider halting execution if on prod and a test header is received
                }

            } catch (\Exception $e) {
                /**
                 * In the circumstance the environment is checked before the factory
                 * is loaded then this block will fail. Rather than consider this an
                 * error, simply swallow it quietly as it's probably intentional and
                 * can be considered a non-testing situation.
                 */
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
     * @param array|string $mEnvironment The environment(s) to query
     *
     * @return boolean
     */
    public static function is($mEnvironment)
    {
        if (is_array($mEnvironment)) {
            return array_search(static::get(), array_map('strtoupper', $mEnvironment)) !== false;
        } else {
            return static::get() === strtoupper($mEnvironment);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the environment is not the supplied environment
     *
     * @param string $sEnvironment The environment to query
     *
     * @return boolean
     */
    public static function not($sEnvironment)
    {
        return static::get() !== strtoupper($sEnvironment);
    }

    // --------------------------------------------------------------------------

    /**
     * Lists the available environments
     *
     * @return array
     */
    public static function available()
    {
        return [
            static::ENV_PROD,
            static::ENV_STAGE,
            static::ENV_DEV,
            static::ENV_TEST,
            static::ENV_HTTP_TEST,
        ];
    }
}
