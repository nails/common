<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cms_page_model extends NAILS_Model
{
	public function create()
	{
		//	TODO Create a new blank page
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function update()
	{
		//	TODO Update a page
	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	public function delete()
	{
		//	TODO Delete a page
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_all()
	{
		return array();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's ID
	 * 
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @param bool $include_revisions Whether to include translation revisions
	 * @return stdClass
	 **/
	public function get_by_id( $id, $include_revisions = FALSE )
	{
		$this->db->where( 'cb.id', $id );
		$_result = $this->get_all( $include_revisions );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's slug
	 * 
	 * @access public
	 * @param string $slug The slug of the object to fetch
	 * @param bool $include_revisions Whether to include translation revisions
	 * @return stdClass
	 **/
	public function get_by_slug( $slug, $include_revisions = FALSE )
	{
		$this->db->where( 'cb.slug', $slug );
		$_result = $this->get_all( $include_revisions );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _write_routes()
	{
		//	TODO Rewrite the routes include file
	}
}


/* End of file cms_page_model.php */
/* Location: ./models/cms_page_model.php */