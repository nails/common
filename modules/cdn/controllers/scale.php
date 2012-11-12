<?php

/**
* Name:			Scale
*
* Created:		12/11/2012
* Modified:		12/11/2012
*
* Description:	Generates a scaled version of an image
* 
*/

//	Include _cdn_local.php; executes common functionality
require_once '_cdn_local.php';
require_once 'thumb.php';

class Scale extends Thumb
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
		
		if ( $this->_serve_not_modified( $this->_cache_file ) )
			return;
		
		// --------------------------------------------------------------------------
		
		//	The browser does not have a local cache (or it's out of date) check the
		//	cache to see if this image has been processed already; serve it up if
		//	it has.
		
		if ( file_exists( CACHE_DIR . $this->_cache_file ) ) :
		
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
				
				//	Save to the cache, this involves saving a local copy then punting that up to S3
				
				//	Save local version
				$thumb->save( CACHE_DIR . $this->_cache_file, strtoupper( substr( $this->_extension, 1 ) ) );
			
			else :
			
				//	This object does not exist.
				return $this->_bad_src();
			
			endif;
			
		endif;
	}
}


/* End of file thumb.php */
/* Location: ./application/modules/cdn_local/controllers/thumb.php */