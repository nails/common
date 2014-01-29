<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			cms_slider_model.php
 *
 * Description:		This model handles everything to do with CMS sliders
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Cms_slider_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table				= NAILS_DB_PREFIX . 'cms_slider';
		$this->_table_prefix		= 's';
		$this->_table_item			= NAILS_DB_PREFIX . 'cms_slider_item';
		$this->_table_item_prefix	= 'si';
	}


	// --------------------------------------------------------------------------


	public function get_all( $include_slider_items = FALSE )
	{
		$this->db->select( $this->_table_prefix . '.*,u.first_name,u.last_name,u.profile_img,u.gender,ue.email' );
		$this->db->join( NAILS_DB_PREFIX . 'user u', $this->_table_prefix . '.modified_by = u.id' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', $this->_table_prefix . '.modified_by = ue.user_id AND ue.is_primary = 1' );
		$_sliders = parent::get_all();

		foreach ( $_sliders AS $m ) :

			if ( $include_slider_items ) :

				//	Fetch the nested slider items
				$m->items = $this->get_slider_items( $m->id );

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		return $_sliders;
	}


	// --------------------------------------------------------------------------


	public function get_by_id( $include_slider_items = FALSE )
	{
		$_slider = $this->get_all( $include_slider_items );

		if ( ! $_slider ) :

			return FALSE;

		endif;

		return $_slider[0];
	}


	// --------------------------------------------------------------------------


	public function get_slider_items( $slider_id )
	{
		$this->db->where( 'slider_id', $slider_id );
		$this->db->order_by( 'order' );
		$_items = $this->db->get( $this->_table_item )->result();

		foreach ( $_items AS $i ) :

			$this->_format_slider_item( $i );

		endforeach;

		return $_items;
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


	protected function _format_slider_item( &$obj )
	{
		parent::_format_object( $obj );

		// --------------------------------------------------------------------------

		$obj->slider_id	= (int) $obj->slider_id;
		$obj->object_id	= $obj->object_id ? (int) $obj->object_id : NULL;
		$obj->page_id	= $obj->page_id ? (int) $obj->page_id : NULL;

		unset( $obj->slider_id );
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CMS_SLIDER_MODEL' ) ) :

	class Cms_slider_model extends NAILS_Cms_slider_model
	{
	}

endif;


/* End of file cms_slider_model.php */
/* Location: ./models/cms_slider_model.php */