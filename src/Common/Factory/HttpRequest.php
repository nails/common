<?php

/**
 * Simple HTTP requests
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory;

use Nails\Factory;
use Nails\Testing;

abstract class HttpRequest
{
    /**
     * The request options
     *
     * @var array
     */
    protected $aOptions = [];

    // --------------------------------------------------------------------------

    /**
     * HttpRequest constructor.
     *
     * @param array $aOptions An array of options to set
     */
    public function __construct(array $aOptions = [])
    {
        foreach ($aOptions as $sProperty => $mValue) {
            $this->setOption($sProperty, $mValue);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set an option
     *
     * @param string $sProperty The property to set
     * @param mixed  $mValue    The value to set
     *
     * @return $this
     */
    public function setOption($sProperty, $mValue)
    {
        $this->aOptions[$sProperty] = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get an option
     *
     * @param string $sProperty The property to return
     *
     * @return mixed
     */
    public function getOption($sProperty)
    {
        return getFromArray($sProperty, $this->aOptions);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a header
     *
     * @param $sHeader
     * @param $mValue
     *
     * @return $this
     */
    public function setHeader($sHeader, $mValue)
    {
        if (empty($this->aConfig['headers'])) {
            $this->aConfig['headers'] = [];
        }

        $this->aConfig['headers'][$sHeader] = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the path of the request
     *
     * @param string $sPath The path to set
     *
     * @return $this
     */
    public function path($sPath)
    {
        return $this->setOption('path', $sPath);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the required headers for immitating a user
     *
     * @param integer $iUserId the user to immitate
     *
     * @return $this
     */
    public function as($iUserId)
    {
        return $this
            ->setHeader(Testing::TEST_HEADER_NAME, Testing::TEST_HEADER_VALUE)
            ->setHeader(Testing::TEST_HEADER_USER_NAME, $iUserId);
    }

    // --------------------------------------------------------------------------

    /**
     * Configures and executes the HTTP request
     *
     * @param string $sPath The path to set for the request
     *
     * @return HttpResponse
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function execute($sPath)
    {
        if (!empty($sPath)) {
            $this->path($sPath);
        }
        return Factory::factory('HttpResponse');
    }
}
