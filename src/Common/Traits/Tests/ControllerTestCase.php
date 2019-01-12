<?php

/**
 * Convenience methods for tests which are testing controllers
 *
 * @package     Nails
 * @subpackage  common
 * @category    traits
 * @author      Nails Dev Team
 */

namespace Nails\Common\Traits\Tests;

use Nails\Factory;
use Nails\Testing;

trait ControllerTestCase
{
    /**
     * The ID of the user to execute the request as
     *
     * @var integer
     */
    private static $aRequestAsUserId = null;

    // --------------------------------------------------------------------------

    /**
     * Returns a new HttpClient, configured with some defaults
     *
     * @param array $aConfig A configiration array for the Http client
     *
     * @return \GuzzleHttp\Client
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected static function getHttpClient(array $aConfig = [])
    {
        if (!array_key_exists('base_uri', $aConfig)) {
            $aConfig['base_uri'] = site_url();
        }

        if (!array_key_exists('verify', $aConfig)) {
            $aConfig['verify'] = false;
        }

        if (!array_key_exists('http_errors', $aConfig)) {
            $aConfig['http_errors'] = false;
        }

        if (!array_key_exists('allow_redirects', $aConfig)) {
            $aConfig['allow_redirects'] = false;
        }

        if (!array_key_exists('headers', $aConfig)) {
            $aConfig['headers'] = [
                Testing::TEST_HEADER_NAME => Testing::TEST_HEADER_VALUE,
            ];
        }

        if (!empty(static::static::$aRequestAsUserId)) {
            $aConfig['headers'][Testing::TEST_HEADER_USER_NAME] = static::$aRequestAsUserId;
        }

        return Factory::factory('HttpClient', '', $aConfig);
    }

    // --------------------------------------------------------------------------

    protected static function as($iUserId)
    {
        static::$aRequestAsUserId = $iUserId;
    }

    // --------------------------------------------------------------------------

    /**
     * Execute a HTTP GET request
     *
     * @param string $sPath The path to GET
     * @param array  $aData The data to pass
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected static function get($sPath, array $aData)
    {
        return static::getHttpClient()->get($sPath, ['query' => $aData]);
    }

    // --------------------------------------------------------------------------

    /**
     * Execute a HTTP POST request
     *
     * @param string $sPath The path to POST to
     * @param array  $aData The data to pass
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected static function post($sPath, array $aData)
    {
        return static::getHttpClient()->post($sPath, ['form_params' => $aData]);
    }
}
