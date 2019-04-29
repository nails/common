<?php

/**
 * The class provides a Dummy router if it is requested before the CI router has been instantiated.
 * This is only the case if an error happens _very_ early on in the CI bootstrapping sequence.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 */

namespace Nails\Common\CodeIgniter\Core\Router;

class Dummy
{
    public function __call($name, $arguments)
    {
        return null;
    }

    public function __get($name)
    {
        return null;
    }
}
