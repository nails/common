<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		CORE_NAILS_Timezone_Model
 *
 * Description:	This model contains all methods for handling timezones
 * 
 **/

class CORE_NAILS_Timezone_Model extends NAILS_Model
{
	public function get_all()
	{
		$this->db->select( 'id,gmt_offset,label' );
		$this->db->order_by( 'gmt_offset', 'DESC' );
		$this->db->order_by( 'label' );
		return $this->db->get( 'timezone' )->result();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_all_flat()
	{
		$_out		= array();
		$_timezones	= $this->get_all();
		
		for( $i=0; $i<count( $_timezones ); $i++ ) :
		
			$_sign = $_timezones[$i]->gmt_offset < 0 ? '' : '+';
			$_out[$_timezones[$i]->id] = 'GMT ' . $_sign . $_timezones[$i]->gmt_offset . ' ' . $_timezones[$i]->label;
		
		endfor;
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}

/* End of file core_nails_language_model.php */
/* Location: ./system/application/models/core_nails_language_model.php */