<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		CORE_NAILS_Language_Model
 *
 * Description:	This model contains all methods for handling languages
 * 
 **/

class CORE_NAILS_Language_Model extends NAILS_Model
{
	private $_default_lang;
	
	// --------------------------------------------------------------------------
	
	
	public function get_all()
	{
		$this->db->select( 'l.id,l.name,l.safe_name,l.priority,l.supported' );
		$this->db->order_by( 'l.supported', 'DESC' );
		$this->db->order_by( 'l.name' );
		return $this->db->get( 'language l' )->result();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_all_supported()
	{
		$this->db->where( 'l.supported', TRUE );
		return $this->get_all();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_all_flat()
	{
		$_out	= array();
		$_langs	= $this->get_all();
		
		for( $i=0; $i<count( $_langs ); $i++ ) :
		
			$_out[$_langs[$i]->id] = $_langs[$i]->name;
		
		endfor;
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_all_supported_flat()
	{
		$_out	= array();
		$_langs	= $this->get_all_supported();
		
		for( $i=0; $i<count( $_langs ); $i++ ) :
		
			$_out[$_langs[$i]->id] = $_langs[$i]->name;
		
		endfor;
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_by_id( $id )
	{
		$this->db->where( 'l.id', $id );
		$_result = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_by_safename( $safename )
	{
		$this->db->where( 'l.safe_name', $safename );
		$_result = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_default_id()
	{
		if ( $this->_default_lang )
			return $this->_default_lang->id;
		
		// --------------------------------------------------------------------------
		
		//	Fetch and cache
		$_default = $this->get_by_safename( APP_DEFAULT_LANG_SAFE );
		
		if ( isset( $_default->id ) ) :
		
			$this->_defaut_lang = $_default;
			return $_default->id;
			
		else :
		
			NULL;
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_default_name()
	{
		if ( $this->_default_lang )
			return $this->_default_lang->name;
		
		// --------------------------------------------------------------------------
		
		//	Fetch and cache
		$_default = $this->get_by_safename( APP_DEFAULT_LANG_SAFE );
		
		if ( isset( $_default->id ) ) :
		
			$this->_defaut_lang = $_default;
			return $_default->name;
			
		else :
		
			NULL;
			
		endif;
	}
}

/* End of file core_nails_language_model.php */
/* Location: ./system/application/models/core_nails_language_model.php */