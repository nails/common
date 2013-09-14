<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		CDN API
 *
 * Description:	This controller handles CDN API methods
 *
 **/

require_once '_api.php';

/**
 * OVERLOADING NAILS' API MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Cdnapi extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
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


	public function get_upload_token()
	{
		//	Define $_out array
		$_out = array();

		// --------------------------------------------------------------------------

		if ( $this->user->is_logged_in() ) :

			$_out['token'] = $this->cdn->generate_api_upload_token( active_user( 'id' ) );

		else :

			$_out['status'] = 400;
			$_out['error']	= 'You must be logged in to generate an upload token.';

		endif;

		// --------------------------------------------------------------------------

		$this->_out( $_out );
	}


	// --------------------------------------------------------------------------


	public function object_create()
	{
		//	Define $_out array
		$_out = array();

		// --------------------------------------------------------------------------

		//	User must supply a valid upload token
		$_user = $this->cdn->validate_api_upload_token( $this->input->post( 'token' ) );

		if ( ! $_user ) :

			$_out['status']	= 400;
			$_out['error']	= $this->cdn->error();

			$this->_out( $_out, 'JSON', FALSE );
			return;

		else :

			$this->user->set_active_user( $_user );

		endif;


		// --------------------------------------------------------------------------

		//	Uploader verified, bucket defined and valid?
		$_bucket = $this->input->post( 'bucket' );

		if ( ! $_bucket ) :

			$_out['status']	= 400;
			$_out['error']	= 'Bucket not defined.';

			$this->_out( $_out, 'JSON', FALSE );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Attempt upload
		$_upload = $this->cdn->object_create( 'upload', $_bucket );

		if ( $_upload ) :

			//	Success! Return as per the user's preference
			if ( $this->input->post( 'return' ) ) :

				$_format = explode( '|', $this->input->post( 'return' ) );

				switch( strtoupper( $_format[0] ) ) :

					//	URL
					case 'URL' :

						if ( isset( $_format[1] ) ) :

							switch( strtoupper( $_format[1] ) ) :

								case 'THUMB' :

									//	Generate a url for each request
									$_out['object_url']	= array();
									$_sizes				= explode( ',', $_format[2] );

									foreach ( $_sizes AS $sizes ) :

										$_size = explode( 'x', $sizes );

										$_w = isset( $_size[0] )	? $_size[0] : '';
										$_h = isset( $_size[1] )	? $_size[1] : '';

										$_out['object_url'][] = cdn_thumb( $_upload->id, $_w, $_h );

									endforeach;

									$_out['object_id']	= $_upload->id;

								break;

								case 'SCALE' :

									//	Generate a url for each request
									$_out['object_url']	= array();
									$_sizes				= explode( ',', $_format[2] );

									foreach ( $_sizes AS $sizes ) :

										$_size = explode( 'x', $sizes );

										$_w = isset( $_size[0] )	? $_size[0] : '';
										$_h = isset( $_size[1] )	? $_size[1] : '';

										$_out['object_url'][] = cdn_scale( $_upload->id, $_w, $_h );

									endforeach;

									$_out['object_id']	= $_upload->id;

								break;

								case 'SERVE' :
								default :

									$_out['object_url']	= cdn_serve( $_upload->id );
									$_out['object_id']	= $_upload->id;

								break;

							endswitch;

						else :

							//	Unknow, return the serve URL & ID
							$_out['object_url']	= cdn_serve( $_upload->id );
							$_out['object_id']	= $_upload->id;

						endif;

					break;

					// --------------------------------------------------------------------------

					default:

						//	just return the object
						$_out['object'] = $_upload;

					break;

				endswitch;

			else :

				//	just return the object
				$_out['object'] = $_upload;

			endif;

		else :

			$_out['status']	= 400;
			$_out['error']	= $this->cdn->error();

		endif;

		// --------------------------------------------------------------------------

		//	Make sure the _out() method doesn't send a header, annoyingly SWFupload does
		//	not return the server response to the script when a non-200 status code is detected

		$this->_out( $_out, 'JSON', FALSE );
	}


	// --------------------------------------------------------------------------


	public function object_delete()
	{
		//	Define $_out array
		$_out = array();

		// --------------------------------------------------------------------------

		$_object_id	= $this->input->post( 'object_id' );
		$_delete	= $this->cdn->object_delete( $_object_id );

		if ( ! $_delete ) :

			$_out['status']	= 400;
			$_out['error']	= implode( '', $this->cdn->errors() );

		endif;

		// --------------------------------------------------------------------------

		$this->_out( $_out, 'JSON' );
	}

	// --------------------------------------------------------------------------


	public function add_object_tag()
	{
		$_object_id	= $this->input->get( 'object_id' );
		$_tag_id	= $this->input->get( 'tag_id' );
		$_out		= array();

		$_added = $this->cdn->object_tag_add( $_object_id, $_tag_id );

		if ( $_added ) :

			//	Get new count for this tag
			$_out = array(
				'new_total'	=> $this->cdn->object_tag_count( $_tag_id )
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

		$_deleted = $this->cdn->object_tag_delete( $_object_id, $_tag_id );

		if ( $_deleted ) :

			//	Get new count for this tag
			$_out = array(
				'new_total'	=> $this->cdn->object_tag_count( $_tag_id )
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


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' API MODULES
 *
 * The following block of code makes it simple to extend one of the core admin
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CDN' ) ) :

	class Cdnapi extends NAILS_Cdnapi
	{
	}

endif;

/* End of file cdn.php */
/* Location: ./application/modules/api/controllers/cdn.php */