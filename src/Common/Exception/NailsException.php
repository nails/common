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
     *
     * @var string
     */
    const DOCUMENTATION_URL = '';

    // --------------------------------------------------------------------------

    /**
     * An array of data to pass along with the exception; useful for code which might
     * need to know more about the exception, or if you wish to bundle multiple errors
     * into a single exception
     *
     * @var array
     */
    protected $aData;

    // --------------------------------------------------------------------------

    /**
     * Returns the URL for any relevant documentation
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return static::DOCUMENTATION_URL;
    }

    // --------------------------------------------------------------------------

    /**
     * Set exception data
     *
     * @param array $aData Any data you'd like to pass into the exception
     *
     * @return $this
     */
    public function setData(array $aData): NailsException
    {
        $this->aData = $aData;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get exception data
     *
     * @return array
     */
    public function getData()
    {
        return $this->aData;
    }
}
