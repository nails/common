<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin Changelog Model
 *
 * Description:	This model contains logic for manipulating the admin change log
 *
 */

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Admin_changelog_model extends NAILS_Model
{
	protected $_changes;
	protected $_batch_save;

	// --------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Define data structure
		$this->_table			= NAILS_DB_PREFIX . 'admin_changelog';
		$this->_table_prefix	= 'acl';

		// --------------------------------------------------------------------------

		//	Set defaults
		$this->_changes		= array();
		$this->_batch_save	= TRUE;

		// --------------------------------------------------------------------------

		//	Add a hook for after the controller is done so we can process the changes
		//	and save to the DB.

		$_hook				= array();
		$_hook['classref']	= &$this;
		$_hook['method']	= 'save';
		$_hook['params']	= '';

		if ( $GLOBALS['EXT']->add_hook( 'post_controller', $_hook ) == FALSE ) :

			$this->_batch_save = FALSE;
			log_message( 'error', 'Admin_changelog_model could not set the post_controller hook to save items in batches.' );

		endif;
	}


	// --------------------------------------------------------------------------


	public function add( $verb, $article, $item, $item_id, $title, $url, $field, $old_value, $new_value, $strict_comparison = TRUE )
	{
		//	if the old_value and the new_value are the same then why are you
		//	logging a change!? Lazy [read: efficient] dev.

		$new_value = trim( $new_value );
		$old_value = trim( $old_value );

		if ( $strict_comparison ) :

			if ( $new_value === $old_value ) :

				return FALSE;

			endif;

		else :

			if ( $new_value == $old_value ) :

				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Define the key for this change; keys should be common across identical items
		//	so we can group changes of the same item together.

		$_key = md5( active_user( 'id' ) . '|' . $verb . '|' . $article . '|' . $item . '|' . $item_id . '|' . $title . '|' . $url );

		if ( empty( $this->_changes[active_user( 'id' )][$_key] ) ) :

			$this->_changes[$_key]				= array();
			$this->_changes[$_key]['user_id']	= active_user( 'id' ) ? active_user( 'id' ) : NULL;
			$this->_changes[$_key]['verb']		= $verb;
			$this->_changes[$_key]['article']	= $article;
			$this->_changes[$_key]['item']		= $item;
			$this->_changes[$_key]['item_id']	= $item_id;
			$this->_changes[$_key]['title']		= $title;
			$this->_changes[$_key]['url']		= $url;
			$this->_changes[$_key]['changes']	= array();

		endif;

		// --------------------------------------------------------------------------

		//	Generate a subkey, so that multiple calls to the same field
		//	overwrite each other

		$_subkey = md5( $field );

		$this->_changes[$_key]['changes'][$_subkey]				= new stdClasS();
		$this->_changes[$_key]['changes'][$_subkey]->field		= $field;
		$this->_changes[$_key]['changes'][$_subkey]->old_value	= $old_value;
		$this->_changes[$_key]['changes'][$_subkey]->new_value	= $new_value;

		// --------------------------------------------------------------------------

		//	If we're not saving  in batches then save now
		if ( ! $this->_batch_save ) :

			$this->save();

		endif;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	public function save()
	{
		//	Process all the items and save to the DB, then clean up
		if ( $this->_changes ) :

			$this->_changes = array_values( $this->_changes );

			for ( $i = 0; $i < count( $this->_changes ); $i++ ) :

				$this->_changes[$i]['changes']		= array_values( $this->_changes[$i]['changes'] );
				$this->_changes[$i]['changes'] 		= serialize( $this->_changes[$i]['changes'] );
				$this->_changes[$i]['created']		= date( 'Y-m-d H:i:s' );
				$this->_changes[$i]['created_by']	= active_user( 'id' );
				$this->_changes[$i]['modified']		= date( 'Y-m-d H:i:s' );
				$this->_changes[$i]['modified_by']	= active_user( 'id' );

			endfor;

			$this->db->insert_batch( $this->_table, $this->_changes );

		endif;

		// --------------------------------------------------------------------------

		$this->clear();
	}


	// --------------------------------------------------------------------------


	public function clear()
	{
		$this->_changes = array();
	}


	// --------------------------------------------------------------------------


	public function get_recent( $limit = 100 )
	{
		$this->db->limit( $limit );
		return $this->get_all();
	}


	// --------------------------------------------------------------------------


	protected function _getcount_common( $data = NULL, $_caller = NULL )
	{
		parent::_getcount_common( $data );

		// --------------------------------------------------------------------------

		//	Set the select
		if ( $_caller !== 'COUNT_ALL' ) :

			$this->db->select( $this->_table_prefix . '.*, u.first_name, u.last_name, u.gender, u.profile_img, ue.email' );

		endif;

		//	Join user tables
		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = ' . $this->_table_prefix . '.user_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = ' . $this->_table_prefix . '.user_id AND ue.is_primary = 1', 'LEFT' );

		//	Set the order
		$this->db->order_by( $this->_table_prefix . '.created, ' . $this->_table_prefix . '.id', 'DESC' );

		// --------------------------------------------------------------------------

		//	Handle $data
		if ( ! empty( $data['where'] ) ) :



		endif;
	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$obj )
	{
		parent::_format_object( $obj );

		if ( ! empty( $obj->item_id ) ) :

			$obj->item_id = (int) $obj->item_id;

		endif;

		$obj->changes = unserialize( $obj->changes );

		$obj->user				= new stdClass();
		$obj->user->id			= $obj->user_id;
		$obj->user->first_name	= $obj->first_name;
		$obj->user->last_name	= $obj->last_name;
		$obj->user->gender		= $obj->gender;
		$obj->user->profile_img	= $obj->profile_img;
		$obj->user->email		= $obj->email;

		unset( $obj->user_id );
		unset( $obj->first_name );
		unset( $obj->last_name );
		unset( $obj->gender );
		unset( $obj->profile_img );
		unset( $obj->email );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core Nails
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_ADMIN_CHANGELOG_MODEL' ) ) :

	class Admin_changelog_model extends NAILS_Admin_changelog_model
	{
	}

endif;

/* End of file admin_help_model.php */
/* Location: ./application/models/admin_help_model.php */