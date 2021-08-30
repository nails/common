<?php

/**
 * The class abstracts CI's Config class.
 *
 * @todo        - remove dependency on CI
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\FactoryException;
use Nails\Factory;

/**
 * Class Config
 *
 * @package Nails\Common\Service
 */
class Config
{
    /**
     * --------------------------------------------------------------------------
     *
     * The following properties and methods are legacy to support the CodeIgniter
     * Config class. New functionality should use the Nails\Config class
     *
     * --------------------------------------------------------------------------
     */

    /**
     * The database object
     *
     * @var \CI_Config
     */
    private $oConfig;

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter Config class
     *
     * @param string $sMethod    The method being called
     * @param array  $aArguments Any arguments being passed
     *
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        if (method_exists($this, $sMethod)) {

            return call_user_func_array([$this, $sMethod], $aArguments);

        } else {

            $this->setupCiConfigClass();
            return call_user_func_array([$this->oConfig, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter Config class
     *
     * @param string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        $this->setupCiConfigClass();
        return $this->oConfig->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter Config class
     *
     * @param string $sProperty The property to set
     * @param mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->setupCiConfigClass();
        $this->oConfig->{$sProperty} = $mValue;
    }

    // --------------------------------------------------------------------------

    /**
     * Instantiates the CI Config class, if not already instantiated
     */
    private function setupCiConfigClass()
    {
        if (empty($this->oConfig)) {
            $this->oConfig = get_instance()->config;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Prepends BASE_URL or SECURE_BASE_URL to a string
     *
     * @param string $sUri       The URI to append
     * @param bool   $bUseSecure Whether to use BASE_URL or SECURE_BASE_URL
     *
     * @return string
     * @throws FactoryException
     */
    public static function siteUrl(string $sUri, bool $bUseSecure = false): string
    {
        if (preg_match('/^(https?:\/\/|#)/', $sUri)) {
            //  Absolute URI; return unaltered
            return $sUri;

        } else {

            $sBaseUrl = $bUseSecure
                ? rtrim(\Nails\Config::get('SECURE_BASE_URL'), '/') . '/'
                : rtrim(\Nails\Config::get('BASE_URL'), '/') . '/';

            /** @var Locale $oLocale */
            $oLocale = Factory::service('Locale');
            $sLocale = $oLocale->getUrlSegment($oLocale->get());
            $sLocale = $sLocale && $sUri ? $sLocale . '/' : $sLocale;

            return $sBaseUrl . $sLocale . ltrim($sUri, '/');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Alias to siteUrl()
     *
     * @param string $sUri       The URI to append
     * @param bool   $bUseSecure Whether to use BASE_URL or SECURE_BASE_URL
     *
     * @return string
     */
    public static function site_url($sUri, $bUseSecure = false)
    {
        return static::siteUrl($sUri, $bUseSecure);
    }
}
