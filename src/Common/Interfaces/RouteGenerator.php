<?php

/**
 * This interface is implemented by Route Generators
 *
 * @package     Nails
 * @subpackage  common
 * @category    routes
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Interfaces;

interface RouteGenerator
{
    /**
     * Returns an array of routes in the form of key value pairs
     *
     * @return string[]
     */
    public static function generate(): array;
}
