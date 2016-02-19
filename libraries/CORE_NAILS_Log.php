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

use Nails\Factory;
use Nails\Environment;

class CORE_NAILS_Log extends CI_Log
{
    /**
     * Construct the library
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        /**
         * Ignore whatever the parent constructor says about whether logging is enabled
         * or not. We'll work it out below.
         */

        $this->_enabled = null;
    }

    // --------------------------------------------------------------------------

    /**
     * Wrties to the system log
     * @param  string  $level     The log message's level
     * @param  string  $msg       The message to write to the log file
     * @param  boolean $php_error Whether or not this is a PHP error
     * @return boolean
     */
    public function write_log($level = 'error', $msg = '', $php_error = false)
    {
        /**
         * Ensure this is set correctly. Would use the constructor, however that is
         * called before the pre_system hook (as the constructor of the hook class
         * calls log_message() which in turn constructs this class. The docs LIE when
         * they say only benchmark and hooks class are loaded)
         */

        if (defined('DEPLOY_LOG_DIR')) {

            $this->_log_path = DEPLOY_LOG_DIR;

            //  If we haven't already, check to see if DEPLOY_LOG_DIR is writeable
            if (is_null($this->_enabled)) {

                if (is_writeable($this->_log_path)) {

                    //  Writeable!
                    $this->_enabled = true;

                } else {

                    //  Not writeable, disable logging and kick up a fuss
                    $this->_enabled = false;

                    //  Send developer mail, but only once
                    if (!defined('NAILS_LOG_ERROR_REPORTED')) {

                        if (isset($_SERVER['REQUEST_URI'])) {

                            $uri = $_SERVER['REQUEST_URI'];

                        } else {

                            //  Most likely on the CLI
                            if (isset($_SERVER['argv'])) {

                                $uri = 'CLI: ' . implode(' ', $_SERVER['argv']);

                            } else {

                                $uri = 'Unable to determine URI';
                            }
                        }

                        $msg     = strtoupper($level).' '.((strtoupper($level) == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";
                        $appname = defined('APP_NAME') ? APP_NAME : '[Could not determine app name]';

                        $subject  = 'Log folders are not writeable on ' . $appname;
                        $message  = 'I just tried to write to the log folder for ' . $appname . ' and found them not to be writeable.' . "\n";
                        $message .= '' . "\n";
                        $message .= 'Get this fixed ASAP - I\'ll bug you every time this happens.' . "\n";
                        $message .= '' . "\n";
                        $message .= 'FYI, the entry was:' . "\n";
                        $message .= '' . "\n";
                        $message .= $msg . "\n";
                        $message .= '' . "\n";
                        $message .= 'The calling URI was:' . "\n";
                        $message .= '' . "\n";
                        $message .= $uri . "\n";
                        $message .= '' . "\n";
                        $message .= 'The path was:' . "\n";
                        $message .= '' . "\n";
                        $message .= $this->_log_path . "\n";
                        $message .= '' . "\n";
                        $message .= 'PHP SAPI Name:' . "\n";
                        $message .= '' . "\n";
                        $message .= php_sapi_name() . "\n";
                        $message .= '' . "\n";
                        $message .= 'PHP Debug Backtrace:' . "\n";
                        $message .= '' . "\n";
                        $message .= json_encode(debug_backtrace()) . "\n";

                        //  Set from details
                        try {

                            $oEmailer = Factory::service('Emailer');

                            $fromName = $oEmailer->getFromName();
                            $fromEmail = $oEmailer->getFromEmail();


                        } catch (\Exception $e) {

                            $fromName  = 'Log Error Reporter';
                            $fromEmail = 'root@' . gethostname();
                        }

                        $to      = Environment::not('PRODUCTION') && defined('EMAIL_OVERRIDE') && EMAIL_OVERRIDE ? EMAIL_OVERRIDE : APP_DEVELOPER_EMAIL;
                        $headers = 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n" .
                                'X-Mailer: PHP/' . phpversion()  . "\r\n" .
                                'X-Priority: 1 (Highest)' . "\r\n" .
                                'X-Mailer: X-MSMail-Priority: High/' . "\r\n" .
                                'Importance: High';

                        if (!empty($to)) {

                            @mail($to, '!!' . $subject, $message, $headers);
                        }

                        define('NAILS_LOG_ERROR_REPORTED', true);
                    }
                }
            }

        } else {

            //  Don't bother writing as we don't know where to write.
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
