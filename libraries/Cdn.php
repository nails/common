<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			CDN
*
* Description:	A Library for dealing with content in the CDN
*
*/

class Cdn
{

	private $_ci;
	private $_cdn;
	private $db;
	private $_errors;
	private $_magic;
	private $_cache_values;
	private $_cache_keys;
	private $_cache_method;

	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function __construct( $options = NULL )
	{
		$this->_ci		=& get_instance();
		$this->db		=& get_instance()->db;
		$this->_errors	= array();

		// --------------------------------------------------------------------------

		//	Set the cache method
		//	TODO: check for availability of things like memcached

		$this->_cache_values	= array();
		$this->_cache_keys		= array();
		$this->_cache_method	= 'LOCAL';

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

		// --------------------------------------------------------------------------

		//	Define the mime.magic file
		if ( ! DEPLOY_CDN_MAGIC ) :

			$_found			= FALSE;
			$_locations		= array();
			$_locations[]	= '/etc/mime.types';
			$_locations[]	= '/private/etc/apache2/mime.types';

			foreach( $_locations AS $location ) :

				if ( file_exists( $location ) ) :

					$_found	= $location;
					break;

				endif;

			endforeach;

			//	Did we find anything?
			if ( $_found ) :

				//	Whoop! We totes did.
				$this->_magic = $_found;;

			else :

				//	Hmm, set this to NULL so that PHP uses it's internal database
				$this->_magic = NULL;

			endif;

		else :

			$this->_magic = DEPLOY_CDN_MAGIC;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Destruct the model
	 *
	 * @access	public
	 * @return	void
	 **/
	public function __destruct()
	{
		//	Clear cache's
		if ( isset( $this->_cache_keys ) && $this->_cache_keys ) :

			foreach ( $this->_cache_keys AS $key ) :

				$this->_unset_cache( $key );

			endforeach;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Loads the appropriate driver
	 *
	 * @access	protected
	 * @return	void
	 **/
	protected function _include_driver()
	{
		switch ( strtoupper( APP_CDN_DRIVER ) ) :

			case 'AWS_LOCAL' :

				include_once NAILS_PATH . 'libraries/_resources/cdn_drivers/aws_local.php';
				return 'Aws_local_CDN';

			break;

			// --------------------------------------------------------------------------

			case 'LOCAL':
			default:

				include_once NAILS_PATH . 'libraries/_resources/cdn_drivers/local.php';
				return 'Local_CDN';

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/*	! CACHE METHODS */


	// --------------------------------------------------------------------------


	/**
	 * Provides models with the an easy interface for saving data to a cache.
	 *
	 * @access	protected
	 * @param string $key The key for the cached item
	 * @param mixed $value The data to be cached
	 * @return	array
	 **/
	protected function _set_cache( $key, $value )
	{
		if ( ! $key )
			return FALSE;

		// --------------------------------------------------------------------------

		//	Prep the key, the key should have a prefix unique to this model
		$_prefix = $this->_cache_prefix();

		// --------------------------------------------------------------------------

		switch ( $this->_cache_method ) :

			case 'LOCAL' :

				$this->_cache_values[md5( $_prefix . $key )] = serialize( $value );
				$this->_cache_keys[]	= $key;

			break;

			// --------------------------------------------------------------------------

			case 'MEMCACHED' :

				//	TODO

			break;

		endswitch;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Lookup a cache item
	 *
	 * @access	protected
	 * @param	string	$key	The key to fetch
	 * @return	mixed
	 **/
	protected function _get_cache( $key )
	{
		if ( ! $key )
			return FALSE;

		// --------------------------------------------------------------------------

		//	Prep the key, the key should have a prefix unique to this model
		$_prefix = $this->_cache_prefix();

		// --------------------------------------------------------------------------

		switch ( $this->_cache_method ) :

			case 'LOCAL' :

				if ( isset( $this->_cache_values[md5( $_prefix . $key )] ) ) :

					return unserialize( $this->_cache_values[md5( $_prefix . $key )] );

				else :

					return FALSE;

				endif;

			break;

			// --------------------------------------------------------------------------

			case 'MEMCACHED' :

				//	TODO

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Unset a cache item
	 *
	 * @access	protected
	 * @param	string	$key	The key to fetch
	 * @return	boolean
	 **/
	protected function _unset_cache( $key )
	{
		if ( ! $key )
			return FALSE;

		// --------------------------------------------------------------------------

		//	Prep the key, the key should have a prefix unique to this model
		$_prefix = $this->_cache_prefix();

		// --------------------------------------------------------------------------

		switch ( $this->_cache_method ) :

			case 'LOCAL' :

				unset( $this->_cache_values[md5( $_prefix . $key )] );

				$_key = array_search( $key, $this->_cache_keys );

				if ( $_key !== FALSE ) :

					unset( $this->_cache_keys[$_key] );

				endif;

			break;

			// --------------------------------------------------------------------------

			case 'MEMCACHED' :

				//	TODO

			break;

		endswitch;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Unset an object from the cache in one fell swoop
	 *
	 * @access	protected
	 * @param	object	$object	The object to remove from the cache
	 * @return	boolean
	 **/
	protected function _unset_cache_object( $object, $clear_cachedir = TRUE )
	{
		$this->_unset_cache( 'object-' . $object->id );
		$this->_unset_cache( 'object-' . $object->filename );
		$this->_unset_cache( 'object-' . $object->filename . '-' . $object->bucket->id );
		$this->_unset_cache( 'object-' . $object->filename . '-' . $object->bucket->slug );

		// --------------------------------------------------------------------------

		//	Clear out any
		if ( $clear_cachedir ) :

			// Create a handler for the directory
			$_bucket	= $object->bucket->slug;
			$_object	= $object->filename;

			$_pattern	= '#^' . $_bucket . '-' . substr( $_object, 0, strrpos( $_object, '.' ) ) . '#';
			$_fh		= opendir( DEPLOY_CACHE_DIR );

			// Open directory and walk through the filenames
			while ( $file = readdir( $_fh ) ) :

				// If file isn't this directory or its parent, add it to the results
				if ( $file != '.' && $file != '..' ) :

					// Check with regex that the file format is what we're expecting and not something else
					if( preg_match( $_pattern, $file ) ) :

						// add to our file array for later use
						@unlink( DEPLOY_CACHE_DIR . $file );

					endif;

				endif;

			endwhile;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Define the cache key prefix
	 *
	 * @access	protected
	 * @return	string
	 **/
	protected function _cache_prefix()
	{
		return 'CDN_';
	}


	// --------------------------------------------------------------------------


	/*	! ERROR METHODS */


	// --------------------------------------------------------------------------


	/**
	 * Retrieves the error array
	 *
	 * @access	public
	 * @return	array
	 **/
	public function get_errors()
	{
		return $this->_errors;
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the last error
	 *
	 * @access	public
	 * @return	string
	 **/
	public function last_error()
	{
		return end( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Adds an error message; not protected like model _set_error because the
	 * driver needs to be able to call it.
	 *
	 * @access	public
	 * @param	array	$error	The error message to add
	 * @return	void
	 **/
	public function set_error( $error )
	{
		$this->_errors[] = $error;
	}


	// --------------------------------------------------------------------------


	/**
	 * Catches shortcut calls
	 *
	 * @access	public
	 * @return	mixed
	 **/
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


	/**
	 * Retrieves all objects form the database
	 *
	 * @access	public
	 * @return	array
	 **/
	public function get_objects( $page = NULL, $per_page = NULL, $data = array(), $_caller = 'GET_OBJECTS' )
	{
		$this->db->select( 'o.id, o.filename, o.filename_display, o.created, o.created_by, o.modified, o.modified_by, o.serves, o.downloads, o.thumbs, o.scales' );
		$this->db->select( 'o.mime, o.filesize, o.img_width, o.img_height, o.is_animated' );
		$this->db->select( 'ue.email, u.first_name, u.last_name, u.profile_img, u.gender' );
		$this->db->select( 'b.id bucket_id, b.label bucket_label, b.slug bucket_slug' );

		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = o.created_by', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = o.created_by AND ue.is_primary = 1', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'cdn_bucket b', 'b.id = o.bucket_id', 'LEFT' );

		// --------------------------------------------------------------------------

		//	Apply common items; pass $data
		$this->_getcount_objects_common( $data, $_caller );

		// --------------------------------------------------------------------------

		//	Facilitate pagination
		if ( NULL !== $page ) :

			//	Adjust the page variable, reduce by one so that the offset is calculated
			//	correctly. Make sure we don't go into negative numbers
			$page--;
			$page = $page < 0 ? 0 : $page;

			//	Work out what the offset should be
			$_per_page	= NULL == $per_page ? 50 : (int) $per_page;
			$_offset	= $page * $per_page;

			$this->db->limit( $per_page, $_offset );

		endif;

		// --------------------------------------------------------------------------

		$_objects = $this->db->get( NAILS_DB_PREFIX . 'cdn_object o' )->result();

		for ( $i = 0; $i < count( $_objects ); $i++ ) :

			//	Format the object, make it pretty
			$this->_format_object( $_objects[$i] );

		endfor;

		return $_objects;
	}


	// --------------------------------------------------------------------------


	/**
	 * Retrieves all trashed objects form the database
	 *
	 * @access	public
	 * @return	array
	 **/
	public function get_objects_from_trash( $page = NULL, $per_page = NULL, $data = array(), $_caller = 'GET_OBJECTS_FROM_TRASH' )
	{
		$this->db->select( 'o.id, o.filename, o.filename_display, o.created, o.created_by, o.modified, o.modified_by, o.serves, o.downloads, o.thumbs, o.scales' );
		$this->db->select( 'o.mime, o.filesize, o.img_width, o.img_height, o.is_animated' );
		$this->db->select( 'ue.email, u.first_name, u.last_name, u.profile_img, u.gender' );
		$this->db->select( 'b.id bucket_id, b.label bucket_label, b.slug bucket_slug' );

		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = o.created_by', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = o.created_by AND ue.is_primary = 1', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'cdn_bucket b', 'b.id = o.bucket_id', 'LEFT' );

		// --------------------------------------------------------------------------

		//	Apply common items; pass $data
		$this->_getcount_objects_common( $data, $_caller );

		// --------------------------------------------------------------------------

		//	Facilitate pagination
		if ( NULL !== $page ) :

			//	Adjust the page variable, reduce by one so that the offset is calculated
			//	correctly. Make sure we don't go into negative numbers
			$page--;
			$page = $page < 0 ? 0 : $page;

			//	Work out what the offset should be
			$_per_page	= NULL == $per_page ? 50 : (int) $per_page;
			$_offset	= $page * $per_page;

			$this->db->limit( $per_page, $_offset );

		endif;

		// --------------------------------------------------------------------------

		$_objects = $this->db->get( NAILS_DB_PREFIX . 'cdn_object_trash o' )->result();

		for ( $i = 0; $i < count( $_objects ); $i++ ) :

			//	Format the object, make it pretty
			$this->_format_object( $_objects[$i] );

		endfor;

		return $_objects;
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns a single object object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 **/
	public function get_object( $object, $bucket = NULL, $data = array() )
	{
		if ( is_numeric( $object ) ) :

			//	Check the cache
			$_cache_key	= 'object-' . $object;
			$_cache		= $this->_get_cache( $_cache_key );

			if ( $_cache ) :

				return $_cache;

			endif;

			// --------------------------------------------------------------------------

			$this->db->where( 'o.id', $object );

		else :

			//	Check the cache
			$_cache_key	 = 'object-' . $object;
			$_cache_key .= $bucket ? '-' . $bucket : '';
			$_cache		 = $this->_get_cache( $_cache_key );

			if ( $_cache ) :

				return $_cache;

			endif;

			// --------------------------------------------------------------------------

			$this->db->where( 'o.filename', $object );

			if ( $bucket ) :

				if ( is_numeric( $bucket ) ) :

					$this->db->where( 'b.id', $bucket );

				else :

					$this->db->where( 'b.slug', $bucket );

				endif;

			endif;

		endif;

		$_objects = $this->get_objects( NULL, NULL, $data, 'GET_OBJECT' );

		if ( ! $_objects ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Cache the object
		$this->_set_cache( $_cache_key, $_objects[0] );

		// --------------------------------------------------------------------------

		return $_objects[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns a single object object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 **/
	public function get_object_from_trash( $object, $bucket = NULL, $data = array() )
	{
		if ( is_numeric( $object ) ) :

			//	Check the cache
			$_cache_key	= 'object-trash-' . $object;
			$_cache		= $this->_get_cache( $_cache_key );

			if ( $_cache ) :

				return $_cache;

			endif;

			// --------------------------------------------------------------------------

			$this->db->where( 'o.id', $object );

		else :

			//	Check the cache
			$_cache_key	 = 'object-trash-' . $object;
			$_cache_key .= $bucket ? '-' . $bucket : '';
			$_cache		 = $this->_get_cache( $_cache_key );

			if ( $_cache ) :

				return $_cache;

			endif;

			// --------------------------------------------------------------------------

			$this->db->where( 'o.filename', $object );

			if ( $bucket ) :

				if ( is_numeric( $bucket ) ) :

					$this->db->where( 'b.id', $bucket );

				else :

					$this->db->where( 'b.slug', $bucket );

				endif;

			endif;

		endif;

		$_objects = $this->get_objects_from_trash( NULL, NULL, $data, 'GET_OBJECT_FROM_TRASH' );

		if ( ! $_objects ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Cache the object
		$this->_set_cache( $_cache_key, $_objects[0] );

		// --------------------------------------------------------------------------

		return $_objects[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts all objects
	 *
	 * @access public
	 * @param mixed $data any data to pass to _getcount_objects_common()
	 * @return int
	 **/
	public function count_all_objects( $data = array() )
	{
		//	Apply common items
		$this->_getcount_objects_common( $data, 'COUNT_ALL_OBJECTS' );

		// --------------------------------------------------------------------------

		return $this->db->count_all_results( NAILS_DB_PREFIX . 'cdn_object o'  );
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts all objects
	 *
	 * @access public
	 * @param mixed $data any data to pass to _getcount_objects_common()
	 * @return int
	 **/
	public function count_all_objects_from_trash( $data = array() )
	{
		//	Apply common items
		$this->_getcount_objects_common( $data, 'COUNT_ALL_OBJECTS_FROM_TRASH' );

		// --------------------------------------------------------------------------

		return $this->db->count_all_results( NAILS_DB_PREFIX . 'cdn_object_trash o'  );
	}


	// --------------------------------------------------------------------------


	/**
	 * Applies common conditionals
	 *
	 * This method applies the conditionals which are common across the get_*()
	 * methods and the count() method.
	 *
	 * @access public
	 * @param string $data Data passed from the calling method
	 * @param string $_caller The name of the calling method
	 * @return void
	 **/
	protected function _getcount_objects_common( $data = array(), $_caller = NULL )
	{
		//	Handle wheres
		$_wheres = array( 'where', 'where_in', 'or_where_in', 'where_not_in', 'or_where_not_in' );

		foreach ( $_wheres AS $where_type ) :

			if ( ! empty( $data[$where_type] ) ) :

				if ( is_array( $data[$where_type] ) ) :

					//	If it's a single dimensional array then just bung that into
					//	the db->where(). If not, loop it and parse.

					$_first = reset( $data[$where_type] );

					if ( is_string( $_first ) ) :

						$this->db->$where_type( $data[$where_type] );

					else :

						foreach( $data[$where_type] AS $where ) :

							//	Work out column
							$_column = ! empty( $where['column'] ) ? $where['column'] : NULL;
							if ( $_column === NULL ) :

								$_column = ! empty( $where[0] ) && is_string( $where[0] ) ? $where[0] : NULL;

							endif;

							//	Work out value
							$_value = isset( $where['value'] ) ? $where['value'] : NULL;
							if ( $_value === NULL ) :

								$_value = ! empty( $where[1] ) ? $where[1] : NULL;

							endif;

							$_escape = isset( $where['escape'] ) ? (bool) $where['escape'] : TRUE;

							if ( $_column ) :

								$this->db->$where_type( $_column, $_value, $_escape );

							endif;

						endforeach;

					endif;

				elseif ( is_string( $data[$where_type] ) ) :

					$this->db->$where_type( $data[$where_type] );

				endif;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Handle Likes
		//	TODO

		// --------------------------------------------------------------------------

		//	Handle sorting
		if ( ! empty( $data['sort'] ) ) :

			/**
			 * How we handle sorting
			 * =====================
			 *
			 * - If $data['sort'] is a string assume it's the field to sort on, use the default order
			 * - If $data['sort'] is a single dimension array then assume the first element (or the element
			 *   named 'column') is the column; and the second element (or the element named 'order') is the
			 *   direction to sort in
			 * - If $data['sort'] is a multidimensional array then loop each element and test as above.
			 *
			 **/


			if ( is_string( $data['sort'] ) ) :

				//	String
				$this->db->order_by( $data['sort'] );

			elseif( is_array( $data['sort'] ) ) :

				$_first = reset( $data['sort'] );

				if ( is_string( $_first ) ) :

					//	Single dimension array
					$_sort = $this->_getcount_objects_common_parse_sort( $data['sort'] );

					if ( ! empty( $_sort['column'] ) ) :

						$this->db->order_by( $_sort['column'], $_sort['order'] );

					endif;

				else :

					//	Multi dimension array
					foreach( $data['sort'] AS $sort ) :

						$_sort = $this->_getcount_objects_common_parse_sort( $sort );

						if ( ! empty( $_sort['column'] ) ) :

							$this->db->order_by( $_sort['column'], $_sort['order'] );

						endif;

					endforeach;

				endif;

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Parses the sort field which may be passed to the get_all methods
	 *
	 * @access	protected
	 * @return	array
	 **/
	protected function _getcount_objects_common_parse_sort( $sort )
	{
		$_out = array( 'column' => NULL, 'order' => NULL );

		// --------------------------------------------------------------------------

		if ( is_string( $sort ) ) :

			$_out['column'] = $sort;
			return $_out;

		elseif ( isset( $sort['column'] ) ) :

			$_out['column'] = $sort['column'];

		else :

			//	Take the first element
			$_out['column'] = reset( $sort );
			$_out['column'] = is_string( $_out['column'] ) ? $_out['column'] : NULL;

		endif;

		if ( $_out['column'] ) :

			//	Determine order
			if ( isset( $sort['order'] ) ) :

				$_out['order'] = $sort['order'];

			elseif( count( $sort ) > 1 ) :

				//	Take the last element
				$_out['order'] = end( $sort );
				$_out['order'] = is_string( $_out['order'] ) ? $_out['order'] : NULL;

			endif;

		endif;

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns objects uploaded by the user
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 **/
	public function get_objects_for_user( $user_id, $page = NULL, $per_page = NULL, $data = array(), $_caller = 'GET_OBJECTS_FOR_USER' )
	{
		$this->db->where( 'o.created_by', $user_id );
		return $this->get_objects( $page, $per_page, $data, $_caller );
	}


	// --------------------------------------------------------------------------


	/**
	 * Calls the upload method of the driver
	 *
	 * @access	public
	 * @param	none
	 * @return	void
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
					$_name	= empty( $options['filename_display'] ) ? basename( $object ) : $options['filename_display'];

				endif;

			else :

				$_file	= $_FILES[ $object ]['tmp_name'];
				$_name	= empty( $options['filename_display'] ) ? $_FILES[ $object ]['name'] : $options['filename_display'];

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
				if ( is_writeable( DEPLOY_CACHE_DIR ) ) :

					$_cache_file = sha1( microtime() . rand( 0 ,999 ) . active_user( 'id' ) );
					$_fp = fopen( DEPLOY_CACHE_DIR . $_cache_file, 'w' );
					fwrite( $_fp, $object );
					fclose( $_fp );

					// --------------------------------------------------------------------------

					//	Specify the file specifics
					$_file			= DEPLOY_CACHE_DIR . $_cache_file;
					$_name			= empty( $options['filename_display'] ) ? $_cache_file : $options['filename_display'];
					$_data->file	= DEPLOY_CACHE_DIR . $_cache_file;
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

				$_data->bucket			= new stdClass();
				$_data->bucket->id		= $_bucket->id;
				$_data->bucket->slug	= $_bucket->slug;

			else :

				return FALSE;

			endif;

		else :

			$_data->bucket			= new stdClass();
			$_data->bucket->id		= $_bucket->id;
			$_data->bucket->slug	= $_bucket->slug;

		endif;

		// --------------------------------------------------------------------------

		//	Is this an acceptable file? Check against the allowed_types array (if present)

		$_ext = $this->get_ext_from_mimetype( $_data->mime );	//	So other parts of this method can access $_ext;
		if ( $_bucket->allowed_types ) :

			//	Handle stupid bloody MS Office 'x' documents
			//	If the returned extension is doc, xls or ppt compare it to the uploaded
			//	extension but append an x, if they match then force the x version.
			//	Also override the mime type

			//	Makka sense? Hate M$.
			$_user_ext = substr( $_name, strrpos( $_name, '.' ) + 1 );

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

			$_data->filename =  $_name;

		elseif ( isset( $options['filename'] ) && $options['filename'] ) :

			$_data->filename = $options['filename'];

		else :

			//	Generate a filename
			$_data->filename = time() . '_' . md5( active_user( 'id' ) . microtime( TRUE ) . rand( 0, 999 ) ) . '.' . $_ext;

		endif;

		//	And set the display name
		$_data->name	= $_name;

		// --------------------------------------------------------------------------

		$_upload = $this->_cdn->object_create( $_data );

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

			@unlink( DEPLOY_CACHE_DIR . $_cache_file );

		endif;

		// --------------------------------------------------------------------------

		return $_status;
	}


	// --------------------------------------------------------------------------


	/**
	 * Deletes an object
	 *
	 * @access	public
	 * @return	boolean
	 **/
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

		//	Turn off DB Errors
		$_previous = $this->db->db_debug;
		$this->db->db_debug = FALSE;

		//	Start transaction
		$this->db->trans_start();

			//	Create trash object
			$this->db->insert( NAILS_DB_PREFIX . 'cdn_object_trash' );

			//	Remove original object
			$this->db->where( 'id', $_object->id );
			$this->db->delete( NAILS_DB_PREFIX . 'cdn_object' );

		$this->db->trans_complete();

		//	Set DB errors as they were
		$this->db->db_debug = $_previous;

		if ( $this->db->trans_status() !== FALSE ) :

			//	Clear caches
			$this->_unset_cache_object( $_object );

			// --------------------------------------------------------------------------

			return TRUE;

		else :

			$this->set_error( lang( 'cdn_error_delete' ) );
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Restores an object from the trash
	 *
	 * @access	public
	 * @return	boolean
	 **/
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
			$this->db->insert( NAILS_DB_PREFIX . 'cdn_object' );

			//	Remove trash object
			$this->db->where( 'id', $_object->id );
			$this->db->delete( NAILS_DB_PREFIX . 'cdn_object_trash' );

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
	 **/
	public function object_destroy( $object_id )
	{
		if ( ! $object_id ) :

			$this->set_error( lang( 'cdn_error_object_invalid' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_object = $this->get_object( $object_id );

		if ( $_object ) :

			//	Delete the object first
			if ( ! $this->object_delete( $_object->id ) ) :

				return FALSE;

			endif;

		else :

			//	Object doesn't exist but may exist in the trash
			$_object = $this->get_object_from_trash( $object_id );

			if ( ! $_object ) :

				$this->set_error( 'Nothing to destroy.' );
				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Attempt to remove the file
		if ( $this->_cdn->object_destroy( $_object->filename, $_object->bucket->slug ) ) :

			//	Remove the database entries
			$this->db->trans_begin();

			$this->db->where( 'id', $_object->id );
			$this->db->delete( NAILS_DB_PREFIX . 'cdn_object' );

			$this->db->where( 'id', $_object->id );
			$this->db->delete( NAILS_DB_PREFIX . 'cdn_object_trash' );

			if ( $this->db->trans_status() === FALSE ) :

				$this->db->trans_rollback();
				return FALSE;

			else :

				$this->db->trans_commit();

			endif;

			// --------------------------------------------------------------------------

			//	Clear the caches
			$this->_unset_cache_object( $_object );

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
		$_tag = $this->db->get( NAILS_DB_PREFIX . 'cdn_bucket_tag t' )->row();

		if ( ! $_tag ) :

			$this->set_error( lang( 'cdn_error_tag_invalid' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Test if tag has already been applied to the object, if it has gracefully fail
		$this->db->where( 'object_id', $_object->id );
		$this->db->where( 'tag_id', $_tag->id );
		if ( $this->db->count_all_results( NAILS_DB_PREFIX . 'cdn_object_tag' ) ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		//	Seems good, add the tag
		$this->db->set( 'object_id', $_object->id );
		$this->db->set( 'tag_id', $_tag->id );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->insert( NAILS_DB_PREFIX . 'cdn_object_tag' );

		return $this->db->affected_rows() ? TRUE : FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Deletes a tag from an object
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
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
		$_tag = $this->db->get( NAILS_DB_PREFIX . 'cdn_bucket_tag t' )->row();

		if ( ! $_tag ) :

			$this->set_error( lang( 'cdn_error_tag_invalid' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Seems good, delete the tag
		$this->db->where( 'object_id', $_object->id );
		$this->db->where( 'tag_id', $_tag->id );
		$this->db->delete( NAILS_DB_PREFIX . 'cdn_object_tag' );

		return $this->db->affected_rows() ? TRUE : FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts the number of objects a tag contains
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 **/
	public function object_tag_count( $tag_id )
	{
		$this->db->where( 'ot.tag_id', $tag_id );
		$this->db->join( NAILS_DB_PREFIX . 'cdn_object o', 'o.id = ot.object_id' );
		return $this->db->count_all_results( NAILS_DB_PREFIX . 'cdn_object_tag ot' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Increments the stats of the object
	 *
	 * @access	public
	 * @param	none
	 * @return	string
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
			$this->db->update( NAILS_DB_PREFIX . 'cdn_object o' );

		elseif ( $bucket ) :

			$this->db->where( 'b.slug', $bucket );
			$this->db->update( NAILS_DB_PREFIX . 'cdn_object o JOIN ' . NAILS_DB_PREFIX . 'cdn_bucket b ON b.id = o.bucket_id' );

		else :

			$this->db->update( NAILS_DB_PREFIX . 'cdn_object o' );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Creates a new object record in the DB; called from various other methods
	 *
	 * @access	public
	 * @param	array
	 * @param	boolean
	 * @return	string
	 **/
	protected function _create_object( $data, $return_object = FALSE )
	{
		$this->db->set( 'bucket_id',		$data->bucket->id );
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

		$this->db->insert( NAILS_DB_PREFIX . 'cdn_object' );

		$_object_id = $this->db->insert_id();

		if ( $this->db->affected_rows() ) :

			//	Add a tag if there's one defined
			if ( isset( $data->tag_id ) && ! empty( $data->tag_id ) ) :

				$this->db->where( 'id', $data->tag_id );

				if ( $this->db->count_all_results( NAILS_DB_PREFIX . 'cdn_bucket_tag' ) ) :

					$this->db->set( 'object_id',	$_object_id );
					$this->db->set( 'tag_id',		$data->tag_id );
					$this->db->set( 'created',		'NOW()', FALSE );

					$this->db->insert( NAILS_DB_PREFIX . 'cdn_object_tag' );

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
	 * @access	protected
	 * @param	object	$object	The object to format
	 * @return	void
	 **/
	protected function _format_object( &$object )
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
		$object->bucket->label	= $object->bucket_label;
		$object->bucket->slug	= $object->bucket_slug;

		unset( $object->bucket_id );
		unset( $object->bucket_label );
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


	/*	! BUCKET METHODS */


	// --------------------------------------------------------------------------


	/**
	 * Returns an array of all bucket objects
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 **/
	public function get_buckets( $list_bucket = FALSE, $filter_tag = FALSE, $include_deleted = FALSE )
	{
		$this->db->select( 'b.id,b.slug,b.label,b.allowed_types,b.max_size,b.created,b.created_by,b.modified,b.modified_by' );
		$this->db->select( 'ue.email, u.first_name, u.last_name, u.profile_img, u.gender' );
		$this->db->select( '(SELECT COUNT(*) FROM ' . NAILS_DB_PREFIX . 'cdn_object WHERE bucket_id = b.id) object_count' );

		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = b.created_by', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = b.created_by AND ue.is_primary = 1', 'LEFT' );

		$_buckets = $this->db->get( NAILS_DB_PREFIX . 'cdn_bucket b' )->result();

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
			$this->db->select( '(SELECT COUNT(*) FROM ' . NAILS_DB_PREFIX . 'cdn_object_tag ot JOIN ' . NAILS_DB_PREFIX . 'cdn_object o ON o.id = ot.object_id WHERE tag_id = bt.id ) total' );
			$this->db->order_by( 'bt.label' );
			$this->db->where( 'bt.bucket_id', $bucket->id );
			$bucket->tags = $this->db->get( NAILS_DB_PREFIX . 'cdn_bucket_tag bt' )->result();

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

			$this->db->insert( NAILS_DB_PREFIX . 'cdn_bucket' );

			if ( $this->db->affected_rows() ) :

				return $this->db->insert_id();

			else :

				$this->_cdn->destroy( $bucket );

				$this->set_error( lang( 'cdn_error_bucket_insert' ) );
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
	 **/
	public function bucket_list( $bucket, $filter_tag = FALSE )
	{
		//	Filtering by tag?
		if ( $filter_tag ) :

			$this->db->join( NAILS_DB_PREFIX . 'cdn_object_tag ft', 'ft.object_id = o.id AND ft.tag_id = ' . $filter_tag );

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
	 **/
	public function bucket_destroy( $bucket )
	{
		$_bucket = $this->get_bucket( $bucket, TRUE );

		if ( ! $_bucket ) :

			$this->set_error( lang( 'cdn_error_bucket_invalid' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Destroy any containing objects
		$_errors = 0;
		foreach( $_bucket->objects AS $obj ) :

			if ( ! $this->object_destroy( $obj->id ) ) :

				$this->set_error( 'Unable to delete object "' . $obj->filename_display . '" (ID:' . $obj->id . ').' );
				$_errors++;

			endif;

		endforeach;

		if ( $_errors ) :

			$this->set_error( 'Unable to delete bucket, bucket not empty.' );
			return FALSE;

		else :

			//	Remove the bucket
			if ( $this->_cdn->bucket_destroy( $_bucket->slug ) ) :

				$this->db->where( 'id', $_bucket->id );
				$this->db->delete( NAILS_DB_PREFIX . 'cdn_bucket' );

				return TRUE;

			else :

				$this->set_error( 'Unable to remove empty bucket directory.' );
				return FALSE;

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Adds a tag to a bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
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

		// --------------------------------------------------------------------------

		//	Test tag
		$this->db->where( 'bucket_id', $_bucket->id );
		$this->db->where( 'label', $label );
		if ( $this->db->count_all_results( NAILS_DB_PREFIX . 'cdn_bucket_tag' ) ) :

			$this->set_error( lang( 'cdn_error_tag_exists' ) );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Seems good, add the tag
		$this->db->set( 'bucket_id', $_bucket->id );
		$this->db->set( 'label', $label );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->insert( NAILS_DB_PREFIX . 'cdn_bucket_tag' );

		return $this->db->affected_rows() ? TRUE : FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Deletes a tag from a bucket
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
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

		// --------------------------------------------------------------------------

		//	Test tag
		$this->db->where( 'bucket_id', $_bucket->id );

		if ( is_numeric( $label ) ) :

			$this->db->where( 'id', $label );

		else :

			$this->db->where( 'label', $label );

		endif;


		if ( ! $this->db->count_all_results( NAILS_DB_PREFIX . 'cdn_bucket_tag' ) ) :

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

		$this->db->delete( NAILS_DB_PREFIX . 'cdn_bucket_tag' );

		return $this->db->affected_rows() ? TRUE : FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renames a bucket tag
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 **/
	public function bucket_tag_rename( $bucket, $label, $new_name )
	{
		//	TODO: Rename a bucket tag
		return FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Formats a bucket object
	 *
	 * @access	protected
	 * @param	object	$bucket	The bucket to format
	 * @return	void
	 **/
	protected function _format_bucket( &$bucket )
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


	/**
	 * Attempts to detect whether a gif is animated or not
	 * Credit where credit's due: http://php.net/manual/en/function.imagecreatefromgif.php#59787
	 *
	 * @access	protected
	 * @param	string $file the path to the file to check
	 * @return	boolean
	 **/
	protected function _detect_animated_gif( $file )
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
	 * Fetches the extension from the mime type
	 *
	 * @access	public
	 * @return	string
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

		if ( ! $this->_magic ) :

			$_file	= fopen( $this->_magic, 'r' );
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

		else :

			//	We don't have a magic database, eep. Try to work it out using CodeIgniter's mapping
			require FCPATH . APPPATH . 'config/mimes.php';

			$_ext = FALSE;

			foreach ( $mimes AS $ext => $mime ) :

				if ( is_array( $mime ) ) :

					foreach( $mime AS $submime ) :

						if ( $submime == $mime_type ) :

							$_ext = $ext;
							break;

						endif;

					endforeach;

					if ( $_ext ) :

						break;

					endif;

				else :

					if ( $mime == $mime_type ) :

						$_ext = $ext;
						break;

					endif;

				endif;

			endforeach;


		endif;

		// --------------------------------------------------------------------------

		//	Being anal here, some extensions *need* to be forced
		switch ( $_ext ) :

			case 'jpeg' : $_ext = 'jpg';	break;

		endswitch;

		// --------------------------------------------------------------------------

		return $_ext;
	}


	// --------------------------------------------------------------------------


	/**
	 * Gets the mime type from the extension
	 *
	 * @access	public
	 * @return	string
	 **/
	public function get_mimetype_from_ext( $ext )
	{
		//	Prep $ext, make sure it has no dots
		$ext = substr( $ext, (int) strrpos( $ext, '.' ) + 1 );

		// --------------------------------------------------------------------------

		//	Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
		//	Thanks, 'chaos' - http://stackoverflow.com/a/1147952/789224

		$_file = fopen( $this->_magic, 'r' );
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


	/**
	 * Gets the mime type of a file on disk
	 *
	 * @access	public
	 * @return	string
	 **/
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

			$_fi = @finfo_open( FILEINFO_MIME_TYPE, $this->_magic );

			if ( $_fi ) :

				$_result = finfo_file( $_fi, $object );

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
	 **/
	public function url_serve_scheme( $force_download = FALSE )
	{
		return $this->_cdn->url_serve_scheme( $force_download );
	}


	// --------------------------------------------------------------------------


	/**
	 * Calls the driver's public cdn_serve_url method
	 *
	 * @access	public
	 * @param	array $objects An array of the Object IDs which should be zipped together
	 * @return	string
	 **/
	public function url_serve_zipped( $objects, $filename = 'download.zip' )
	{
		$_data		= array( 'where_in' => array( array( 'o.id', $objects ) ) );
		$_objects	= $this->get_objects( $_data );

		$_ids		= array();
		$_ids_hash	= array();
		foreach ( $_objects AS $obj ) :

			$_ids[]			= $obj->id;
			$_ids_hash[]	= $obj->id . $obj->bucket->id;

		endforeach;

		$_ids		= implode( '-', $_ids );
		$_ids_hash	= implode( '-', $_ids_hash );
		$_hash		= md5( APP_PRIVATE_KEY . $_ids . $_ids_hash . $filename );

		return $this->_cdn->url_serve_zipped( $_ids, $_hash, $filename );
	}


	// --------------------------------------------------------------------------


	/**
	 * Verifies a zip file's hash
	 *
	 * @access	public
	 * @return	boolean
	 **/
	public function verify_url_serve_zipped_hash( $hash, $objects, $filename = 'download.zip' )
	{
		if ( ! is_array( $objects ) ) :

			$objects = explode( '-', $objects );

		endif;

		$_data		= array( 'where_in' => array( array( 'o.id', $objects ) ) );
		$_objects	= $this->get_objects( $_data );

		$_ids		= array();
		$_ids_hash	= array();

		foreach ( $_objects AS $obj ) :

			$_ids[]			= $obj->id;
			$_ids_hash[]	= $obj->id . $obj->bucket->id;

		endforeach;

		$_ids		= implode( '-', $_ids );
		$_ids_hash	= implode( '-', $_ids_hash );

		return md5( APP_PRIVATE_KEY . $_ids . $_ids_hash . $filename ) === $hash ? $_objects : FALSE;;
	}


	// --------------------------------------------------------------------------


	/**
	 * Calls the driver's public cdn_serve_url_scheme method
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 **/
	public function url_serve_zipped_scheme( $filename = NULL )
	{
		return $this->_cdn->url_serve_scheme( $filename );
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
	 **/
	public function url_expiring_scheme()
	{
		return $this->_cdn->url_expiring_scheme();
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates an API upload token.
	 *
	 * @access	public
	 * @return	string
	 **/
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


	/**
	 * Verifies an aPI upload token
	 *
	 * @access	public
	 * @return	string
	 **/
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


	// --------------------------------------------------------------------------


	/**
	 * Finds objects which have no file coutnerparts
	 *
	 * @access	public
	 * @return	string
	 **/
	public function find_orphaned_objects()
	{
		$_out = array( 'orphans' => array(), 'elapsed_time' => 0 );

		//	Time how long this takes
		//	Start timer
		$this->_ci->benchmark->mark( 'orphan_search_start' );

		$this->db->select( 'o.id, o.filename, o.filename_display, o.mime, o.filesize, b.slug bucket_slug, b.label bucket' );
		$this->db->join( NAILS_DB_PREFIX . 'cdn_bucket b', 'o.bucket_id = b.id' );
		$this->db->order_by( 'b.label' );
		$this->db->order_by( 'o.filename_display' );
		$_orphans = $this->db->get( NAILS_DB_PREFIX . 'cdn_object o' );

		while ( $row = $_orphans->_fetch_object() ) :

			if ( ! $this->_cdn->object_exists( $row->filename, $row->bucket_slug ) ) :

				$_out['orphans'][] = $row;

			endif;

		endwhile;

		//	End timer
		$this->_ci->benchmark->mark( 'orphan_search_end' );
		$_out['elapsed_time'] = $this->_ci->benchmark->elapsed_time( 'orphan_search_start', 'orphan_search_end' );

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Finds fiels which have no object coutnerparts
	 *
	 * @access	public
	 * @return	string
	 **/
	public function find_orphaned_files()
	{
		return array();
	}


	// --------------------------------------------------------------------------


	/**
	 * Runs the CDN tests
	 *
	 * @access	public
	 * @return	string
	 **/
	public function run_tests()
	{
		//	If defined, run the pre_test method for the driver
		$_result = TRUE;
		if ( method_exists( $this->_cdn, 'pre_test' ) ) :

			call_user_func( array( $this->_cdn, 'pre_test' ) );

		endif;

		// --------------------------------------------------------------------------

		//	Run tests
		$this->_ci->load->library( 'curl' );

		// --------------------------------------------------------------------------

		//	Create a test bucket
		$_test_id			= md5( microtime( TRUE ) . uniqid() );
		$_test_bucket		= 'test-' . $_test_id;
		$_test_bucket_id	= $this->bucket_create( $_test_bucket, $_test_bucket );

		if ( ! $_test_bucket_id ) :

			$this->set_error( 'Failed to create a new bucket.' );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch and test all buckets
		$_buckets = $this->get_buckets();

		foreach ( $_buckets AS $bucket ) :

			//	Can fetch bucket by ID?
			$_bucket = $this->get_bucket( $bucket->id );

			if( ! $_bucket ) :

				$this->set_error( 'Unable to fetch bucket by ID; ID: ' . $bucket->id );
				continue;

			endif;

			// --------------------------------------------------------------------------

			//	Can fetch bucket by slug?
			$_bucket = $this->get_bucket( $bucket->slug );

			if( ! $_bucket ) :

				$this->set_error( 'Unable to fetch bucket by slug; slug: ' . $bucket->slug );
				continue;

			endif;

			// --------------------------------------------------------------------------

			//	Can we write a small image to the bucket? Or a PDf, whatever the bucket
			//	will accept. Do these in order of filesize, we want to be dealing with as
			//	small a file as possible.

			$_file			= array();
			$_file['txt']	= NAILS_PATH . 'assets/tests/cdn/txt.txt';
			$_file['jpg']	= NAILS_PATH . 'assets/tests/cdn/jpg.jpg';
			$_file['pdf']	= NAILS_PATH . 'assets/tests/cdn/pdf.pdf';

			if ( empty( $_bucket->allowed_types ) ) :

				//	Not specified, use the txt as it's so tiny
				$_file = $_file['txt'];

			else :

				//	find a file we can use
				foreach( $_file AS $ext => $path ) :

					if ( array_search( $ext, $_bucket->allowed_types ) !== FALSE ) :

						$_file = $path;
						break;

					endif;

				endforeach;

			endif;

			//	Copy this file temporarily to the cache
			$_cachefile = DEPLOY_CACHE_DIR . 'test-' . $bucket->slug . '-' . $_test_id . '.jpg';

			if ( ! @copy( $_file, $_cachefile ) ) :

				$this->set_error( 'Unable to create temporary cache file.' );
				continue;

			endif;

			$_upload = $this->object_create( $_cachefile, $_bucket->id );

			if ( ! $_upload ) :

				$this->set_error( 'Unable to create a new object in bucket "' . $bucket->id . ' / ' . $bucket->slug . '"' );
				continue;

			endif;

			// --------------------------------------------------------------------------

			//	Can we serve the object?
			$_url = $this->url_serve( $_upload->id );

			if ( ! $_url ) :

				$this->set_error( 'Unable to generate serve URL for uploaded file' );
				continue;

			endif;

			$_test	= $this->_ci->curl->simple_get( $_url );
			$_code	= ! empty( $this->_ci->curl->info['http_code'] ) ? $this->_ci->curl->info['http_code'] : '';

			if ( ! $_test || $_code != 200 ) :

				$this->set_error( 'Failed to serve object with 200 OK (' . $bucket->slug . ' / ' . $_upload->filename . ').<small>' . $_url . '</small>' );
				continue;

			endif;

			// --------------------------------------------------------------------------

			//	Can we thumb the object?
			$_url = $this->url_thumb( $_upload->id, 10, 10 );

			if ( ! $_url ) :

				$this->set_error( 'Unable to generate thumb URL for object.' );
				continue;

			endif;

			$_test	= $this->_ci->curl->simple_get( $_url );
			$_code	= ! empty( $this->_ci->curl->info['http_code'] ) ? $this->_ci->curl->info['http_code'] : '';

			if ( ! $_test || $_code != 200 ) :

				$this->set_error( 'Failed to thumb object with 200 OK (' . $bucket->slug . ' / ' . $_upload->filename . ').<small>' . $_url . '</small>' );
				continue;

			endif;

			// --------------------------------------------------------------------------

			//	Can we scale the object?
			$_url = $this->url_scale( $_upload->id, 10, 10 );

			if ( ! $_url ) :

				$this->set_error( 'Unable to generate scale URL for object.' );
				continue;

			endif;

			$_test	= $this->_ci->curl->simple_get( $_url );
			$_code	= ! empty( $this->_ci->curl->info['http_code'] ) ? $this->_ci->curl->info['http_code'] : '';

			if ( ! $_test || $_code != 200 ) :

				$this->set_error( 'Failed to scale object with 200 OK (' . $bucket->slug . ' / ' . $_upload->filename . ').<small>' . $_url . '</small>' );
				continue;

			endif;

			// --------------------------------------------------------------------------

			//	Can we delete the object?
			$_test = $this->object_delete( $_upload->id );

			if ( ! $_test ) :

				$this->set_error( 'Unable to delete test object (' . $bucket->slug . '/' . $_upload->filename . '; ID: ' . $_upload->id . ').' );

			endif;

			// --------------------------------------------------------------------------

			//	Can we destroy the object?
			$_test = $this->object_destroy( $_upload->id );

			if ( ! $_test ) :

				$this->set_error( 'Unable to destroy test object (' . $bucket->slug . '/' . $_upload->filename . '; ID: ' . $_upload->id . ').' );

			endif;

			// --------------------------------------------------------------------------

			//	Delete the cache files
			if ( ! @unlink( $_cachefile ) ) :

				$this->set_error( 'Unable to delete temporary cache file: ' . $_cachefile );

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Attempt to destroy the test bucket
		$_test = $this->bucket_destroy( $_test_bucket_id );

		if ( ! $_test ) :

			$this->set_error( 'Unable to destroy test bucket: ' . $_test_bucket_id );

		endif;

		// --------------------------------------------------------------------------

		//	If defined, run the post_test method fo the driver
		if ( method_exists( $this->_cdn, 'post_test' ) ) :

			call_user_func( array( $this->_cdn, 'post_test' ) );

		endif;

		// --------------------------------------------------------------------------

		//	Any errors?
		if ( $this->get_errors() ) :

			return FALSE;

		else :

			return TRUE;

		endif;
	}
}

/* End of file cdn.php */
/* Location: ./libraries/cdn.php */