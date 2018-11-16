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
            $aConfig['verify'] = Environment::is('DEVELOPMENT') ? false : true;
        }

        if (!array_key_exists('http_errors', $aConfig)) {
            $aConfig['http_errors'] = false;
        }

        return Factory::factory('HttpClient', '', $aConfig);
    }
}
