<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Model extends CI_Model {

	protected $data;
	protected $user;
	protected $_error = array();
	protected $_table;
	protected $_table_prefix;

	private $_cache_values;
	private $_cache_keys;
	private $_cache_method;
	
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
		$this->user = get_userobject();

		// --------------------------------------------------------------------------

		//	Set the cache method
		//	TODO: check for availability of things like memcached

		$this->_cache_values	= array();
		$this->_cache_keys		= array();
		$this->_cache_method	= 'LOCAL';
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
		$this->_error[] = $error;
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Get any errors
	 *
	 * @access	public
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_error()
	{
		return $this->_error;
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

			show_error( 'Table variable not set' );

		endif;

		// --------------------------------------------------------------------------

		if ( $data )
			$this->db->set( $data );
		
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

			show_error( 'Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';
			$_table		= $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $data )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->db->set( $data );
		$this->db->set( $_prefix . 'modified', 'NOW()', FALSE );

		if ( $this->user->is_logged_in() ) :

			$this->db->set( $_prefix . 'modified_by', active_user( 'id' ) );

		endif;

		$this->db->where( $_prefix . 'id', $id );
		$this->db->update( $_table );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
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

			show_error( 'Table variable not set' );

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( 'id', $id );
		$this->db->delete( $this->_table );
		
		return $this->db->affected_rows() ? TRUE : FALSE;
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

			show_error( 'Table variable not set' );

		else :

			$_table		= $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;

		endif;

		// --------------------------------------------------------------------------
		
		return $this->db->get( $_table )->result();
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches all objects
	 * 
	 * @access public
	 * @param none
	 * @return int
	 **/
	public function count()
	{
		if ( ! $this->_table ) :

			show_error( 'Table variable not set' );

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

			show_error( 'Table variable not set' );

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
	public function get_by_slug( $slug )
	{
		if ( ! $this->_table ) :

			show_error( 'Table variable not set' );

		else :

			$_prefix	= $this->_table_prefix ? $this->_table_prefix . '.' : '';

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( $_prefix . 'slug', $slug );
		$_result = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result ) :

			return FALSE;

		endif;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}


	// --------------------------------------------------------------------------


	protected function _generate_slug( $label, $table = NULL, $column = NULL )
	{
		//	Prep table and column
		$_prefix	= ! $_table && $this->_table_prefix ? $this->_table_prefix . '.' : '';
		$_table		= ! $table ? $this->_table : $table;
		$_column	= ! $column ? 'slug' : $column;

		// --------------------------------------------------------------------------

		if ( ! $_table ) :

			show_error( 'Table variable not set' );

		endif;

		if ( ! $_column ) :

			show_error( 'Column variable not set' );

		endif;

		// --------------------------------------------------------------------------

		$_counter = 0;
		
		do
		{
			$_slug = url_title( $label, 'dash', TRUE );

			if ( $_counter ) :

				$_slug_test = $_slug . '-' . $_counter;

			else :

				$_slug_test = $_slug;

			endif;

			$this->db->where( $_prefix . $_column, $_slug_test );
			$_counter++;

		} while( $this->db->count_all_results( $_prefix . $_table ) );

		return $_slug_test;
	}
}

/* End of file CORE_NAILS_Model.php */
/* Location: ./core/CORE_NAILS_Model.php */