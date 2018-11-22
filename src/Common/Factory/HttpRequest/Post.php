<?php

/**
 * Simple HTTP POST requests
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory\HttpRequest;

class Post extends Nails\Common\Factory\HttpRequest\Get
{
    const HTTP_METHOD = 'POST';

    // --------------------------------------------------------------------------

    /**
     * Populates the body property of the request
     *
     * @param mixed $mBody The value to assign
     *
     * @return $this
     */
    public function body(array $mBody = [])
    {
        return $this->setOption('body', $mBody);
    }
}
