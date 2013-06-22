<?php

/**
 * Name:		Scale
 *
 * Description:	Generates a scaled version of an image
 * 
 **/

//	Include _cdn_local.php; executes common functionality
require_once '_cdn_local.php';
require_once 'thumb.php';

/**
 * OVERLOADING NAILS' CDN MODULES
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

class NAILS_Scale extends Thumb
{
	/**
	 * Generate the thumbnail
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		//	We must have a bucket, object and extension in order to work with this
		if ( ! $this->_bucket || ! $this->_object || ! $this->_extension )
			return $this->_bad_src();
		
		// --------------------------------------------------------------------------
		
		//	Set the prefix for the cache file
		$this->_cache_file = 'scale-' . $this->_cache_file;
		
		// --------------------------------------------------------------------------
		
		//	Check the request headers; avoid hitting the disk at all if possible. If the Etag
		//	matches then send a Not-Modified header and terminate execution.
		
		if ( $this->_serve_not_modified( $this->_cache_file ) ) :

			$this->cdn->increment_count( 'SCALE', $this->_object, $this->_bucket );
			return;

		endif;
		
		// --------------------------------------------------------------------------
		
		//	The browser does not have a local cache (or it's out of date) check the
		//	cache to see if this image has been processed already; serve it up if
		//	it has.
		
		if ( file_exists( CACHE_DIR . $this->_cache_file ) ) :
		
			$this->cdn->increment_count( 'SCALE', $this->_object, $this->_bucket );
			$this->_serve_from_cache( $this->_cache_file );
		
		else :
		
			//	Cache object does not exist, fetch the original, process it and save a
			//	version in the cache bucket.
			
			if ( file_exists( CDN_PATH . $this->_bucket . '/' . $this->_object ) ) :
			
				//	Object exists, time for manipulation fun times :>
				
				//	Set some PHPThumbFactory options
				$_options['resizeUp']		= TRUE;
				$_options['jpegQuality']	= 80;
				
				// --------------------------------------------------------------------------
				
				//	Perform the resize (3rd param tells PHPThumbFactory that we're using a
				//	data stream rather than a file).
				
				$thumb = PhpThumbFactory::create( CDN_PATH . $this->_bucket . '/' . $this->_object, $_options );
				$thumb->resize( $this->_width, $this->_height );
				
				// --------------------------------------------------------------------------
				
				//	Set the appropriate cache headers
				header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', time() ) . 'GMT' );
				header( 'ETag: "' . md5( $this->_cache_file ) . '"' );
				header( 'X-CDN-CACHE: MISS' );
				
				// --------------------------------------------------------------------------
				
				//	Output the newly rendered file to the browser
				$thumb->show();
				
				// --------------------------------------------------------------------------

				//	Bump the counter
				$this->cdn->increment_count( 'SCALE', $this->_object, $this->_bucket );

				// --------------------------------------------------------------------------
				
				//	Save local version, make sure cache is writable
				if ( is_writable( CACHE_DIR . $this->_cache_file ) ) :

					$thumb->save( CACHE_DIR . $this->_cache_file , strtoupper( substr( $this->_extension, 1 ) ) );

				else :

					//	Inform developers
					$_subject	= 'Cache (scale) dir not writeable';
					$_message	= 'The CDN cannot write to the cache directory.'."\n\n";
					$_message	.= 'Dir: ' . CACHE_DIR . $this->_cache_file . "\n\n";
					$_message	.= 'URL: ' . $_SERVER['REQUEST_URI'];
					
					send_developer_mail( $_subject, $_message );

				endif;
			
			else :
			
				//	This object does not exist.
				return $this->_bad_src();
			
			endif;
			
		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' CDN MODULES
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_SCALE' ) ) :

	class Scale extends NAILS_Scale
	{
	}

endif;


/* End of file thumb.php */
/* Location: ./application/modules/cdn_local/controllers/thumb.php */