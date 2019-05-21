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

use Nails\Common\Helper\Directory;

if (!function_exists('deleteDir')) {
    function deleteDir($sDir)
    {
        return Directory::delete($sDir);
    }
}

if (!function_exists('directoryMap')) {
    function directoryMap(string $sDir, int $iDepth = 0, bool $bHidden = false)
    {
        return Directory::map($sDir, $iDepth, $bHidden);
    }
}

// --------------------------------------------------------------------------

include NAILS_CI_SYSTEM_PATH . 'helpers/directory_helper.php';
