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
    public static function get(): string
    {
        if (empty(self::$sEnvironment)) {
            self::set(
                Config::get('ENVIRONMENT', self::ENV_DEV)
            );
        }

        return self::$sEnvironment;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the active environment
     *
     * @param string $sEnvironment The environment to set
     */
    private static function set(string $sEnvironment): void
    {
        self::isValid($sEnvironment);
        self::$sEnvironment = trim(strtoupper($sEnvironment));
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the environment is the supplied environment
     *
     * @param string[]|string $mEnvironment The environment(s) to query
     *
     * @return bool
     */
    public static function is($mEnvironment): bool
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
     * @return bool
     */
    public static function not(string $sEnvironment): bool
    {
        return self::get() !== strtoupper($sEnvironment);
    }

    // --------------------------------------------------------------------------

    /**
     * Lists the available environments
     *
     * @return string[string]
     */
    public static function available(): array
    {
        return [
            self::ENV_PROD      => self::ENV_PROD,
            self::ENV_STAGE     => self::ENV_STAGE,
            self::ENV_DEV       => self::ENV_DEV,
            self::ENV_TEST      => self::ENV_TEST,
            self::ENV_HTTP_TEST => self::ENV_HTTP_TEST,
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
