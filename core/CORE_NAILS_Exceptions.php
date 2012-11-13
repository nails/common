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
}

/* End of file CORE_NAILS_Exceptions.php */
/* Location: ./system/application/core/CORE_NAILS_Exceptions.php */