<?php

/**
 * Simple HTTP DELETE requests
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory\HttpRequest;

class Delete extends Nails\Common\Factory\HttpRequest\Get
{
    const HTTP_METHOD = 'DELETE';
}
