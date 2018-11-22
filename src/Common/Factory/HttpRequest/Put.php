<?php

/**
 * Simple HTTP PUT requests
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory\HttpRequest;

class Put extends Nails\Common\Factory\HttpRequest\Post
{
    const HTTP_METHOD = 'Put';
}
