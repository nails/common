<?php

/**
 * This file provides exception related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

if (!function_exists('showFatalError')) {
    /**
     * Renders the fatal error screen and alerts developers
     *
     * @param  string $sSubject The subject of the developer alert
     * @param  string $sMessage The body of the developer alert
     *
     * @return void
     */
    function showFatalError($sSubject = '', $sMessage = '')
    {
        Factory::service('ErrorHandler')
            ->showFatalErrorScreen($sSubject, $sMessage);
    }
}

