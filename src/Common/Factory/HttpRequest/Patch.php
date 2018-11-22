<?php

/**
 * Simple HTTP PATCH requests
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory\HttpRequest;

class Patch extends Nails\Common\Factory\HttpRequest\Post
{
    const HTTP_METHOD = 'PATCH';
}
