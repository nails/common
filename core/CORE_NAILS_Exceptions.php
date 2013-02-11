<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Exceptions extends CI_Exceptions {
	
	private $error_has_occured = FALSE;
	private $recent_errors = array();
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * 404 Page Not Found Handler
	 * 
	 * Modded to use the template 404 page rather than the default one. Bit Messy, but works.
	 *
	 * @access	private
	 * @param	string	a message to pass to the view, if any
	 * @param	boolean	whether to log the error or not
	 * @return	void
	 */
	public function show_404( $use_page = '', $log_error = TRUE )
	{
		$_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		// By default we log this, but allow a dev to skip it
		if ( $log_error && ! $_ci->input->is_cli_request() )
			log_message( 'error', '404 Page Not Found --> ' . $use_page . ' --> ' . $_SERVER['REQUEST_URI'] );
		
		// --------------------------------------------------------------------------
		
		//	If running on the command line just return a string
		if ( $_ci->input->is_cli_request() ) :
		
			$_ci->output->set_output( "\n404 Page Not Found\n\n" );
			echo $_ci->output->get_output();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set the correct header
		$_ci->output->set_header( 'HTTP/1.1 404 Not Found' );
		
		// --------------------------------------------------------------------------
		
		//	Pull in some data which is required in the headers
		$this->data['title']		=  $_ci->config->item( 'title' );
		$this->data['description']	=  $_ci->config->item( 'description' );
		$this->data['keywords']		=  $_ci->config->item( 'keywords' );
		
		// --------------------------------------------------------------------------
		
		//	Useful to have a reference to the user object too
		$this->data['user']			=& get_userobject();
		
		// --------------------------------------------------------------------------
		
		//	If $_data['user'] === FALSE then include the user_model library and instanciate
		//	it as it clearly hasn't already been done
		
		if ( $this->data['user'] === FALSE ) :
		
			//	Load up the user model and set the constant which NAIL_Controller would set
			$_ci->load->model( 'user_model' );
			$this->data['user']	=& $_ci->user_model;
			define( 'IA_USR_OBJ', 'user_model' );
			
			// --------------------------------------------------------------------------
			
			//	Assets
			require_once( FCPATH . APPPATH . 'core/IA_Controller_Assets.php' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Finally, a little heads up to the views that this is a 404
		$this->data['is_404'] = TRUE;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$_ci->load->view( 'structure/header',	$this->data );
		$_ci->load->view( 'system/404',			$this->data );
		$_ci->load->view( 'structure/footer',	$this->data );
		
		// --------------------------------------------------------------------------
		
		//	Send the output to the browser
		echo $_ci->output->get_output();
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