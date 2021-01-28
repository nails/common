<?php

/**
 * Simple HTTP OPTIONS requests
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory\HttpRequest;

use Nails\Common\Factory\HttpRequest;

/**
 * Class Options
 *
 * @package Nails\Common\Factory\HttpRequest
 */
class Options extends HttpRequest
{
    /**
     * The HTTP Method for the request
     *
     * @var string
     */
    const HTTP_METHOD = 'OPTIONS';
}
