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

			case 'aws_local' :

				include_once NAILS_PATH . 'libraries/_resources/cdn_drivers/aws_local.php';
				return 'Aws_local_CDN';

			break;

			// --------------------------------------------------------------------------

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

		//	Test the drive
		if ( method_exists( $this->_cdn, $method ) ) :

			return call_user_func_array( array( $this->_cdn, $method ), $arguments );

		endif;

		throw new Exception( 'Call to undefined method Cdn::' . $method . '()' );
	}


	// --------------------------------------------------------------------------


	/*	! OBJECT METHODS */


	// --------------------------------------------------------------------------


	public function get_objects()
	{
		$this->db->select( 'o.id, o.filename, o.filename_display, o.created, o.created_by, o.modified, o.modified_by, o.serves, o.downloads, o.thumbs, o.scales' );
		$this->db->select( 'o.mime, o.filesize, o.img_width, o.img_height, o.is_animated' );
		$this->db->select( 'u.email, u.first_name, u.last_name, u.profile_img, u.gender' );
		$this->db->select( 'b.id bucket_id, b.slug bucket_slug' );

		$this->db->join( 'user u', 'u.id = o.created_by', 'LEFT' );
		$this->db->join( 'cdn_bucket b', 'b.id = o.bucket_id', 'LEFT' );

		$this->db->order_by( 'o.filename_display' );

		$_objects = $this->db->get( 'cdn_object o' )->result();

		foreach ( $_objects AS $obj ) :

			//	Format the object, make it pretty
			$this->_format_object( $obj );

		endforeach;

		return $_objects;
	}


	// --------------------------------------------------------------------------


	public function get_objects_from_trash()
	{
		$this->db->select( 'o.id, o.filename, o.filename_display, o.created, o.created_by, o.modified, o.modified_by, o.serves, o.downloads, o.thumbs, o.scales' );
		$this->db->select( 'o.mime, o.filesize, o.img_width, o.img_height, o.is_animated' );
		$this->db->select( 'u.email, u.first_name, u.last_name, u.profile_img, u.gender' );
		$this->db->select( 'b.id bucket_id, b.slug bucket_slug' );

		$this->db->join( 'user u', 'u.id = o.created_by', 'LEFT' );
		$this->db->join( 'cdn_bucket b', 'b.id = o.bucket_id', 'LEFT' );

		$this->db->order_by( 'o.filename_display' );

		$_objects = $this->db->get( 'cdn_object_trash o' )->result();

		foreach ( $_objects AS $obj ) :

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
	 * Returns a single object object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function get_object_from_trash( $object, $bucket = NULL )
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

		$_objects = $this->get_objects_from_trash();

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
		$this->db->where( 'o.created_by', $user_id );
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

					$this->set_error( lang( 'cdn_error_no_file' ) );
					return FALSE;

				else :

					$_file	= $object;
					$_name	= basename( $object );

				endif;

			else :

				$_file	= $_FILES[ $object ]['tmp_name'];
				$_name	= $_FILES[ $object ]['name'];

			endif;

			// --------------------------------------------------------------------------

			//	Specify the file specifics

			//	Content-type; using finfo because the $_FILES variable can't be trusted
			//	(uploads from Uploadify always report as application/octet-stream;
			//	stupid flash. Unless, of course, the content-type has beens et explicityly
			//	by the developer

			if ( isset( $options['content-type'] ) ) :

				$_data->mime = $options['content-type'];

			else :

				$_data->mime = $this->get_mime_type_from_file( $_file );

			endif;

			//	Now set the actual file data
			$_data->file = $_file;

		else :

			//	We've been given a data stream, use that. If no content-type has been set
			//	then fall over - we need to know what we're dealing with

			if ( ! isset( $options['content-type'] ) ) :

				$this->set_error( lang( 'cdn_stream_content_type' ) );
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

					$this->set_error( lang( 'cdn_error_cache_write_fail' ) );
					return FALSE;

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Test and set the bucket, if it doesn't exist, create it
		if ( is_numeric( $bucket ) || is_string( $bucket ) ) :

			$_bucket = $this->get_bucket( $bucket );

		else :

			$_bucket = $bucket;

		endif;

		if ( ! $_bucket ) :

			if ( $this->bucket_create( $bucket ) ) :

				$_bucket = $this->get_bucket( $bucket );

				$_data->bucket_id	= $_bucket->id;
				$_data->bucket_slug	= $_bucket->slug;

			else :

				return FALSE;

			endif;

		else :

			$_data->bucket_id	= $_bucket->id;
			$_data->bucket_slug	= $_bucket->slug;

		endif;

		// --------------------------------------------------------------------------

		//	Does the user have permission to write to the bucket?
		if ( ! $this->_can_edit_bucket( $_bucket ) ) :

			$this->set_error( lang( 'cdn_error_bucket_nopermission' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Is this an acceptable file? Check against the allowed_types array (if present)

		$_ext	= $this->get_ext_from_mimetype( $_data->mime );	//	So other parts of this method can access $_ext;

		if ( $_bucket->allowed_types ) :

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

			if ( array_search( $_ext, $_bucket->allowed_types ) === FALSE ) :

				if ( count( $_bucket->allowed_types ) > 1 ) :

					array_splice( $_bucket->allowed_types, count( $_bucket->allowed_types ) - 1, 0, array( ' and ' ) );
					$_accepted = implode( ', .', $_bucket->allowed_types );
					$_accepted = str_replace( ', . and , ', ' and ', $_accepted );
					$this->set_error(  lang( 'cdn_error_bad_mime_plural', $_accepted ) );

				else :

					$_accepted = implode( '', $_bucket->allowed_types );
					$this->set_error(  lang( 'cdn_error_bad_mime', $_accepted ) );

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
				$this->set_error( lang( 'cdn_error_filesize', $_fs_in_kb ) );
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

					$this->set_error( lang( 'cdn_error_maxwidth', $options['dimensions']['max_width'] ) );
					$error = TRUE;

				endif;

			endif;

			// --------------------------------------------------------------------------

			if ( isset( $options['dimensions']['max_height'] ) ) :

				if ( $_data->img->height > $options['dimensions']['max_height'] ) :

					$this->set_error( lang( 'cdn_error_maxheight', $options['dimensions']['max_height'] ) );
					$error = TRUE;

				endif;

			endif;

			// --------------------------------------------------------------------------

			if ( isset( $options['dimensions']['min_width'] ) ) :

				if ( $_data->img->width < $options['dimensions']['min_width'] ) :

					$this->set_error( lang( 'cdn_error_minwidth', $options['dimensions']['min_width'] ) );
					$error = TRUE;

				endif;

			endif;

			// --------------------------------------------------------------------------

			if ( isset( $options['dimensions']['min_height'] ) ) :

				if ( $_data->img->height < $options['dimensions']['min_height'] ) :

					$this->set_error( lang( 'cdn_error_minheight', $options['dimensions']['min_height'] ) );
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

		$_upload = $this->_cdn->object_create( $_data->bucket_slug, $_data->filename, $_data->file, $_data->mime );

		// --------------------------------------------------------------------------

		if ( $_upload ) :

			$_object = $this->_create_object( $_data, TRUE );

			if ( $_object ) :

				$_status = $_object;

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


	public function object_delete( $object )
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

		if ( ! $this->_can_edit_object( $_object ) ) :

			$this->set_error( lang( 'cdn_error_object_nopermission' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_data						= array();
		$_data['id']				= $_object->id;
		$_data['bucket_id']			= $_object->bucket->id;
		$_data['filename']			= $_object->filename;
		$_data['filename_display']	= $_object->filename_display;
		$_data['mime']				= $_object->mime;
		$_data['filesize']			= $_object->filesize;
		$_data['img_width']			= $_object->img_width;
		$_data['img_height']		= $_object->img_height;
		$_data['is_animated']		= $_object->is_animated;
		$_data['created']			= $_object->created;
		$_data['created_by']		= $_object->creator->id;
		$_data['modified']			= $_object->modified;
		$_data['modified_by']		= $_object->modified_by;
		$_data['serves']			= $_object->serves;
		$_data['downloads']			= $_object->downloads;
		$_data['thumbs']			= $_object->thumbs;
		$_data['scales']			= $_object->scales;

		$this->db->set( $_data );
		$this->db->set( 'trashed', 'NOW()', FALSE );

		//	Start transaction
		$this->db->trans_start();

			//	Create trash object
			$this->db->insert( 'cdn_object_trash' );

			//	Remove original object
			$this->db->where( 'id', $_object->id );
			$this->db->delete( 'cdn_object' );

		$this->db->trans_complete();

		if ( $this->db->trans_status() !== FALSE ) :

			return TRUE;

		else :

			$this->set_error( lang( 'cdn_error_delete' ) );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function object_restore( $object )
	{
		if ( ! $object ) :

			$this->set_error( lang( 'cdn_error_object_invalid' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_object = $this->get_object_from_trash( $object );

		if ( ! $_object ) :

			$this->set_error( lang( 'cdn_error_object_invalid' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Can the user modify the bucket/objects? Admins always can but if the bucket
		//	has a user then the current user must be the owner

		if ( ! $this->_can_edit_object( $_object ) ) :

			$this->set_error( lang( 'cdn_error_object_nopermission' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_data						= array();
		$_data['id']				= $_object->id;
		$_data['bucket_id']			= $_object->bucket->id;
		$_data['filename']			= $_object->filename;
		$_data['filename_display']	= $_object->filename_display;
		$_data['mime']				= $_object->mime;
		$_data['filesize']			= $_object->filesize;
		$_data['img_width']			= $_object->img_width;
		$_data['img_height']		= $_object->img_height;
		$_data['is_animated']		= $_object->is_animated;
		$_data['created']			= $_object->created;
		$_data['created_by']		= $_object->creator->id;
		$_data['serves']			= $_object->serves;
		$_data['downloads']			= $_object->downloads;
		$_data['thumbs']			= $_object->thumbs;
		$_data['scales']			= $_object->scales;

		if ( get_userobject()->is_logged_in() ) :

			$_data['modified_by']	= active_user( 'id' );

		endif;

		$this->db->set( $_data );
		$this->db->set( 'modified', 'NOW()', FALSE );

		//	Start transaction
		$this->db->trans_start();

			//	Restore object
			$this->db->insert( 'cdn_object' );

			//	Remove trash object
			$this->db->where( 'id', $_object->id );
			$this->db->delete( 'cdn_object_trash' );

		$this->db->trans_complete();

		if ( $this->db->trans_status() !== FALSE ) :

			return TRUE;

		else :

			$this->set_error( lang( 'cdn_error_delete' ) );
			return FALSE;

		endif;
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
	public function object_destroy( $object )
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

		if ( ! $this->_can_edit_object( $_object ) ) :

			$this->set_error( lang( 'cdn_error_object_nopermission' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->_cdn->object_delete( $_object->filename, $_object->bucket->slug ) ) :

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

			$_object = $this->get_object( $object );

			if ( $_object ) :

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
		$this->db->where( 't.id', $tag_id );
		$_tag = $this->db->get( 'cdn_bucket_tag t' )->row();

		if ( ! $_tag ) :

			$this->set_error( lang( 'cdn_error_tag_invalid' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Can the user modify the bucket/objects? Admins always can but if the bucket
		//	has a user then the current user must be the owner

		if ( ! $this->_can_edit_bucket( $_tag->bucket_id ) ) :

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
		$this->db->where( 't.id', $tag_id );
		$_tag = $this->db->get( 'cdn_bucket_tag t' )->row();

		if ( ! $_tag ) :

			$this->set_error( lang( 'cdn_error_tag_invalid' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Can the user modify the bucket/objects? Admins always can but if the bucket
		//	has a user then the current user must be the owner

		if ( ! $this->_can_edit_bucket( $_tag->bucket_id ) ) :

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
		$this->db->select( 'b.id,b.slug,b.label,b.allowed_types,b.max_size,b.created,b.created_by,b.modified,b.modified_by' );
		$this->db->select( 'u.email, u.first_name, u.last_name, u.profile_img, u.gender' );
		$this->db->select( '(SELECT COUNT(*) FROM cdn_object WHERE bucket_id = b.id) object_count' );

		$this->db->join( 'user u', 'u.id = b.created_by', 'LEFT' );

		$_buckets = $this->db->get( 'cdn_bucket b' )->result();

		// --------------------------------------------------------------------------

		foreach ( $_buckets AS &$bucket ) :

			//	Format bucket object
			$this->_format_bucket( $bucket );

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
	public function bucket_create( $bucket, $label = NULL )
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
			if ( ! $label ) :

				$this->db->set( 'label', ucwords( str_replace( '-', ' ', $bucket ) ) );

			else :

				$this->db->set( 'label', $label );

			endif;
			$this->db->set( 'created', 'NOW()', FALSE );
			$this->db->set( 'modified', 'NOW()', FALSE );

			if ( get_userobject()->is_logged_in() ) :

				$this->db->set( 'created_by',	active_user( 'id' ) );
				$this->db->set( 'modified_by',	active_user( 'id' ) );

			endif;

			$this->db->insert( 'cdn_bucket' );

			if ( $this->db->affected_rows() ) :

				return $this->db->insert_id();

			else :

				$this->_cdn->destroy( $bucket );

				$this->set_error( lang( 'cdn_error_bucket_insert' ) );
				return FALSE;

			endif;

		else :
			here($this->last_error());
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
		if ( is_numeric( $bucket ) || is_string( $bucket ) ) :

			$_bucket = $this->get_bucket( $bucket );

		else :

			$_bucket = $bucket;

		endif;

		if ( ! $_bucket ) :

			$this->set_error( lang( 'cdn_error_bucket_invalid' ) );
			return FALSE;

		endif;

		//	If the bucket has an owner/user then only the owner user can add tags to the bucket
		//	Administrators can add to any bucket

		if ( ! $this->_can_edit_bucket( $_bucket ) ) :

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
		if ( is_numeric( $bucket ) || is_string( $bucket ) ) :

			$_bucket = $this->get_bucket( $bucket );

		else :

			$_bucket = $bucket;

		endif;

		if ( ! $_bucket ) :

			$this->set_error( lang( 'cdn_error_bucket_invalid' ) );
			return FALSE;

		endif;

		//	If the bucket has an owner/user then only the owner user can delete tags from the bucket
		//	Administrators can add to any bucket

		if ( ! $this->_can_edit_bucket( $_bucket ) ) :

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
	 * Returns the last error
	 *
	 * @access	public
	 * @return	string
	 * @author	Pablo
	 **/
	public function last_error()
	{
		return end( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the last error
	 *
	 * @access	public
	 * @return	array
	 * @author	Pablo
	 **/
	public function error()
	{
		$_error = end( $this->_errors );
		reset( $this->_errors );
		return $_error;
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

		if ( get_userobject()->is_logged_in() ) :

			$this->db->set( 'created_by',	active_user( 'id' ) );
			$this->db->set( 'modified_by',	active_user( 'id' ) );

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
	 * Formats an object object
	 *
	 * @access	private
	 * @param	object	$object	The object to format
	 * @return	void
	 * @author	Pablo
	 **/
	private function _format_object( &$object )
	{
		$object->id				= (int) $object->id;
		$object->filesize		= (int) $object->filesize;
		$object->img_width		= (int) $object->img_width;
		$object->img_height		= (int) $object->img_height;
		$object->is_animated	= (bool) $object->is_animated;
		$object->serves			= (int) $object->serves;
		$object->downloads		= (int) $object->downloads;
		$object->thumbs			= (int) $object->thumbs;
		$object->scales			= (int) $object->scales;
		$object->modified_by	= $object->modified_by ? (int) $object->modified_by : NULL;

		// --------------------------------------------------------------------------

		$object->creator				= new stdClass();
		$object->creator->id			= $object->created_by ? (int) $object->created_by : NULL;
		$object->creator->first_name	= $object->first_name;
		$object->creator->last_name		= $object->last_name;
		$object->creator->email			= $object->email;
		$object->creator->profile_img	= $object->profile_img;
		$object->creator->gender		= $object->gender;

		unset( $object->created_by );
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
	 * Formats a bucket object
	 *
	 * @access	private
	 * @param	object	$bucket	The bucket to format
	 * @return	void
	 * @author	Pablo
	 **/
	private function _format_bucket( &$bucket )
	{
		$bucket->id				= (int) $bucket->id;
		$bucket->object_count	= (int) $bucket->object_count;
		$bucket->max_size		= (int) $bucket->max_size;
		$bucket->allowed_types	= array_filter( explode( '|', $bucket->allowed_types ) );
		$bucket->modified_by	= $bucket->modified_by ? (int) $bucket->modified_by : NULL;

		// --------------------------------------------------------------------------

		$bucket->creator				= new stdClass();
		$bucket->creator->id			= $bucket->created_by ? (int) $bucket->created_by : NULL;
		$bucket->creator->first_name	= $bucket->first_name;
		$bucket->creator->last_name		= $bucket->last_name;
		$bucket->creator->email			= $bucket->email;
		$bucket->creator->profile_img	= $bucket->profile_img;
		$bucket->creator->gender		= $bucket->gender;

		unset( $bucket->created_by );
		unset( $bucket->first_name );
		unset( $bucket->last_name );
		unset( $bucket->email );
		unset( $bucket->profile_img );
		unset( $bucket->gender );
	}


	// --------------------------------------------------------------------------


	private function _can_edit_object( $object, $user_id = NULL )
	{
		if ( is_numeric( $object ) || is_string( $object ) ) :

			$_object = $this->get_object( $object );

		else :

			$_object = $object;

		endif;

		if ( is_null( $user_id ) ) :

			$_user = active_user();

		else :

			$_user = get_userobject()->get_by_id( $user_id );

		endif;

		$_usrobj =& get_userobject();

		// --------------------------------------------------------------------------

		//	Admins can always read/write to objects
		if ( $_usrobj->is_admin( $_user ) ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $_object->creator->id || $_object->creator->id == $_user->id ) :

			return TRUE;

		endif;

		return FALSE;
	}


	// --------------------------------------------------------------------------


	private function _can_edit_bucket( $bucket, $user_id = NULL )
	{
		if ( is_numeric( $bucket ) || is_string( $bucket ) ) :

			$_bucket = $this->get_bucket( $bucket );

		else :

			$_bucket = $bucket;

		endif;

		if ( is_null( $user_id ) ) :

			$_user = active_user();

		else :

			$_user = get_userobject()->get_by_id( $user_id );

		endif;

		$_usrobj =& get_userobject();

		// --------------------------------------------------------------------------

		//	Admins can always read/write to buckets
		if ( $_usrobj->is_admin( $_user ) ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $_bucket->creator->id || $_bucket->creator->id == $_user->id ) :

			return TRUE;

		endif;

		return FALSE;
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

		$_file	= fopen( DEPLOY_CDN_MAGIC, 'r' );
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

		$_file = fopen( DEPLOY_CDN_MAGIC, 'r' );
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

			$_fi = @finfo_open( FILEINFO_MIME_TYPE, DEPLOY_CDN_MAGIC );

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
	public function url_serve( $object, $force_download = FALSE )
	{
		$_object = $this->get_object( $object );

		if ( ! $_object ) :

			//	Let the renderer show a bad_src graphic
			$_object				= new stdClass();
			$_object->filename		= '';
			$_object->bucket		= new stdClass();
			$_object->bucket->slug	= '';

		endif;

		return $this->_cdn->url_serve( $_object->filename, $_object->bucket->slug, $force_download );
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


	// --------------------------------------------------------------------------


	public function generate_api_upload_token( $user_id = NULL, $duration = 7200, $restrict_ip = TRUE )
	{
		if ( $user_id === NULL ) :

			$user_id = active_user( 'id' );

		endif;

		$_user = get_userobject()->get_by_id( $user_id );

		if ( ! $_user ) :

			$this->set_error( 'Invalid user ID' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------


		$_token		= array();
		$_token[]	= (int) $_user->id;			//	User ID
		$_token[]	= $_user->password_md5;		//	User Password
		$_token[]	= $_user->email;			//	User Email
		$_token[]	= time() + (int) $duration;	//	Expire time (+2hours)

		if ( $restrict_ip ) :

			$_token[]	= get_instance()->input->ip_address();

		else :

			$_token[]	= FALSE;

		endif;

		//	Hash
		$_token[]	= md5( serialize( $_token ) . APP_PRIVATE_KEY );

		//	Encrypt and return
		return get_instance()->encrypt->encode( implode( '|', $_token ), APP_PRIVATE_KEY );
	}


	// --------------------------------------------------------------------------


	public function validate_api_upload_token( $token )
	{
		$_token = get_instance()->encrypt->decode( $token, APP_PRIVATE_KEY );

		if ( ! $_token ) :

			//	Error #1: Could not decrypot
			$this->set_error( 'Invalid Token (Error #1)' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_token	 = explode( '|', $_token );

		if ( !$_token ) :

			//	Error #2: Could not explode
			$this->set_error( 'Invalid Token (Error #2)' );
			return FALSE;

		elseif ( count( $_token ) != 6 ) :

			//	Error #3: Bad count
			$this->set_error( 'Invalid Token (Error #3)' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Correct data types
		$_token[0]	= (int) $_token[0];
		$_token[3]	= (int) $_token[3];

		// --------------------------------------------------------------------------

		//	Check hash
		$_hash = $_token[5];
		unset( $_token[5]);

		if ( $_hash != md5( serialize( $_token ) . APP_PRIVATE_KEY ) ) :

			//	Error #4: Bad hash
			$this->set_error( 'Invalid Token (Error #4)' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch and check user
		$_user = get_userobject()->get_by_id( $_token[0] );

		//	User exists?
		if ( ! $_user ) :

			//	Error #5: User not found
			$this->set_error( 'Invalid Token (Error #5)' );
			return FALSE;

		endif;

		//	Valid email?
		if ( $_user->email != $_token[2] ) :

			//	Error #6: Invalid Email
			$this->set_error( 'Invalid Token (Error #6)' );
			return FALSE;

		endif;

		//	Valid password?
		if ( $_user->password_md5 != $_token[1] ) :

			//	Error #7: Invalid password
			$this->set_error( 'Invalid Token (Error #7)' );
			return FALSE;

		endif;

		//	User suspended?
		if ( $_user->is_suspended ) :

			//	Error #8: User suspended
			$this->set_error( 'Invalid Token (Error #8)' );
			return FALSE;

		endif;

		//	Valid IP?
		if ( ! $_token[4] && $_token[4] != get_instance()->input->ip_address() ) :

			//	Error #9: Invalid IP
			$this->set_error( 'Invalid Token (Error #9)' );
			return FALSE;

		endif;

		//	Expired?
		if ( $_token[3] < time() ) :

			//	Error #10: Token expired
			$this->set_error( 'Invalid Token (Error #10)' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	If we got here then the token is valid
		return $_user;
	}
}

/* End of file cdn.php */
/* Location: ./libraries/cdn.php */