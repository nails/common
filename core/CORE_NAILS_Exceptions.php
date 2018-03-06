<?php

/**
 * This class overrides some default CodeIgniter exception handling and provides
 * some additional methods.
 *
 * @package     Nails
 * @subpackage  common
 * @category    errors
 * @author      Nails Dev Team
 * @link
 */

use Nails\Common\Exception\NailsException;

class CORE_NAILS_Exceptions extends CI_Exceptions
{
    private $bErrorHasOccurred = false;
    private $aRecentErrors     = [];

    // --------------------------------------------------------------------------

    /**
     * Override the show_error method in order to throw an exception rather than display a screen.
     *
     * @param string  $sHeading      The error heading
     * @param string  $sMessage      The exception message (can be an array)
     * @param string  $sTemplate     The template to use
     * @param int     $iStatusCode   The exception number
     * @param boolean $bUseException Whether to use an exception
     *
     * @throws \Nails\Common\Exception\NailsException
     * @return void
     */
    public function show_error(
        $sHeading,
        $sMessage,
        $sTemplate = 'error_general',
        $iStatusCode = 500,
        $bUseException = true
    ) {
        if (is_array($sMessage)) {
            $sMessage = implode($sMessage, ' ');
        }

        if ($bUseException) {
            throw new NailsException($sMessage, $iStatusCode);
        } else {

            $sTemplate = APPPATH . 'errors/' . $sTemplate . '.php';
            if (!file_exists($sTemplate)) {
                _NAILS_ERROR($sMessage, $sHeading);
            } else {
                ob_start();
                include($sTemplate);
                $sBuffer = ob_get_contents();
                ob_end_clean();
                echo $sBuffer;
            }

            exit;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * PHP error handler
     *
     * Overriding the error handler to be a little more efficient/helpful.
     * When executed on a dev/staging environment we want the normal error reporting
     * but when executed on a production box we want errors to be logged to the DB and
     * any output muted. Sever errors should generate an exception in the CodeBase project
     *
     * @param  string  $sSeverity
     * @param  string  $sMessage
     * @param  string  $sFilePath
     * @param  integer $sLine
     *
     * @return string
     */
    public function show_php_error($sSeverity, $sMessage, $sFilePath, $sLine)
    {
        $this->bErrorHasOccurred = true;
        $this->aRecentErrors[]   = (object) [
            'severity' => $sSeverity,
            'message'  => $sMessage,
            'filepath' => $sFilePath,
            'line'     => $sLine,
        ];

        // --------------------------------------------------------------------------

        return parent::show_php_error($sSeverity, $sMessage, $sFilePath, $sLine);
    }

    // --------------------------------------------------------------------------

    /**
     * Reports whether any errors have occurred during processing
     * @return boolean
     */
    public function error_has_occurred()
    {
        return $this->bErrorHasOccurred;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of errors which have occurred
     * @return array
     */
    public function recent_errors()
    {
        return $this->aRecentErrors;
    }

    // --------------------------------------------------------------------------

    /**
     * Flushes recorded errors
     * @return void
     */
    public function clearErrors()
    {
        $this->bErrorHasOccurred = false;
        $this->aRecentErrors     = [];
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 404 page and halts script execution
     *
     * @param string $sPage     The URI which 404'd
     * @param bool   $bLogError Whether to log the error
     *
     * @return void
     */
    public function show_404($sPage = '', $bLogError = true)
    {
        $sHeading = '404 Page Not Found';
        $sMessage = 'The page you requested was not found.';

        if (empty($sPage)) {
            $sPage = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        }

        /**
         * By default we log this, but allow a dev to skip it. Additionally, skip
         * if it's a HEAD request.
         *
         * Reasoning: I often use HEAD requests to check the existence of a file
         * in JS before fetching it. I feel that these shouldn't be logged. A
         * direct GET/POST/etc request to a non-existent file is more  likely a
         * user following a dead link so these _should_ be logged.
         *
         * If you disagree, open up an issue and we'll work something out.
         */

        $sRequestMethod = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : '';

        if ($bLogError && $sRequestMethod != 'HEAD') {
            log_message('error', '404 Page Not Found --> ' . $sPage);
        }


        // --------------------------------------------------------------------------

        /**
         * If the route failed to resolve then there will be no instance of the CI_Controller,
         * and as such the app will have failed to startup completely - simulate a controller
         * being loaded if this is the case. Few of CodeIgniter's services will have been loaded
         * so we need to load them now, we'll also boot up a controller which does nothing so
         * that the app's constructor chain is fired.
         */
        if (!class_exists('CI_Controller')) {

            require_once BASEPATH . 'core/Controller.php';

            load_class('Output', 'core');
            load_class('Security', 'core');
            load_class('Input', 'core');
            load_class('Lang', 'core');

            new \Nails\Common\Controller\Nails404Controller();
        }

        // --------------------------------------------------------------------------

        defineConst('NAILS_IS_404', true);

        set_status_header(404);

        $sMessage = '<p>' . implode('</p><p>', (!is_array($sMessage)) ? [$sMessage] : $sMessage) . '</p>';

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }
        ob_start();
        include(APPPATH . 'errors/error_404.php');
        $sBuffer = ob_get_contents();
        ob_end_clean();
        echo $sBuffer;
        exit;
    }
}
