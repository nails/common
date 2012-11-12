<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			CDN
*
* Created:		12/11/2012
* Modified:		12/11/2012
*
* Description:	A Library for dealing with content in the Local CDN
* 
*/

class Local_CDN {
	
	private $_ci;
	public $errors;
	
	
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
		$this->_ci		=& get_instance();
		$this->errors	= array();
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
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the upload method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function delete( $file, $bucket )
	{
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the upload method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function copy( $source, $file, $bucket, $options = array() )
	{
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the upload method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function move( $source, $file, $bucket, $options = array() )
	{
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the upload method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function replace( $file, $bucket, $replace_with, $options = array() )
	{
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
		return $this->errors;
	}
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Adds an error message
	 *
	 * @access	private
	 * @param	array	$message	The error message to add
	 * @return	void
	 * @author	Pablo
	 **/
	private function _error( $message )
	{
		$this->errors[] = $message;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/*	! URL GENERATOR METHODS */
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Generates the correct URL for serving up a file
	 *
	 * @access	static
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$file	The filename of the object
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_serve_url( $bucket, $file )
	{
		$_out  = 'cdn/serve/';
		$_out .= $bucket . '/';
		$_out .= $file;
		
		// --------------------------------------------------------------------------
		
		return site_url( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Generates the correct URL for using the thumb utility
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
		$_out  = 'cdn/thumb/';
		$_out .= $width . '/' . $height . '/';
		$_out .= $bucket . '/';
		$_out .= $file;
		
		// --------------------------------------------------------------------------
		
		return site_url( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Generates the correct URL for using the scale utility
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
		$_out  = 'cdn/scale/';
		$_out .= $width . '/' . $height . '/';
		$_out .= $bucket . '/';
		$_out .= $file;
		
		// --------------------------------------------------------------------------
		
		return site_url( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Generates the correct URL for using the placeholder utility
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
		$_out  = 'cdn/placeholder/';
		$_out .= $width . '/' . $height . '/' . $border;
		
		// --------------------------------------------------------------------------
		
		return site_url( $_out );
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

/* End of file logger.php */
/* Location: ./application/libraries/logger.php */