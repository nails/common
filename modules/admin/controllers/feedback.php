<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Feedback
*
* Docs:			-
*
* Created:		05/06/2011
* Modified:		05/06/2011
*
* Description:	-
* 
*/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

class Feedback extends Admin_Controller {
		
	/**
	 * Announces this module's details to anyone who asks.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function announce()
	{
		//	Configurations
		$d->priority		= 25;							//	Module's order in nav (unique).
		$d->name			= 'Feedback';					//	Display name.
		$d->funcs['index']	= 'View All Feedback';				//	Sub-nav function.
		$d->announce_to		= array();						//	Which groups can access this module.
		$d->searchable		= FALSE;						//	Is module searchable?
		
		//	Dynamic
		$d->base_url		= basename( __FILE__, '.php' );	//	For link generation.
		
		return $d;
	}
	
	
	// --------------------------------------------------------------------------
	
		
	/**
	 * Construct module
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function __construct()
	{
		parent::__construct();
		
		//	Load model
		$this->load->model( 'beta_feedback_model' );
	}
	
	
	// --------------------------------------------------------------------------
	
		
	/**
	 * List feedback
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function index()
	{
		$this->data['page']->admin_m = 'feedback';
		
		$this->data['feedback'] = $this->beta_feedback_model->get_all();
		
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'feedback/feedback',		$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Delete feedback
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function delete()
	{
		$this->data['page']->admin_m = 'feedback';
		
		$_id = $this->uri->segment(4, NULL);
		
		if ( $this->beta_feedback_model->delete( $_id ) ) :
		
			$this->data['success'] = 'Feedback successfully deleted!';
			
		else:
		
			$this->data['error'] = 'There was a problem deleting this feedback entry. Please try again.';
			
		endif;
		
		return $this->index();

	}	
}

/* End of file feedback.php */
/* Location: ./application/modules/admin/controllers/feedback.php */