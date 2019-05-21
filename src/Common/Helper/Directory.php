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

use Nails\Common\Exception\Directory\DirectoryDoesNotExistException;
use Nails\Common\Exception\Directory\DirectoryIsNotWritableException;
use Nails\Common\Exception\Directory\DirectoryNameException;
use Nails\Common\Exception\FactoryException;
use Nails\Factory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Directory
{
    /**
     * Maps a directory
     *
     * @param string $sDir    The directory to map
     * @param int    $iDepth  How deep to go down the rabbit hole
     * @param bool   $bHidden Whether to show hidden files or not
     *
     * @return array
     * @throws FactoryException
     */
    public static function map(string $sDir, int $iDepth = 0, bool $bHidden = false): array
    {
        if (!is_dir($sDir)) {
            return [];
        }

        return directory_map($sDir, $iDepth, $bHidden);
    }

    // --------------------------------------------------------------------------

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

    // --------------------------------------------------------------------------

    /**
     * Creates a temporary directory
     * hat-tip: https://stackoverflow.com/a/30010928/789224
     *
     * @param string|null $sDir         Where to create the temporary directory (uses system temp directory by default)
     * @param string      $sPrefix      A prefix to use
     * @param int         $iMode        The mode of the created directory
     * @param int         $iMaxAttempts The maximum number of attempts
     *
     * @return string
     * @throws DirectoryDoesNotExistException
     * @throws DirectoryIsNotWritableException
     * @throws DirectoryNameException
     */
    public static function tempdir(string $sDir = null, string $sPrefix = 'tmp_', int $iMode = 0700, int $iMaxAttempts = 1000)
    {
        //  Use the system temp dir by default
        if (is_null($sDir)) {
            $sDir = sys_get_temp_dir();
        }

        //  Trim trailing slashes from $sDir
        $sDir = rtrim($sDir, DIRECTORY_SEPARATOR);

        /**
         * If we don't have permission to create a directory, fail, otherwise we will
         * be stuck in an endless loop
         */
        if (!is_dir($sDir)) {
            throw new DirectoryDoesNotExistException(
                '"' . $sDir . '" does not exist'
            );
        } elseif (!is_writable($sDir)) {
            throw new DirectoryIsNotWritableException(
                '"' . $sDir . '" is not writable'
            );
        }

        //  Make sure characters in prefix are safe
        if (strpbrk($sPrefix, '\\/:*?"<>|') !== false) {
            throw new DirectoryNameException(
                '"' . $sDir . '" name contains invalid characters'
            );
        }

        /**
         * Attempt to create a random directory until it works. Abort if we reach
         * $iMaxAttempts. Something screwy could be happening with the filesystem
         * and our loop could otherwise become endless.
         */
        $iAttempts = 0;
        do {
            $sPath = sprintf('%s%s%s%s', $sDir, DIRECTORY_SEPARATOR, $sPrefix, mt_rand(100000, mt_getrandmax()));
        } while (
            !mkdir($sPath, $iMode) &&
            $iAttempts++ < $iMaxAttempts
        );

        return $sPath . DIRECTORY_SEPARATOR;
    }
}
