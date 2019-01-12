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
    const HTTP_METHOD = 'GET';

    // --------------------------------------------------------------------------

    /**
     * Populates the query property of the request
     *
     * @param array $aParams The value to assign
     *
     * @return $this
     */
    public function query(array $aParams = [])
    {
        return $this->setOption('query', $aParams);
    }
}
