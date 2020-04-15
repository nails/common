<?php

/**
 * Provides logging facilities
 *
 * @todo        - Deprecate this in favour of something like monolog
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Factory;

use Nails\Common\Exception\FactoryException;
use Nails\Config;
use Nails\Factory;

/**
 * Class Logger
 *
 * @package Nails\Common\Factory
 */
class Logger
{
    /** @var bool */
    public $bMute = false;

    /** @var bool */
    public $bDummy = false;

    /** @var bool */
    protected $bExists;

    /** @var string */
    protected $sDir;

    /** @var string */
    protected $sFile;

    // --------------------------------------------------------------------------

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        $this
            ->setFile()
            ->setDir();
    }

    // --------------------------------------------------------------------------

    /**
     * Writes a line to the log
     *
     * @param string $sLine The line to write
     *
     * @return $this
     * @throws FactoryException
     */
    public function line($sLine = ''): self
    {
        //  Is dummy mode enabled? If it is then don't do anything.
        if ($this->bDummy) {
            return $this;
        }

        // --------------------------------------------------------------------------

        $sLogPath = $this->sDir . $this->sFile;
        $oNow     = Factory::factory('DateTime');

        // --------------------------------------------------------------------------

        if (is_null($this->bExists)) {

            if (!file_exists($sLogPath)) {

                $sDir = dirname($sLogPath);
                if (!is_dir($sDir)) {
                    mkdir($sDir, 0750, true);
                }

                // --------------------------------------------------------------------------

                $sFirstLine = '<?php die(\'Unauthorised\'); ?>' . "\n\n";
                if (write_file($sLogPath, $sFirstLine)) {
                    $this->bExists = true;
                } else {
                    $this->bExists = false;
                }

            } else {
                $this->bExists = true;
            }
        }

        // --------------------------------------------------------------------------

        if ($this->bExists) {
            if (empty($sLine)) {
                write_file($sLogPath, "\n", 'a');
            } else {
                write_file($sLogPath, 'INFO - ' . $oNow->format('Y-m-d H:i:s') . ' --> ' . trim($sLine) . "\n", 'a');
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the filename which is being written to
     *
     * @param string $sFile The file to write to
     */
    public function setFile($sFile = ''): self
    {
        //  Reset the log exists var so that line() checks again
        $this->bExists = null;

        // --------------------------------------------------------------------------

        if (!empty($sFile)) {
            $this->sFile = $sFile;
        } else {
            $oNow        = Factory::factory('DateTime');
            $this->sFile = 'log-' . $oNow->format('Y-m-d') . '.php';
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the active log file
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->sFile;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the log directory which is being written to
     *
     * @param string $sDir The directory to write to
     */
    public function setDir($sDir = ''): self
    {
        //  Reset the log exists var so that line() checks again
        $this->bExists = null;

        // --------------------------------------------------------------------------

        if (!empty($sDir) && substr($sDir, 0, 1) === DIRECTORY_SEPARATOR) {
            $this->sDir = $sDir;
        } elseif (!empty($sDir)) {
            $this->sDir = Config::get('LOG_DIR') . $sDir;
        } else {
            $this->sDir = Config::get('LOG_DIR');
        }

        $this->sDir = addTrailingSlash($this->sDir);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the active log directory
     *
     * @return string
     */
    public function getDir(): string
    {
        return $this->sDir;
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
