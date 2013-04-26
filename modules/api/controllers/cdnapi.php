<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		CDN API
 *
 * Description:	This controller handles CDN API methods
 * 
 **/

require_once '_api.php';

class Cdnapi extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Instant search specific constructor
	 * 
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Check this module is enabled in settings
		if ( ! module_is_enabled( 'cdn' ) ) :
		
			//	Cancel execution, module isn't enabled
			$this->_method_not_found( $this->uri->segment( 2 ) );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		
		$this->load->library( 'cdn' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function add_object_tag()
	{
		$_object_id	= $this->input->get( 'object_id' );
		$_tag_id	= $this->input->get( 'tag_id' );
		$_out		= array();
		
		$_added = $this->cdn->add_object_tag( $_object_id, $_tag_id );
		
		if ( $_added ) :
		
			//	Get new count for this tag
			$_out = array(
				'new_total'	=> $this->cdn->count_tag_objects( $_tag_id )
			);
		
		else :
		
			$_out = array(
				'status'	=> 400,
				'error'		=> implode( $this->cdn->errors() )
			);
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->_out( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function delete_object_tag()
	{
		$_object_id	= $this->input->get( 'object_id' );
		$_tag_id	= $this->input->get( 'tag_id' );
		$_out		= array();
		
		$_deleted = $this->cdn->delete_object_tag( $_object_id, $_tag_id );
		
		if ( $_deleted ) :
		
			//	Get new count for this tag
			$_out = array(
				'new_total'	=> $this->cdn->count_tag_objects( $_tag_id )
			);
		
		else :
		
			$_out = array(
				'status'	=> 400,
				'error'		=> implode( $this->cdn->errors() )
			);
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->_out( $_out );
	}
}

/* End of file cdn.php */
/* Location: ./application/modules/api/controllers/cdn.php */