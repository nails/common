<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Debugger
*
* Docs:			-
*
* Created:		12/12/2011
* Modified:		12/12/2011
*
* Description:	The debugger is used to analyse the state of the system at
*				any particular moment of the execution.
* 
*/

class Debug {

	private $moments;
	private $ci;
	private $running;
	
	/**
	* Initialise the debugger
	* 
	* @access	public
	* @return	void
	* @author	Pablo
	* 
	**/
	public function __construct()
	{
		//	Is the debugger running?
		if ( defined( 'DEBUG_MODE' ) && DEBUG_MODE ) :
		
			$this->running = TRUE;
			$this->ci = & get_instance();
			
		else :
		
			$this->running = FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	* Mark a debug moment
	* 
	* @access	public
	* @return	void
	* @author	Pablo
	* 
	**/
	public function mark()
	{
		//	Debugging?
		if ( ! $this->running ) return;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	* Outputs the HTML for the debug frame
	* 
	* @access	public
	* @return	void
	* @author	Pablo
	* 
	**/
	public function output_html()
	{
		//	Debugging?
		if ( ! $this->running ) return;
		
		//	Gather debug data
		$debug_data = NULL;
		
		//	Return the view
		return $this->ci->load->view( 'system/debug/footer', $debug_data, TRUE );
	}
}

/* End of file debug.php */
/* Location: ./application/modules/system/libraries/debug.php */