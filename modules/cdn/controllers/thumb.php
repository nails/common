<?php

/**
 * Name:		Thumb
 *
 * Description:	Generates a thumbnail of an image
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

class NAILS_Thumb extends NAILS_CDN_Controller
{
	protected $_fail;
	protected $_bucket;
	protected $_width;
	protected $_height;
	protected $_object;
	protected $_extension;
	protected $_cache_file;


	// --------------------------------------------------------------------------


	/**
	 * Construct the class; set defaults
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Determine dynamic values
		$this->_width		= $this->uri->segment( 3, 100 );
		$this->_height		= $this->uri->segment( 4, 100 );
		$this->_bucket		= $this->uri->segment( 5 );
		$this->_object		= urldecode( $this->uri->segment( 6 ) );
		$this->_extension	= ( ! empty( $this->_object ) ) ? strtolower( substr( $this->_object, strrpos( $this->_object, '.' ) ) ) : FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Generate the thumbnail
	 *
	 * @access	public
	 * @return	void
	 **/
	public function index( $crop_method = 'THUMB' )
	{
		//	Sanitize the crop method
		$_cropmethod = strtoupper( $crop_method );

		switch ( $_cropmethod ) :

			case 'SCALE'	:	$_phpthumb_method = 'resize';			break;
			case 'THUMB'	:
			default			:	$_phpthumb_method = 'adaptiveResize';	break;

		endswitch;

		// --------------------------------------------------------------------------

		//	Define the cache file
		$this->_cache_file = $this->_bucket . '-' . substr( $this->_object, 0, strrpos( $this->_object, '.' ) ) .'-' . $_cropmethod . '-' . $this->_width . 'x' . $this->_height . $this->_extension;

		// --------------------------------------------------------------------------

		//	We must have a bucket, object and extension in order to work with this
		if ( ! $this->_bucket || ! $this->_object || ! $this->_extension ) :

			log_message( 'error', 'CDN: ' . $_cropmethod . ': Missing _bucket, _object or _extension' );
			return $this->_bad_src();

		endif;

		// --------------------------------------------------------------------------

		//	Check the request headers; avoid hitting the disk at all if possible. If the Etag
		//	matches then send a Not-Modified header and terminate execution.

		if ( $this->_serve_not_modified( $this->_cache_file ) ) :

			$this->cdn->object_increment_count( $_cropmethod, $this->_object, $this->_bucket );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	The browser does not have a local cache (or it's out of date) check the
		//	cache to see if this image has been processed already; serve it up if
		//	it has.

		if ( file_exists( $this->_cachedir . $this->_cache_file ) ) :

			$this->cdn->object_increment_count( $_cropmethod, $this->_object, $this->_bucket );
			$this->_serve_from_cache( $this->_cache_file );

		else :

			//	Cache object does not exist, fetch the original, process it and save a
			//	version in the cache bucket.

			//	Fetch the file to use
			$_usefile = $this->_fetch_sourcefile( $this->_bucket, $this->_object );

			if ( ! $_usefile ) :

				log_message( 'error', 'CDN: ' . $_cropmethod . ': No sourcefile was returned.' );
				return $this->_bad_src( $this->_width, $this->_height );

			elseif( ! filesize( $_usefile ) ) :

				//	Hmm, empty, delete it and try one mroe time
				@unlink( $_usefile );

				$_usefile = $this->_fetch_sourcefile( $this->_bucket, $this->_object );

				if ( ! $_usefile ) :

					log_message( 'error', 'CDN: ' . $_cropmethod . ': No sourcefile was returned, second attempt.' );
					return $this->_bad_src( $this->_width, $this->_height );

				elseif( ! filesize( $_usefile ) ) :

					log_message( 'error', 'CDN: ' . $_cropmethod . ': sourcefile exists, but has a zero filesize.' );
					return $this->_bad_src( $this->_width, $this->_height );

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	Time to start Image processing

			//	Are we dealing with an animated Gif? If so handle differently - extract each
			//	frame, resize, then recompile. Otherwise, just resize

			$_object = $this->cdn->get_object( $this->_object, $this->_bucket );

			if ( ! $_object ) :

				//return $this->_bad_src( $this->_width, $this->_height );

			endif;

			// --------------------------------------------------------------------------

			//	Set the appropriate cache headers
			$this->_set_cache_headers( time(), $this->_cache_file, FALSE );

			// --------------------------------------------------------------------------

			//	Handle the actual resize
			if ( 1==0 && $_object->is_animated ) :

				$this->_resize_animated( $_usefile, $_phpthumb_method );

			else :

				$this->_resize( $_usefile, $_phpthumb_method );

			endif;

			// --------------------------------------------------------------------------

			//	Bump the counter
			//$this->cdn->object_increment_count( $_cropmethod, $_object->id );

		endif;
	}


	// --------------------------------------------------------------------------


	private function _resize( $usefile, $PHPThumb_method )
	{
		//	Set some PHPThumb options
		$_options					= array();
		$_options['resizeUp']		= TRUE;
		$_options['jpegQuality']	= 80;

		// --------------------------------------------------------------------------

		//	Perform the resize

		//	Turn errors off, if something bad happens we want to
		//	output the _bad_src image and log the issue.

		$_old_errors = error_reporting();
		error_reporting(0);

		try
		{
			//	Catch any output, don't want anything going to the browser unless
			//	we're sure it's ok

			ob_start();

			$PHPThumb = new PHPThumb\GD( $usefile, $_options );
			$PHPThumb->{$PHPThumb_method}( $this->_width, $this->_height );

			//	Save cache version
			$PHPThumb->save( $this->_cachedir . $this->_cache_file , strtoupper( substr( $this->_extension, 1 ) ) );

			//	Flush the buffer
			ob_end_clean();
		}
		catch( Exception $e )
		{
			//	Log the error
			log_message( 'error', 'CDN: ' . $PHPThumb_method . ': ' . $e->getMessage() );

			//	Switch error reporting back how it was
			error_reporting( $_old_errors );

			//	Flush the buffer
			ob_end_clean();

			//	Bad SRC
			return $this->_bad_src( $this->_width, $this->_height );
		}

		$this->_serve_from_cache( $this->_cache_file, FALSE );

		//	Switch error reporting back how it was
		error_reporting( $_old_errors );
	}


	// --------------------------------------------------------------------------


	private function _resize_animated( $usefile, $PHPThumb_method )
	{
		$_hash			= md5( microtime( TRUE ) . uniqid() ) . uniqid();
		$_frames		= array();
		$_cachefiles	= array();
		$_durations		= array();
		$_gfe			= new GifFrameExtractor\GifFrameExtractor();
		$_gc			= new GifCreator\GifCreator();

		// --------------------------------------------------------------------------

		//	Extract all the frames, resize them and save to the cache
		$_gfe->extract( $usefile );

		$_i = 0;
		foreach ( $_gfe->getFrames() as $frame ) :

			//	Define the filename
			$_filename		= $_hash . '-' . $_i . '.gif';
			$_temp_filename	= $_hash . '-' . $_i . '-original.gif';
			$_i++;

			//	Set these for recompiling
			$_frames[]		= $this->_cachedir . $_filename;
			$_cachefiles[]	= $this->_cachedir . $_temp_filename;
			$_durations[]	= $frame['duration'];

			// --------------------------------------------------------------------------

			//	Set some PHPThumb options
			$_options					= array();
			$_options['resizeUp']		= TRUE;

			// --------------------------------------------------------------------------

			//	Perform the resize; first save the original frame to disk
			imagegif( $frame['image'], $this->_cachedir . $_temp_filename );

			$PHPThumb = new PHPThumb\GD( $this->_cachedir . $_temp_filename, $_options );
			$PHPThumb->{$PHPThumb_method}( $this->_width, $this->_height );

			// --------------------------------------------------------------------------

			//	Save cache version
			$PHPThumb->save( $this->_cachedir . $_filename , strtoupper( substr( $this->_extension, 1 ) ) );

		endforeach;

		//	Recompile the resized images back into an animated gif and save to the cache
		//	TODO: We assume the gif loops infinitely but we should really check.
		//	Issue made on the libraries gitHub asking for this feature.
		//	View here: https://github.com/Sybio/GifFrameExtractor/issues/3

		$_gc->create( $_frames, $_durations, 0 );
		$_data = $_gc->getGif();

		// --------------------------------------------------------------------------

		//	Output to browser
		header( 'Content-Type: image/gif', TRUE );
		echo $_data;

		// --------------------------------------------------------------------------

		//	Save to cache
		$this->load->helper( 'file' );
		write_file( $this->_cachedir . $this->_cache_file, $_data );

		// --------------------------------------------------------------------------

		//	Remove cache frames
		foreach ( $_frames as $frame ) :

			@unlink( $frame );

		endforeach;

		foreach ( $_cachefiles as $frame ) :

			@unlink( $frame );

		endforeach;

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_THUMB' ) ) :

	class Thumb extends NAILS_Thumb
	{
	}

endif;

/* End of file thumb.php */
/* Location: ./modules/cdn/controllers/thumb.php */