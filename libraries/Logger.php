<?php

/**
 * Provides enhanced logging facilities
 * @todo : Deprecate this in favour of something like monolog
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

class Logger
{
    private $log;
    private $isCli;
    public $mute_output;
    public $dummy_mode;

    // --------------------------------------------------------------------------

    /**
     * Constructor
     **/
    public function __construct()
    {
        //  Load helper
        get_instance()->load->helper('file');

        // --------------------------------------------------------------------------

        //  On the CLI?
        $this->isCli = get_instance()->input->is_cli_request();

        // --------------------------------------------------------------------------

        //  Define defaults
        $this->log         = new stdClass();
        $this->log->exists = false;
        $this->log->file   = DEPLOY_LOG_DIR .  'log-' . date('Y-m-d') . '.php';
        $this->mute_output = false;
        $this->dummy_mode  = false;
    }

    // --------------------------------------------------------------------------

    /**
     * Writes a line to the log
     * @param   string
     * @return  void
     **/
    public function line($line = '')
    {
        //  Is dummy mode enabled? If it is then don't do anything.
        if ($this->dummy_mode) {

            return true;
        }

        // --------------------------------------------------------------------------

        //  If the log file doesn't exist (or we haven't checked already), attempt to create it
        if (!$this->log->exists) {

            if (!file_exists($this->log->file)) {

                //  Check directory is there
                $dir = dirname($this->log->file);

                if (!is_dir($dir)) {

                    //  Create structure
                    mkdir($dir, 0750, true);
                }

                // --------------------------------------------------------------------------

                $firstLine = '<?php if !defined(\'BASEPATH\') exit(\'No direct script access allowed\'); ?>' . "\n\n";
                if (write_file($this->log->file, $firstLine)) {

                    $this->log->exists = true;

                } else {

                    $this->log->exists = false;
                }

            } else {

                $this->log->exists = true;
            }
        }

        // --------------------------------------------------------------------------

        if ($this->log->exists) {

            if (empty($line)) {

                write_file($this->log->file, "\n", 'a');

            } else {

                write_file($this->log->file, 'INFO - ' . date('Y-m-d H:i:s') . ' --> ' . trim($line) . "\n", 'a');
            }
        }

        // --------------------------------------------------------------------------

        //  If we're working on the command line then pump it out there too

        if ($this->isCli) {

            fwrite(STDOUT, $line . "\n");
        }

        // --------------------------------------------------------------------------

        //  If we're not on production and the request is not CLI then echo to the browser
        if (strtoupper(ENVIRONMENT) != 'PRODUCTION' && !$this->isCli && !$this->mute_output) {

            @ob_start();
            echo $line . "<br />\n";
            @ob_flush();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Change the file which is being logged to
     * @param   string
     * @return  void
     **/
    public function set_file($file = null)
    {
        //  Reset the log exists var so that line() checks again
        $this->log->exists = false;

        // --------------------------------------------------------------------------

        $this->log->file = $file ? FCPATH . APPPATH . 'logs/' . $file : FCPATH . APPPATH . 'logs/' .  date('Y-m-d') . '.php';
    }
}
