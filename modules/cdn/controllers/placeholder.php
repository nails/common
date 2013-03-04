<?php

/**
 * Name:			Placeholder
 *
 * Description:	Generates a placeholder image
 * 
 **/

//	Include _cdn_local.php; executes common functionality
require_once '_cdn_local.php';

class Placeholder extends NAILS_CDN_Controller
{
	private $_tile;
	private $_width;
	private $_height;
	private $_border;
	private $_cache_file;
	
	// --------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	'Constant' variables
		$this->_tile	= $this->_cdn_root . '_resources/img/placeholder.png';
		
		//	Determine dynamic values
		$this->_width	= $this->uri->segment( 3, 100 );
		$this->_height	= $this->uri->segment( 4, 100 );
		$this->_border	= $this->uri->segment( 5, 1 );
		
		//	Apply limits (prevent DOS)
		$this->_width	= ( $this->_width > 2000 )	? 2000 : $this->_width;
		$this->_height	= ( $this->_height > 2000 )	? 2000 : $this->_height;
		$this->_border	= ( $this->_border > 2000 )	? 2000 : $this->_border;
		
		//	Set a unique filename (but one which is constant if requested twice, i.e
		//	no random values)
		
		$this->_cache_file	= 'placeholder-' . $this->_width . 'x' . $this->_height . '-' . $this->_border . '.png';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function index()
	{
		//	Check the request headers; avoid hitting the disk at all if possible. If the Etag
		//	matches then send a Not-Modified header and terminate execution.
		
		if ( $this->_serve_not_modified( $this->_cache_file ) )
			return;
		
		// --------------------------------------------------------------------------
		
		//	The browser does not have a local cache (or it's out of date) check the
		//	cache directory to see if this image has been processed already; serve it up if
		//	it has.
		
		if ( file_exists( CACHE_DIR . $this->_cache_file ) ) :
		
			$this->_serve_from_cache( $this->_cache_file );
		
		else :
		
			//	Cache object does not exist, create a new one and cache it
			
			//	Get and create the placeholder graphic
			$_tile	= imagecreatefrompng( $this->_tile );
			
			// --------------------------------------------------------------------------
			
			//	Create the container
			$_img	= imagecreatetruecolor( $this->_width, $this->_height );
			
			// --------------------------------------------------------------------------
			
			//	Tile the placeholder
			imagesettile( $_img, $_tile );
			imagefilledrectangle( $_img, 0, 0, $this->_width, $this->_height, IMG_COLOR_TILED );
			
			// --------------------------------------------------------------------------
			
			//	Draw a border
			$_border = imagecolorallocate( $_img, 190, 190, 190 );
				
			for ( $i = 0; $i <	 $this->_border; $i++ ) :
			
				//	Left
				imageline( $_img, 0+$i, 0, 0+$i, $this->_height, $_border );
				
				//	Top
				imageline( $_img, 0, 0+$i, $this->_width, 0+$i, $_border );
				
				//	Bottom
				imageline( $_img, 0, $this->_height-1-$i, $this->_width, $this->_height-1-$i,  $_border );
				
				//	Right
				imageline( $_img, $this->_width-1-$i, 0, $this->_width-1-$i, $this->_height,  $_border );
				
			endfor;
			
			// --------------------------------------------------------------------------
			
			//	Set the appropriate cache headers
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', time() ) . 'GMT' );
			header( 'ETag: "' . md5( $this->_cache_file ) . '"' );
			header( 'X-CDN-CACHE: MISS' );
			
			// --------------------------------------------------------------------------
			
			//	Output to browser
			header( 'Content-Type: image/png' );
			imagepng( $_img );
			
			// --------------------------------------------------------------------------
			
			//	Save to CACHE_DIR
			imagepng( $_img, CACHE_DIR . $this->_cache_file );
			
			// --------------------------------------------------------------------------
			
			//	Destroy the images to free up resource
			imagedestroy( $_tile );
			imagedestroy( $_img );
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function _remap()
	{
		$this->index();
	}
}


/* End of file placeholder.php */
/* Location: ./application/modules/cdn_local/controllers/placeholder.php */