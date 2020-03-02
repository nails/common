<?php

/**
 * Provides enhanced logging facilities
 *
 * @todo        - Deprecate this in favour of something like monolog
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Config;
use Nails\Factory;

/**
 * Class Logger
 *
 * @package Nails\Common\Service
 */
class Logger
{
    private $oLog;
    public  $bMute;
    public  $bDummy;

    // --------------------------------------------------------------------------

    /**
     * Construct the library
     */
    public function __construct()
    {
        //  Load helper
        Factory::helper('file');

        // --------------------------------------------------------------------------

        //  Define defaults
        $this->oLog   = new \stdClass();
        $this->bMute  = false;
        $this->bDummy = false;

        $this->setFile();
        $this->setDir();
    }

    // --------------------------------------------------------------------------

    /**
     * Writes a line to the log
     *
     * @param string $sLine The line to write
     *
     * @return void
     */
    public function line($sLine = '')
    {
        //  Is dummy mode enabled? If it is then don't do anything.
        if ($this->bDummy) {
            return;
        }

        // --------------------------------------------------------------------------

        $sLogPath = $this->oLog->dir . $this->oLog->file;
        $oDate    = Factory::factory('DateTime');

        // --------------------------------------------------------------------------

        //  If the log file doesn't exist (or we haven't checked already), attempt to create it
        if (!$this->oLog->exists) {

            if (!file_exists($sLogPath)) {

                //  Check directory is there
                $sDir = dirname($sLogPath);

                if (!is_dir($sDir)) {

                    //  Create structure
                    mkdir($sDir, 0750, true);
                }

                // --------------------------------------------------------------------------

                $sFirstLine = '<?php die(\'Unauthorised\'); ?>' . "\n\n";
                if (write_file($sLogPath, $sFirstLine)) {
                    $this->oLog->exists = true;
                } else {
                    $this->oLog->exists = false;
                }

            } else {
                $this->oLog->exists = true;
            }
        }

        // --------------------------------------------------------------------------

        if ($this->oLog->exists) {

            if (empty($sLine)) {
                write_file($sLogPath, "\n", 'a');
            } else {
                write_file($sLogPath, 'INFO - ' . $oDate->format('Y-m-d H:i:s') . ' --> ' . trim($sLine) . "\n", 'a');
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set the filename which is being written to
     *
     * @param string $sFile The file to write to
     */
    public function setFile($sFile = '')
    {
        //  Reset the log exists var so that line() checks again
        $this->oLog->exists = false;

        // --------------------------------------------------------------------------

        if (!empty($sFile)) {
            $this->oLog->file = $sFile;
        } else {
            $oDate            = Factory::factory('DateTime');
            $this->oLog->file = 'log-' . $oDate->format('Y-m-d') . '.php';
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Return the active log file
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->oLog->file;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the log directory which is being written to
     *
     * @param string $sDir The directory to write to
     */
    public function setDir($sDir = '')
    {
        //  Reset the log exists var so that line() checks again
        $this->oLog->exists = false;

        // --------------------------------------------------------------------------

        if (!empty($sDir) && substr($sDir, 0, 1) === '/') {
            $this->oLog->dir = $sDir;
        } elseif (!empty($sDir)) {
            $this->oLog->dir = Config::get('LOG_DIR') . $sDir;
        } else {
            $this->oLog->dir = Config::get('LOG_DIR');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Return the active log directory
     *
     * @return string
     */
    public function getDir(): string
    {
        return $this->oLog->dir;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a stream handle for the active log file
     *
     * @return bool|resource
     */
    public function getStream()
    {
        return fopen($this->getDir() . $this->getFile(), 'a', false);
    }
}
