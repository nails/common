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

use Nails\Environment;
use Nails\Factory;
use Nails\Testing;

trait ControllerTestCase
{
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
            $aConfig['verify'] = Environment::is(Environment::ENV_DEV) ? false : true;
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

        return Factory::factory('HttpClient', '', $aConfig);
    }
}
