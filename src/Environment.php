<?php

/**
 * This class handles the environment variables
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails;

use Nails\Common\Exception\EnvironmentException;

class Environment
{
    /**
     * Get the current environment
     * @return string
     */
    public static function get()
    {
        return trim(strtoupper(ENVIRONMENT));
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the environment is the supplied environment
     * @param  string $sEnvironment The environment to query
     * @return boolean
     */
    public static function is($sEnvironment)
    {
        return self::get() === strtoupper($sEnvironment);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the environment is not the supplied environment
     * @param  string $sEnvironment The environment to query
     * @return boolean
     */
    public static function not($sEnvironment)
    {
        return self::get() !== strtoupper($sEnvironment);
    }
}
