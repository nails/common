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

/**
 * Class Post
 *
 * @package Nails\Common\Factory\HttpRequest
 */
class Post extends Get
{
    const HTTP_METHOD = 'POST';

    // --------------------------------------------------------------------------

    /**
     * The form values to POST
     *
     * @var array
     */
    protected $aFormParams = [];

    /**
     * The body to POST
     *
     * @var string
     */
    protected $sBody = '';

    // --------------------------------------------------------------------------

    /**
     * Populates the form parameters of the request
     *
     * @param array $aParams The form parameters
     *
     * @return $this
     */
    public function params(array $aParams = []): self
    {
        $this->aFormParams = $aParams;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Populates the body of the request
     *
     * @param string $sBody   The request body
     * @param bool   $bIsJson Whether the body is JSON or not
     */
    public function body(string $sBody, bool $bIsJson = true): self
    {
        $this->sBody = $sBody;
        if ($bIsJson) {
            $this
                ->setHeader('Content-Type', 'application/json')
                ->setHeader('Content-Length', strlen($sBody));
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the request
     *
     * @param array $aClientConfig   The config array for the HTTP Client
     * @param array $aRequestOptions The options for the request
     */
    protected function compile(array &$aClientConfig, array &$aRequestOptions): void
    {
        parent::compile($aClientConfig, $aRequestOptions);

        if (!empty($this->aFormParams)) {
            $aRequestOptions['form_params'] = $this->aFormParams;
        }

        if (!empty($this->sBody)) {
            $aRequestOptions['body'] = $this->sBody;
        }
    }
}
