<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		System Controller
 * 
 * Description:	Executes common system functionality
 * 
 **/

class NAILS_System_Controller extends NAILS_Controller
{
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
		$this->lang->load( 'system', RENDER_LANG_SLUG );
	}


	// --------------------------------------------------------------------------


	protected function _validate_token()
	{
		if ( ! $this->user->is_superuser() && $this->input->get( 'token' ) ) :

			//	Validate token
			$_token	= $this->input->get( 'token' );
			$_guid	= $this->input->get( 'guid' );
			$_time	= $this->input->get( 'time' );
			$_ip	= $this->input->ip_address();

			$_check = md5( $_guid . APP_PRIVATE_KEY . DEPLOY_PRIVATE_KEY . $_ip . $_time  );

			//	Check tokens match and that the difference since $_time is wihtin reason (4 hours)
			if ( $_token != $_check || ( time() - $_time ) > 144400 ) :

				show_404();

			endif;

		endif;
	}
}


/* End of file _system.php */
/* Location: ./application/modules/system/controllers/_system.php */