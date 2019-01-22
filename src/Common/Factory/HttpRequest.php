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

use GuzzleHttp\Client;
use Nails\Common\Helper\ArrayHelper;
use Nails\Environment;
use Nails\Factory;
use Nails\Testing;

abstract class HttpRequest
{
    /**
     * The HTTP Method for the request
     *
     * @var string
     */
    const HTTP_METHOD = '';

    // --------------------------------------------------------------------------

    /**
     * The request headers
     *
     * @var array
     */
    protected $aHeaders = [];

    /**
     * The Base URI for the request
     *
     * @var string
     */
    protected $sBaseUri;

    /**
     * The path for the request
     *
     * @var string
     */
    protected $sPath;

    // --------------------------------------------------------------------------

    /**
     * HttpRequest constructor.
     *
     * @param string $sBaseUri The Base URI for the request
     * @param string $sPath    The path for the request
     * @param array  $aHeaders An array of headers to set
     */
    public function __construct($sBaseUri = null, $sPath = null, array $aHeaders = [])
    {
        $this->baseUri($sBaseUri);
        $this->path($sPath);

        foreach ($aHeaders as $sHeader => $mValue) {
            $this->setHeader($sHeader, $mValue);
        }
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
        if (empty($this->aHeaders)) {
            $this->aHeaders = [];
        }

        $this->aHeaders[$sHeader] = $mValue;
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
        return isset($this->aHeaders) ? $this->aHeaders : [];
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
        return isset($this->aHeaders[$sHeader]) ? $this->aHeaders[$sHeader] : null;
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
     * Populates the baseUri property of the request
     *
     * @param string $sBaseUri The base for the request
     *
     * @return $this
     */
    public function baseUri($sBaseUri)
    {
        $this->sBaseUri = $sBaseUri ?: BASE_URL;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Populates the path property of the request
     *
     * @param string $sPath The path for the request
     *
     * @return $this
     */
    public function path($sPath)
    {
        $this->sPath = $sPath;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Configures and executes the HTTP request
     *
     * @return HttpResponse
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function execute()
    {
        $aClientConfig   = [
            'base_uri'        => $this->sBaseUri,
            'verify'          => !(Environment::is(Environment::ENV_DEV) || Environment::is(Environment::ENV_TEST)),
            'allow_redirects' => Environment::not(Environment::ENV_TEST),
            'http_errors'     => Environment::not(Environment::ENV_TEST),
        ];
        $aRequestOptions = [
            'headers' => $this->aHeaders,
        ];

        $this->compile($aClientConfig, $aRequestOptions);

        $oClient = Factory::factory('HttpClient', '', $aClientConfig);

        return Factory::factory(
            'HttpResponse',
            '',
            $oClient->request(static::HTTP_METHOD, $this->sPath, $aRequestOptions)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the request
     *
     * @param array $aClientConfig   The config array for the HTTP Client
     * @param array $aRequestOptions The options for the request
     */
    abstract protected function compile(array &$aClientConfig, array &$aRequestOptions);
}
