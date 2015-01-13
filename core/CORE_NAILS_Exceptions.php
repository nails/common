<?php

class CORE_NAILS_Exceptions extends CI_Exceptions {

    private $error_has_occured = false;
    private $recent_errors     = array();

    // --------------------------------------------------------------------------

    /**
     * PHP error handler
     *
     * Overriding the error handler to be a little more efficient/helpful.
     * When executed on a dev/staging environment we want the normal error reporting
     * but when executed on a production box we want errors to be logged to the DB and
     * any output muted. Sever errors should generate an exception in the CodeBase project
     *
     * @param   string  a message to pass to the view, if any
     * @param   boolean whether to log the error or not
     * @return  void
     */
    function show_php_error($severity, $message, $filepath, $line)
    {
        $_temp           = new stdClass();
        $_temp->severity = $severity;
        $_temp->message  = $message;
        $_temp->filepath = $filepath;
        $_temp->line     = $line;

        // --------------------------------------------------------------------------

        $this->error_has_occurred = true;
        $this->recent_errors[]    = $_temp;

        // --------------------------------------------------------------------------

        unset($_temp);

        // --------------------------------------------------------------------------

        return parent::show_php_error($severity, $message, $filepath, $line);
    }

    // --------------------------------------------------------------------------

    /**
     * Reports whether any errors have occurred during processing
     * @return boolean
     */
    public function error_has_occurred()
    {
        return $this->error_has_occurred;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of errors which ahve occurred
     * @return array
     */
    public function recent_errors()
    {
        return $this->recent_errors;
    }

    // --------------------------------------------------------------------------

    /**
     * Flushes recorded errors
     * @return void
     */
    public function clear_errors()
    {
        $this->error_has_occurred = false;
        $this->recent_errors      = array();
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 404 page and halts script execution
     * @param  string  $page     The URI which 404'd
     * @param  boolean $logError Whether or not to log the 404
     * @return void
     */
    public function show_404($page = '', $logError = true)
    {
        $heading = "404 Page Not Found";
        $message = "The page you requested was not found.";

        if (empty($page)) {

            $page = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        }

        // By default we log this, but allow a dev to skip it
        if ($logError)
        {
            log_message('error', '404 Page Not Found --> ' . $page);
        }

        if (!defined('NAILS_IS_404')) {

            define('NAILS_IS_404', true);
        }

        echo $this->show_error($heading, $message, 'error_404', 404);
        exit;
    }
}
