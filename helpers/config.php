<?php

/**
 * This file provides config related helper functions
 *
 * @package    Nails
 * @subpackage common
 * @category   Helper
 * @author     Nails Dev Team
 */

use Nails\Config;

if (!function_exists('config')) {
    function config(string $sKey, $mDefault = null)
    {
        return Config::get($sKey, $mDefault);
    }
}
