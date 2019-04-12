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

include NAILS_CI_SYSTEM_PATH . 'helpers/file_helper.php';
