<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_category_model.php
 *
 * Description:		This model handles interfacing with shop categorys
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_category_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		$this->_table			= NAILS_DB_PREFIX . 'shop_category';
		$this->_table_prefix	= 'sc';
	}


	// --------------------------------------------------------------------------


	public function create( $data, $return_object = FALSE )
	{
		//	Some basic sanity testing
		if ( empty( $data->label ) ) :

			$this->_set_error( '"label" is a required field.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->trans_begin();

		//	Create a new blank object to work with
		$_data	= array( 'label' => $data->label );
		$_id	= parent::create( $_data );

		if ( ! $_id ) :

			$this->_set_error( 'Unable to create base category object.' );
			$this->db->trans_rollback();
			return FALSE;

		elseif ( $this->update( $_id, $data ) ) :

			$this->db->trans_commit();

			if ( $return_object ) :

				return $this->get_by_id( $_id );

			else :

				return $_id;

			endif;

		else :

			$this->db->trans_rollback();
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function update( $id, $data = array() )
	{
		$_data = new stdClass();

		// --------------------------------------------------------------------------

		//	Prep the data
		if ( empty( $data->label ) ) :

			$this->_set_error( '"label" is a required field.' );
			return FALSE;

		else :

			$_data->label = trim( $data->label );

		endif;

		if ( isset( $data->parent_id ) ) :

			$_data->parent_id = (int) $data->parent_id;

			if ( empty( $_data->parent_id ) ) :

				$_data->parent_id = NULL;

			endif;

			if ( $_data->parent_id == $id ) :

				$this->_set_error( '"parent_id" cannot be the same as the category\'s ID.' );
				return FALSE;

			endif;

		endif;

		if ( isset( $data->description ) ) :

			$_data->description = $data->description;

		endif;

		if ( isset( $data->seo_title ) ) :

			$_data->seo_title = strip_tags( $data->seo_title );

		endif;

		if ( isset( $data->seo_description ) ) :

			$_data->seo_description = strip_tags( $data->seo_description );

		endif;

		// --------------------------------------------------------------------------

		//	Generate the slug
		//	If there's a parent then prefix the slug with the parent's slug

		if ( ! empty( $_data->parent_id ) ) :

			$this->db->select( 'slug' );
			$this->db->where( 'id', $_data->parent_id );
			$_parent = $this->db->get( $this->_table )->row();

			if ( empty( $_parent ) ) :

				$_prefix = '';

				//	Also, invalid aprent, so NULL out parent_id
				$_data->parent_id = NULL;

			else :

				$_prefix = $_parent->slug . '/';

			endif;

		else :

			//	No parent == no prefix
			$_prefix = '';

		endif;

		$_data->slug		= $this->_generate_slug( $_data->label, $_prefix, '', NULL, NULL, $id );
		$_data->slug_end	= array_pop( explode( '/', $_data->slug ) );

		// --------------------------------------------------------------------------

		//	Attempt the update
		$this->db->trans_begin();

		if ( parent::update( $id, $_data ) ) :

			//	Success! Generate this category's breadcrumbs
			$_data				= new stdClass();
			$_data->breadcrumbs	= json_encode( $this->_generate_breadcrumbs( $id ) );

			if ( ! parent::update( $id, $_data ) ) :

				$this->db->trans_rollback();
				$this->_set_error( 'Failed to update category breadcrumbs.' );
				return FALSE;

			endif;

			// --------------------------------------------------------------------------

			//	Also regenerate breadcrumbs and slugs for all children
			$_children = $this->_get_children( $id );

			if ( $_children ) :

				foreach ( $_children AS $child_id ) :

					$_child_data = new stdClass();

					//	Breadcrumbs is easy
					$_child_data->breadcrumbs = json_encode( $this->_generate_breadcrumbs( $child_id ) );

					//	Slugs are slightly harder, we need to get the child's parent's slug
					//	and use it as a prefix

					$this->db->select( 'parent_id, label' );
					$this->db->where( 'id', $child_id );
					$_child = $this->db->get( $this->_table )->row();

					if ( ! empty( $_child ) ) :

						$this->db->select( 'slug' );
						$this->db->where( 'id', $_child->parent_id );
						$_parent = $this->db->get( $this->_table )->row();
						$_prefix = empty( $_parent ) ? '' : $_parent->slug . '/';

						$_child_data->slug		= $this->_generate_slug( $_child->label, $_prefix, '', NULL, NULL, $child_id );
						$_child_data->slug_end	= array_pop( explode( '/', $_child_data->slug ) );

					endif;

					if ( ! parent::update( $child_id, $_child_data ) ) :

						$this->db->trans_rollback();
						$this->_set_error( 'Failed to update child category.' );
						return FALSE;

					endif;

				endforeach;

			endif;

			$this->db->trans_commit();
			return TRUE;

		else :

			$this->db->trans_rollback();
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _generate_breadcrumbs( $id )
	{
		//	Fetch current
		$this->db->select( 'id,slug,label' );
		$this->db->where( 'id', $id );
		$_current = $this->db->get( $this->_table )->result();

		if ( empty( $_current ) ) :

			return FALSE;

		endif;

		//	Fetch parents
		$_parents = $this->_get_parents( $id );

		if ( ! empty( $_parents ) ) :

			$this->db->select( 'id,slug,label' );
			$this->db->where_in( 'id', $_parents );
			$_parents = $this->db->get( $this->_table )->result();

		endif;

		//	Finally, build breadcrumbs
		return array_merge( $_parents, $_current );
	}


	// --------------------------------------------------------------------------


	protected function _get_parents( $id )
	{
		$_return = array();

		$this->db->select( 'parent_id' );
		$this->db->where( 'id', $id );
		$_parent = $this->db->get( $this->_table )->row();

		if ( ! empty( $_parent->parent_id ) ) :

			$_temp		= array( $_parent->parent_id );
			$_return	= array_merge( $_return, $_temp, $this->_get_parents( $_parent->parent_id ) );

		endif;

		return array_unique( array_filter( $_return ) );
	}


	// --------------------------------------------------------------------------


	protected function _get_children( $id )
	{
		$_return = array();

		$this->db->select( 'id' );
		$this->db->where( 'parent_id', $id );
		$_children = $this->db->get( $this->_table )->result();

		if ( ! empty( $_children ) ) :

			foreach( $_children AS $child ) :

				$_temp		= array( $child->id );
				$_return	= array_merge( $_return, $_temp, $this->_get_children( $child->id ) );

			endforeach;

		endif;

		return array_unique( array_filter( $_return ) );
	}


	// --------------------------------------------------------------------------


	public function get_all_nested_flat( $separator = ' &rsaquo; ' )
	{
		$_categories	= $this->get_all();
		$_out			= array();

		foreach( $_categories AS $cat ) :

			$_out[$cat->id] = array();

			foreach ( $cat->breadcrumbs AS $crumb ) :

				$_out[$cat->id][] = $crumb->label;

			endforeach;

			$_out[$cat->id] = implode( $separator, $_out[$cat->id] );

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _getcount_common( $data = array(), $_caller = NULL )
	{
		if ( empty( $data['sort'] ) ) :

			$data['sort'] = 'slug';

		else :

			$data = array( 'sort' => 'slug' );

		endif;

		// --------------------------------------------------------------------------

		if ( ! empty( $data['include_count'] ) ) :

			if ( empty( $this->db->ar_select ) ) :

				//	No selects have been called, call this so that we don't *just* get the product count
				$_prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
				$this->db->select( $_prefix . '*' );

			endif;

			$this->db->select( '(SELECT COUNT(*) FROM ' . NAILS_DB_PREFIX .  'shop_product_category WHERE category_id = ' . $this->_table_prefix . '.id) product_count' );

		endif;

		// --------------------------------------------------------------------------

		return parent::_getcount_common( $data, $_caller );
	}


	// --------------------------------------------------------------------------


	public function format_url( $slug )
	{
		return site_url( app_setting( 'url', 'shop' ) . 'category/' . $slug );
	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$object )
	{
		//	Type casting
		$object->id				= (int) $object->id;
		$object->parent_id		= $object->parent_id ? (int) $object->parent_id : NULL;
		$object->created_by		= $object->created_by ? (int) $object->created_by : NULL;
		$object->modified_by	= $object->modified_by ? (int) $object->modified_by : NULL;
		$object->children		= array();

		$object->breadcrumbs	= (array) @json_decode( $object->breadcrumbs );

		$object->depth			= count( explode( '/', $object->slug ) ) - 1;
		$object->url			= $this->format_url( $object->slug );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core shop
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_CATEGORY_MODEL' ) ) :

	class Shop_category_model extends NAILS_Shop_category_model
	{
	}

endif;

/* End of file shop_category_model.php */
/* Location: ./modules/shop/models/shop_category_model.php */