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

			header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 401 Unauthorized' );
			die( '<h1>' . lang( 'unauthorised' ) . '</h1>' );

		endif;

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

		//	Set log details
		_LOG_FILE( 'cron/' . $log_dir . '/' . $log_file . '-' . date( 'Y-m-d' ) . '.php' );
		_LOG( 'Starting job [' . $this->task . ']...' );
		_LOG();
	}


	// --------------------------------------------------------------------------


	protected function _end( $log_message = NULL )
	{
		//	How'd we do?
		$_end		= microtime( TRUE ) * 10000;
		$_duration	= ( $_end - $this->start ) / 10000;

		// --------------------------------------------------------------------------

		_LOG();
		_LOG( 'Finished job [' . $this->task . ']' );
		_LOG( 'Job took ' . number_format( $_duration, 5 ) . ' seconds' );
		_LOG();
		_LOG( '----------------------------------------' );
		_LOG();

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