<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			cms_menu_model.php
 *
 * Description:		This model handles everything to do with CMS menus
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Cms_menu_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table				= NAILS_DB_PREFIX . 'cms_menu';
		$this->_table_prefix		= 'm';
		$this->_table_item			= NAILS_DB_PREFIX . 'cms_menu_item';
		$this->_table_item_prefix	= 'mi';
	}


	// --------------------------------------------------------------------------


	public function get_all( $include_menu_items = FALSE )
	{
		$this->db->select( $this->_table_prefix . '.*,u.first_name,u.last_name,u.profile_img,u.gender,ue.email' );
		$this->db->join( NAILS_DB_PREFIX . 'user u', $this->_table_prefix . '.modified_by = u.id' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', $this->_table_prefix . '.modified_by = ue.user_id AND ue.is_primary = 1' );
		$_menus = parent::get_all();

		foreach ( $_menus AS $m ) :

			if ( $include_menu_items ) :

				//	Fetch the nested menu items
				$m->items = $this->_nest_items( $this->get_menu_items( $m->id ) );

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		return $_menus;
	}


	// --------------------------------------------------------------------------


	public function get_by_id( $include_menu_items = FALSE )
	{
		$_menu = $this->get_all( $include_menu_items );

		if ( ! $_menu ) :

			return FALSE;

		endif;

		return $_menu[0];
	}


	// --------------------------------------------------------------------------


	public function get_menu_items( $menu_id )
	{
		$this->db->where( 'menu_id', $menu_id );
		$_items = $this->db->get( $this->_table_item )->result();

		foreach ( $_items AS $i ) :

			$this->_format_menu_item( $i );

		endforeach;

		return $_items;
	}


	// --------------------------------------------------------------------------


	/**
	 *	Hat tip to Timur; http://stackoverflow.com/a/9224696/789224
	 **/
	protected function _nest_items( &$list, $parent = NULL )
	{
		$result = array();

		for ( $i = 0, $c = count( $list ); $i < $c; $i++ ) :

			if ( $list[$i]->parent_id == $parent ) :

				$list[$i]->children	= $this->_nest_items( $list, $list[$i]->id );
				$result[]			= $list[$i];

			endif;

		endfor;

		return $result;
	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$obj )
	{
		$_temp				= new stdClass();
		$_temp->id			= $obj->modified_by;
		$_temp->email		= $obj->email;
		$_temp->first_name	= $obj->first_name;
		$_temp->last_name	= $obj->last_name;
		$_temp->gender		= $obj->gender;
		$_temp->profile_img	= $obj->profile_img;

		$obj->modified_by	= $_temp;

		unset( $obj->email );
		unset( $obj->first_name );
		unset( $obj->last_name );
		unset( $obj->gender );
		unset( $obj->profile_img );
	}


	// --------------------------------------------------------------------------


	protected function _format_menu_item( &$obj )
	{
		parent::_format_object( $obj );

		// --------------------------------------------------------------------------

		$obj->menu_id	= (int) $obj->menu_id;
		$obj->parent_id	= $obj->parent_id ? (int) $obj->parent_id : NULL;
		$obj->page_id	= $obj->page_id ? (int) $obj->page_id : NULL;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CMS_MENU_MODEL' ) ) :

	class Cms_menu_model extends NAILS_Cms_menu_model
	{
	}

endif;


/* End of file cms_menu_model.php */
/* Location: ./models/cms_menu_model.php */