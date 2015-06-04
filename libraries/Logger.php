<?php

/**
 * Provides enhanced logging facilities
 * @todo: Deprecate this in favour of something like monolog
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

class Logger
{
    private $oLog;
    private $bIsCli;
    public $bMute;
    public $bDummy;

    // --------------------------------------------------------------------------

    /**
     * Construct the library
     */
    public function __construct()
    {
        //  Load helper
        get_instance()->load->helper('file');

        // --------------------------------------------------------------------------

        //  On the CLI?
        $this->bIsCli = get_instance()->input->is_cli_request();

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
     * @param  string $sLine The line to write
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

                $sFirstLine = '<?php exit(\'Unauthorised\');' . "\n\n";
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

                write_file($sLogPath, 'INFO - ' . date('Y-m-d H:i:s') . ' --> ' . trim($sLine) . "\n", 'a');
            }
        }

        // --------------------------------------------------------------------------

        //  If we're working on the command line then pump it out there too

        if ($this->bIsCli) {

            fwrite(STDOUT, $sLine . "\n");
        }

        // --------------------------------------------------------------------------

        //  If we're not on production and the request is not CLI then echo to the browser
        if (strtoupper(ENVIRONMENT) != 'PRODUCTION' && !$this->bIsCli && !$this->bMute) {

            @ob_start();
            echo $sLine . "<br />\n";
            @ob_flush();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set the filename which is being written to
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

            $this->oLog->file = 'log-' . date('Y-m-d') . '.php';
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set the log directory which is being written to
     * @param string $sDir The directory to write to
     */
    public function setDir($sDir = '')
    {
        //  Reset the log exists var so that line() checks again
        $this->oLog->exists = false;

        // --------------------------------------------------------------------------

        if (!empty($sDir)) {

            //  Absolute or relative?
            if (substr($sDir, 0, 1) === '/') {

                $this->oLog->dir = $sDir;

            } else {

                $this->oLog->dir = DEPLOY_LOG_DIR . $sDir;
            }

        } else {

            $this->oLog->dir = DEPLOY_LOG_DIR;
        }
    }
}
