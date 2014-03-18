<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Blank Avatar
 * Description:	Generates a blank avatar
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

class NAILS_Blank_avatar extends NAILS_CDN_Controller
{
	protected $_fail;
	protected $_man;
	protected $_woman;
	protected $_width;
	protected $_height;
	protected $_sex;
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

		//	'Constant' variables
		$this->_man			= $this->_cdn_root . '_resources/img/avatar_man.png';
		$this->_woman		= $this->_cdn_root . '_resources/img/avatar_woman.png';

		// --------------------------------------------------------------------------

		//	Determine dynamic values
		$this->_width		= $this->uri->segment( 3, 100 );
		$this->_height		= $this->uri->segment( 4, 100 );
		$this->_sex			= $this->uri->segment( 5, 'man' );

		//	Set a unique filename (but one which is constant if requested twice, i.e
		//	no random values)

		$this->_cache_file	= 'blank_avatar-' . $this->_width . 'x' . $this->_height . '-' . $this->_sex . '.png';
	}


	// --------------------------------------------------------------------------


	/**
	 * Generate the thumbnail
	 *
	 * @access	public
	 * @return	void
	 **/
	public function index()
	{
		//	Check the request headers; avoid hitting the disk at all if possible. If the Etag
		//	matches then send a Not-Modified header and terminate execution.

		if ( $this->_serve_not_modified( $this->_cache_file ) )
			return;

		// --------------------------------------------------------------------------

		//	The browser does not have a local cache (or it's out of date) check the
		//	cache to see if this image has been processed already; serve it up if
		//	it has.

		if ( file_exists( DEPLOY_CACHE_DIR . $this->_cache_file ) ) :

			$this->_serve_from_cache( $this->_cache_file );

		else :

			//	Cache object does not exist, fetch the original, process it and save a
			//	version in the cache bucket.

			//	Which original are we using?
			switch( $this->_sex ) :

				case 'female' :
				case 'woman' :
				case 'f' :
				case 'w' :
				case '2' :

					$_src = $this->_woman;

				break;

				// --------------------------------------------------------------------------

				case 'male' :
				case 'man' :
				case 'm' :
				case '1' :

					$_src = $this->_man;

				break;

				// --------------------------------------------------------------------------

				//	Fallback to a default avatar
				//	TODO: Make this avatar gender neutral
				default :

					$_src = $this->_man;

				break;

			endswitch;

			if ( file_exists( $_src ) ) :

				//	Object exists, time for manipulation fun times :>

				//	Set some PHPThumb options
				$_options['resizeUp']		= TRUE;

				// --------------------------------------------------------------------------

				//	Perform the resize
				$PHPThumb = new PHPThumb\GD( $_src, $_options );
				$PHPThumb->adaptiveResize( $this->_width, $this->_height );

				// --------------------------------------------------------------------------

				//	Set the appropriate cache headers
				$this->_set_cache_headers( time(), $this->_cache_file, FALSE );

				// --------------------------------------------------------------------------

				//	Output the newly rendered file to the browser
				$PHPThumb->show();

				// --------------------------------------------------------------------------

				//	Save local version
				$PHPThumb->save( DEPLOY_CACHE_DIR . $this->_cache_file );

			else :

				//	This object does not exist.
				log_message( 'error', 'CDN: Blank Avatar: File not found; ' . $_src );
				return $this->_bad_src( $this->_width, $this->_height );

			endif;

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLANK_AVATAR' ) ) :

	class Blank_avatar extends NAILS_Blank_avatar
	{
	}

endif;


/* End of file blank_avatar.php */
/* Location: ./modules/cdn/controllers/blank_avatar.php */