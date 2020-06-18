<?php

/**
 * Alters CI log functionality
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\CodeIgniter\Libraries;

use Nails\Common\Exception\NailsException;
use Nails\Config;
use Nails\Factory;
use Nails\Environment;
use CI_Log;

class Log extends CI_Log
{
    /**
     * Construct the library
     */
    public function __construct()
    {
        parent::__construct();

        /**
         * Ignore whatever the parent constructor says about whether logging is enabled
         * or not. We'll work it out below.
         */

        $this->_enabled = null;
    }

    // --------------------------------------------------------------------------

    /**
     * Wrties to the system log
     *
     * @param string $level     The log message's level
     * @param string $msg       The message to write to the log file
     * @param bool   $php_error Whether or not this is a PHP error
     *
     * @return bool
     */
    public function write_log($level = 'error', $msg = '', $php_error = false)
    {
        /**
         * Ensure this is set correctly. Would use the constructor, however that is
         * called before the pre_system hook (as the constructor of the hook class
         * calls log_message() which in turn constructs this class. The docs LIE when
         * they say only benchmark and hooks class are loaded)
         */

        if (Config::get('LOG_DIR')) {

            $this->_log_path = Config::get('LOG_DIR');

            //  If we haven't already, check to see if Config::get('LOG_DIR') is writable
            if (is_null($this->_enabled)) {
                if (is_writable($this->_log_path)) {
                    $this->_enabled = true;

                } else {
                    throw new NailsException(
                        'Unable to write to logs'
                    );
                }
            }

        } else {
            return false;
        }

        // --------------------------------------------------------------------------

        //  Test Log folder, but only if the error level is to be captured
        $level = strtoupper($level);

        if (!isset($this->_levels[$level]) || ($this->_levels[$level] > $this->_threshold)) {
            return false;
        }

        return parent::write_log($level, $msg, $php_error);
    }
}
