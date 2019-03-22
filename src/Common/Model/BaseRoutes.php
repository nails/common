<?php

/**
 * Base class for route generators
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 * @todo (Pablo - 2019-03-22) - Enforce this as an interface
 */

namespace Nails\Common\Model;

use Nails\Common\Exception\NailsException;

abstract class BaseRoutes
{
    /**
     * Returns an array of routes to write
     *
     * @throws NailsException
     */
    public function getRoutes()
    {
        throw new NailsException(get_called_class() . ' must implement getRoutes() method');
    }
}
