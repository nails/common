<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Mini_model
*
* Docs:			http://docs.internavenue.com/mini/
*
* Created:		22/07/2011
* Modified:		19/12/2011
*
* Description:	This model helps the Mini utility do it's thing.
* 
*/

class Mini_model {

	private $_ci;
	private $_cache;
	private $_expire;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Construct the class; set defaults
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function __construct()
	{
		$this->_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		$this->_cache	= array();
		$this->_expire	= 1209600;	//	2 weeks
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Shortens $url
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function shorten( $url )
	{
		//	In cache?
		$_cache_key = array_search( $url, $this->_cache );
		
		// --------------------------------------------------------------------------
		
		if ( $_cache_key !== FALSE )
			return $this->_cache[ $_cache_key ];
		
		// --------------------------------------------------------------------------
		
		//	Lookup DB for this URL, if it's there use that ID
		$this->_ci->db->where( 'translates_as', $url );
		$_q = $this->_ci->db->get( 'url_short' );
		
		// --------------------------------------------------------------------------
		
		if ( $_q->num_rows() ) :
		
			$_url = $_q->row();
			
			//	Save to cache
			$this->_cache[$_url->id] = $_url->translates_as;
			
			//	Return the ID
			return $_url->id;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If not in cache or DB create a new, unique short URL
		do
		{
			$rand_str = random_string( 'alnum', 6 );
		} while( ! $this->_ci->db->insert( 'url_short', array( 'id' => $rand_str, 'translates_as' => $url, 'created' => time() ) ) );
		
		// --------------------------------------------------------------------------
		
		//	Save to cache
		$this->_cache[$rand_str] = $url;
		
		// --------------------------------------------------------------------------
		
		return $rand_str;
	}
	
	// --------------------------------------------------------------------------
	
	public function expand( $hash )
	{
		$this->_ci->db->where( 'id', $hash );
		$_q = $this->_ci->db->get( 'url_short' );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_q->num_rows() )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Expired?
		$_url = $_q->row();
		
		if ( ( time() - $_url->created ) > $this->_expire )
			return 'EXPIRED';
		
		// --------------------------------------------------------------------------
		
		return $_url->translates_as;
	}

}

/* End of file mini_model.php */
/* Location: ./application/modules/cron/controllers/mini_model.php */