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
     * @param array $aData A configiration array for the Http client
     *
     * @return \GuzzleHttp\Client
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected static function getHttpClient(array $aData = [])
    {
        if (!array_key_exists('base_uri', $aData)) {
            //$aData['base_uri'] = site_url();
            $aData['base_uri'] = 'https://localhost';
        }

        if (!array_key_exists('verify', $aData)) {
            $aData['verify'] = Environment::is('DEVELOPMENT') ? false : true;
        }

        if (!array_key_exists('http_errors', $aData)) {
            $aData['http_errors'] = false;
        }

        return Factory::factory('HttpClient', '', $aData);
    }
}
