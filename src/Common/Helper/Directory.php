<?php

/**
 * Directory helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Directory
{
    /**
     * Recursively deletes a directory
     *
     * @param string $sDir the directory to delete
     */
    public static function delete(string $sDir): void
    {
        if (is_dir($sDir)) {
            $oFiles = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $sDir,
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($oFiles as $oFile) {
                $sfunction = $oFile->isDir() ? 'rmdir' : 'unlink';
                $sfunction($oFile->getRealPath());
            }

            rmdir($sDir);
        }
    }
}
