<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Logger
 *
 * Description:	A Library for logging data to the server
 * 
 */

class Logger {
	
	private $ci;
	private $log;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct( $log_file = NULL, $log_dir = NULL )
	{
		$this->ci =& get_instance();
		
		$this->ci->load->helper( 'file' );
		
		$this->log->dir	= $this->log_dir( $log_dir );
		$this->log->file	= ( $log_file ) ? $log_file : date( 'Y-m-d' ) . '.php';
		$this->log->exists	= FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Writes a line to the log
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	public function line( $line = '' )
	{
		//	If the log file doesn't exist (or we haven't checked already), attempt to create it
		if ( ! $this->log->exists ) :
		
			if ( ! file_exists( $this->log->dir . $this->log->file ) ) :
			
				if ( write_file( $this->log->dir . $this->log->file, '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\'); ?>'."\n\n" ) ) :
				
					$this->log->exists = TRUE;
					
				else :
				
					$this->log->exists = FALSE;
				
				endif;
				
			else :
			
				$this->log->exists = TRUE;
			
			endif;
				
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $this->log->exists ) :
		
			if ( empty( $line ) ) :
				
				write_file( $this->log->dir . $this->log->file, "\n", 'a' );
				
			else :
				
				write_file( $this->log->dir . $this->log->file, date('Y-m-d H:i:s').' -- ' . trim( $line ) . "\n", 'a' );
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If we're working on the command line then pump it out there too
		
		if ( $this->ci->input->is_cli_request() ) :
		
			fwrite( STDOUT, $line . "\n" );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If we're not on production and the request is not CLI then echo to the browser
		if ( ENVIRONMENT != 'production' && ! $this->ci->input->is_cli_request() ) :
		
			echo $line . "<br />\n";
			@ob_flush();
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Change the file which is being logged to
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	public function log_file( $log_file = NULL )
	{
		//	Reset the log exists var so that line() checks again
		$this->log->exists = FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->log->file = ( $log_file ) ? $log_file : date( 'Y-m-d' ) . '.php';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Change the directory which is being logged to
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	public function log_dir( $log_dir = NULL )
	{
		//	Reset the log exists var so that line() checks again
		$this->log->exists = FALSE;
		
		// --------------------------------------------------------------------------
		
		if ( $log_dir ) :

			$this->log->dir = FCPATH . APPPATH . 'logs/' . $log_dir;
			$this->log->dir .= substr( $this->log->dir, -1 ) != '/' ? '/' : '';

		else :

			$this->log->dir = FCPATH . APPPATH . 'logs/';

		endif;
	}
}

/* End of file logger.php */
/* Location: ./application/libraries/logger.php */