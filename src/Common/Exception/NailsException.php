<?php

/**
 * Generic Exception
 *
 * @package     Nails
 * @subpackage  common
 * @category    Exceptions
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Exception;

class NailsException extends \Exception
{
    /**
     * The URL for any relevant documentation
     * @var string
     */
    const DOCUMENTATION_URL = '';

    // --------------------------------------------------------------------------

    /**
     * Returns the URL for any relevant documentation
     * @return string
     */
    public function getDocumentationUrl()
    {
        return static::DOCUMENTATION_URL;
    }
}
