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

use Nails\Common\Helper\ArrayHelper;
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
        return ArrayHelper::getFromArray($sProperty, $this->aOptions);
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
        if (empty($this->aOptions['headers'])) {
            $this->aOptions['headers'] = [];
        }

        $this->aOptions['headers'][$sHeader] = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return isset($this->aOptions['headers']) ? $this->aOptions['headers'] : [];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a single header
     *
     * @param string $sHeader The header to return
     *
     * @return mixed|null
     */
    public function getHeader($sHeader)
    {
        return isset($this->aOptions['headers'][$sHeader]) ? $this->aOptions['headers'][$sHeader] : null;
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
     * Set the required headers for imitating a user
     *
     * @param integer $iUserId the user to imitate
     *
     * @return $this
     */
    public function asUser($iUserId)
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
    public function execute($sPath = null)
    {
        if (!empty($sPath)) {
            $this->path($sPath);
        }

        //  @todo (Pablo - 2019-01-12) - Validate the request
        //  @todo (Pablo - 2019-01-12) - Compile the request

        return Factory::factory('HttpResponse');
    }
}
