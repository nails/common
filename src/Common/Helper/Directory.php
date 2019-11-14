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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class Directory
 *
 * @package Nails\Common\Helper
 */
class Directory
{
    /**
     * Returns all files within a directory
     *
     * @param string      $sPath          The directory to map
     * @param int|null    $iMaxDepth      How deep to map, 1 = this dir, 2 this dir and next, etc
     * @param bool        $bAbsolutePath  Return the absolute path of each file
     * @param bool        $bIncludeHidden Include hidden files (i.e. dot files like .htaccess)
     * @param int         $iCurrentDepth  For recursion, the current depth of the iteration
     * @param string|null $sInitialPath   For recursion, the initial path to map
     * @param array       $aResults       For recursion, the array to populate
     *
     * @return array
     */
    public static function map(
        string $sPath,
        int $iMaxDepth = null,
        bool $bAbsolutePath = true,
        bool $bIncludeHidden = false,
        int $iCurrentDepth = 0,
        string $sInitialPath = null,
        array &$aResults = []
    ): array {

        if (!is_dir($sPath)) {
            return [];
        }

        if ($iMaxDepth === $iCurrentDepth) {
            return $aResults;
        }

        $sPath = rtrim($sPath, '/') . '/';

        if (is_null($sInitialPath)) {
            $sInitialPath = $sPath;
        }

        $oIterator = new \FilesystemIterator(
            $sPath
        );

        /** @var \SplFileInfo $oItem */
        foreach ($oIterator as $oItem) {
            if ($oItem->isDir()) {
                static::map(
                    $oItem->getPathname(),
                    $iMaxDepth,
                    $bAbsolutePath,
                    $bIncludeHidden,
                    $iCurrentDepth + 1,
                    $sInitialPath,
                    $aResults
                );
            } elseif (!$bIncludeHidden && preg_match('/^\./', $oItem->getFilename())) {
                continue;
            } elseif (!$bAbsolutePath) {
                $aResults[] = preg_replace('/^' . preg_quote($sInitialPath, '/') . '/', '', $oItem->getPathname());
            } else {
                $aResults[] = $oItem->getPathname();
            }
        }

        sort($aResults);

        return $aResults;
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
