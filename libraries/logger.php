<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Logger
*
*
* Docs:			-
*
* Created:		26/05/2011
* Modified:		26/05/2011
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
	public function __construct( $log_file = NULL, $log_dir = './application/logs/' )
	{
		$this->ci =& get_instance();
		
		$this->ci->load->helper( 'file' );
		
		$this->log->dir		= $log_dir;
		$this->log->file	= ( $log_file ) ? $log_file : date( 'Y-m-d' ) . '.php';
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
	public function line( $line )
	{
		if ( ! file_exists( $this->log->dir . $this->log->file ) )
			write_file( $this->log->dir . $this->log->file, '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\'); ?>'."\n\n");
			
		if ( empty( $line ) ) :
			
			write_file( $this->log->dir . $this->log->file, "\n", 'a' );
			
		else :
			
			write_file( $this->log->dir . $this->log->file, date('Y-m-d H:i:s').' -- ' . $line . "\n", 'a' );
			
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
		$this->log->dir = ( $log_dir ) ? $log_dir : './application/logs/';
	}
}

/* End of file logger.php */
/* Location: ./application/libraries/logger.php */