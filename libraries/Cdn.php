<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			CDN
*
* Description:	A Library for dealing with content in the CDN
* 
*/

class Cdn {
	
	private $_ci;
	private $_cdn;
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct( $options = NULL )
	{
		$this->_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Load the helper
		$this->_ci->load->helper( 'cdn' );
		
		// --------------------------------------------------------------------------
		
		$this->initialize( $options );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Sets (or resets) the configuration for this instance of the CDN library
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function initialize( $options = NULL )
	{
		$_driver = isset( $options['driver'] ) ? strtolower( $options['driver'] ) : strtolower( CDN_DRIVER );
		
		// --------------------------------------------------------------------------
		
		unset($this->_cdn);
		
		// --------------------------------------------------------------------------
		
		//	Determine which driver to use
		
		//	Load the file
		$this->_include_driver( $_driver );
		
		//	Instanciate
		switch ( $_driver ) :
		
			case 'local':
			default:
			
				$this->_cdn = new Local_CDN( $options );
			
			break;
		
		endswitch;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	static function _include_driver( $driver )
	{
		switch ( $driver ) :
		
			case 'local':
			default:
			
				include_once NAILS_PATH . 'libraries/_resources/cdn_drivers/local.php';
				return 'Local_CDN';
			
			break;
		
		endswitch;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/*	! UTILITY METHODS */
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the upload method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function upload( $file, $bucket, $options = array(), $is_raw = FALSE )
	{
		return $this->_cdn->upload( $file, $bucket, $options, $is_raw );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the delete method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function delete( $file, $bucket )
	{
		return $this->_cdn->delete( $file, $bucket );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the move method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function copy( $source, $file, $bucket, $options = array() )
	{
		return $this->_cdn->move( $source, $file, $bucket, $options );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the move method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function move( $source, $file, $bucket, $options = array() )
	{
		return $this->_cdn->move( $source, $file, $bucket, $options );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the replace method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function replace( $file, $bucket, $replace_with, $options = array(), $is_raw = FALSE )
	{
		return $this->_cdn->replace( $file, $bucket, $replace_with, $options, $is_raw );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/*	! BUCKET METHODS */
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Creates a new bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function create_bucket( $bucket )
	{
		return $this->_cdn->create_bucket( $bucket );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Deletes a bucket, only if empty
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function delete_bucket( $bucket )
	{
		return $this->_cdn->delete_bucket( $bucket );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/*	! HELPER METHODS */
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Returns the error array
	 *
	 * @access	public
	 * @return	array
	 * @author	Pablo
	 **/
	public function errors()
	{
		return $this->_cdn->errors();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Returns the error array
	 *
	 * @access	static
	 * @return	array
	 * @author	Pablo
	 **/
	static function get_ext_from_mimetype( $mime_type )
	{
		//	Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
		//	Thanks, 'chaos' - http://stackoverflow.com/a/1147952/789224
		
		// --------------------------------------------------------------------------
		
		//	Before we start map some common mime-types which are troublesome with the system.
		//	Please forgive me future dev.
		
		switch ( $mime_type ) :
		
			case 'application/vnd.ms-office' :	return 'doc';	break;	//	OpenOffice .doc
			case 'text/rtf' :					return 'rtf';	break;	//	Rich Text Format
		
		endswitch;
		
		// --------------------------------------------------------------------------
		
		$_file	= fopen( CDN_MAGIC, 'r' );
		$_ext	= NULL;
		
		while( ( $line = fgets( $_file ) ) !== FALSE ) :
		
			$line = trim( preg_replace( '/#.*/', '', $line ) );
			
			if ( ! $line )
				continue;
				
			$_parts = preg_split( '/\s+/', $line );
			
			if ( count( $_parts ) == 1 )
				continue;
				
			$_type = array_shift( $_parts );
			
			foreach ( $_parts as $part ) :
			
				if ( $_type == strtolower( $mime_type ) ) :
				
					$_ext = $part;
					
					break;
					
				endif;
				
			endforeach;
				
		endwhile;
		
		fclose( $_file );
		
		// --------------------------------------------------------------------------
		
		//	Being anal here, some extensions *need* to be forced
		switch ( $_ext ) :
		
			case 'jpeg' : $_ext = 'jpg';	break;
		
		endswitch;
		
		// --------------------------------------------------------------------------
		
		return $_ext;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	static function get_mimetype_from_ext( $ext )
	{
		//	Prep $ext, make sure it has no dots
		$ext = substr( $ext, (int) strrpos( $ext, '.' ) + 1 );
		
		// --------------------------------------------------------------------------
		
		//	Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
		//	Thanks, 'chaos' - http://stackoverflow.com/a/1147952/789224
		
		$_file = fopen( CDN_MAGIC, 'r' );
		$_mime = NULL;
		
		
		while( ( $line = fgets( $_file ) ) !== FALSE ) :
		
			$line = trim( preg_replace( '/#.*/', '', $line ) );
			
			if ( ! $line )
				continue;
				
			$_parts = preg_split( '/\s+/', $line );
			
			if ( count( $_parts ) == 1 )
				continue;
			
			$_part = array_shift( $_parts );
			
			foreach( $_parts as $_ext ) :
			
				if ( strtolower( $_ext ) == strtolower( $ext ) ) :
				
					$_mime = $_part;
					
					break;
					
				endif;
				
			endforeach;
				
		endwhile;
		
		fclose( $_file );
		
		// --------------------------------------------------------------------------
		
		return $_mime;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	static function get_mime_type_from_file( $file )
	{
		$_fi = finfo_open( FILEINFO_MIME_TYPE );
		
		//	Use normal magic
		$_result = finfo_file( $_fi, $file );
		
		//	If normal magic responds with a ZIP, use specific magic to test if it's
		//	an office doc - doing this because Jon T told us that specifying the file
		//	to use might cause the funciton to 'forget' it's other magic, so using
		//	defaults first and falling back to this.
		
		if ( $_result == 'application/zip' ) :
		
			$_fi = finfo_open( FILEINFO_MIME_TYPE, '/etc/magic' );
			$_result = finfo_file( $_fi, $file );
			
			//	If this comes back as an octet stream then fallback to application/zip
			if ( $_result == 'application/octet-stream' ) :
			
				$_result = 'application/zip';
			
			endif;
		
		endif;
		
		return $_result;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/*	! URL GENERATOR METHODS */
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_serve_url method
	 *
	 * @access	static
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$file	The filename of the object
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_serve_url( $bucket, $file )
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_serve_url( $bucket, $file );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_serve_url_scheme method
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_serve_url_scheme()
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_serve_url_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_thumb_url method
	 *
	 * @access	static
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$file	The filename of the image we're 'thumbing'
	 * @param	string	$width	The width of the thumbnail
	 * @param	string	$height	The height of the thumbnail
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_thumb_url( $bucket, $file, $width, $height )
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_thumb_url( $bucket, $file, $width, $height );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_thumb_url_scheme method
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_thumb_url_scheme()
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_thumb_url_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_thumb_url method
	 *
	 * @access	static
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$file	The filename of the image we're 'scaling'
	 * @param	string	$width	The width of the scaled image
	 * @param	string	$height	The height of the scaled image
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_scale_url( $bucket, $file, $width, $height )
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_scale_url( $bucket, $file, $width, $height );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_serve_url_scheme method
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_scale_url_scheme()
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_scale_url_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_placeholder_url method
	 *
	 * @access	static
	 * @param	int		$width	The width of the placeholder
	 * @param	int		$height	The height of the placeholder
	 * @param	int		border	The width of the border round the placeholder
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_placeholder_url( $width = 100, $height = 100, $border = 0 )
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_placeholder_url( $width, $height, $border );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_serve_url_scheme method
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_placeholder_url_scheme()
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_placeholder_url_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_blank_avatar_url method
	 *
	 * @access	static
	 * @param	int		$width	The width of the placeholder
	 * @param	int		$height	The height of the placeholder
	 * @param	mixed	$sex	What blank avatar to show
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_blank_avatar_url( $width = 100, $height = 100, $sex = 'male' )
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_blank_avatar_url( $width, $height, $sex );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's static cdn_serve_url_scheme method
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_blank_avatar_url_scheme()
	{
		$_class = self::_include_driver( strtolower( CDN_DRIVER ) );
		
		return $_class::cdn_blank_avatar_url_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Destructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __destruct()
	{
		
	}
}

/* End of file cdn.php */
/* Location: ./application/libraries/cdn.php */