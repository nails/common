<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Model extends CI_Model {

	protected $data;
	protected $user;
	protected $_errors;
	protected $_table;
	protected $_table_prefix;

	protected $_cache_values;
	protected $_cache_keys;
	protected $_cache_method;

	// --------------------------------------------------------------------------


	/**
	 * Construct the model
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
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
		$this->_errors = array();
	}


	// --------------------------------------------------------------------------


	/**
	 * Destruct the model
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
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
	 * Set a generic error
	 *
	 * @access	protected
	 * @param	string	$error	The error message
	 * @return	void
	 * @author	Pablo
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
	 * @author	Pablo
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
	 * @author	Pablo
	 **/
	public function last_error()
	{
		return end( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Provides models with the an easy interface for saving data to a cache.
	 *
	 * @access	protected
	 * @param string $key The key for the cached item
	 * @param mixed $value The data to be cached
	 * @return	array
	 * @author	Pablo
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
	 * @author	Pablo
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
	 * @author	Pablo
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
	 * @author	Pablo
	 **/
	private function _cache_prefix()
	{
		return get_called_class();
	}


	// --------------------------------------------------------------------------


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

		endif;

		$this->db->where( $_prefix . 'id', $id );
		$this->db->update( $_table );

		if ( $this->db->affected_rows() ) :

			return TRUE;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Deletes an existing object
	 *
	 * @access public
	 * @param int $id The ID of the object to delete
	 * @return bool
	 **/
	public function delete( $id )
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::delete() Table variable not set' );

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( 'id', $id );
		$this->db->delete( $this->_table );

		if ( $this->db->affected_rows() ) :

			return TRUE;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches all objects
	 *
	 * @access public
	 * @param none
	 * @return array
	 **/
	public function get_all()
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::get_all() Table variable not set' );

		else :

			$_table = $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------

		$_results = $this->db->get( $_table )->result();

		for ( $i = 0; $i < count( $_results ); $i++ ) :

			$this->_format_object( $_results[$i] );

		endfor;

		return $_results;
	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$object )
	{
		//	Extend this method to format the returned objects
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches all objects as a flat array
	 *
	 * @access public
	 * @param string $label_col The name of the column to use as the label
	 * @param string $id_col The name of the column to use as the ID
	 * @return array
	 **/
	public function get_all_flat( $label_col = 'label', $id_col = 'id' )
	{
		$_items	= $this->get_all();
		$_out	= array();

		foreach( $_items AS $item ) :

			$_out[$item->{$id_col}] = $item->{$label_col};

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts all objects
	 *
	 * @access public
	 * @param none
	 * @return int
	 **/
	public function count()
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::count() Table variable not set' );

		else :

			$_table		= $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------

		return $this->db->count_all_results( $_table );
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's ID
	 *
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @return	stdClass
	 **/
	public function get_by_id( $id )
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::get_by_id() Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( $_prefix . 'id', $id );
		$_result = $this->get_all();

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
	 * @return	stdClass
	 **/
	public function get_by_slug( $slug, $slug_field = 'slug' )
	{
		if ( ! $this->_table ) :

			show_error( get_called_class() . '::get_by_slug() Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( $_prefix . $slug_field, $slug );
		$_result = $this->get_all();

		// --------------------------------------------------------------------------

		if ( ! $_result ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return $_result[0];
	}


	// --------------------------------------------------------------------------


	protected function _generate_slug( $label, $table = NULL, $column = NULL, $_add_prefix = '', $_add_suffix = '' )
	{
		//	Prep table and column
		$_prefix	= ! $table && $this->_table_prefix ? $this->_table_prefix . '.' : '';
		$_table		= ! $table ? $this->_table : $table;
		$_column	= ! $column ? 'slug' : $column;

		// --------------------------------------------------------------------------

		if ( ! $_table ) :

			show_error( get_called_class() . '::_generate_slug() Table variable not set' );

		endif;

		if ( ! $_column ) :

			show_error( get_called_class() . '::_generate_slug() Column variable not set' );

		endif;

		// --------------------------------------------------------------------------

		$_counter = 0;

		do
		{
			$_slug = url_title( str_replace( '/', '-', $label ), 'dash', TRUE );

			if ( $_counter ) :

				$_slug_test = $_add_prefix . $_slug . $_add_suffix . '-' . $_counter;

			else :

				$_slug_test = $_add_prefix . $_slug . $_add_suffix;

			endif;

			$this->db->where( $_prefix . $_column, $_slug_test );
			$_counter++;

		} while( $this->db->count_all_results( $_prefix . $_table ) );

		return $_slug_test;
	}
}

/* End of file CORE_NAILS_Model.php */
/* Location: ./core/CORE_NAILS_Model.php */