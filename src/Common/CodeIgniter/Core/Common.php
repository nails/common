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

use Nails\Bootstrap;
use Nails\Functions;
use Nails\Common\Helper\ArrayHelper;

if (!function_exists('getControllerData')) {
    function &getControllerData()
    {
        return Bootstrap::getControllerData();
    }
}

if (!function_exists('setControllerData')) {
    function setControllerData($sKey, $mValue)
    {
        Bootstrap::setControllerData($sKey, $mValue);
    }
}

if (!function_exists('show_error')) {

    function show_error($sMessage = '', $sSubject = '', $iStatusCode = 500)
    {
        Functions::showError($$sMessage, $sSubject, $iStatusCode);
    }
}

if (!function_exists('show_401')) {
    function show_401($bLogError = true)
    {
        Functions::show401($bLogError);
    }
}

if (!function_exists('show401')) {
    function show401($bLogError = true)
    {
        Functions::show401($bLogError);
    }
}

if (!function_exists('unauthorised')) {
    function unauthorised($bLogError = true)
    {
        Functions::show401($bLogError);
    }
}

if (!function_exists('show_404')) {
    function show_404($bLogError = true)
    {
        Functions::show404($bLogError);
    }
}

if (!function_exists('show404')) {
    function show404($bLogError = true)
    {
        Functions::show404($bLogError);
    }
}

if (!function_exists('getFromArray')) {
    function getFromArray($sKey, $aArray, $mDefault = null)
    {
        return ArrayHelper::getFromArray($sKey, $aArray, $mDefault);
    }
}

if (!function_exists('isCli')) {
    function isCli()
    {
        return Functions::isCli();
    }
}

if (!function_exists('isAjax')) {
    function isAjax()
    {
        return Functions::isAjax();
    }
}
