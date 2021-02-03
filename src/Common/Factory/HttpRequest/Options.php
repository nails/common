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

    // --------------------------------------------------------------------------

    /**
     * Compile the request
     *
     * @param array $aClientConfig   The config array for the HTTP Client
     * @param array $aRequestOptions The options for the request
     */
    protected function compile(array &$aClientConfig, array &$aRequestOptions): void
    {
        //  Nothing to compile, but method is required by the parent
    }
}
