<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		CORE_NAILS_Language_Model
 *
 * Description:	This model contains all methods for handling languages
 * 
 **/

class CORE_NAILS_Language_Model extends NAILS_Model
{
	public function get_all()
	{
		$this->db->select( 'l.id,l.name,l.safe_name,l.priority,supported' );
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
}

/* End of file core_nails_language_model.php */
/* Location: ./system/application/models/core_nails_language_model.php */