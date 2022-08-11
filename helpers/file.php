<?php

/**
 * This class allows the Nails Factory to load CodeIgniter helpers in the same way as it loads native helpers.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Common\Helper\File;

if (!function_exists('readFileChunked')) {
    function readFileChunked($sFilename, $iChunkSize = 1048576)
    {
        return File::readFileChunked($sFilename, $iChunkSize);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('fileExistsCS')) {
    function fileExistsCS($sFilename)
    {
        return File::fileExistsCS($sFilename);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isDirCS')) {
    function isDirCS($sDir)
    {
        return File::isDirCS($sDir);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('formatBytes')) {
    function formatBytes(int $iBytes, int $iPrecision = 2): string
    {
        return File::formatBytes($iBytes, $iPrecision);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('returnBytes')) {
    function returnBytes(string $sSize): int
    {
        return File::returnBytes($sSize);
    }
}

// --------------------------------------------------------------------------

include NAILS_CI_SYSTEM_PATH . 'helpers/file_helper.php';
