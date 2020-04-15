<?php

/**
 * This file provides log related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

if (!function_exists('_LOG')) {

    /**
     * Writes a line to the log
     *
     * @param string $sLine The line to write
     *
     * @return void
     */
    function _LOG($sLine = ''): void
    {
        /** @var \Nails\Common\Service\Logger $oLogger */a
        $oLogger = Factory::service('Logger');
        $oLogger->line($sLine);
    }
}

if (!function_exists('_LOG_MUTE_OUTPUT')) {

    /**
     * Temporarily mute logging
     *
     * @param bool $bMute Whether mute is on or off
     *
     * @return void
     */
    function _LOG_MUTE_OUTPUT(bool $bMute = true): void
    {
        /** @var \Nails\Common\Service\Logger $oLogger */
        $oLogger = Factory::service('Logger');
        $oLogger->mute($bMute);
    }
}

if (!function_exists('_LOG_DUMMY_MODE')) {

    /**
     * Switch the logger into dummy mode
     *
     * @param boolean $bDummy Whether dummy mode is on or off
     *
     * @return void
     */
    function _LOG_DUMMY_MODE(bool $bDummy = true): void
    {
        /** @var \Nails\Common\Service\Logger $oLogger */
        $oLogger = Factory::service('Logger');
        $oLogger->dummy($bDummy);
    }
}
