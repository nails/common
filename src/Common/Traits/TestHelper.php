<?php

namespace Nails\Common\Traits;

/**
 * Trait TestHelper
 *
 * @package Nails\Common\Traits
 */
trait TestHelper
{
    /**
     * Executes a private method
     *
     * @param string $sClass     The class containing the private method
     * @param string $sMethod    The method to call
     * @param array  $aArguments Arguments to pass to the method
     *
     * @return array|mixed
     * @throws \ReflectionException
     */
    private static function executePrivateMethod($sClass, $sMethod, array $aArguments = [])
    {
        $oClass    = new \ReflectionClass($sClass);
        $oInstance = new $sClass();

        $oMethod = $oClass->getMethod($sMethod);

        //  PHP 8.6 deprecates passing an object when invoking a static method
        return $oMethod->invokeArgs($oMethod->isStatic() ? null : $oInstance, $aArguments);
    }
}
