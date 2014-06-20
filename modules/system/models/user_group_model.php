<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		User_group_model
 *
 * Description:	The user group model handles manageing groups
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_User_group_model extends NAILS_Model
{
	protected $default_group;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table			= NAILS_DB_PREFIX . 'user_group';
		$this->_table_prefix	= 'ug';

		// --------------------------------------------------------------------------

		$this->_default_group = $this->get_default_group();
	}


	// --------------------------------------------------------------------------


	/**
	 * Set's a group as the default group
	 * @param mixed $group_id_slug The group's ID or slug
	 */
	public function set_as_default( $group_id_slug )
	{
		$_group = $this->get_by_id_or_slug( $group_id_slug );

		if ( ! $_group ) :

			$this->_set_error( 'Invalid Group' );

		endif;

		// --------------------------------------------------------------------------

		$this->db->trans_begin();

		//	Unset old default
		$this->db->set( 'is_default', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		if ( $this->user_model->is_logged_in() ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;
		$this->db->where( 'is_default', TRUE );
		$this->db->update( $this->_table );

		//	Set new default
		$this->db->set( 'is_default', TRUE );
		$this->db->set( 'modified', 'NOW()', FALSE );
		if ( $this->user_model->is_logged_in() ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;
		$this->db->where( 'id', $_group->id );
		$this->db->update( $this->_table );

		if ( $this->db->trans_status() === FALSE ) :

			$this->db->trans_rollback();
			return FALSE;

		else :

			$this->db->trans_commit();

			//	Refresh the default group variable
			$this->get_default_group();

			return TRUE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_default_group()
	{
		$_data['where']		= array();
		$_data['where'][]	= array( 'column' => 'is_default', 'value' => TRUE );

		$_group = $this->get_all( NULL, NULL, $_data );

		if ( ! $_group ) :

			show_fatal_error( 'No Default Group Set', 'A default user group must be set.' );

		endif;

		$this->_default_group = $_group[0];

		return $this->_default_group;
	}


	// --------------------------------------------------------------------------


	public function get_default_group_id()
	{
		return $this->_default_group->id;
	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$obj )
	{
		$obj->acl = @unserialize( $obj->acl );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core
 * models. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_USER_GROUP_MODEL' ) ) :

	class User_group_model extends NAILS_User_group_model
	{
	}

endif;

/* End of file user_group_model.php */
/* Location: ./system/application/models/user_group_model.php */