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

use Nails\Common\Exception\EnvironmentException;
use Nails\Config;

/**
 * Class Environment
 *
 * @package Nails
 */
final class Environment
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
        if (empty(self::$sEnvironment)) {

            self::set(Config::get('ENVIRONMENT', self::ENV_DEV));

            try {

                $oInput = Factory::service('Input');
                if (self::not(self::ENV_PROD) && $oInput->header(Testing::TEST_HEADER_NAME) === Testing::TEST_HEADER_VALUE) {
                    self::set(self::ENV_HTTP_TEST);
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

        return self::$sEnvironment;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the active environment
     *
     * @param string $sEnvironment The environment to set
     */
    private static function set($sEnvironment)
    {
        self::isValid($sEnvironment);
        self::$sEnvironment = trim(strtoupper($sEnvironment));
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
            return array_search(self::get(), array_map('strtoupper', $mEnvironment)) !== false;
        } else {
            return self::get() === strtoupper($mEnvironment);
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
        return self::get() !== strtoupper($sEnvironment);
    }

    // --------------------------------------------------------------------------

    /**
     * Lists the available environments
     *
     * @return string[]
     */
    public static function available(): array
    {
        return [
            self::ENV_PROD,
            self::ENV_STAGE,
            self::ENV_DEV,
            self::ENV_TEST,
            self::ENV_HTTP_TEST,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether the supplied string is a valid environment
     *
     * @param string $sEnvironment The environment to test
     *
     * @throws EnvironmentException
     */
    public static function isValid(string $sEnvironment): void
    {
        if (!in_array($sEnvironment, self::available())) {
            throw new EnvironmentException('"' . $sEnvironment . '" is not a valid environment.');
        }
    }
}
