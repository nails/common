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
	private $db;
	private $_errors;
	
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
		$this->db		=& get_instance()->db;
		$this->_errors	= array();

		// --------------------------------------------------------------------------

		//	Load langfile
		$this->_ci->lang->load( 'cdn/cdn', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------
		
		//	Load the helper
		$this->_ci->load->helper( 'cdn' );
		
		// --------------------------------------------------------------------------
		
		//	Load the storage driver
		$_class = $this->_include_driver( );
		$this->_cdn = new $_class( $options );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _include_driver()
	{
		switch ( strtolower( CDN_DRIVER ) ) :
		
			case 'local':
			default:
			
				include_once NAILS_PATH . 'libraries/_resources/cdn_drivers/local.php';
				return 'Local_CDN';
			
			break;
		
		endswitch;
	}


	// --------------------------------------------------------------------------


	public function __call( $method, $arguments )
	{
		//	Shortcut methods
		$_shortcuts		= array();
		$_shortcuts['upload'] = 'object_create';
		$_shortcuts['delete'] = 'object_delete';

		if ( isset( $_shortcuts[$method] ) ) :

			return call_user_func_array( array( $this, $_shortcuts[$method] ), $arguments );

		endif;

		throw new Exception( 'Call to undefined method Cdn::' . $method . '()' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/*	! OBJECT METHODS */
	
	
	// --------------------------------------------------------------------------


	public function get_objects()
	{
		$this->db->select( 'o.id, o.user_id, o.filename, o.filename_display, o.created, o.modified, o.serves, o.thumbs, o.scales' );
		$this->db->select( 'o.mime, o.filesize, o.img_width, o.img_height' );
		$this->db->select( 'u.email, u.first_name, u.last_name, u.profile_img, u.gender' );
		$this->db->select( 'b.id bucket_id, b.slug bucket_slug' );
		
		$this->db->join( 'user u', 'u.id = o.user_id', 'LEFT' );
		$this->db->join( 'cdn_bucket b', 'b.id = o.bucket_id', 'LEFT' );

		$this->db->order_by( 'o.filename_display' );
		
		$_objects = $this->db->get( 'cdn_object o' )->result();
		
		foreach ( $_objects AS $obj ) :

			//	Fetch any attachments
			$this->db->where( 'object_id', $obj->id );
			$obj->attachments = $this->db->get( 'cdn_object_attachment' )->result();

			// --------------------------------------------------------------------------

			//	Format the object, make it pretty
			$this->_format_object( $obj );

		endforeach;

		return $_objects;
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns a single object object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function get_object( $object, $bucket = NULL )
	{
		if ( is_numeric( $object ) ) :
		
			$this->db->where( 'o.id', $object );
		
		else :
		
			$this->db->where( 'o.filename', $object );
			
			if ( $bucket ) :
			
				if ( is_numeric( $bucket ) ) :
				
					$this->db->where( 'b.id', $bucket );
				
				else :
				
					$this->db->where( 'b.slug', $bucket );
				
				endif;
			
			endif;
		
		endif;

		$_objects = $this->get_objects();

		if ( ! $_objects )
			return FALSE;

		return $_objects[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns objects uploaded by the user
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function get_objects_for_user( $user_id )
	{
		$this->db->where( 'o.user_id', $user_id );
		return $this->get_objects();
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
	public function object_create( $object, $bucket, $options = array(), $is_raw = FALSE )
	{
		//	Define variables we'll need
		$_data = new stdClass();
		
		// --------------------------------------------------------------------------
		
		//	Clear errors
		$this->errors = array();
		
		// --------------------------------------------------------------------------
		
		//	Fetch the contents of the file
		if ( ! $is_raw ) :
		
			//	Check file exists in $_FILES
			if ( ! isset( $_FILES[ $object ] ) || $_FILES[ $object ]['size'] == 0 ) :
			
				//	If it's not in $_FILES does that file exist on the file system?
				if ( ! file_exists( $object ) ) :
				
					$this->cdn->set_error( lang( 'cdn_error_no_file' ) );
					return FALSE;
				
				else :
				
					$_file	= $object;
					$_name	= $object;
				
				endif;
			
			else :
			
				$_file	= $_FILES[ $object ]['tmp_name'];
				$_name	= $_FILES[ $object ]['name'];
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Specify the file specifics
			
			//	Content-type; using finfo because the $_FILES variable can't be trusted
			//	(uploads from Uploadify always report as application/octet-stream;
			//	stupid flash.
			
			$_data->mime = $this->get_mime_type_from_file( $_file );
			
			//	Now set the actual file data
			$_data->file = $_file;
			
		else :
		
			//	We've been given a data stream, use that. If no content-type has been set
			//	then fall over - we need to know what we're dealing with
			
			if ( ! isset( $options['content-type'] ) ) :
			
				$this->cdn->set_error( lang( 'cdn_stream_content_type' ) );
				return FALSE;
			
			else :
			
				//	Write the file to the cache temporarily
				if ( is_writeable( APP_CACHE ) ) :
				
					$_cache_file = sha1( microtime() . rand( 0 ,999 ) . active_user( 'id' ) );
					$_fp = fopen( APP_CACHE . $_cache_file, 'w' );
					fwrite( $_fp, $object );
					fclose( $_fp );
					
					// --------------------------------------------------------------------------
					
					//	Specify the file specifics
					$_file			= APP_CACHE . $_cache_file;
					$_name			= $_cache_file;
					$_data->file	= APP_CACHE . $_cache_file;
					$_data->mime	= $options['content-type'];
				
				else :
				
					$this->cdn->set_error( lang( 'cdn_error_cache_write_fail' ) );
					return FALSE;
				
				endif;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------

		//	Test and set the bucket, if it doesn't exist, create it
		$_bucket = $this->get_bucket( $bucket );
		
		if ( ! $_bucket ) :
		
			if ( $this->bucket_create( $bucket ) ) :
			
				$_bucket = $this->get_bucket( $bucket );
				
				$_data->bucket_id	= $_bucket->id;
				$_data->bucket_slug	= $bucket;
			
			else :
			
				return FALSE;
			
			endif;
			
		else :
		
			$_data->bucket_id	= $_bucket->id;
			$_data->bucket_slug	= $bucket;
		
		endif;

		// --------------------------------------------------------------------------
		
		//	Does the user have permission to write to the bucket?

		if ( ! get_userobject()->is_admin() && $_bucket->user_id && $_bucket->user_id != active_user( 'id' ) ) :
		
			$this->cdn->set_error( lang( 'cdn_error_bucket_nopermission' ) );
			return FALSE;
		
		endif;

		// --------------------------------------------------------------------------
		
		//	Is this an acceptable file? Check against the allowed_types array (if present)
		
		$_ext	= $this->get_ext_from_mimetype( $_data->mime );	//	So other parts of this method can access $_ext;
		
		if ( $_bucket->allowed_types && $_bucket->allowed_types != '*' ) :
		
			$_types	= explode( '|', $_bucket->allowed_types );
			
			// --------------------------------------------------------------------------
			
			//	Handle stupid bloody MS Office 'x' documents
			//	If the returned extension is doc, xls or ppt compare it to the uploaded
			//	extension but append an x, if they match then force the x version.
			//	Also override the mime type
			
			//	Makka sense? Hate M$.
			$_user_ext = substr( $_FILES[$object]['name'], strrpos( $_FILES[$object]['name'], '.' ) + 1 );
			
			switch ( $_ext ) :
			
				case 'doc' :
				
					if ( $_user_ext == 'docx' ) :
					
						$_ext			= 'docx';
						$_data->mime	= 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
					
					endif;
				
				break;
				
				case 'ppt' :
				
					if ( $_user_ext == 'pptx' ) :
					
						$_ext			= 'pptx';
						$_data->mime	= 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
					
					endif;
				
				break;
				
				case 'xls' :
				
					if ( $_user_ext == 'xlsx' ) :
					
						$_ext			= 'xlsx';
						$_data->mime	= 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
					
					endif;
				
				break;
			
			endswitch;
			
			// --------------------------------------------------------------------------
			
			if ( array_search( $_ext, $_types ) === FALSE ) :
			
				if ( count( $_types ) > 1 ) :
				
					array_splice( $_types, count( $_types ) - 1, 0, array( ' and ' ) );
					$_accepted = implode( ', .', $_types );
					$_accepted = str_replace( ', . and , ', ' and ', $_accepted );
					$this->cdn->set_error(  lang( 'cdn_error_bad_mime_plural', $_accepted ) );
				
				else :
				
					$_accepted = implode( '', $_types );
					$this->cdn->set_error(  lang( 'cdn_error_bad_mime', $_accepted ) );
				
				endif;
				
				return FALSE;
			
			endif;

		endif;
		
		// --------------------------------------------------------------------------

		//	Is the file within the filesize limit?
		$_data->filesize = filesize( $_data->file );
		
		if ( $_bucket->max_size ) :
		
			if ( $_data->filesize > $_bucket->max_size ) :
			
				$_fs_in_kb = format_bytes( $_bucket->max_size );
				$this->cdn->set_error( lang( 'cdn_error_filesize', $_fs_in_kb ) );
				return FALSE;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------

		//	Is the object an image?
		$_images	= array();
		$_images[]	= 'image/jpg';
		$_images[]	= 'image/jpeg';
		$_images[]	= 'image/png';
		$_images[]	= 'image/gif';

		if ( array_search( $_data->mime, $_images ) !== FALSE ) :

			list( $_w, $_h ) = @getimagesize( $_file );

			$_data->img					= new stdClass();
			$_data->img->width			= $_w;
			$_data->img->height			= $_h;
			$_data->img->is_animated	= NULL;

			// --------------------------------------------------------------------------

			if ( $_data->mime == 'image/gif' ) :

				//	Detect animated gif
				$_data->img->is_animated = $this->_detect_animated_gif( $_data->file );

			endif;

		endif;

		// --------------------------------------------------------------------------
		
		//	What about dimension limits? Obviously this only applies to images.
		if ( isset( $_data->img ) && isset( $options['dimensions'] ) ) :
		
			//	Fetch info about the file
			$error = FALSE;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['max_width'] ) ) :
			
				if ( $_data->img->width > $options['dimensions']['max_width'] ) :
				
					$this->cdn->set_error( lang( 'cdn_error_maxwidth', $options['dimensions']['max_width'] ) );
					$error = TRUE;
					
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['max_height'] ) ) :
			
				if ( $_data->img->height > $options['dimensions']['max_height'] ) :
				
					$this->cdn->set_error( lang( 'cdn_error_maxheight', $options['dimensions']['max_height'] ) );
					$error = TRUE;
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['min_width'] ) ) :
			
				if ( $_data->img->width < $options['dimensions']['min_width'] ) :
				
					$this->cdn->set_error( lang( 'cdn_error_minwidth', $options['dimensions']['min_width'] ) );
					$error = TRUE;
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['min_height'] ) ) :
			
				if ( $_data->img->height < $options['dimensions']['min_height'] ) :
				
					$this->cdn->set_error( lang( 'cdn_error_minheight', $options['dimensions']['min_height'] ) );
					$error = TRUE;
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( $error ) :
			
				return FALSE;
				
			endif;
		
		endif;

		// --------------------------------------------------------------------------

		//	Has a tag been defined?
		if ( isset( $options['tag'] ) ) :
		
			$_data->tag_id = $options['tag'];
		
		endif;

		// --------------------------------------------------------------------------

		//	If a certain filename has been specified then send that to the CDN (this
		//	will overwrite any existing file so use with caution)
		
		if ( isset( $options['filename'] ) && $options['filename'] == 'USE_ORIGINAL' ) :
		
			$_data->filename =  $_FILES[$object]['name'];
			
		elseif ( isset( $options['filename'] ) && $options['filename'] ) :
		
			$_data->filename = $options['filename'];
			
		else :
		
			//	Generate a filename
			$_data->filename = time() . '_' . md5( active_user( 'id' ) . microtime( TRUE ) . rand( 0, 999 ) ) . '.' . $_ext;
		
		endif;
		
		//	And set the display name
		$_data->name	= $_name;
		
		// --------------------------------------------------------------------------

		$_upload = $this->_cdn->upload( $_data->bucket_slug, $_data->file, $_data->filename );

		// --------------------------------------------------------------------------

		if ( $_upload ) :

			$_object = $this->_create_object( $_data, TRUE );

			if ( $_object ) :

				$_status = $_object;

				// --------------------------------------------------------------------------

				//	Any atatchments to add?
				if ( isset( $options['attachment'] ) && $options['attachment'] ) :

					$_label	= isset( $options['attachment']->label ) ? $options['attachment']->label : NULL;
					$_table	= isset( $options['attachment']->table ) ? $options['attachment']->table : NULL;
					$_col	= isset( $options['attachment']->col ) ? $options['attachment']->col : NULL;
					$_id	= isset( $options['attachment']->id ) ? $options['attachment']->id : NULL;

					$this->object_attachment_add( $_object->id, $_label, $_table, $_col, $_id );

				endif;

			else :

				$this->_cdn->destroy( $_data->filename, $_data->bucket_slug );

				$_status = FALSE;

			endif;

		else :

			$_status = FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	If a cachefile was created then we should remove it
		if ( isset( $_cache_file ) && $_cache_file ) :
		
			@unlink( APP_CACHE . $_cache_file );
			
		endif;

		// --------------------------------------------------------------------------

		return $_status;
	}


	// --------------------------------------------------------------------------


	public function object_attachment_add( $object_id, $data )
	{
		if ( ! $object_id ) :
		
			$this->set_error( lang( 'cdn_error_object_invalid' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_object = $this->get_object( $object_id );
		
		if ( ! $_object ) :
		
			$this->set_error( lang( 'cdn_error_object_invalid' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_data						= array();
		$_data['object_id']			= $_object->id;
		$_data['label']				= isset( $data['label'] ) ? $data['label'] : NULL;
		$_data['table']				= isset( $data['table'] ) ? $data['table'] : NULL;
		$_data['column']			= isset( $data['column'] ) ? $data['column'] : NULL;
		$_data['attached_to_id']	= isset( $data['attached_to_id'] ) ? $data['attached_to_id'] : NULL;
		$_data['select_cols']		= isset( $data['select_cols'] ) ? $data['select_cols'] : NULL;
		$_data['select_table']		= isset( $data['select_table'] ) ? $data['select_table'] : NULL;
		$_data['select_id_col']		= isset( $data['select_id_col'] ) ? $data['select_id_col'] : NULL;

		$this->db->set( $_data );
		$this->db->set( 'created',			'NOW()', FALSE );

		if ( active_user( 'id' ) ) :

			$this->db->set( 'created_by',		active_user( 'id' ) );

		endif;

		$this->db->insert( 'cdn_object_attachment' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function object_attachment_delete( $object_id, $data )
	{
		$_data						= array();
		$_data['object_id']			= $_object_id;
		$_data['label']				= isset( $data['label'] ) ? $data['label'] : NULL;
		$_data['table']				= isset( $data['table'] ) ? $data['table'] : NULL;
		$_data['column']			= isset( $data['column'] ) ? $data['column'] : NULL;
		$_data['attached_to_id']	= isset( $data['attached_to_id'] ) ? $data['attached_to_id'] : NULL;
		$_data['select_cols']		= isset( $data['select_cols'] ) ? $data['select_cols'] : NULL;
		$_data['select_table']		= isset( $data['select_table'] ) ? $data['select_table'] : NULL;
		$_data['select_id_col']		= isset( $data['select_id_col'] ) ? $data['select_id_col'] : NULL;

		$this->db->where( $_data );

		$this->db->delete( 'cdn_object_attachment' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function object_attachment_purge($data )
	{
		$_data						= array();
		$_data['label']				= isset( $data['label'] ) ? $data['label'] : NULL;
		$_data['table']				= isset( $data['table'] ) ? $data['table'] : NULL;
		$_data['column']			= isset( $data['column'] ) ? $data['column'] : NULL;
		$_data['attached_to_id']	= isset( $data['attached_to_id'] ) ? $data['attached_to_id'] : NULL;
		$_data['select_cols']		= isset( $data['select_cols'] ) ? $data['select_cols'] : NULL;
		$_data['select_table']		= isset( $data['select_table'] ) ? $data['select_table'] : NULL;
		$_data['select_id_col']		= isset( $data['select_id_col'] ) ? $data['select_id_col'] : NULL;

		$this->db->where( $_data );

		$this->db->delete( 'cdn_object_attachment' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function object_attachment_exists( $object_id, $data )
	{
		$_data						= array();
		$_data['object_id']			= $_object_id;
		$_data['label']				= isset( $data['label'] ) ? $data['label'] : NULL;
		$_data['table']				= isset( $data['table'] ) ? $data['table'] : NULL;
		$_data['column']			= isset( $data['column'] ) ? $data['column'] : NULL;
		$_data['attached_to_id']	= isset( $data['attached_to_id'] ) ? $data['attached_to_id'] : NULL;
		$_data['select_cols']		= isset( $data['select_cols'] ) ? $data['select_cols'] : NULL;
		$_data['select_table']		= isset( $data['select_table'] ) ? $data['select_table'] : NULL;
		$_data['select_id_col']		= isset( $data['select_id_col'] ) ? $data['select_id_col'] : NULL;

		$this->db->where( $_data );

		return (bool) $this->db->count_all_results( 'cdn_object_attachment' );
	}


	// --------------------------------------------------------------------------


	public function object_attachment_repoint( $new_object, $data)
	{
		$this->db->set( 'object_id', $new_object );

		$_data						= array();
		$_data['label']				= isset( $data['label'] ) ? $data['label'] : NULL;
		$_data['table']				= isset( $data['table'] ) ? $data['table'] : NULL;
		$_data['column']			= isset( $data['column'] ) ? $data['column'] : NULL;
		$_data['attached_to_id']	= isset( $data['attached_to_id'] ) ? $data['attached_to_id'] : NULL;
		$_data['select_cols']		= isset( $data['select_cols'] ) ? $data['select_cols'] : NULL;
		$_data['select_table']		= isset( $data['select_table'] ) ? $data['select_table'] : NULL;
		$_data['select_id_col']		= isset( $data['select_id_col'] ) ? $data['select_id_col'] : NULL;

		$this->db->where( $_data );

		$this->db->update( 'cdn_object_attachment' );

		return (bool) $this->db->affected_rows();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Permenantly deletes an object
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function object_delete( $object, $bucket )
	{
		if ( ! $object ) :
		
			$this->set_error( lang( 'cdn_error_object_invalid' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_object = $this->get_object( $object );
		
		if ( ! $_object ) :
		
			$this->set_error( lang( 'cdn_error_object_invalid' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Can the user modify the bucket/objects? Admins always can but if the bucket
		//	has a user then the current user must be the owner
		
		if ( ! get_userobject()->is_admin() && $_object->user->id && $_object->user->id != active_user( 'id' ) ) :
		
			$this->set_error( lang( 'cdn_error_object_nopermission' ) );
			return FALSE;
		
		endif;

		// --------------------------------------------------------------------------

		if ( $this->_cdn->delete( $_object->filename, $bucket ) ) :

			//	Remove the database entry
			$this->db->where( 'id', $_object->id );
			$this->db->delete( 'cdn_object' );

			return TRUE;

		else :

			return FALSE;

		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Copies an object from one bucket to another
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function object_copy( $source, $object, $bucket, $options = array() )
	{
		//	TODO: Copy object between buckets
		return FALSE;
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
	public function object_move( $source, $object, $bucket, $options = array() )
	{
		//	TODO: Move object between buckets
		return FALSE;
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
	public function object_replace( $object, $bucket, $replace_with, $options = array(), $is_raw = FALSE )
	{
		//	Firstly, attempt the upload
		$_upload = $this->object_create( $replace_with, $bucket, $options, $is_raw );
		
		// --------------------------------------------------------------------------
		
		if ( $_upload ) :
		
			//	Update any existing attachments to point to this new object
			$_object = $this->get_object( $object );

			if ( $_object ) :

				$this->db->set( 'object_id', $_upload->id );
				$this->db->where( 'object_id', $_object->id );
				$this->db->update( 'cdn_object_attachment' );

				// --------------------------------------------------------------------------

				//	Attempt the delete
				$this->delete( $_object->id, $bucket );

			endif;
			
			// --------------------------------------------------------------------------
			
			return $_upload;
		
		else :
		
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Adds a tag to an object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function object_tag_add( $object_id, $tag_id )
	{
		//	Valid object?
		$_object = $this->get_object( $object_id );
		
		if ( ! $_object ) :
		
			$this->set_error( lang( 'cdn_error_object_invalid' ) );
			return FALSE;
		
		endif;
		
		
		// --------------------------------------------------------------------------
		
		//	Valid tag?
		$this->db->select( 't.*, b.user_id' );
		$this->db->join( 'cdn_bucket b', 'b.id = t.bucket_id', 'LEFT' );
		
		$this->db->where( 't.id', $tag_id );
		
		$_tag = $this->db->get( 'cdn_bucket_tag t' )->row();
		
		if ( ! $_tag ) :
		
			$this->set_error( lang( 'cdn_error_tag_invalid' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Can the user modify the bucket/objects? Admins always can but if the bucket
		//	has a user then the current user must be the owner
		
		if ( ! get_userobject()->is_admin() && $_tag->user_id && $_tag->user_id != active_user( 'id' ) ) :
		
			$this->set_error( lang( 'cdn_error_bucket_nopermission' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Test if tag has already been applied to the object, if it has gracefully fail
		$this->db->where( 'object_id', $_object->id );
		$this->db->where( 'tag_id', $_tag->id );
		if ( $this->db->count_all_results( 'cdn_object_tag' ) ) :
		
			return TRUE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Seems good, add the tag
		$this->db->set( 'object_id', $_object->id );
		$this->db->set( 'tag_id', $_tag->id );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->insert( 'cdn_object_tag' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Deletes a tag from an object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function object_tag_delete( $object_id, $tag_id )
	{
		//	Valid object?
		$_object = $this->get_object( $object_id );
		
		if ( ! $_object ) :
		
			$this->set_error( lang( 'cdn_error_object_invalid' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Valid tag?
		$this->db->select( 't.*, b.user_id' );
		$this->db->join( 'cdn_bucket b', 'b.id = t.bucket_id', 'LEFT' );
		
		$this->db->where( 't.id', $tag_id );
		
		$_tag = $this->db->get( 'cdn_bucket_tag t' )->row();
		
		if ( ! $_tag ) :
		
			$this->set_error( lang( 'cdn_error_tag_invalid' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Can the user modify the bucket/objects? Admins always can but if the bucket
		//	has a user then the current user must be the owner
		
		if ( ! get_userobject()->is_admin() && $_tag->user_id && $_tag->user_id != active_user( 'id' ) ) :
		
			$this->set_error( lang( 'cdn_error_bucket_nopermission' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Seems good, delete the tag
		$this->db->where( 'object_id', $_object->id );
		$this->db->where( 'tag_id', $_tag->id );
		$this->db->delete( 'cdn_object_tag' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Counts the number of objects a tag contains
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function object_tag_count( $tag_id )
	{
		$this->db->where( 'ot.tag_id', $tag_id );
		$this->db->join( 'cdn_object o', 'o.id = ot.object_id' );
		return $this->db->count_all_results( 'cdn_object_tag ot' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Increments the stats of the object
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function object_increment_count( $action, $object, $bucket = NULL )
	{
		switch ( strtoupper( $action ) ) :
		
			case 'SERVE'	:

				$this->db->set( 'o.serves', 'o.serves+1', FALSE );

			break;

			// --------------------------------------------------------------------------

			case 'DOWNLOAD'	:

				$this->db->set( 'o.downloads', 'o.downloads+1', FALSE );

			break;

			// --------------------------------------------------------------------------

			case 'THUMB' :

				$this->db->set( 'o.thumbs', 'o.thumbs+1', FALSE );

			break;

			// --------------------------------------------------------------------------

			case 'SCALE' :

				$this->db->set( 'o.scales', 'o.scales+1', FALSE );

			break;

		endswitch;

		if ( is_numeric( $object ) ) :

			$this->db->where( 'o.id', $object );

		else :

			$this->db->where( 'o.filename', $object );

		endif;

		if ( $bucket && is_numeric( $bucket ) ) :

			$this->db->where( 'o.bucket_id', $bucket );
			$this->db->update( 'cdn_object o' );

		elseif ( $bucket ) :

			$this->db->where( 'b.slug', $bucket );
			$this->db->update( 'cdn_object o JOIN cdn_bucket b ON b.id = o.bucket_id' );

		else :

			$this->db->update( 'cdn_object o' );

		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/*	! BUCKET METHODS */
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Returns an array of all bucket objects
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function get_buckets( $list_bucket = FALSE, $filter_tag = FALSE, $include_deleted = FALSE )
	{
		$this->db->select( 'id,slug,user_id,allowed_types,max_size,created,modified' );
		$this->db->select( '(SELECT COUNT(*) FROM cdn_object WHERE bucket_id = b.id) object_count' );
		
		$_buckets = $this->db->get( 'cdn_bucket b' )->result();
		
		// --------------------------------------------------------------------------
		
		foreach ( $_buckets AS &$bucket ) :
		
			//	Format bucket object
			$bucket->id			= (int) $bucket->id;
			$bucket->user_id	= (int) $bucket->user_id;
			
			// --------------------------------------------------------------------------
			
			//	List contents
			if ( $list_bucket ) :
			
				$bucket->objects = $this->bucket_list( $bucket->id, $filter_tag, $include_deleted );
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Fetch tags & counts
			$this->db->select( 'bt.id,bt.label,bt.created' );
			$this->db->select( '(SELECT COUNT(*) FROM cdn_object_tag ot JOIN cdn_object o ON o.id = ot.object_id WHERE tag_id = bt.id ) total' );
			$this->db->order_by( 'bt.label' );
			$this->db->where( 'bt.bucket_id', $bucket->id );
			$bucket->tags = $this->db->get( 'cdn_bucket_tag bt' )->result();
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_buckets;
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns a single bucket object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function get_bucket( $bucket, $list_bucket = FALSE, $filter_tag = FALSE )
	{
		if ( is_numeric( $bucket ) ) :
		
			$this->db->where( 'b.id', $bucket );
		
		else :
		
			$this->db->where( 'b.slug', $bucket );
		
		endif;

		$_bucket = $this->get_buckets( $list_bucket, $filter_tag, TRUE );

		if ( ! $_bucket )
			return FALSE;

		return $_bucket[0];
	}

	
	// --------------------------------------------------------------------------


	/**
	 * Creates a new bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function bucket_create( $bucket )
	{
		//	Test if bucket exists, if it does stop, job done.
		$_bucket = $this->get_bucket( $bucket );
		
		if ( $_bucket ) :
		
			return $_bucket->id;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_bucket = $this->_cdn->bucket_create( $bucket );

		if ( $_bucket ) :

			$this->db->set( 'slug', $bucket );
			$this->db->set( 'created', 'NOW()', FALSE );
			$this->db->set( 'modified', 'NOW()', FALSE );
			
			if ( active_user( 'id' ) ) :
			
				$this->db->set( 'user_id', active_user( 'id' ) );
			
			endif;
			
			$this->db->insert( 'cdn_bucket' );
			
			if ( $this->db->affected_rows() ) :
				
				return $this->db->insert_id();
			
			else :
			
				$this->_cdn->destroy( $bucket );
				
				$this->cdn->set_error( lang( 'cdn_error_bucket_insert' ) );
				return FALSE;
			
			endif;

		else :

			return FALSE;

		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Lists the contents of a bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function bucket_list( $bucket, $filter_tag = FALSE )
	{
		//	Filtering by tag?
		if ( $filter_tag ) :
		
			$this->db->join( 'cdn_object_tag ft', 'ft.object_id = o.id AND ft.tag_id = ' . $filter_tag );
		
		endif;

		// --------------------------------------------------------------------------

		//	Filter by bucket
		if ( is_numeric( $bucket ) ) :
		
			$this->db->where( 'b.id', $bucket );
		
		else :
		
			$this->db->where( 'b.slug', $bucket );
		
		endif;

		// --------------------------------------------------------------------------

		return $this->get_objects();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Permenantly delete a bucket and its contents
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function bucket_delete( $bucket )
	{
		//	TODO: Delete a bucket and its contents
		return FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Adds a tag to a bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function bucket_tag_add( $bucket, $label )
	{
		$label = trim( $label );
		
		if ( ! $label ) :
		
			$this->set_error( lang( 'cdn_error_tag_invalid' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Test bucket
		$_bucket = $this->get_bucket( $bucket );
		
		if ( ! $_bucket ) :
		
			$this->set_error( lang( 'cdn_error_bucket_invalid' ) );
			return FALSE;
		
		endif;
		
		//	If the bucket has an owner/user then only the owner user can add tags to the bucket
		//	Administrators can add to any bucket
		
		if ( ! get_userobject()->is_admin() && $_bucket->user_id && $bucket->user_id != active_user( 'id' ) ) :
		
			$this->set_error( lang( 'cdn_error_bucket_nopermission' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Test tag
		$this->db->where( 'bucket_id', $_bucket->id );
		$this->db->where( 'label', $label );
		if ( $this->db->count_all_results( 'cdn_bucket_tag' ) ) :
		
			$this->set_error( lang( 'cdn_error_tag_exists' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Seems good, add the tag
		$this->db->set( 'bucket_id', $_bucket->id );
		$this->db->set( 'label', $label );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->insert( 'cdn_bucket_tag' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Deletes a tag from a bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function bucket_tag_delete( $bucket, $label )
	{
		//	Test bucket
		$_bucket = $this->get_bucket( $bucket );
		
		if ( ! $_bucket ) :
		
			$this->set_error( lang( 'cdn_error_bucket_invalid' ) );
			return FALSE;
		
		endif;
		
		//	If the bucket has an owner/user then only the owner user can delete tags from the bucket
		//	Administrators can add to any bucket
		
		if ( ! get_userobject()->is_admin() && $_bucket->user_id && $bucket->user_id != active_user( 'id' ) ) :
		
			$this->set_error( lang( 'cdn_error_bucket_nopermission' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Test tag
		$this->db->where( 'bucket_id', $_bucket->id );
		
		if ( is_numeric( $label ) ) :
		
			$this->db->where( 'id', $label );
			
		else :
		
			$this->db->where( 'label', $label );
		
		endif;
		
		
		if ( ! $this->db->count_all_results( 'cdn_bucket_tag' ) ) :
		
			$this->set_error( lang( 'cdn_error_tag_notexist' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Seems good, delete the tag
		$this->db->where( 'bucket_id', $_bucket->id );
		
		if ( is_numeric( $label ) ) :
		
			$this->db->where( 'id', $label );
			
		else :
		
			$this->db->where( 'label', $label );
		
		endif;
		
		$this->db->delete( 'cdn_bucket_tag' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Renames a bucket tag
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function bucket_tag_rename( $bucket, $label, $new_name )
	{
		//	TODO: Rename a bucket tag
		return FALSE;
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
		return $this->_errors;
	}


	// --------------------------------------------------------------------------


	/**
	 * Adds an error message
	 *
	 * @access	public
	 * @param	array	$message	The error message to add
	 * @return	void
	 * @author	Pablo
	 **/
	public function set_error( $message )
	{
		$this->_errors[] = $message;
	}


	// --------------------------------------------------------------------------


	private function _create_object( $data, $return_object = FALSE )
	{
		$this->db->set( 'bucket_id',		$data->bucket_id );
		$this->db->set( 'filename',			$data->filename );
		$this->db->set( 'filename_display',	$data->name );
		$this->db->set( 'mime',				$data->mime );
		$this->db->set( 'filesize',			$data->filesize );
		$this->db->set( 'created',			'NOW()', FALSE );
		$this->db->set( 'modified',			'NOW()', FALSE );
		
		if ( active_user( 'id' ) ) :
		
			$this->db->set( 'user_id',		active_user( 'id' ) );
		
		endif;
		
		// --------------------------------------------------------------------------

		if ( isset( $data->img->width ) && isset( $data->img->height ) ) :
				
			$this->db->set( 'img_width',	$data->img->height );
			$this->db->set( 'img_height',	$data->img->width );
			
		endif;

		// --------------------------------------------------------------------------

		//	Check whether file is animated gif
		if ( $data->mime == 'image/gif' ) :

			if ( isset( $data->img->is_animated ) ) :

				$this->db->set( 'is_animated', $data->img->is_animated );

			else :

				$this->db->set( 'is_animated', FALSE );

			endif;

		endif;
		
		// --------------------------------------------------------------------------
		
		$this->db->insert( 'cdn_object' );

		$_object_id = $this->db->insert_id();
		
		if ( $this->db->affected_rows() ) :
		
			//	Add a tag if there's one defined
			if ( isset( $data->tag_id ) && ! empty( $data->tag_id ) ) :
			
				$this->db->where( 'id', $data->tag_id );
				
				if ( $this->db->count_all_results( 'cdn_bucket_tag' ) ) :
				
					$this->db->set( 'object_id',	$_object_id );
					$this->db->set( 'tag_id',		$data->tag_id );
					$this->db->set( 'created',		'NOW()', FALSE );

					$this->db->insert( 'cdn_object_tag' );
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------

			if ( $return_object ) :

				return $this->get_object( $_object_id );

			else :

				return $_object_id;

			endif;
			
		else :
		
			return FALSE;
			
		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Formats an object
	 *
	 * @access	private
	 * @param	array	$message	The error message to add
	 * @return	void
	 * @author	Pablo
	 **/
	private function _format_object( &$object )
	{
		$object->id			= (int) $object->id;
		$object->filesize	= (int) $object->filesize;
		$object->img_width	= (int) $object->img_width;
		$object->img_height	= (int) $object->img_height;
		$object->serves		= (int) $object->serves;
		$object->thumbs		= (int) $object->thumbs;
		$object->scales		= (int) $object->scales;
		
		// --------------------------------------------------------------------------
		
		$object->user				= new stdClass();
		$object->user->id			= $object->user_id;
		$object->user->first_name	= $object->first_name;
		$object->user->last_name	= $object->last_name;
		$object->user->email		= $object->email;
		$object->user->profile_img	= $object->profile_img;
		$object->user->gender		= $object->gender;
		
		unset( $object->user_id );
		unset( $object->first_name );
		unset( $object->last_name );
		unset( $object->email );
		unset( $object->profile_img );
		unset( $object->gender );

		// --------------------------------------------------------------------------

		$object->bucket			= new stdClass();
		$object->bucket->id		= $object->bucket_id;
		$object->bucket->slug	= $object->bucket_slug;

		unset( $object->bucket_id );
		unset( $object->bucket_slug );

		// --------------------------------------------------------------------------

		foreach ( $object->attachments AS $attachment ) :

			$attachment->id				= (int) $attachment->id;
			$attachment->attached_to_id	= (int) $attachment->attached_to_id;

			unset( $attachment->object_id );
			unset( $attachment->created );
			unset( $attachment->created_by );

		endforeach;


		// --------------------------------------------------------------------------

		//	Quick flag for detecting images
		$object->is_img = FALSE;

		switch( $object->mime ) :

			case 'image/jpg' :
			case 'image/jpeg' :
			case 'image/gif' :
			case 'image/png' :

				$object->is_img = TRUE;

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Attempts to detect whether a gif is animated or not
	 * Credit where credit's due: http://php.net/manual/en/function.imagecreatefromgif.php#59787
	 *
	 * @access	private
	 * @param	string $file the path to the file to check
	 * @return	boolean
	 * @author	Pablo
	 **/
	private function _detect_animated_gif( $file )
	{
		$filecontents=file_get_contents($file);

		$str_loc=0;
		$count=0;
		while ($count < 2) # There is no point in continuing after we find a 2nd frame
		{

			$where1=strpos($filecontents,"\x00\x21\xF9\x04",$str_loc);
			if ($where1 === FALSE)
			{
				break;
			}
			else
			{
				$str_loc=$where1+1;
				$where2=strpos($filecontents,"\x00\x2C",$str_loc);
				if ($where2 === FALSE)
				{
					break;
				}
				else
				{
					if ($where1+8 == $where2)
					{
						$count++;
					}
					$str_loc=$where2+1;
				}
			}
		}

		if ($count > 1)
		{
			return(true);
		}
		else
		{
			return(false);
		}
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Returns the error array
	 *
	 * @access	public
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_ext_from_mimetype( $mime_type )
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
	
	
	public function get_mimetype_from_ext( $ext )
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
	
	
	public function get_mime_type_from_file( $object )
	{
		$_fi = finfo_open( FILEINFO_MIME_TYPE );
		
		//	Use normal magic
		$_result = finfo_file( $_fi, $object );
		
		//	If normal magic responds with a ZIP, use specific magic to test if it's
		//	an office doc - doing this because Jon T told us that specifying the file
		//	to use might cause the funciton to 'forget' it's other magic, so using
		//	defaults first and falling back to this.
		
		if ( $_result == 'application/zip' ) :
		
			$_fi = @finfo_open( FILEINFO_MIME_TYPE, CDN_MAGIC );

			if ( $_fi ) :

				$_result	= finfo_file( $_fi, $object );

			endif;
			
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
	 * Calls the driver's public cdn_serve_url method
	 *
	 * @access	public
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$object	The filename of the object
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_serve( $object )
	{
		$_object = $this->get_object( $object );

		if ( ! $_object ) :

			//	Let the renderer show a bad_src graphic
			$_object				= new stdClass();
			$_object->filename		= '';
			$_object->bucket		= new stdClass();
			$_object->bucket->slug	= '';

		endif;

		return $this->_cdn->url_serve( $_object->filename, $_object->bucket->slug );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_serve_url_scheme method
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_serve_scheme()
	{
		return $this->_cdn->url_serve_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_thumb_url method
	 *
	 * @access	public
	 * @param	string	$object	The filename of the image we're 'thumbing'
	 * @param	string	$width	The width of the thumbnail
	 * @param	string	$height	The height of the thumbnail
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_thumb( $object, $width, $height )
	{
		$_object = $this->get_object( $object );

		if ( ! $_object ) :

			//	Let the renderer show a bad_src graphic
			$_object				= new stdClass();
			$_object->filename		= '';
			$_object->bucket		= new stdClass();
			$_object->bucket->slug	= '';

		endif;

		return $this->_cdn->url_thumb( $_object->filename, $_object->bucket->slug, $width, $height );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_thumb_url_scheme method
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_thumb_scheme()
	{
		return $this->_cdn->url_thumb_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_thumb_url method
	 *
	 * @access	public
	 * @param	string	$bucket	The bucket which the image resides in
	 * @param	string	$object	The filename of the image we're 'scaling'
	 * @param	string	$width	The width of the scaled image
	 * @param	string	$height	The height of the scaled image
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_scale( $object, $width, $height )
	{
		$_object = $this->get_object( $object );

		if ( ! $_object ) :

			//	Let the renderer show a bad_src graphic
			$_object				= new stdClass();
			$_object->filename		= '';
			$_object->bucket		= new stdClass();
			$_object->bucket->slug	= '';

		endif;
		
		return $this->_cdn->url_scale( $_object->filename, $_object->bucket->slug, $width, $height );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_serve_url_scheme method
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_scale_scheme()
	{
		return $this->_cdn->url_scale_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_placeholder_url method
	 *
	 * @access	public
	 * @param	int		$width	The width of the placeholder
	 * @param	int		$height	The height of the placeholder
	 * @param	int		border	The width of the border round the placeholder
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_placeholder( $width = 100, $height = 100, $border = 0 )
	{
		return $this->_cdn->url_placeholder( $width, $height, $border );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_serve_url_scheme method
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_placeholder_scheme()
	{
		return $this->_cdn->url_placeholder_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_blank_avatar_url method
	 *
	 * @access	public
	 * @param	int		$width	The width of the placeholder
	 * @param	int		$height	The height of the placeholder
	 * @param	mixed	$sex	The gender of the blank avatar to show
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_blank_avatar( $width = 100, $height = 100, $sex = 'unknown' )
	{
		return $this->_cdn->url_blank_avatar( $width, $height, $sex );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_serve_url_scheme method
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_blank_avatar_scheme()
	{
		return $this->_cdn->url_blank_avatar_scheme();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_expiring_url method
	 *
	 * @access	public
	 * @param	string	$bucket		The bucket which the image resides in
	 * @param	string	$object		The filename of the image we're 'scaling'
	 * @param	string	$expires	The length of time the URL should be valid for, in seconds
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_expiring( $object, $expires )
	{
		$_object = $this->get_object( $object );

		if ( ! $_object ) :

			//	Let the renderer show a bad_src graphic
			$_object				= new stdClass();
			$_object->filename		= '';
			$_object->bucket		= new stdClass();
			$_object->bucket->slug	= '';

		endif;

		return $this->_cdn->url_expiring( $_object->filename, $_object->bucket->slug, $expires );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Calls the driver's public cdn_expiring_url_scheme method
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function url_expiring_scheme()
	{
		return $this->_cdn->url_expiring_scheme();
	}
}

/* End of file cdn.php */
/* Location: ./libraries/cdn.php */