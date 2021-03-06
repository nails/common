<?php
/**
 * Provides enhanced logging facilities
 *
 * @todo        - Deprecate this in favour of something like monolog
 *
 * @package     Nails
 * @subpackage  common
 * @category    Service
 * @author      Nails Dev Team
 * @link        https://docs.nailsapp.co.uk/core-services/logger
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\FactoryException;
use Nails\Config;
use Nails\Factory;

/**
 * Class Logger
 *
 * @package Nails\Common\Service
 */
class Logger
{
    /** @var \Nails\Common\Factory\Logger */
    protected $oLogger;

    // --------------------------------------------------------------------------

    /**
     * Logger constructor.
     *
     * @throws FactoryException
     */
    public function __construct()
    {
        $this->oLogger = Factory::factory('Logger');
    }

    // --------------------------------------------------------------------------

    /**
     * Writes a line to the app log
     *
     * @param string $sLine The line to write to the log
     * @param string $sType The type of log entry
     *
     * @return $this
     * @throws FactoryException
     */
    public function line($sLine = '', string $sType = null): self
    {
        $this->oLogger->line($sLine);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Logs a message of type \Nails\Common\Factory\Logger::TYPE_DEBUG
     *
     * @param string $sLine The line to write
     *
     * @return $this
     */
    public function debug($sLine = ''): self
    {
        $this->oLogger->debug($sLine);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Logs a message of type \Nails\Common\Factory\Logger::TYPE_INFO
     *
     * @param string $sLine The line to write
     *
     * @return $this
     */
    public function info($sLine = ''): self
    {
        $this->oLogger->info($sLine);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Logs a message of type \Nails\Common\Factory\Logger::TYPE_WARNING
     *
     * @param string $sLine The line to write
     *
     * @return $this
     */
    public function warning($sLine = ''): self
    {
        $this->oLogger->warning($sLine);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Logs a message of type \Nails\Common\Factory\Logger::TYPE_ERROR
     *
     * @param string $sLine The line to write
     *
     * @return $this
     */
    public function error($sLine = ''): self
    {
        $this->oLogger->error($sLine);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current log file
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->oLogger->getFile();
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current log directory
     *
     * @return string
     */
    public function getDir(): string
    {
        return $this->oLogger->getDir();
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a stream to the current log file
     *
     * @return bool|resource
     */
    public function getStream()
    {
        return $this->oLogger->getStream();
    }

    // --------------------------------------------------------------------------

    /**
     * Mute or unmute the log
     *
     * @param bool $sMute Whether to mute the log or not
     *
     * @return $this
     */
    public function mute(bool $sMute = true): self
    {
        $this->oLogger->bMute = $sMute;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Enable or disable dummy mode
     *
     * @param bool $sEnabled Whether dummy mode is enabled
     *
     * @return $this
     */
    public function dummy($sEnabled = true): self
    {
        $this->oLogger->bDummy = $sEnabled;
        return $this;
    }
}
