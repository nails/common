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
		$this->data['enabled'] = $this->user->is_logged_in() ? TRUE : FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Load CDN library
		$this->load->library( 'cdn' );
		
		// --------------------------------------------------------------------------
		
		if ( $this->data['enabled'] ) :
		
			//	Define the directory, if a bucket has been specified use that, if not
			//	then use the user's upload directory
			
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
						
							if ( $this->cdn->get_bucket( $_bucket[0] ) ) :
							
								$_test_ok = TRUE;
								
							else :
							
								$_test_ok	= FALSE;
								$_error		= 'Bucket <strong>"' . $_bucket[0] . '"</strong> does not exist';
							
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
					$this->data['bucket_label']	= ucwords( $_bucket[0] );
				
				else :
				
					$this->data['enabled']		= FALSE;
					$this->data['bad_bucket']	= $_error;
				
				endif;
			
			else :
			
				$this->data['bucket']		= 'user-' . active_user( 'id' );
				$this->data['bucket_label']	= 'Your User Upload Directory';
				
				// --------------------------------------------------------------------------
				
				//	Test bucket, if it doesn't exist, create it
				if ( ! $this->cdn->get_bucket( $this->data['bucket'] ) ) :
				
					if ( ! $this->cdn->create_bucket( $this->data['bucket'] ) ) :
					
						 $this->data['enabled']		= FALSE;
						 $this->data['bad_bucket']	= 'Unable to create upload bucket.';
					
					endif;
				
				endif;
			
			endif;
			
		endif;
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
		
			$this->data['bucket'] = $this->cdn->get_bucket( $this->data['bucket'], TRUE, $this->input->get( 'filter-tag' ) );
			
			// --------------------------------------------------------------------------
			
			$this->asset->load( 'jquery.min.js', TRUE );
			$this->asset->load( 'jquery.ui.min.js', TRUE );
			$this->asset->load( 'nails.default.min.js', TRUE );
			$this->asset->load( 'nails.api.min.js', TRUE );
			$this->asset->load( 'nails.cdn.manager.min.js', TRUE );
			$this->asset->load( 'mustache.min.js', TRUE );
			$this->asset->load( 'jquery.fancybox.min.js', TRUE );
			
			// --------------------------------------------------------------------------
			
			$this->load->view( 'manager/browse', $this->data );
			
		else :
		
			$this->load->view( 'manager/disabled', $this->data );
		
		endif;
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
		//	Returning to...?
		$_return = 'cdn/manager/browse?' . $_SERVER['QUERY_STRING'];
		
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
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I couldn\'t create the upload folder.' );
			redirect( $_return );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_bucket = $this->cdn->get_bucket( $this->data['bucket'] );
		
		//	Define upload options
		$_options = array();
		
		if ( $_bucket->allowed_types ) :
		
			$_options['allowed_types']	 = $_bucket->allowed_types;
		
		else :
		
			//	Images
			$_options['allowed_types']	 = 'jpg|png|gif';
			
			//	Office & Documents
			$_options['allowed_types']	.= '|doc|docx|xls|xlsx|pps|ppt|pptx';
			$_options['allowed_types']	.= '|pages|odt|rtf|txt|csv|key|xml|pdf';
			
			//	Audio
			$_options['allowed_types']	.= '|mp3|m3u|m4a|wav|wma|aif';
			
			//	Video
			$_options['allowed_types']	.= '|avi|mov|mp4|mpg|m4v';
			
			//	Archives
			$_options['allowed_types']	.= '|zip|zipx|rar|tar';
		
		endif;
		
		if ( $_bucket->max_size ) :
		
			$_options['max_size']	 = $_bucket->max_size;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Upload the file, 
		$_upload = $this->cdn->upload( 'userfile', $this->data['bucket'], $_options );
		
		if ( $_upload ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> File uploaded successfully!' );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . implode( $this->cdn->errors() ) );
		
		endif;
		
		redirect( $_return );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	public function delete()
	{
		//	Returning to...?
		$_return = 'cdn/manager/browse?' . $_SERVER['QUERY_STRING'];
		
		// --------------------------------------------------------------------------
		
		//	User is authorised to delete?
		if ( ! $this->data['enabled'] ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> file deletions are not available right now.' );
			redirect( $_return );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->library( 'CDN' );
		
		//	Attempt Delete
		$_delete = $this->cdn->delete( $this->uri->segment( 4 ), $this->data['bucket'] );
		
		if ( $_delete ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> File deleted successfully!' );
			
			if ( strpos( $_return, '?' ) ) :
			
				$_return .= '&';
			
			else :
			
				$_return .= '?';
			
			endif;
			
			$_return .= 'deleted=true';
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . implode( $this->cdn->errors() ) );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		redirect( $_return );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function new_tag()
	{
		//	Returning to...?
		$_return = 'cdn/manager/browse?' . $_SERVER['QUERY_STRING'];
		
		// --------------------------------------------------------------------------
		
		$_added = $this->cdn->add_bucket_tag( $this->data['bucket'], $this->input->post( 'label' ) );
		
		if ( $_added ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Tag added successfully!' );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . implode( $this->cdn->errors() ) );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		redirect( $_return );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function delete_tag()
	{
		//	Returning to...?
		$_return = 'cdn/manager/browse?' . $_SERVER['QUERY_STRING'];
		
		// --------------------------------------------------------------------------
		
		$_deleted = $this->cdn->delete_bucket_tag( $this->data['bucket'], $this->uri->segment( 4 ) );
		
		if ( $_deleted ) :
		
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Tag deleted successfully!' );
		
		else :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> ' . implode( $this->cdn->errors() ) );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		redirect( $_return );
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