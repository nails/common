<?php

/**
 * Simple HTTP HEAD requests
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory\HttpRequest;

use Nails\Common\Factory\HttpRequest;

/**
 * Class Head
 *
 * @package Nails\Common\Factory\HttpRequest
 */
class Head extends Options
{
    /**
     * The HTTP Method for the request
     *
     * @var string
     */
    const HTTP_METHOD = 'HEAD';
}
