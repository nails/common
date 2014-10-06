<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Logger
 *
 * Description:	A Library for logging data to the server
 *
 */

class Logger
{
	private $_log;
	private $_is_cli;
	public $mute_output;
	public $dummy_mode;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function __construct()
	{
		//	Load helper
		get_instance()->load->helper( 'file' );

		// --------------------------------------------------------------------------

		//	On the CLI?
		$this->_is_cli = get_instance()->input->is_cli_request();

		// --------------------------------------------------------------------------

		//	Define defaults
		$this->_log			= new stdClass();
		$this->_log->exists	= FALSE;
		$this->_log->file	= DEPLOY_LOG_DIR .  'log-' . date( 'Y-m-d' ) . '.php';
		$this->mute_output	= FALSE;
		$this->dummy_mode	= FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Writes a line to the log
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 **/
	public function line( $line = '' )
	{
		//	Is dummy mode enabled? If it is then don't do anything.
		if ( $this->dummy_mode ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		//	If the log file doesn't exist (or we haven't checked already), attempt to create it
		if ( ! $this->_log->exists ) :

			if ( ! file_exists( $this->_log->file ) ) :

				//	Check directory is there
				$_dir = dirname( $this->_log->file );

				if ( ! is_dir( $_dir ) ) :

					//	Create structure
					mkdir( $_dir, 0750, TRUE );

				endif;

				// --------------------------------------------------------------------------

				if ( write_file( $this->_log->file, '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\'); ?>'."\n\n" ) ) :

					$this->_log->exists = TRUE;

				else :

					$this->_log->exists = FALSE;

				endif;

			else :

				$this->_log->exists = TRUE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->_log->exists ) :

			if ( empty( $line ) ) :

				write_file( $this->_log->file, "\n", 'a' );

			else :

				write_file( $this->_log->file, 'INFO - ' . date( 'Y-m-d H:i:s' ) . ' --> ' . trim( $line ) . "\n", 'a' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	If we're working on the command line then pump it out there too

		if ( $this->_is_cli ) :

			fwrite( STDOUT, $line . "\n" );

		endif;

		// --------------------------------------------------------------------------

		//	If we're not on production and the request is not CLI then echo to the browser
		if ( strtoupper( ENVIRONMENT ) != 'PRODUCTION' && ! $this->_is_cli && ! $this->mute_output ) :

			@ob_start();
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
	 **/
	public function set_file( $file = NULL )
	{
		//	Reset the log exists var so that line() checks again
		$this->_log->exists = FALSE;

		// --------------------------------------------------------------------------

		$this->_log->file = $file ? FCPATH . APPPATH . 'logs/' . $file : FCPATH . APPPATH . 'logs/' .  date( 'Y-m-d' ) . '.php';
	}
}

/* End of file logger.php */
/* Location: ./application/libraries/logger.php */