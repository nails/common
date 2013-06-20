<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			CDN
*
* Description:	A Library for dealing with content in the Local CDN
* 
*/

class Local_CDN {
	
	private $db;
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
		//	Shortcut to DB
		$this->db		=& get_instance()->db;
		$this->errors	= array();
		
		// --------------------------------------------------------------------------
		
		//	Load langfile
		get_instance()->lang->load( 'cdn_local', RENDER_LANG_SLUG );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/*	! OBJECT METHODS */
	
	
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
				
					$this->_error( lang( 'cdn_local_no_file' ) );
					return FALSE;
				
				else :
				
					$_file	= $file;
					$_name	= $file;
				
				endif;
			
			else :
			
				$_file	= $_FILES[ $file ]['tmp_name'];
				$_name	= $_FILES[ $file ]['name'];
			
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
			
				$this->_error( lang( 'cdn_local_stream_content_type' ) );
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
					$_name					= $_cache_file;
					$_data['file']			= APP_CACHE . $_cache_file;
					$_data['content-type']	= $options['content-type'];
				
				else :
				
					$this->_error( lang( 'cdn_local_error_cache_write_fail' ) );
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
					$this->_error(  lang( 'cdn_local_error_bad_mime_plural', $_accepted ) );
				
				else :
				
					$_accepted = implode( '', $_types );
					$this->_error(  lang( 'cdn_local_error_bad_mime', $_accepted ) );
				
				endif;
				
				return FALSE;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Is the file within the filesize limit?
		if ( isset( $options['max_size'] ) ) :
		
			if ( filesize( $_file ) > $options['max_size'] ) :
			
				$_fs_in_kb = format_bytes( $options['max_size'] );
				$this->_error( lang( 'cdn_local_error_filesize', $_fs_in_kb ) );
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
				
