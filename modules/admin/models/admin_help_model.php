<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin Help Model
 *
 * Description:	This model contains logic for the admin help pages.
 * 
 */

class Admin_help_model extends NAILS_Model
{
	public function get_all()
	{
		$this->db->select( 'id, title, description, vimeo_id' );
		return $this->db->get( 'admin_help_video' )->result();
	}

	// --------------------------------------------------------------------------

	public function count()
	{
		return $this->db->count_all( 'admin_help_video' );
	}
}

/* End of file admin_help_model.php */
/* Location: ./application/models/admin_help_model.php */