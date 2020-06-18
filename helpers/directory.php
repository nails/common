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
    function deleteDir($sDir): void
    {
        Directory::delete($sDir);
    }
}

if (!function_exists('directoryMap')) {
    function directoryMap(
        string $sPath,
        int $iMaxDepth = null,
        bool $bAbsolutePath = true,
        bool $bIncludeHidden = false,
        int $iCurrentDepth = 0,
        string $sInitialPath = null,
        array &$aResults = []
    ): array {
        return Directory::map(
            $sPath,
            $iMaxDepth,
            $bAbsolutePath,
            $bIncludeHidden,
            $iCurrentDepth,
            $sInitialPath,
            $aResults
        );
    }
}

// --------------------------------------------------------------------------

include NAILS_CI_SYSTEM_PATH . 'helpers/directory_helper.php';
