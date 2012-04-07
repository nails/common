<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			QuickCache
*
* Created:		24/05/2011
* Modified:		16/06/2011
*
* Description:	Library for quickly and easily caching objects
*
* Requirements:	-
*
* Change log:	-
* 
*/

class Quickcache {
	
	private $ci;
	private $cache;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		$this->ci =& get_instance();
		
		//	Load the file helper
		$this->ci->load->helper( 'file' );
		
		//	Set default vars
		$this->cache->table	= 'quickcache';				//	Cache table
		$this->cache->path	= FCPATH . 'assets/cache/';	//	Cache directory
		$this->cache->ext	= 'cache';					//	Cache file extension
		$this->ttl			= 3600; 					//	Default TTL
		
		//	Clean out expired cached objects
		$this->ci->db->where( 'expires < ' . time() );
		$q = $this->ci->db->get( $this->cache->table );
		
		if ( $q->num_rows() ) :
		
			$cache_obj = $q->result();
			
			$ids = array();
			foreach ( $cache_obj AS $f ) :
			
				if ( ! @unlink( $this->cache->path . $f->cache_file ) ) :
				
					$this->_add_error( 'Unable to remove expired cache file for "' . $f->id . '"' );
					
				else :
				
					$ids[] = $f->id;
				
				endif;
			
			endforeach;
			
			if ( count( $ids ) ) :
			
				$this->ci->db->where_in( 'id', $ids );
				$this->ci->db->delete( $this->cache->table );
			
			endif;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Caches $data with identifier (optionally set a TTL)
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @para	int
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function cache( $id, $data, $ttl = NULL )
	{
		//	Set default TTL
		$ttl = ( $ttl ) ? $ttl : $this->ttl;
		
		//	Prep data
		$cache_data['dtype']	= $this->_get_dtype( $data );
		$cache_data['data']		=  json_encode( $data );
		
		//	See if cache ID is already in use,if so, update that one
		$this->ci->db->where( 'id', $id );
		$q = $this->ci->db->get( $this->cache->table );
		
		if ( $q->num_rows() == 1 ) :
		
			//	Update this cache file and extende the TTL
			if ( ! write_file( $this->cache->path . $q->row()->cache_file, json_encode( $cache_data ) ) ) :
			
				$this->_add_error( 'Unable to write data.' );
				return FALSE;
			
			endif;
			
			$this->ci->db->set( 'expires', time() + $ttl );
			$this->ci->db->update( $this->cache->table );
			
			if ( $this->ci->db->affected_rows() != 1 ) :
			
				@unlink( $this->cache->path . $cache_file );
				$this->_add_error( 'Unable to save cache reference in database.' );
				return FALSE;
			
			endif;
			
			return TRUE;
		
		endif;
		
		//	Generate cache file
		$cache_file = md5( microtime() * mt_rand()  . microtime() ) . '.' . $this->cache->ext;
		if ( ! write_file( $this->cache->path . $cache_file, json_encode( $cache_data ) ) ) :
		
			$this->_add_error( 'Unable to write data.' );
			return FALSE;
		
		endif;
		
		//	Store ref in DB.
		$this->ci->db->set( 'id', $id );
		$this->ci->db->set( 'cache_file', $cache_file );
		$this->ci->db->set( 'expires', time() + $ttl );
		$this->ci->db->insert( $this->cache->table );
		
		if ( $this->ci->db->affected_rows() != 1 ) :
		
			$this->_add_error( 'Unable to save cache reference in database.' );
			return FALSE;
		
		endif;
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Reads a cache file
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 * @author	Pablo
	 **/
	public function read( $id )
	{
		//	Lookup cache file
		$this->ci->db->where( 'id', $id );
		$q = $this->ci->db->get( $this->cache->table );
		
		if ( $q->num_rows() ) :
		
			$f = $q->row();
			$_out = read_file( $this->cache->path . $f->cache_file );
			
			if ( $_out ) :
			
				$cache_data = json_decode( $_out );
				
				//	Type case as nessecary
				switch ( $cache_data->dtype ) :
				
					case 'object' :
					
						return (object) json_decode( $cache_data->data );
					
					break;
					
					case 'array' :
					
						return (array) json_decode( $cache_data->data );
					
					break;
					
					default:
					
						return json_decode( $cache_data->data );
					
					break;
				
				endswitch;
				
				return json_decode( $_out );
			
			else :
			
				$this->_add_error( 'Error reading cache file "' . $id . '" - file: ' . $f->cache_file );
				return FALSE;
			
			endif;
		
		else :
		
			$this->_add_error( 'Cache file "' . $id . '" does not exist.' );
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Return the error var if any
	 *
	 * @access	public
	 * @return	array
	 * @author	Pablo
	 **/
	public function errors()
	{
		return $this->error;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Set an error
	 *
	 * @access	private
	 * @param	string	error to add
	 * @return	void
	 * @author	Pablo
	 **/
	private function _add_error( $error )
	{
		$this->error[] = $error;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determine the datatype of a variable
	 *
	 * @access	private
	 * @param	mixed
	 * @return	string
	 * @author	Pablo
	 **/
	private function _get_dtype( $data )
	{
		if ( is_array( $data ) )
			return 'array';
			
		if ( is_bool( $data ) )
			return 'bool';
			
		if ( is_float( $data ) )
			return 'float';
			
		if ( is_int( $data ) )
			return 'int';
			
		if ( is_null( $data ) )
			return 'null';
			
		if ( is_object( $data ) )
			return 'object';
			
		if ( is_string( $data ) )
			return 'string';
	}
}

/* End of file quickcache.php */
/* Location: ./application/libraries/quickcache.php */