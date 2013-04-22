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

//	Include _cdn_local.php; executes common functionality
require_once '_cdn_local.php';

class NAILS_Manager extends NAILS_CDN_Controller
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
		
		//	Module enabled?
		if ( ! module_is_enabled( 'cdn' ) ) :
		
			show_404();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Determine if browsing/uploading is permitted
		if ( $this->user->is_logged_in() ) :
		
			$this->data['enabled'] = $this->user->is_superuser() ? TRUE : FALSE;
		
		endif;
		
		if ( $this->data['enabled'] ) :
		
			//	Supported type?
			switch( $this->uri->segment( 4 ) ) :
			
				case 'image' :
				
					$this->data['type']			= 'image';
					$this->data['type_single']	= 'image';
					$this->data['type_plural']	= 'images';
				
				break;
				
				// --------------------------------------------------------------------------
				
				case 'file' :
				
					$this->data['type']			= 'file';
					$this->data['type_single']	= 'file';
					$this->data['type_plural']	= 'files';
				
				break;
				
				// --------------------------------------------------------------------------
				
				default :
				
					$this->data['enabled'] = FALSE;
				
				break;
			
			endswitch;
			
			// --------------------------------------------------------------------------
			
			//	Define the directory, if a bucket has been specified use that, if not
			//	then use the user's uplaod directory
			
			if ( $this->input->get( 'bucket' ) && $this->input->get( 'hash' ) ) :
			
				//	Decrypt the bucket and cross reference with the hash. Doing this so
				//	That users can't casually specify a bucket and upload willy nilly.
				
				$_bucket	= $this->input->get( 'bucket' );
				$_hash		= $this->input->get( 'hash' );
				
				$_decrypted	= $this->encrypt->decode( $_bucket, APP_PRIVATE_KEY );
				
				if ( $_decrypted ) :
				
					$_bucket = explode( '|', $_decrypted );
					
					if ( $_bucket[0] && isset( $_bucket[1] ) ) :
					
						//	Bucket and nonce set, cross-check
						if ( md5( $_bucket[0] . '|' . $_bucket[1] . '|' . APP_PRIVATE_KEY ) === $_hash ) :
						
							if ( is_dir( CDN_PATH . $_bucket[0] ) ) :
							
								$_test_ok = TRUE;
								
							else :
							
								$_test_ok	= FALSE;
								$_error		= 'Bucket does not exist';
							
							endif;
						
						else :
						
							$_test_ok	= FALSE;
							$_error		= 'Could not verify bucket hash';
						
						endif;
					
					else :
					
						$_test_ok	= FALSE;
						$_error		= 'Incomplete bucket hash';
					
					endif;
				
				else :
				
					$_test_ok	= FALSE;
					$_error		= 'Could not decrypt bucket hash';
				
				endif;
				
				if ( $_test_ok ) :
				
					$this->data['bucket']		= $_bucket[0];
					$this->_directory	= CDN_PATH . $this->data['bucket'];
				
				else :
				
					$this->data['enabled']		= FALSE;
					$this->data['bad_bucket']	= $_error;
				
				endif;
			
			else :
			
				$this->data['bucket']	= active_user( 'id' ) . '-' . $this->data['type'];
				$this->_directory		= CDN_PATH . $this->data['bucket'];
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load helpers and libraries
		$this->load->helper( 'directory' );
		$this->load->library( 'cdn' );
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
		//	Fetch files
		if ( $this->data['enabled'] ) :
		
			//	Which directory? If a bucket hash has been specified then list that, if not
			//	use the user's upload directory
			
			
			// --------------------------------------------------------------------------
			
			$this->data['files'] = directory_map( $this->_directory );
			
			if ( $this->data['files'] === FALSE )
				$this->data['files'] = array();
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Define vars from CKEditor
		$this->data['ckeditor_func_num'] = $this->input->get( 'CKEditorFuncNum' );
		
		// --------------------------------------------------------------------------
		
		$this->asset->load( 'mustache.min.js', TRUE );
		$this->asset->load( 'jquery.fancybox.min.js', TRUE );
		
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
			
				$_options					= array();
				$_options['filename']		= 'USE_ORIGINAL';
				$_options['allowed_types']	= 'jpg|gif|png';
			
			break;
			
			// --------------------------------------------------------------------------
			
			case 'file' :
			
				$_options					= array();
				$_options['filename']		= 'USE_ORIGINAL';
			
			break;
			
			// --------------------------------------------------------------------------
			
			default :
			
				show_404();
			
			break;
		
		endswitch;
		
		// --------------------------------------------------------------------------
		
		//	Returning to...?
		$_return = $this->input->post( 'return' ) ? $this->input->post( 'return' ) : 'cdn/manager/browse/' . $this->data['type'];
		
		// --------------------------------------------------------------------------
		
		//	User is authorised to upload?
		if ( ! $this->data['enabled'] ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . $this->data['type_single'] . ' uploads are not available right now.' );
			redirect( $_return );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->library( 'cdn' );
		
		//	Create bucket, if it's there already this will gracefully fail
		if ( ! $this->cdn->create_bucket( $this->data['bucket'] ) ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I couldn\'t to create the upload folder.' );
			redirect( $_return );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Attempt upload
		//	TODO define the appropriate configs: certain file types and no randomly
		//	generate names - let the user's choose their own name
		
		$_upload = $this->cdn->upload( 'userfile', $this->data['bucket'], $_options );
		
		if ( $_upload ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> ' . $this->data['type_single'] . ' uploaded successfully!' );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . implode( $this->cdn->errors() ) );
		
		endif;
		
		redirect( $_return );
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
			
			case 'file' :
			
				$this->data['type']			= 'file';
				$this->data['type_single']	= 'file';
			
			break;
			
			// --------------------------------------------------------------------------
			
			default :
			
				show_404();
			
			break;
		
		endswitch;
		
		// --------------------------------------------------------------------------
		
		//	Returning to...?
		$_return = $this->input->get( 'return' ) ? $this->input->get( 'return' ) : 'cdn/manager/browse/' . $this->data['type'];
		
		// --------------------------------------------------------------------------
		
		//	User is authorised to delete?
		if ( ! $this->data['enabled'] ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . $this->data['type_single'] . ' deletions are not available right now.' );
			redirect( $_return );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->library( 'CDN' );
		
		//	Attempt upload
		//	TODO define the appropriate configs: certain file types and no randomly
		//	generate names - let the user's choose their own name
		
		$_delete = $this->cdn->delete( $this->uri->segment( 5 ), $this->data['bucket'] );
		
		if ( $_delete ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> ' . $this->data['type_single'] . ' deleted successfully!' );
			
			if ( strpos( $_return, '?' ) ) :
			
				$_return .= '&';
			
			else :
			
				$_return .= '?';
			
			endif;
			
			$_return .= 'deleted=true';
			
			redirect( $_return );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . implode( $this->cdn->errors() ) );
			redirect( $_return );
		
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