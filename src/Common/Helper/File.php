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

class File
{
    /**
     * Caches the results of static::fileExistsCS()
     * @var array
     */
    protected static $aFileExistsCache = [];

    // --------------------------------------------------------------------------

    /**
     * Caches the results of static::isDirCS()
     * @var array
     */
    protected static $aIsDirCache = [];

    // --------------------------------------------------------------------------

    /**
     * Outputs a file in bytesized chunks.
     * http://teddy.fr/2007/11/28/how-serve-big-files-through-php/
     *
     * @param  string  $sFilename  The file to output
     * @param  integer $iChunkSize The chunk size, in bytes
     *
     * @return bool|int
     */
    public static function readFileChunked($sFilename, $iChunkSize = 1048576)
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
    public static function fileExistsCS($sFilename)
    {
        if (array_key_exists($sFilename, static::$aFileExistsCache)) {
            return static::$aFileExistsCache[$sFilename];
        }

        $sDirectory = dirname($sFilename);
        $aFiles     = array_map(function ($sFile) {
            return basename($sFile);
        }, glob($sDirectory . DIRECTORY_SEPARATOR . '*', GLOB_NOSORT));

        if (in_array(basename($sFilename), $aFiles)) {
            //  Test if the directory exists
            $bResult = isDirCS($sDirectory);
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
     * @param string $sDir The directory to trst
     *
     * @return bool
     */
    public static function isDirCS($sDir)
    {
        if (array_key_exists($sDir, static::$aIsDirCache)) {
            return static::$aIsDirCache[$sDir];
        }

        $aDirBits = explode(DIRECTORY_SEPARATOR, $sDir);
        $bResult  = true;
        while (count($aDirBits) > 1) {
            $sDirectory     = array_pop($aDirBits);
            $sDirectoryPath = implode(DIRECTORY_SEPARATOR, $aDirBits);
            $aDirectories   = array_map(function ($sDirectory) {
                return basename($sDirectory);
            }, glob($sDirectoryPath . '/*', GLOB_NOSORT | GLOB_ONLYDIR));

            if (!in_array($sDirectory, $aDirectories)) {
                $bResult = false;
                break;
            }
        }

        static::$aIsDirCache[$sDir] = $bResult;
        return $bResult;
    }
}
