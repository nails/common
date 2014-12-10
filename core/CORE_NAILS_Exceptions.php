<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Exceptions extends CI_Exceptions {

	private $error_has_occured = FALSE;
	private $recent_errors = array();


	// --------------------------------------------------------------------------

	/**
	 * PHP error handler
	 *
	 * Overriding the error handler to be a little more efficient/helpful.
	 * When executed on a dev/staging environment we want the normal error reporting
	 * but when executed on a production box we want errors to be logged to the DB and
	 * any output muted. Sever errors should generate an exception in the CodeBase project
	 *
	 * @access	private
	 * @param	string	a message to pass to the view, if any
	 * @param	boolean	whether to log the error or not
	 * @return	void
	 */
	function show_php_error($severity, $message, $filepath, $line)
	{
		$_temp	= new stdClass();
		$_temp->severity	= $severity;
		$_temp->message		= $message;
		$_temp->filepath	= $filepath;
		$_temp->line		= $line;

		// --------------------------------------------------------------------------

		$this->error_has_occurred	= TRUE;
		$this->recent_errors[]		= $_temp;

		// --------------------------------------------------------------------------

		unset( $_temp );

		// --------------------------------------------------------------------------

		return parent::show_php_error( $severity, $message, $filepath, $line );
	}


	// --------------------------------------------------------------------------


	public function error_has_occurred()
	{
		return $this->error_has_occurred;
	}


	// --------------------------------------------------------------------------


	public function recent_errors()
	{
		return $this->recent_errors;
	}


	// --------------------------------------------------------------------------


	public function clear_errors()
	{
		$this->error_has_occurred	= FALSE;
		$this->recent_errors		= array();
	}


	// --------------------------------------------------------------------------


	public function show_404( $page = '', $log_error = TRUE )
	{
		$heading = "404 Page Not Found";
		$message = "The page you requested was not found.";

		if ( empty( $page ) ) :

			$page = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';

		endif;

		// By default we log this, but allow a dev to skip it
		if ( $log_error )
		{
			log_message( 'error', '404 Page Not Found --> ' . $page );
		}

		if ( ! defined( 'NAILS_IS_404' ) ) :

			define( 'NAILS_IS_404', TRUE );

		endif;

		echo $this->show_error( $heading, $message, 'error_404', 404 );
		exit;
	}

	// --------------------------------------------------------------------------

	public function log_exception($severity, $message, $filepath, $line)
	{
		parent::log_exception($severity, $message, $filepath, $line);

		// --------------------------------------------------------------------------

		//	Do we need to tell anybody else about this error?
		$levels = array(
					E_ERROR				=> 'E_ERROR',
					E_WARNING			=> 'E_WARNING',
					E_PARSE				=> 'E_PARSE',
					E_NOTICE			=> 'E_NOTICE',
					E_CORE_ERROR		=> 'E_CORE_ERROR',
					E_CORE_WARNING		=> 'E_CORE_WARNING',
					E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
					E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
					E_USER_ERROR		=> 'E_USER_ERROR',
					E_USER_WARNING		=> 'E_USER_WARNING',
					E_USER_NOTICE		=> 'E_USER_NOTICE',
					E_STRICT			=> 'E_STRICT'
				);


		$level   = !isset($levels[$severity]) ? $severity : $levels[$severity];
		$message = $level . ': ' . $message . ' in ' . $filepath . ' on ' . $line;

		if (defined('APP_ROLLBAR_ACCESS_TOKEN')) {

			Rollbar::report_message($message, $level);
		}
	}
}

/* End of file CORE_NAILS_Exceptions.php */
/* Location: ./system/application/core/CORE_NAILS_Exceptions.php */