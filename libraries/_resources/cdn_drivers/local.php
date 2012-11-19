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
	 * Creates a new object
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function upload( $file, $bucket, $options = array(), $is_raw = FALSE )
	{
		//	Define variables we'll need
		$_data = array();
		
		// --------------------------------------------------------------------------
		
		//	Clear errors
		$this->errors = array();
		
		// --------------------------------------------------------------------------
		
		//	Fetch the contents of the file
		if ( ! $is_raw ) :
		
			//	Check file exists in $_FILES
			if ( ! isset( $_FILES[ $file ] ) || $_FILES[ $file ]['size'] == 0 ) :
			
				//	If it's not in $_FILES does that file exist on the file system?
				if ( ! file_exists( $file ) ) :
				
					$this->_error( 'You did not select a file to upload.' );
					return FALSE;
				
				else :
				
					$_file = $file;
				
				endif;
			
			else :
			
				$_file = $_FILES[ $file ]['tmp_name'];
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Specify the file specifics
			
			//	Content-type; using finfo because the $_FILES variable can't be trusted
			//	(uploads from Uploadify always report as application/octet-stream;
			//	stupid flash.
			
			$_data['content-type'] = Cdn::get_mime_type_from_file( $_file );
			
			//	Now set the actual file data
			$_data['file'] = $_file;
			
		else :
		
			//	We've been given a data stream, use that. If no content-type has been set
			//	then fall over - we need to know what we're dealing with
			
			if ( ! isset( $options['content-type'] ) ) :
			
				$this->_error( 'A Content-Type must be defined for data stream uploads.' );
				return FALSE;
			
			else :
			
				//	Write the file to the cache temporarily
				if ( is_writeable( APP_CACHE ) ) :
				
					$_cache_file = sha1( microtime() . rand( 0 ,999 ) . active_user( 'id' ) );
					$_fp = fopen( APP_CACHE . $_cache_file, 'w' );
					fwrite( $_fp, $file );
					fclose( $_fp );
					
					// --------------------------------------------------------------------------
					
					//	Specify the file specifics
					$_file					= APP_CACHE . $_cache_file;
					$_data['file']			= APP_CACHE . $_cache_file;
					$_data['content-type']	= $options['content-type'];
				
				else :
				
					$this->_error( 'Cache directory is not writeable.' );
					return FALSE;
				
				endif;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Is this an acceptable file? Check against the allowed_types array (if present)
		
		$_ext	= Cdn::get_ext_from_mimetype( $_data['content-type'] );	//	So other parts of this method can access $_ext;
		
		if ( isset( $options['allowed_types'] ) ) :
		
			$_types	= explode( '|', $options['allowed_types'] );
			
			// --------------------------------------------------------------------------
			
			//	Handle stupid bloody MS Office 'x' documents
			//	If the returned extension is doc, xls or ppt compare it to the uploaded
			//	extension but append an x, if they match then force the x version.
			//	Also override the mime type
			
			//	Makka sense? Hate M$.
			$_user_ext = substr( $_FILES[$file]['name'], strrpos( $_FILES[$file]['name'], '.' ) + 1 );
			
			switch ( $_ext ) :
			
				case 'doc' :
				
					if ( $_user_ext == 'docx' ) :
					
						$_ext = 'docx';
						$_data['content-type'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
					
					endif;
				
				break;
				
				case 'ppt' :
				
					if ( $_user_ext == 'pptx' ) :
					
						$_ext = 'pptx';
						$_data['content-type'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
					
					endif;
				
				break;
				
				case 'xls' :
				
					if ( $_user_ext == 'xlsx' ) :
					
						$_ext = 'xlsx';
						$_data['content-type'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
					
					endif;
				
				break;
			
			endswitch;
			
			// --------------------------------------------------------------------------
			
			if ( array_search( $_ext, $_types ) === FALSE ) :
			
				if ( count( $_types ) > 1 ) :
				
					array_splice( $_types, count( $_types ) - 1, 0, array( ' and ' ) );
					$_accepted = implode( ', .', $_types );
					$_accepted = str_replace( ', . and , ', ' and ', $_accepted );
					$this->_error( 'The file type is not allowed, accepted file types are: .' . $_accepted . '.' );
				
				else :
				
					$_accepted = implode( '', $_types );
					$this->_error( 'The file type is not allowed, accepted file type is .' . $_accepted . '.' );
				
				endif;
				
				return FALSE;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Is the file within the filesize limit?
		if ( isset( $options['max_size'] ) ) :
		
			if ( filesize( $_file ) > $options['max_size'] ) :
			
				$this->_error( 'The file is too large.' );
				return FALSE;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	What about dimension limits? Obviously this only applies to images.
		if ( isset( $options['dimensions'] ) ) :
		
			//	Fetch info about the file
			list( $w , $h ) = getimagesize( $_file );
			$error = FALSE;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['max_width'] ) ) :
			
				if ( $w > $options['dimensions']['max_width'] ) :
				
					$this->_error( 'Image is too wide (max ' . $options['dimensions']['max_width'] . 'px)' );
					$error = TRUE;
					
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['max_height'] ) ) :
			
				if ( $h > $options['dimensions']['max_height'] ) :
				
					$this->_error( 'Image is too tall (max ' . $options['dimensions']['max_height'] . 'px)' );
					$error = TRUE;
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['min_width'] ) ) :
			
				if ( $w < $options['dimensions']['min_width'] ) :
				
					$this->_error( 'Image is too narrow (min ' . $options['dimensions']['min_width'] . 'px)' );
					$error = TRUE;
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['min_height'] ) ) :
			
				if ( $h < $options['dimensions']['min_height'] ) :
				
					$this->_error( 'Image is too short (min ' . $options['dimensions']['min_height'] . 'px)' );
					$error = TRUE;
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			
			if ( $error )
				return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set the bucket
		$_data['bucket'] = $bucket;
		
		// --------------------------------------------------------------------------
		
		//	If a certain filename has been specified then send that to the CDN (this
		//	will overwrite any existing file so use with caution)
		
		if ( isset( $options['filename'] ) && $options['filename'] ) :
		
			$_data['filename'] = $options['filename'];
			
		else :
		
			//	Generate a filename
			$_data['filename'] = md5( active_user( 'id' ) . microtime( TRUE ) . rand( 0, 999 ) ) . '.' . $_ext;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Move the file
		if ( @move_uploaded_file( $_data['file'], CDN_PATH . $_data['bucket'] . '/' . $_data['filename'] ) ) :
		
			$_status = TRUE;
			
		else :
		
			$_status = FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If a cachefile was created then we should remove it
		if ( isset( $_cache_file ) && $_cache_file ) :
		
			@unlink( APP_CACHE . $_cache_file );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Return result
		return $_status ? $_data['filename'] : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Deletes an object
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function delete( $file, $bucket )
	{
		if ( file_exists( CDN_PATH . $bucket . '/' . $file ) ) :
		
			if ( @unlink( CDN_PATH . $bucket . '/' . $file ) ) :
			
				return TRUE;
			
			else :
			
				$this->_error( 'File failed to delete' );
				return FALSE;
			
			endif;
		
		else :
		
			$this->_error( 'No file to delete' );
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Copies one object from one bucket to another
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
	 * Moves an object from one bucket to another
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
	 * Uploads an object and, if successful, deletes the old object
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function replace( $file, $bucket, $replace_with, $options = array(), $is_raw = FALSE )
	{
		//	Firstly, attempt the upload
		$_filename = $this->upload( $replace_with, $bucket, $options, $is_raw );
		
		// --------------------------------------------------------------------------
		
		if ( $_filename ) :
		
			//	Upload was successfull, remove the old file
			if ( file_exists( CDN_PATH . $bucket . '/' . $file ) ) :
			
				//	Attempt the delete
				$this->delete( $file, $bucket );
			
			endif;
			
			// --------------------------------------------------------------------------
			
			return $_filename;
		
		else :
		
			return FALSE;
		
		endif;
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