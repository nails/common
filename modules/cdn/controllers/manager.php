<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Media [Manager]
 *
 * Description:	This controller handles managing media
 * 
 **/

/**
 * OVERLOADING NAILS'S AUTH MODULE
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

class NAILS_Manager extends NAILS_Controller
{
	/**
	 * Construct the class; set defaults
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
		
		//	Determine if browsing/uploading is permitted
		//	TODO Work out a way to do this nicely, maybe o a per group basis?
		
		$this->data['enabled'] = $this->user->is_superuser() ? TRUE : FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->load->helper( 'directory' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Render the media manager
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function browse()
	{
		//	Fetch all the files this user is allowed to see (i.e their own files)
		switch( $this->uri->segment( 4 ) ) :
		
			case 'image' :
			
				$this->data['type']			= 'image';
				$this->data['type_single']	= 'image';
				$this->data['type_plural']	= 'images';
			
			break;
			
			// --------------------------------------------------------------------------
			
			default :
			
				$this->data['enabled'] = FALSE;
			
			break;
		
		endswitch;
		
		// --------------------------------------------------------------------------
		
		//	Fetch files
		if ( $this->data['enabled'] ) :
		
			$this->data['files'] = directory_map( CDN_PATH . '/' . active_user( 'id' ) . '-' . $this->data['type'] );
			
			if ( $this->data['files'] === FALSE )
				$this->data['files'] = array();
			
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'manager/browse', $this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Upload a file to the user's media store
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function upload()
	{
		//	Test media type is supported
		switch( $this->uri->segment( 4 ) ) :
		
			case 'image' :
			
				$this->data['type']			= 'image';
				$this->data['type_single']	= 'Image';
			
			break;
			
			// --------------------------------------------------------------------------
			
			default :
			
				show_404();
			
			break;
		
		endswitch;
		
		// --------------------------------------------------------------------------
		
		//	User is authorised to upload?
		if ( ! $this->data['enabled'] ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . $this->data['type_single'] . ' uploads are not available right now.' );
			redirect( 'cdn/manager/browse/' . $this->data['type'] );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->library( 'CDN' );
		
		//	Create user's upload dir, if it's there already this will gracefully fail
		if ( ! $this->cdn->create_bucket( active_user( 'id' ) . '-' . $this->data['type'] ) ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I couldn\'t to create your personal upload folder.' );
			redirect( 'cdn/manager/browse/' . $this->data['type'] );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Attempt upload
		//	TODO define the appropriate configs: certain file types and no randomly
		//	generate names - let the user's choose their own name
		
		$_upload = $this->cdn->upload( 'userfile', active_user( 'id' ) . '-' . $this->data['type'] );
		
		if ( $_upload ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> ' . $this->data['type_single'] . ' uploaded successfully!' );
			redirect( 'cdn/manager/browse/' . $this->data['type'] );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . implode( $this->cdn->errors() ) );
			redirect( 'cdn/manager/browse/' . $this->data['type'] );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	public function delete()
	{
		//	Test media type is supported
		switch( $this->uri->segment( 4 ) ) :
		
			case 'image' :
			
				$this->data['type']			= 'image';
				$this->data['type_single']	= 'Image';
			
			break;
			
			// --------------------------------------------------------------------------
			
			default :
			
				show_404();
			
			break;
		
		endswitch;
		
		// --------------------------------------------------------------------------
		
		//	User is authorised to delete?
		if ( ! $this->data['enabled'] ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . $this->data['type_single'] . ' deletions are not available right now.' );
			redirect( 'cdn/manager/browse/' . $this->data['type'] );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->library( 'CDN' );
		
		//	Attempt upload
		//	TODO define the appropriate configs: certain file types and no randomly
		//	generate names - let the user's choose their own name
		
		$_delete = $this->cdn->delete( $this->uri->segment( 5 ), active_user( 'id' ) . '-' . $this->data['type'] );
		
		if ( $_delete ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> ' . $this->data['type_single'] . ' deleted successfully!' );
			redirect( 'cdn/manager/browse/' . $this->data['type'] );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . implode( $this->cdn->errors() ) );
			redirect( 'cdn/manager/browse/' . $this->data['type'] );
		
		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S AUTH MODULE
 * 
 * The following block of code makes it simple to extend one of the core auth
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 * 
 * Here's how it works:
 * 
 * CodeIgniter  instanciate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 * 
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
 * 
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 * 
 **/
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION' ) ) :

	class Manager extends NAILS_Manager
	{
	}

endif;

/* End of file manager.php */
/* Location: ./application/modules/cdn/controllers/manager.php */