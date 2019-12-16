<?php

/**
 * HTTP Response
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory;

use GuzzleHttp\Psr7\Response;
use Nails\Common\Helper\ArrayHelper;

class HttpResponse
{
    /**
     * @var Response
     */
    protected $oResponse;

    // --------------------------------------------------------------------------

    /**
     * HttpResponse constructor.
     *
     * @param Response $oResponse
     */
    public function __construct(Response $oResponse)
    {
        $this->oResponse = $oResponse;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the HTTP response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->oResponse;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the response headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->oResponse->getHeaders();
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
        return ArrayHelper::getFromArray($sHeader, $this->oResponse->getHeaders());
    }

    // --------------------------------------------------------------------------

    /**
     * Return the response's status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->oResponse->getStatusCode();
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the response's reason phrase
     *
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->oResponse->getReasonPhrase();
    }

    // --------------------------------------------------------------------------

    /**
     * Return the response's body, optionally parsed as JSON
     *
     * @param boolean $bIsJSON Whether the body is JSON or not
     *
     * @return array|\stdClass|string|null
     */
    public function getBody($bIsJSON = true)
    {
        if ($bIsJSON) {
            return json_decode($this->oResponse->getBody());
        } else {
            return (string) $this->oResponse->getBody();
        }
    }
}
