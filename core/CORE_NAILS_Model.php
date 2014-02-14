<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Model extends CI_Model
{

	//	Common data
	protected $data;
	protected $user;
	protected $_cache_values;
	protected $_cache_keys;
	protected $_cache_method;

	//	Errors
	protected $_errors;

	//	Data/Table structure
	protected $_table;
	protected $_table_prefix;

	protected $_table_id_column;
	protected $_table_slug_column;
	protected $_table_label_column;

	protected $_deleted_flag;

	//	Preferences
	protected $_destructive_delete;
	protected $_per_page;


	/**
	 * --------------------------------------------------------------------------
	 *
	 * CONSTRUCTOR && DESTRUCTOR
	 * The constructor preps common variables and sets the model up for user.
	 * The destructor clears
	 *
	 * --------------------------------------------------------------------------
	 **/


	/**
	 * Construct the model
	 *
	 * @access	public
	 * @return	void
	 **/
	public function __construct( )
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Ensure models all have access to the NAILS_USR_OBJ if it's defined
		if ( function_exists( 'get_userobject' ) ) :

			$this->user = get_userobject();

		endif;

		// --------------------------------------------------------------------------

		//	Set the cache method
		//	TODO: check for availability of things like memcached
		//	TODO: apply same logic to CDN library

		$this->_cache_values	= array();
		$this->_cache_keys		= array();
		$this->_cache_method	= 'LOCAL';

		// --------------------------------------------------------------------------

		//	Define defaults
		$this->_errors				= $this->clear_errors();
		$this->_destructive_delete	= TRUE;
		$this->_table_id_column		= 'id';
		$this->_table_slug_column	= 'slug';
		$this->_table_label_column	= 'label';
		$this->_deleted_flag		= 'is_deleted';
		$this->_per_page			= 50;
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
		//	TODO: decide whether this is necessary; should caches be persistent; gut says yes.

		//	Clear cache's
		if ( isset( $this->_cache_keys ) && $this->_cache_keys ) :

			foreach ( $this->_cache_keys AS $key ) :

				$this->_unset_cache( $key );

			endforeach;

		endif;
	}


	/**
	 * --------------------------------------------------------------------------
	 *
	 * ERROR METHODS
	 * These methods provide a consistent interface for setting and retrieving
	 * errors which are generated.
	 *
	 * --------------------------------------------------------------------------
	 **/


	/**
	 * Set a generic error
	 *
	 * @access	protected
	 * @param	string	$error	The error message
	 * @return	void
	 **/
	protected function _set_error( $error )
	{
		$this->_errors[] = $error;
	}


	// --------------------------------------------------------------------------


	/**
	 * Get any errors
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
	 * Get last error
	 *
	 * @access	public
	 * @return	mixed
	 **/
	public function last_error()
	{
		return end( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Clear the last error
	 *
	 * @access	public
	 * @return	mixed
	 **/
	public function clear_last_error()
	{
		return array_pop( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Clears all errors
	 *
	 * @access	public
	 * @return	mixed
	 **/
	public function clear_errors()
	{
		$this->_errors = array();
		return array();
	}


	/**
	 * --------------------------------------------------------------------------
	 *
	 * CACHE METHODS
	 * These methods provide a consistent interface for setting and retrieving
	 * items from the cache
	 *
	 * --------------------------------------------------------------------------
	 **/


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
	 * Define the cache key prefix
	 *
	 * @access	private
	 * @return	string
	 **/
	protected function _cache_prefix()
	{
		return get_called_class();
	}


	/**
	 * --------------------------------------------------------------------------
	 *
	 * MUTATION METHODS
	 * These methods provide a consistent interface for creating, and manipulating
	 * objects that this model represents. These methods should be extended if any
	 * custom functionality is required.
	 *
	 * See the docs for more info TODO: link to docs
	 *
	 * --------------------------------------------------------------------------
	 **/


	/**
	 * Creates a new object
	 *
	 * @access public
	 * @param array $data The data to create the object with
	 * @param bool $return_obj Whether to return just the new ID or the full object
	 * @return mixed
	 **/
	public function create( $data = array(), $return_object = FALSE )
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::create() Table variable not set' );

		endif;

		// --------------------------------------------------------------------------

		if ( $data ) :

			$this->db->set( $data );

		endif;

		// --------------------------------------------------------------------------

		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( $this->user->is_logged_in() ) :

			$this->db->set( 'created_by', active_user( 'id' ) );
			$this->db->set( 'modified_by', active_user( 'id' ) );

		else :

			$this->db->set( 'created_by', NULL );
			$this->db->set( 'modified_by', NULL );

		endif;

		$this->db->insert( $this->_table );

		if ( $this->db->affected_rows() ) :

			$_id =  $this->db->insert_id();

			// --------------------------------------------------------------------------

			if ( $return_object ) :

				return $this->get_by_id( $_id );

			else :

				return $_id;

			endif;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Updates an existing object
	 *
	 * @access public
	 * @param int $id The ID of the object to update
	 * @param array $data The data to update the object with
	 * @return bool
	 **/
	public function update( $id, $data = array() )
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::update() Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';
			$_table		= $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $data ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->set( $data );
		$this->db->set( $_prefix . 'modified', 'NOW()', FALSE );

		if ( $this->user->is_logged_in() ) :

			$this->db->set( $_prefix . 'modified_by', active_user( 'id' ) );

		else :

			$this->db->set( 'modified_by', NULL );

		endif;

		$this->db->where( $_prefix . 'id', $id );
		$this->db->update( $_table );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	/**
	 * Marks an object as deleted
	 *
	 * If destructive deletion is enabled then this method will permanently
	 * destroy the object. If Non-destructive deletion is enabled then the
	 * $this->_deleted_flag field will be set to TRUE.
	 *
	 * @access public
	 * @param int $id The ID of the object to mark as deleted
	 * @return bool
	 **/
	public function delete( $id )
	{
		//	Perform this check here so the error message is more easily traced.
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::delete() Table variable not set' );

		endif;

		// --------------------------------------------------------------------------

		if ( $this->_destructive_delete ) :

			//	Destructive delete; nuke that row.
			return $this->destroy( $id );

		else :

			//	Non-destructive delete, update the flag
			$_data = array(
				$this->_deleted_flag => TRUE
			);
			return $this->update( $id, $_data );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Unmarks an object as deleted
	 *
	 * If destructive deletion is enabled then this method will return FALSE.
	 * If Non-destructive deletion is enabled then the $this->_deleted_flag
	 * field will be set to FALSE.
	 *
	 * @access public
	 * @param int $id The ID of the object to restore
	 * @return bool
	 **/
	public function restore( $id )
	{
		//	Perform this check here so the error message is more easily traced.
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::restore() Table variable not set' );

		endif;

		// --------------------------------------------------------------------------

		if ( $this->_destructive_delete ) :

			//	Destructive delete; can't be resurrecting the dead.
			return FALSE;

		else :

			//	Non-destructive delete, update the flag
			$_data = array(
				$this->_deleted_flag => FALSE
			);
			return $this->update( $id, $_data );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Permanently deletes an object
	 *
	 * This method will attempt to delete the row from the table, regardless of whether
	 * destructive deletion is enabled or not.
	 *
	 * @access public
	 * @param int $id The ID of the object to destroy
	 * @return bool
	 **/
	public function destroy( $id )
	{
		//	Perform this check here so the error message is more easily traced.
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::destroy() Table variable not set' );

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( 'id', $id );
		$this->db->delete( $this->_table );

		return (bool) $this->db->affected_rows();
	}


	/**
	 * --------------------------------------------------------------------------
	 *
	 * RETRIEVAL & COUNTING METHODS
	 * These methods provide a consistent interface for retrieving and counting objects
	 *
	 * --------------------------------------------------------------------------
	 **/


	/**
	 * Fetches all objects, optionally paginated.
	 *
	 * @access public
	 * @param int $page The page number of the results, if NULL then no pagination
	 * @param int $per_page How many items per page of paginated results
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @param bool $include_deleted If non-destructive delete is enabled then this flag allows you to include deleted items
	 * @param string $_caller Internal flag to pass to _getcount_common(), contains the calling method
	 * @return array
	 **/
	public function get_all( $page = NULL, $per_page = NULL, $data = NULL, $include_deleted = FALSE, $_caller = 'GET_ALL' )
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::get_all() Table variable not set' );

		else :

			$_table = $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------

		//	Apply common items; pass $data
		$this->_getcount_common( $data, $_caller );

		// --------------------------------------------------------------------------

		//	Facilitate pagination
		if ( NULL !== $page ) :

			//	Adjust the page variable, reduce by one so that the offset is calculated
			//	correctly. Make sure we don't go into negative numbers
			$page--;
			$page = $page < 0 ? 0 : $page;

			//	Work out what the offset should be
			$_per_page	= NULL == $per_page ? $this->_per_page : (int) $per_page;
			$_offset	= $page * $per_page;

			$this->db->limit( $per_page, $_offset );

		endif;

		// --------------------------------------------------------------------------

		//	If non-destructive delete is enabled then apply the delete query
		if ( ! $this->_destructive_delete && ! $include_deleted ) :

			$_prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
			$this->db->where( $_prefix . $this->_deleted_flag, FALSE );

		endif;


		// --------------------------------------------------------------------------

		$_results = $this->db->get( $_table )->result();

		for ( $i = 0; $i < count( $_results ); $i++ ) :

			$this->_format_object( $_results[$i] );

		endfor;

		return $_results;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches all objects as a flat array
	 *
	 * The keys of the returned array correlate to the ID of the object, while
	 * the value of the element is the object's label
	 *
	 * @access public
	 * @param int $page The page number of the results, if NULL then no pagination
	 * @param int $per_page How many items per page of paginated results
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @param string $_caller Internal flag to pass to _getcount_common(), contains the calling method
	 * @return array
	 **/
	public function get_all_flat( $page = NULL, $per_page = NULL, $data = NULL, $include_deleted = FALSE, $_caller = 'GET_ALL_FLAT' )
	{
		$_items	= $this->get_all( $page, $per_page, $data, $include_deleted, $_caller );
		$_out	= array();

		//	Nothing returned? Skip the rest of this method, it's pointless.
		if ( ! $_items ) :

			return array();

		endif;

		// --------------------------------------------------------------------------

		//	Test columns
		$_test = reset( $_items );

		if ( ! isset( $_test->{$this->_table_label_column} ) ) :

			show_error( get_called_class() . '::get_all_flat() "' . $this->_table_label_column . '" is not a valid label column.' );

		endif;

		if ( ! isset( $_test->{$this->_table_id_column} ) ) :

			show_error( get_called_class() . '::get_all_flat() "' . $this->_table_id_column . '" is not a valid id column.' );

		endif;

		unset( $_test );

		// --------------------------------------------------------------------------

		foreach( $_items AS $item ) :

			$_out[$item->{$this->_table_id_column}] = $item->{$this->_table_label_column};

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's ID
	 *
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @return	stdClass
	 **/
	public function get_by_id( $id, $data = NULL )
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::get_by_id() Table variable not set' );

		else :

			$_prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( $_prefix . $this->_table_id_column, $id );
		$_result = $this->get_all( NULL, NULL, $data, FALSE, 'GET_BY_ID' );

		// --------------------------------------------------------------------------

		if ( ! $_result ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return $_result[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's slug
	 *
	 * @access public
	 * @param int $slug The slug of the object to fetch
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @return	stdClass
	 **/
	public function get_by_slug( $slug, $data = NULL )
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::get_by_slug() Table variable not set' );

		else :

			$_prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( $_prefix . $this->_table_slug_column, $slug );
		$_result = $this->get_all( NULL, NULL, $data, FALSE, 'GET_BY_SLUG' );

		// --------------------------------------------------------------------------

		if ( ! $_result ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return $_result[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's id or slug
	 *
	 * Auto-detects whether to use the ID or slug as the selector when fetching
	 * an object. Note that this method uses is_numeric() to determine whether
	 * an ID or a slug has been passed, thus numeric slugs (which are against
	 * Nails style guidelines) will be interpreted incorrectly.
	 *
	 * @access public
	 * @param mixed $id_slug The ID or slug of the object to fetch
	 * @param mixed $data Any data to pass to _getcount_common()
	 * @return stdClass
	 **/
	public function get_by_id_or_slug( $id_slug, $data = NULL )
	{
		if ( is_numeric( $id_slug ) ) :

			return $this->get_by_id( $id_slug, $data );

		else :

			return $this->get_by_slug( $id_slug, $data );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts all objects
	 *
	 * @access public
	 * @param mixed $data any data to pass to _getcount_common()
	 * @return int
	 **/
	public function count_all( $data = NULL )
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::count() Table variable not set' );

		else :

			$_table	 = $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------

		//	Apply common items
		$this->_getcount_common( $data, 'COUNT_ALL' );

		// --------------------------------------------------------------------------

		return $this->db->count_all_results( $_table );
	}


	/**
	 * --------------------------------------------------------------------------
	 *
	 * HELPER METHODS
	 * These methods provide additional functionality to models
	 *
	 * --------------------------------------------------------------------------
	 **/


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
	protected function _getcount_common( $data = NULL, $_caller = NULL )
	{
		//	Handle where
		if ( ! empty( $data['where'] ) ) :

			if ( is_array( $data['where'] ) ) :

				//	If it's a single dimensional array then just bung that into
				//	the db->where(). If not, loop it and parse.

				$_first = reset( $data['where'] );

				if ( is_string( $_first ) ) :

					$this->db->where( $data['where'] );

				else :

					foreach( $data['where'] AS $where ) :

						$_column	= ! empty( $where['column'] )	? $where['column']			: NULL;
						$_value		= isset( $where['value'] )		? $where['value']			: NULL;
						$_escape	= isset( $where['escape'] )		? (bool) $where['escape']	: TRUE;

						if ( $_column ) :

							$this->db->where( $_column, $_value, $_escape );

						endif;

					endforeach;

				endif;

			elseif ( is_string( $data['where'] ) ) :

				$this->db->where( $data['where'] );

			endif;

		endif;

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
					$_sort = $this->_getcount_common_parse_sort( $data['sort'] );

					if ( ! empty( $_sort['column'] ) ) :

						$this->db->order_by( $_sort['column'], $_sort['order'] );

					endif;

				else :

					//	Multi dimension array
					foreach( $data['sort'] AS $sort ) :

						$_sort = $this->_getcount_common_parse_sort( $sort );

						if ( ! empty( $_sort['column'] ) ) :

							$this->db->order_by( $_sort['column'], $_sort['order'] );

						endif;

					endforeach;

				endif;

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _getcount_common_parse_sort( $sort )
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
	 * Generates a unique slug
	 *
	 * This method provides the functionality to generate a unique slug for an item in the database.
	 *
	 * @access public
	 * @param string $label The label from which to generate a slug
	 * @param string $prefix Any prefix to add to the slug
	 * @param string $suffix Any suffix to add to the slug
	 * @param string $table The table to use defaults to $this->_table
	 * @param string $column The column to use, defaults to $this->_table_slug_column
	 * @param int $ignore_id An Id to ignore when searching
	 * @param string $id_column The column to use for the ID, defaults to $this->_table_id_column
	 * @return string
	 **/
	protected function _generate_slug( $label, $prefix = '', $suffix = '', $table = NULL, $column = NULL, $ignore_id = NULL, $id_column = NULL )
	{
		//	Perform this check here so the error message is more easily traced.
		if ( NULL === $table ) :

			if ( ! $this->_table ) :

				show_error( get_called_class() . '::_generate_slug() Table variable not set' );

			endif;

			$_table = $this->_table;

		else :

			$_table = $table;

		endif;

		if ( NULL === $column ) :

			if ( ! $this->_table_slug_column ) :

				show_error( get_called_class() . '::_generate_slug() Column variable not set' );

			endif;

			$_column = $this->_table_slug_column;

		else :

			$_column = $column;

		endif;

		// --------------------------------------------------------------------------

		$_counter = 0;

		do
		{
			$_slug = url_title( str_replace( '/', '-', $label ), 'dash', TRUE );

			if ( $_counter ) :

				$_slug_test = $prefix . $_slug . $suffix . '-' . $_counter;

			else :

				$_slug_test = $prefix . $_slug . $suffix;

			endif;

			if ( $ignore_id ) :

				$_id_column = $id_column ? $id_column : $this->_table_id_column;
				$this->db->where( $_id_column . ' !=', $ignore_id );

			endif;

			$this->db->where( $_column, $_slug_test );
			$_counter++;

		} while( $this->db->count_all_results( $_table ) );

		return $_slug_test;
	}


	// --------------------------------------------------------------------------


	/**
	 * Formats a single object
	 *
	 * The get_all() method iterates over each returned item with this method so as to
	 * correctly format the output. Use this to typecast ID's and/or organise data into objects.
	 *
	 * @access public
	 * @param object $obj A reference to the object being formatted.
	 * @return void
	 **/
	protected function _format_object( &$obj )
	{
		//	Extend this method to format the returned objects

		// --------------------------------------------------------------------------

		//	Some common items
		if ( $this->_table_id_column ) :

			if ( ! empty( $obj->{$this->_table_id_column} ) && is_numeric( $obj->{$this->_table_id_column} ) ) :

				$obj->{$this->_table_id_column} = (int) $obj->{$this->_table_id_column};

			endif;

		endif;

		if ( ! empty( $obj->parent_id ) && is_numeric( $obj->parent_id ) ) :

			$obj->parent_id = (int) $obj->parent_id;

		endif;

		if ( ! empty( $obj->user_id ) && is_numeric( $obj->user_id ) ) :

			$obj->user_id = (int) $obj->user_id;

		endif;

		if ( ! empty( $obj->created_by ) && is_numeric( $obj->created_by ) ) :

			$obj->created_by = (int) $obj->created_by;

		endif;

		if ( ! empty( $obj->modified_by ) && is_numeric( $obj->modified_by ) ) :

			$obj->modified_by = (int) $obj->modified_by;

		endif;

		// --------------------------------------------------------------------------

		if ( ! empty( $obj->order ) && is_numeric( $obj->order ) ) :

			$obj->order = (int) $obj->order;

		endif;
	}
}

/* End of file CORE_NAILS_Model.php */
/* Location: ./core/CORE_NAILS_Model.php */