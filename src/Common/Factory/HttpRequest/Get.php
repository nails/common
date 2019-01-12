<?php

/**
 * Simple HTTP GET requests
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory\HttpRequest;

use Nails\Common\Factory\HttpRequest;

class Get extends HttpRequest
{
    /**
     * The HTTP Method for the request
     *
     * @var string
     */
    const HTTP_METHOD = 'GET';

    // --------------------------------------------------------------------------

    /**
     * Any query parameters to add to the request
     *
     * @var array
     */
    protected $aQuery;


    // --------------------------------------------------------------------------

    /**
     * Populates the query property of the request
     *
     * @param array $aQuery The value to assign
     *
     * @return $this
     */
    public function query(array $aQuery = [])
    {
        $this->aQuery = $aQuery;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the request
     *
     * @param array $aClientConfig   The config array for the HTTP Client
     * @param array $aRequestOptions The options for the request
     */
    protected function compile(array &$aClientConfig, array &$aRequestOptions)
    {
        $aRequestOptions['query'] = $this->aQuery;
    }
}
