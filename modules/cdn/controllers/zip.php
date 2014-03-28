<?php

/**
 * Name:		Zip
 *
 * Description:	Serves a zip file containing raw objects
 *
 **/

//	Include _cdn.php; executes common functionality
require_once '_cdn.php';

/**
 * OVERLOADING NAILS' CDN MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Zip extends NAILS_CDN_Controller
{
	protected $_cache_file;


	// --------------------------------------------------------------------------


	public function index()
	{
		//	Decode the token
		$_ids		= $this->uri->segment( 3 );
		$_hash		= $this->uri->segment( 4 );
		$_filename	= urldecode( $this->uri->segment( 5 ) );

		if ( $_ids && $_hash ) :

			//	Check the hash
			$_objects = $this->cdn->verify_url_serve_zipped_hash( $_hash, $_ids, $_filename );

			if ( $_objects ) :

				//	Define the cache file
				$this->_cache_file = 'cdn-zip-' . $_hash . '.zip';

				//	Check the request headers; avoid hitting the disk at all if possible. If the Etag
				//	matches then send a Not-Modified header and terminate execution.

				if ( $this->_serve_not_modified( $this->_cache_file ) ) :

					return;

				endif;

				// --------------------------------------------------------------------------

				//	The browser does not have a local cache (or it's out of date) check the
				//	cache to see if this image has been processed already; serve it up if
				//	it has.

				if ( file_exists( $this->_cachedir . $this->_cache_file ) ) :

					$this->_serve_from_cache( $this->_cache_file );

				else :

					//	Cache object does not exist, fetch the originals, zip them and save a
					//	version in the cache bucket.

					//	Fetch the files to use, if any one doesn't exist any more then this
					//	zip file should fall over.

					$_usefiles		= array();
					$_use_buckets	= FALSE;
					$_prev_bucket	= '';

					foreach ( $_objects AS $obj ) :

						$_temp				= new stdClass();
						$_temp->path		= $this->_fetch_sourcefile( $obj->bucket->slug, $obj->filename );
						$_temp->filename	= $obj->filename_display;
						$_temp->bucket		= $obj->bucket->label;

						if ( ! $_temp->path ) :

							return $this->_bad_src();

						endif;

						if ( ! $_use_buckets && $_prev_bucket && $_prev_bucket !== $obj->bucket->id ) :

							$_use_buckets = TRUE;

						endif;

						$_prev_bucket	= $obj->bucket->id;
						$_usefiles[]	= $_temp;

					endforeach;

					// --------------------------------------------------------------------------

					//	Time to start Zipping!
					$this->load->library( 'zip' );

					//	Save to the zip
					foreach ( $_usefiles AS $file ) :

						$_name = $_use_buckets ? $file->bucket . '/' . $file->filename : $file->filename;
						$this->zip->add_data( $_name, file_get_contents( $file->path ) );

					endforeach;

					//	Save the Zip to the cache directory
					$this->zip->archive( $this->_cachedir . $this->_cache_file );

					// --------------------------------------------------------------------------

					//	Set the appropriate cache headers
					$this->_set_cache_headers( time(), $this->_cache_file, FALSE );

					// --------------------------------------------------------------------------

					//	Output to browser
					$this->zip->download( $_filename );

				endif;

			else :

				$this->_bad_src();

			endif;

		else :

			$this->_bad_src();

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _bad_src( $error = NULL )
	{
		header( 'Cache-Control: no-cache, must-revalidate', TRUE );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT', TRUE );
		header( 'Content-type: application/json', TRUE );
		header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 400 Bad Request', TRUE, 400 );

		// --------------------------------------------------------------------------

		$_out = array(

			'status'	=> 400,
			'message'	=> lang( 'cdn_error_serve_invalid_request' )

		);

		if ( $error ) :

			$_out['error'] = $error;

		endif;

		echo json_encode( $_out );

		// --------------------------------------------------------------------------

		//	Kill script, th, th, that's all folks.
		//	Stop the output class from hijacking our headers and
		//	setting an incorrect Content-Type

		exit(0);
	}


	// --------------------------------------------------------------------------


	public function _remap()
	{
		$this->index();
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' CDN MODULES
 *
 * The following block of code makes it simple to extend one of the core CDN
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_ZIP' ) ) :

	class Zip extends NAILS_Zip
	{
	}

endif;


/* End of file zip.php */
/* Location: ./application/modules/cdn/controllers/zip.php */