					$this->_error( lang( 'cdn_local_error_maxwidth', $options['dimensions']['max_width'] ) );
					$error = TRUE;
					
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['max_height'] ) ) :
			
				if ( $h > $options['dimensions']['max_height'] ) :
				
					$this->_error( lang( 'cdn_local_error_maxheight', $options['dimensions']['max_height'] ) );
					$error = TRUE;
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['min_width'] ) ) :
			
				if ( $w < $options['dimensions']['min_width'] ) :
				
					$this->_error( lang( 'cdn_local_error_minwidth', $options['dimensions']['min_width'] ) );
					$error = TRUE;
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( isset( $options['dimensions']['min_height'] ) ) :
			
				if ( $h < $options['dimensions']['min_height'] ) :
				
					$this->_error( lang( 'cdn_local_error_minheight', $options['dimensions']['min_height'] ) );
					$error = TRUE;
				
				endif;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			
			if ( $error ) :
			
				return FALSE;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Test and set the bucket, if it doesn't exist, create it
		$_bucket = $this->get_bucket( $bucket );
		
		if ( ! $_bucket ) :
		
			if ( $this->create_bucket( $bucket ) ) :
			
				$_bucket = $this->get_bucket( $bucket );
				
				$_data['bucket']		= $_bucket->id;
				$_data['bucket_slug']	= $bucket;
			
			else :
			
				return FALSE;
			
			endif;
			
		else :
		
			$_data['bucket']		= $_bucket->id;
			$_data['bucket_slug']	= $bucket;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Check bucket is writeable
		if ( ! is_writable( CDN_PATH . $bucket ) ) :
		
			$this->_error( lang( 'cdn_local_error_target_write_fail' ) );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Does the user have permission to write to the bucket?

		if ( ! get_userobject()->is_admin() && $_bucket->user_id && $_bucket->user_id != active_user( 'id' ) ) :
		
			$this->_error( 'You do not have permission to modify that bucket.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Has a tag been defined?
		if ( isset( $options['tag'] ) ) :
		
			$_data['tag'] = $options['tag'];
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If a certain filename has been specified then send that to the CDN (this
		//	will overwrite any existing file so use with caution)
		
		if ( isset( $options['filename'] ) && $options['filename'] == 'USE_ORIGINAL' ) :
		
			$_data['filename'] =  $_FILES[$file]['name'];
			
		elseif ( isset( $options['filename'] ) && $options['filename'] ) :
		
			$_data['filename'] = $options['filename'];
			
		else :
		
			//	Generate a filename
			$_data['filename'] = md5( active_user( 'id' ) . microtime( TRUE ) . rand( 0, 999 ) ) . '.' . $_ext;
		
		endif;
		
		//	And set the display name
		$_data['name']	= $_name;
		
		// --------------------------------------------------------------------------
		
		//	Move the file
		if ( move_uploaded_file( $_data['file'], CDN_PATH . $bucket . '/' . $_data['filename'] ) ) :
		
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
		
		//	Handle result
		if ( $_status ) :
		
			//	File was uploaded successfully, add to the DB
			$this->_create_object( $_data ); 
			
			return $_data['filename'];
			
		else :
		
			return FALSE;
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Marks an object as deleted
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function delete( $object, $bucket )
	{
		if ( ! $object ) :
		
			$this->_error( 'Not a valid object.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_object = $this->get_object( $object );
		
		if ( ! $_object || $_object->is_deleted ) :
		
			$this->_error( 'Not a valid object.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Can the user modify the bucket/objects? Admins always can but if the bucket
		//	has a user then the current user must be the owner
		
		if ( ! get_userobject()->is_admin() && $_object->user->id && $_object->user->id != active_user( 'id' ) ) :
		
			$this->_error( 'You do not have permission to modify that object.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Seems in order, update the object
		$this->db->set( 'is_deleted', TRUE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		$this->db->where( 'id', $_object->id );
		$this->db->update( 'cdn_local_object' );
		
		return (bool) $this->db->affected_rows();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Destroys (permenantly deletes) an object
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function destroy( $file, $bucket )
	{
		$_file		= urldecode( $file );
		$_bucket	= urldecode( $bucket );

		if ( file_exists( CDN_PATH . $bucket . '/' . $_file ) ) :
		
			if ( @unlink( CDN_PATH . $bucket . '/' . $_file ) ) :
			
				//	Remove the database entry
				$_object = $this->get_object( $_file, $_bucket );

				if ( $_object ) :

					$this->where( 'id', $_object->id );
					$this->db->delete( 'cdn_local_object' );

				endif;

				return TRUE;
			
			else :
			
				$this->_error( lang( 'cdn_local_error_delete' ) );
				return FALSE;
			
			endif;
		
		else :
		
			$this->_error( lang( 'cdn_local_error_delete_nofile' ) );
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
		//	TODO: Copy object between buckets
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
		//	TODO: Move object between buckets
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
	
	
	private function _create_object( $data )
	{
		$this->db->set( 'bucket_id', $data['bucket'] );
		$this->db->set( 'filename', $data['filename'] );
		$this->db->set( 'filename_display', $data['name'] );
		$this->db->set( 'mime', $data['content-type'] );
		$this->db->set( 'filesize', filesize( CDN_PATH . $data['bucket_slug'] . '/' . $data['filename'] ) );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		
		if ( active_user( 'id' ) ) :
		
			$this->db->set( 'user_id', active_user( 'id' ) );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Mime specific
		switch ( $data['content-type'] ) :
		
			case 'image/jpg' :
			case 'image/jpeg' :
			case 'image/png' :
			case 'image/gif' :
			
				$_file = getimagesize( CDN_PATH . $data['bucket_slug'] . '/' . $data['filename'] );
				
				if ( isset( $_file[0] ) && isset( $_file[1] ) ) :
				
					$this->db->set( 'img_width', $_file[0] );
					$this->db->set( 'img_height', $_file[1] );
					
				endif;
			
			break;
		
		endswitch;
		
		// --------------------------------------------------------------------------
		
		$this->db->insert( 'cdn_local_object' );
		$_object_id = $this->db->insert_id();
		
		if ( $this->db->affected_rows() ) :
		
			//	Add a tag if there's one defined
			if ( isset( $data['tag'] ) && ! empty( $data['tag'] ) ) :
			
				$this->db->where( 'id', $data['tag'] );
				
				if ( $this->db->count_all_results( 'cdn_local_bucket_tag' ) ) :
				
					$this->db->set( 'object_id', $_object_id );
					$this->db->set( 'tag_id', $data['tag'] );
					$this->db->set( 'created', 'NOW()', FALSE );
					$this->db->insert( 'cdn_local_object_tag' );
				
				endif;
			
			endif;
			
			return TRUE;
			
		else :
		
			return FALSE;
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	/**
	 * Fetch an object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function get_object( $object, $bucket = NULL )
	{
		$this->db->select( 'o.id, o.user_id, o.filename, o.filename_display, o.created, o.modified, o.serves, o.thumbs, o.scales, o.is_deleted' );
		$this->db->select( 'o.mime, o.filesize, o.img_width, o.img_height' );
		$this->db->select( 'u.email, um.first_name, um.last_name, um.profile_img, um.gender' );
		
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
				
				$this->db->join( 'cdn_local_bucket b', ' b.id = o.bucket_id' );
			
			endif;
		
		endif;
		
		$this->db->join( 'user u', 'u.id = o.user_id', 'LEFT' );
		$this->db->join( 'user_meta um', 'um.user_id = o.user_id', 'LEFT' );
		
		$_object = $this->db->get( 'cdn_local_object o' )->row();
		
		if ( $_object ) :
		
			$this->_format_object( $_object );
			
			return $_object;
		
		else :
		
			return FALSE;
		
		endif;
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
	public function list_objects_for_user( $user_id, $include_deleted = FALSE )
	{
		$this->db->select( 'o.id, o.user_id, o.filename, b.id bucket_id, b.slug bucket_slug, o.filename_display, o.created, o.modified, o.serves, o.thumbs, o.scales, o.is_deleted' );
		$this->db->select( 'o.mime, o.filesize, o.img_width, o.img_height' );
		$this->db->select( 'u.email, um.first_name, um.last_name, um.profile_img, um.gender' );
		$this->db->where( 'o.user_id', $user_id );
		
		if ( ! $include_deleted ) :
		
			$this->db->where( 'o.is_deleted', FALSE );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->db->join( 'user u', 'u.id = o.user_id', 'LEFT' );
		$this->db->join( 'user_meta um', 'um.user_id = o.user_id', 'LEFT' );
		$this->db->join( 'cdn_local_bucket b', 'o.bucket_id = b.id', 'LEFT' );
		
		$this->db->order_by( 'o.filename_display' );
		
		$_objects = $this->db->get( 'cdn_local_object o' )->result();
		
		foreach( $_objects AS $object ) :
		
			//	Format object object
			$this->_format_object( $object );
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_objects;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Adds a tag to a file
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function add_object_tag( $object_id, $tag_id )
	{
		//	Valid object?
		$_object = $this->get_object( $object_id );
		
		if ( ! $_object ) :
		
			$this->_error( 'Not a valid object.' );
			return FALSE;
		
		endif;
		
		
		// --------------------------------------------------------------------------
		
		//	Valid tag?
		$this->db->select( 't.*, b.user_id' );
		$this->db->join( 'cdn_local_bucket b', 'b.id = t.bucket_id', 'LEFT' );
		
		$this->db->where( 't.id', $tag_id );
		
		$_tag = $this->db->get( 'cdn_local_bucket_tag t' )->row();
		
		if ( ! $_tag ) :
		
			$this->_error( 'Not a valid tag.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Can the user modify the bucket/objects? Admins always can but if the bucket
		//	has a user then the current user must be the owner
		
		if ( ! get_userobject()->is_admin() && $_tag->user_id && $_tag->user_id != active_user( 'id' ) ) :
		
			$this->_error( 'You do not have permission to modify that bucket.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Test if tag has already been applied to the object, if it has gracefully fail
		$this->db->where( 'object_id', $_object->id );
		$this->db->where( 'tag_id', $_tag->id );
		if ( $this->db->count_all_results( 'cdn_local_object_tag' ) ) :
		
			return TRUE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Seems good, add the tag
		$this->db->set( 'object_id', $_object->id );
		$this->db->set( 'tag_id', $_tag->id );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->insert( 'cdn_local_object_tag' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Removes a tag from an object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function delete_object_tag( $object_id, $tag_id )
	{
		//	Valid object?
		$_object = $this->get_object( $object_id );
		
		if ( ! $_object ) :
		
			$this->_error( 'Not a valid object.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Valid tag?
		$this->db->select( 't.*, b.user_id' );
		$this->db->join( 'cdn_local_bucket b', 'b.id = t.bucket_id', 'LEFT' );
		
		$this->db->where( 't.id', $tag_id );
		
		$_tag = $this->db->get( 'cdn_local_bucket_tag t' )->row();
		
		if ( ! $_tag ) :
		
			$this->_error( 'Not a valid tag.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Can the user modify the bucket/objects? Admins always can but if the bucket
		//	has a user then the current user must be the owner
		
		if ( ! get_userobject()->is_admin() && $_tag->user_id && $_tag->user_id != active_user( 'id' ) ) :
		
			$this->_error( 'You do not have permission to modify that bucket.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Seems good, add the tag
		$this->db->where( 'object_id', $_object->id );
		$this->db->where( 'tag_id', $_tag->id );
		$this->db->delete( 'cdn_local_object_tag' );
		
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
	public function count_tag_objects( $tag_id )
	{
		$this->db->where( 'ot.tag_id', $tag_id );
		$this->db->where( 'o.is_deleted', FALSE );
		$this->db->join( 'cdn_local_object o', 'o.id = ot.object_id' );
		return $this->db->count_all_results( 'cdn_local_object_tag ot' );
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
		//	Test if bucket exists, if it does stop, job done.
		$_bucket = $this->get_bucket( $bucket );
		
		if ( $_bucket ) :
		
			return $_bucket->id;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Does't exist, attempt to create
		if ( @ mkdir( CDN_PATH . $bucket ) ) :
		
			$this->db->set( 'slug', $bucket );
			$this->db->set( 'created', 'NOW()', FALSE );
			$this->db->set( 'modified', 'NOW()', FALSE );
			
			if ( active_user( 'id' ) ) :
			
				$this->db->set( 'user_id', active_user( 'id' ) );
			
			endif;
			
			$this->db->insert( 'cdn_local_bucket' );
			
			if ( $this->db->affected_rows() ) :
				
				return $this->db->insert_id();
			
			else :
			
				@unlink( CDN_PATH . $bucket );
				
				$this->_error( lang( 'cdn_local_mkdir_fail' ) );
				return FALSE;
			
			endif;
		
		else :
		
			$this->_error( lang( 'cdn_local_mkdir_fail' ) );
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Gets a single bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function get_bucket( $bucket, $list_bucket = FALSE, $filter_tag = FALSE, $include_deleted = FALSE )
	{
		$this->db->select( 'id,slug,user_id,allowed_types,max_size,created,modified' );
		$this->db->select( '(SELECT COUNT(*) FROM cdn_local_object WHERE bucket_id = b.id AND is_deleted = 0) object_count' );
		
		if ( is_numeric( $bucket ) ) :
		
			$this->db->where( 'b.id', $bucket );
		
		else :
		
			$this->db->where( 'b.slug', $bucket );
		
		endif;
		
		$_bucket = $this->db->get( 'cdn_local_bucket b' )->row();
		
		// --------------------------------------------------------------------------
		
		if( $_bucket ) :
		
			//	Format bucket object
			$_bucket->id		= (int) $_bucket->id;
			$_bucket->user_id	= (int) $_bucket->user_id;
			
			// --------------------------------------------------------------------------
			
			//	List contents
			if ( $list_bucket ) :
			
				$_bucket->objects = $this->list_bucket( $_bucket->id, $filter_tag, $include_deleted );
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Fetch tags & counts
			$this->db->select( 'bt.id,bt.label,bt.created' );
			$this->db->select( '(SELECT COUNT(*) FROM cdn_local_object_tag ot JOIN cdn_local_object o ON o.id = ot.object_id WHERE tag_id = bt.id AND o.is_deleted = 0 ) total' );
			$this->db->order_by( 'bt.label' );
			$this->db->where( 'bt.bucket_id', $_bucket->id );
			$_bucket->tags = $this->db->get( 'cdn_local_bucket_tag bt' )->result();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return $_bucket;
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
	public function list_bucket( $bucket, $filter_tag = FALSE, $include_deleted = FALSE )
	{
		$this->db->select( 'o.id, o.user_id, o.filename, o.filename_display, o.created, o.modified, o.serves, o.thumbs, o.scales, o.is_deleted' );
		$this->db->select( 'o.mime, o.filesize, o.img_width, o.img_height' );
		$this->db->select( 'u.email, um.first_name, um.last_name, um.profile_img, um.gender' );
		
		if ( is_numeric( $bucket ) ) :
		
			$this->db->where( 'o.bucket_id', $bucket );
		
		else :
		
			$this->db->where( 'b.slug', $bucket );
			$this->db->join( 'cdn_local_bucket b', 'b.id = o.bucket_id' );
		
		endif;
		
		if ( ! $include_deleted ) :
		
			$this->db->where( 'o.is_deleted', FALSE );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Filtering by tag?
		if ( $filter_tag ) :
		
			$this->db->join( 'cdn_local_object_tag ft', 'ft.object_id = o.id AND ft.tag_id = ' . $filter_tag );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		
		$this->db->join( 'user u', 'u.id = o.user_id', 'LEFT' );
		$this->db->join( 'user_meta um', 'um.user_id = o.user_id', 'LEFT' );
		
		$this->db->order_by( 'o.filename_display' );
		
		$_objects = $this->db->get( 'cdn_local_object o' )->result();
		
		foreach( $_objects AS $object ) :
		
			//	Format object object
			$this->_format_object( $object );
		
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_objects;
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
		//	TODO
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
	public function add_bucket_tag( $bucket, $label )
	{
		$label = trim( $label );
		
		if ( !$label ) :
		
			$this->_error( 'Invalid label.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Test bucket
		$_bucket = $this->get_bucket( $bucket );
		
		if ( ! $_bucket ) :
		
			$this->_error( 'Not a valid bucket.' );
			return FALSE;
		
		endif;
		
		//	If the bucket has an owner/user then only the owner user can add tags to the bucket
		//	Administrators can add to any bucket
		
		if ( ! get_userobject()->is_admin() && $_bucket->user_id && $bucket->user_id != active_user( 'id' ) ) :
		
			$this->_error( 'You do not have permission to modify that bucket.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Test tag
		$this->db->where( 'bucket_id', $_bucket->id );
		$this->db->where( 'label', $label );
		if ( $this->db->count_all_results( 'cdn_local_bucket_tag' ) ) :
		
			$this->_error( 'Tag already exists.' );
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Seems good, add the tag
		$this->db->set( 'bucket_id', $_bucket->id );
		$this->db->set( 'label', $label );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->insert( 'cdn_local_bucket_tag' );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Removes a tag from a bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function delete_bucket_tag( $bucket, $label )
	{
		//	Test bucket
		$_bucket = $this->get_bucket( $bucket );
		
		if ( ! $_bucket ) :
		
			$this->_error( 'Not a valid bucket.' );
			return FALSE;
		
		endif;
		
		//	If the bucket has an owner/user then only the owner user can delete tags from the bucket
		//	Administrators can add to any bucket
		
		if ( ! get_userobject()->is_admin() && $_bucket->user_id && $bucket->user_id != active_user( 'id' ) ) :
		
			$this->_error( 'You do not have permission to modify that bucket.' );
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
		
		
		if ( ! $this->db->count_all_results( 'cdn_local_bucket_tag' ) ) :
		
			$this->_error( 'Tag does not exist.' );
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
		
		$this->db->delete( 'cdn_local_bucket_tag' );
		
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
	public function rename_bucket_tag( $bucket, $tag, $new_name )
	{
		//	TODO
		return TRUE;
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
		$object->is_deleted	= (bool) $object->is_deleted;
		
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

		if ( isset( $object->bucket_id ) ) :

			$object->bucket			= new stdClass();
			$object->bucket->id		= $object->bucket_id;
			$object->bucket->slug	= $object->bucket_slug;

			unset( $object->bucket_id );
			unset( $object->bucket_slug );

		endif;
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
	 * Returns the scheme of 'serve' urls
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_serve_url_scheme()
	{
		return site_url( 'cdn/serve/{{bucket}}/{{file}}' );
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
	 * Returns the scheme of 'thumb' urls
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_thumb_url_scheme()
	{
		return site_url( 'cdn/thumb/{{width}}/{{height}}/{{bucket}}/{{file}}' );
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
	 * Returns the scheme of 'scale' urls
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_scale_url_scheme()
	{
		return site_url( 'cdn/scale/{{width}}/{{height}}/{{bucket}}/{{file}}' );
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
	 * Returns the scheme of 'placeholder' urls
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_placeholder_url_scheme()
	{
		return site_url( 'cdn/placeholder/{{width}}/{{height}}/{{border}}' );
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
	static function cdn_blank_avatar_url( $width = 100, $height = 100, $sex = 'male' )
	{
		$_out  = 'cdn/blank_avatar/';
		$_out .= $width . '/' . $height . '/' . $sex;
		
		// --------------------------------------------------------------------------
		
		return site_url( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Returns the scheme of 'blank_avatar' urls
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_blank_avatar_url_scheme()
	{
		return site_url( 'cdn/blank_avatar/{{width}}/{{height}}/{{sex}}' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Generates a properly hashed expiring url
	 *
	 * @access	static
	 * @param	string	$bucket		The bucket which the image resides in
	 * @param	string	$object		The object to be served
	 * @param	string	$expires	The length of time the URL should be valid for, in seconds
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_expiring_url( $bucket, $object, $expires )
	{
		//	Hash the expirey time
		$_hash = get_instance()->encrypt->encode( $bucket . '|' . $object . '|' . $expires . '|' . time() . '|' . md5( time() . $bucket . $object . $expires . APP_PRIVATE_KEY ), APP_PRIVATE_KEY );
		$_hash = urlencode( $_hash );
		
		$_out  = 'cdn/serve?token=' . $_hash;
		
		// --------------------------------------------------------------------------
		
		return site_url( $_out );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Returns the scheme of 'expiring' urls
	 *
	 * @access	static
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	static function cdn_expiring_url_scheme()
	{
		return site_url( 'cdn/serve?token={{token}}' );
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

/* End of file local.php */
/* Location: ./application/libraries/_resources/cdn_drivers/local.php */