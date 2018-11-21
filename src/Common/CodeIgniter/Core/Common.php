<?php

/**
 * This file provides functions used internally by Nails
 *
 * @package     Nails
 * @subpackage  common
 * @category    helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('getControllerData')) {
    function &getControllerData()
    {
        return Nails\Bootstrap::getControllerData();
    }
}

if (!function_exists('setControllerData')) {
    function setControllerData($sKey, $mValue)
    {
        Nails\Bootstrap::setControllerData($sKey, $mValue);
    }
}

if (!function_exists('show_error')) {

    function show_error($sMessage = '', $sSubject = '', $iStatusCode = 500)
    {
        \Nails\Functions::showError($$sMessage, $sSubject, $iStatusCode);
    }
}

if (!function_exists('show_401')) {
    function show_401($bLogError = true)
    {
        \Nails\Functions::show401($bLogError);
    }
}

if (!function_exists('show401')) {
    function show401($bLogError = true)
    {
        \Nails\Functions::show401($bLogError);
    }
}

if (!function_exists('unauthorised')) {
    function unauthorised($bLogError = true)
    {
        \Nails\Functions::show401($bLogError);
    }
}

if (!function_exists('show_404')) {
    function show_404($bLogError = true)
    {
        \Nails\Functions::show404($bLogError);
    }
}

if (!function_exists('show404')) {
    function show404($bLogError = true)
    {
        \Nails\Functions::show404($bLogError);
    }
}

if (!function_exists('getFromArray')) {
    function getFromArray($sKey, $aArray, $mDefault = null)
    {
        return \Nails\Functions::getFromArray($sKey, $aArray, $mDefault);
    }
}

if (!function_exists('isCli')) {
    function isCli()
    {
        return \Nails\Functions::isCli();
    }
}

if (!function_exists('isAjax')) {
    function isAjax()
    {
        return \Nails\Functions::isAjax();
    }
}
