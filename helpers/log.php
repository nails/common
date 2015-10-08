<?php

/**
 * This helper brings some shorthand functions for writing to the log
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('_LOG')) {

    /**
     * Writes a line to the log
     * @param  string $sLine The line to write
     * @return void
     */
    function _LOG($sLine = '') {
        return get_instance()->logger->line($sLine);
    }
}

if (!function_exists('_LOG_DIR')) {

    /**
     * Set the log directory which is being written to
     * @param string $sDir The directory to write to
     */
    function _LOG_DIR($sDir = '') {
        return get_instance()->logger->setDir($sDir);
    }
}

if (!function_exists('_LOG_FILE')) {

    /**
     * Set the filename which is being written to
     * @param string $sFile The file to write to
     */
    function _LOG_FILE($sFile = '') {
        return get_instance()->logger->setFile($sFile);
    }
}

if (!function_exists('_LOG_MUTE_OUTPUT')) {

    /**
     * Temporarily mute logging
     * @param  bool $bMute Whether mute is on or off
     * @return void
     */
    function _LOG_MUTE_OUTPUT($bMute = true) {
        get_instance()->logger->bMute = (bool) $bMute;
    }
}

if (!function_exists('_LOG_DUMMY_MODE')) {

    /**
     * Switch the logger into dummy mode
     * @param  boolean $bDummy Whether dummy mode is on or off
     * @return void
     */
    function _LOG_DUMMY_MODE($bDummy = true) {
        get_instance()->logger->bDummy = (bool) $bDummy;
    }
}
