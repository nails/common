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
	 * @author	Pablo
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

		// --------------------------------------------------------------------------

		//	Load phpThumb
		require_once $this->_cdn_root . '_resources/classes/phpthumb/phpthumb.php';
	}


	// --------------------------------------------------------------------------


	/**
	 * Generate the thumbnail
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function index( $crop_method = 'THUMB' )
	{
		//	Sanitize the crop method
		$_cropmethod = strtoupper( $crop_method );

		switch ( $_cropmethod ) :

			case 'SCALE'	:	$_phpthumbfactory_method = 'resize';			break;
			case 'THUMB'	:
			default			:	$_phpthumbfactory_method = 'adaptiveResize';	break;

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

				return $this->_bad_src( $this->_width, $this->_height );

			endif;

			// --------------------------------------------------------------------------

			//	Time to start Image processing

			//	Set some PHPThumbFactory options
			$_options					= array();
			$_options['resizeUp']		= TRUE;
			$_options['jpegQuality']	= 80;

			// --------------------------------------------------------------------------

			//	Perform the resize
			$phpThumbFactory = PhpThumbFactory::create( $_usefile, $_options );
			$phpThumbFactory->{$_phpthumbfactory_method}( $this->_width, $this->_height );

			// --------------------------------------------------------------------------

			//	Set the appropriate cache headers
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', time() ) . 'GMT' );
			header( 'ETag: "' . md5( $this->_cache_file ) . '"' );
			header( 'X-CDN-CACHE: MISS' );

			// --------------------------------------------------------------------------

			//	Output the newly rendered file to the browser
			$phpThumbFactory->show();

			// --------------------------------------------------------------------------

			//	Bump the counter
			$this->cdn->object_increment_count( $_cropmethod, $this->_object, $this->_bucket );

			// --------------------------------------------------------------------------

			//	Save cache version
			$phpThumbFactory->save( $this->_cachedir . $this->_cache_file , strtoupper( substr( $this->_extension, 1 ) ) );

		endif;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_THUMB' ) ) :

	class Thumb extends NAILS_Thumb
	{
	}

endif;

/* End of file thumb.php */
/* Location: ./application/modules/cdn/controllers/thumb.php */