<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Cron Controller
 *
 * Description:	Executes common cron functionality
 *
 **/

class NAILS_Cron_Controller extends NAILS_Controller
{
	protected $start;
	protected $task;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Load language file
		$this->lang->load( 'cron', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------

		//	Command line only
		if ( ENVIRONMENT == 'production' && ! $this->input->is_cli_request() ) :

			header( 'HTTP/1.1 401 Unauthorized' );
			die( '<h1>' . lang( 'unauthorised' ) . '</h1>' );

		endif;

		// --------------------------------------------------------------------------

		$this->load->library( 'logger' );

		// --------------------------------------------------------------------------

		//	E_ALL E_STRICT error reporting, for as error free code as possible
		error_reporting( E_ALL|E_STRICT );
	}


	// --------------------------------------------------------------------------


	protected function _start( $log_dir, $log_file, $task )
	{
		//	Tick tock tick...
		$this->start	= microtime( TRUE ) * 10000;
		$this->task		= $task;

		// --------------------------------------------------------------------------

		//	Set logger details
		$this->logger->log_dir( 'cron/' . $log_dir . '/' );
		$this->logger->log_file( $log_file . '-' . date( 'Y-m-d' ) . '.php' );
		$this->logger->line( 'Starting job [' . $this->task . ']...' );
		$this->logger->line();
	}


	// --------------------------------------------------------------------------


	protected function _end( $log_message = NULL )
	{
		//	How'd we do?
		$_end		= microtime( TRUE ) * 10000;
		$_duration	= ( $_end - $this->start ) / 10000;

		// --------------------------------------------------------------------------

		$this->logger->line();
		$this->logger->line( 'Finished job [' . $this->task . ']' );
		$this->logger->line( 'Job took ' . number_format( $_duration, 5 ) . ' seconds' );
		$this->logger->line();
		$this->logger->line( '----------------------------------------' );
		$this->logger->line();

		// --------------------------------------------------------------------------

		//	Write this to the DB log
		$_data				= array();
		$_data['task']		= $this->task;
		$_data['duration']	= $_duration;
		$_data['message']	= $log_message;

		$this->db->set( $_data );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->insert( NAILS_DB_PREFIX . 'log_cron' );
	}
}


/* End of file _cron.php */
/* Location: ./application/modules/cron/controllers/_cron.php */