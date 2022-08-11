<?php

/**
 * File helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

use Nails\Common\Exception\NailsException;

/**
 * Class File
 *
 * @package Nails\Common\Helper
 */
class File
{
    /**
     * Byte Multipliers
     *
     * @var int
     */
    const BYTE_MULTIPLIER_KB = 1024;
    const BYTE_MULTIPLIER_MB = self::BYTE_MULTIPLIER_KB * 1024;
    const BYTE_MULTIPLIER_GB = self::BYTE_MULTIPLIER_MB * 1024;

    // --------------------------------------------------------------------------

    /**
     * Caches the results of static::fileExistsCS()
     *
     * @var array
     */
    protected static $aFileExistsCache = [];

    // --------------------------------------------------------------------------

    /**
     * Caches the results of static::isDirCS()
     *
     * @var array
     */
    protected static $aIsDirCache = [];

    // --------------------------------------------------------------------------

    /**
     * Outputs a file in byte sized chunks.
     * http://teddy.fr/2007/11/28/how-serve-big-files-through-php/
     *
     * @param string  $sFilename  The file to output
     * @param integer $iChunkSize The chunk size, in bytes
     *
     * @return bool|int
     */
    public static function readFileChunked(string $sFilename, int $iChunkSize = 1048576)
    {
        $iBytesRead = 0;
        $rHandle    = fopen($sFilename, 'rb');
        if ($rHandle === false) {
            return false;
        }

        while (!feof($rHandle)) {
            $sBuffer    = fread($rHandle, $iChunkSize);
            $iBytesRead += strlen($sBuffer);
            echo $sBuffer;
        }

        $bStatus = fclose($rHandle);
        return $bStatus ? $iBytesRead : false;
    }

    // --------------------------------------------------------------------------

    /**
     * A case-sensitive file_exists
     *
     * @param string $sFilename The file to test
     *
     * @return bool
     */
    public static function fileExistsCS(string $sFilename)
    {
        if (empty($sFilename)) {
            return false;
        } elseif (array_key_exists($sFilename, static::$aFileExistsCache)) {
            return static::$aFileExistsCache[$sFilename];
        }

        $sDirectory = dirname($sFilename) . DIRECTORY_SEPARATOR;
        $aFiles     = array_map(function ($sFile) {
            return basename($sFile);
        }, static::listDir($sDirectory));

        if (in_array(basename($sFilename), $aFiles)) {
            //  Test if the directory exists
            $bResult = static::isDirCS($sDirectory);
        } else {
            $bResult = false;
        }

        static::$aFileExistsCache[$sFilename] = $bResult;
        return $bResult;
    }

    // --------------------------------------------------------------------------

    /**
     * A case-sensitive is_dir
     *
     * @param string $sDir The directory to test
     *
     * @return bool
     */
    public static function isDirCS(string $sDir)
    {
        if (empty($sDir)) {
            return false;
        } elseif (array_key_exists($sDir, static::$aIsDirCache)) {
            return static::$aIsDirCache[$sDir];
        }

        $aDirBits = explode(DIRECTORY_SEPARATOR, rtrim($sDir, DIRECTORY_SEPARATOR));
        $bResult  = true;
        while (count($aDirBits) > 1) {
            $sDirectory     = array_pop($aDirBits);
            $sDirectoryPath = implode(DIRECTORY_SEPARATOR, $aDirBits);
            $aDirectories   = array_map(function ($sDirectory) {
                return basename($sDirectory);
            }, glob($sDirectoryPath . DIRECTORY_SEPARATOR . '*', GLOB_NOSORT | GLOB_ONLYDIR));

            if (!in_array($sDirectory, $aDirectories)) {
                $bResult = false;
                break;
            }
        }

        static::$aIsDirCache[$sDir] = $bResult;
        return $bResult;
    }

    // --------------------------------------------------------------------------

    /**
     * [Quickly] lists the contents of a directory
     *
     * @param string $sPath The directory to list
     *
     * @return string[]
     */
    protected static function listDir(string $sPath): array
    {
        return is_dir($sPath)
            ? array_map(
                function ($sFile) use ($sPath) {
                    return $sPath . $sFile;
                },
                scandir($sPath)
            )
            : [];
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a file size given in bytes into a human-friendly string
     *
     * @param int $iBytes     The file size, in bytes
     * @param int $iPrecision The precision to use
     *
     * @return string
     */
    public static function formatBytes(int $iBytes, int $iPrecision = 2): string
    {
        $aUnits = ['B', 'KB', 'MB', 'GB', 'TB'];
        $iBytes = max($iBytes, 0);
        $fPow   = floor(($iBytes ? log($iBytes) : 0) / log(1024));
        $fPow   = min($fPow, count($aUnits) - 1);

        $iBytes   /= (1 << (10 * $fPow));
        $fRounded = round($iBytes, $iPrecision) . ' ' . $aUnits[$fPow];

        return preg_replace_callback(
            '/(.+?)\.(.*?)/',
            function ($matches) {
                return number_format($matches[1]) . '.' . $matches[2];
            },
            $fRounded
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a file size as bytes (e.g max_upload_size)
     * hat-tip: http://php.net/manual/en/function.ini-get.php#96996
     *
     * @param string $sSize The string to convert to bytes
     *
     * @return int
     */
    public static function returnBytes(string $sSize): int
    {
        switch (strtoupper(substr($sSize, -1))) {
            case 'M':
                return (int) $sSize * static::BYTE_MULTIPLIER_MB;

            case 'K':
                return (int) $sSize * static::BYTE_MULTIPLIER_KB;

            case 'G':
                return (int) $sSize * static::BYTE_MULTIPLIER_GB;
        }

        throw new NailsException(sprintf(
            'Invalid format `%s`',
            $sSize
        ));
    }
}
