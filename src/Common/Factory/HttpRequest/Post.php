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

    // --------------------------------------------------------------------------

    /**
     * Populates the form parameters of the request
     *
     * @param array $aParams The form parameters
     *
     * @return $this
     */
    public function params(array $aParams = [])
    {
        $this->aFormParams = $aParams;
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
        parent::compile($aClientConfig, $aRequestOptions);
        $aRequestOptions['form_params'] = $this->aFormParams;
    }
}
