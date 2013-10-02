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

		$this->_table = NAILS_DB_PREFIX . 'shop_category';
	}

	// --------------------------------------------------------------------------


	public function get_all( $include_children = FALSE, $include_product_count = FALSE )
	{
		$_all = parent::get_all();

		if ( $include_children ) :

			foreach( $_all AS $cat ) :

				$this->db->where( 'parent_id', $cat->id );
				$cat->children = parent::get_all();

				if ( $include_product_count ) :

					foreach( $cat->children AS $child ) :

					$this->db->where( 'category_id', $child->id );
					$this->db->group_by( 'product_id' );
					$child->product_count = $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_product_category' );

					endforeach;

				endif;

			endforeach;

		endif;

		if ( $include_product_count ) :

			foreach( $_all AS $cat ) :

				$this->db->where( 'category_id', $cat->id );
				$this->db->group_by( 'product_id' );
				$cat->product_count = $this->db->count_all_results( NAILS_DB_PREFIX . 'shop_product_category' );

			endforeach;

		endif;

		return $_all;
	}


	// --------------------------------------------------------------------------


	public function get_all_nested()
	{
		$this->db->order_by( 'order,label' );
		return $this->_nest_categories( $this->get_all() );
	}


	// --------------------------------------------------------------------------

	/**
	 *	Hat tip to Timur; http://stackoverflow.com/a/9224696/789224
	 **/
	protected function _nest_categories( &$list, $parent = NULL )
	{
		$result = array();

		for ( $i = 0, $c = count( $list ); $i < $c; $i++ ) :

			if ( $list[$i]->parent_id == $parent ) :

				$list[$i]->children	= $this->_nest_categories( $list, $list[$i]->id );
				$result[]			= $list[$i];

			endif;

		endfor;

		return $result;
	}


	// --------------------------------------------------------------------------


	public function get_all_nested_flat( $separator = ' &rsaquo; ', $murder_parents_of_children = TRUE )
	{
		$_out			= array();
		$_categories	= $this->get_all();

		foreach ( $_categories AS $cat ) :

			$_out[$cat->id] = $this->_find_parents( $cat->parent_id, $_categories, $separator ) . $cat->label;

		endforeach;

		sort( $_out );

		// --------------------------------------------------------------------------

		//	Remove parents from the array if they have any children
		if ( $murder_parents_of_children ) :

			for ( $i = 0; $i < count( $_out ); $i++ ) :

				$_found		= FALSE;
				$_needle	= $_out[$i] . $separator;

				//	Hat tip - http://uk3.php.net/manual/en/function.array-search.php#90711
				foreach ( $_out as $item ) :

					if ( strpos( $item, $_needle ) !== FALSE ) :

						$_found = TRUE;
						break;

					endif;

				endforeach;

				if ( $_found)	 :

					unset( $_out[$i] );

				endif;

			endfor;

		endif;

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _find_parents( $parent_id, &$source, $separator )
	{
		if ( ! $parent_id ) :

			//	No parent ID, end of the line señor!
			return '';

		else :

			//	There is a parent, look for it
			foreach ( $source AS $src ) :

				if ( $src->id == $parent_id ) :

					$_parent = $src;

				endif;

			endforeach;

			if ( isset( $_parent ) && $_parent ) :

				//	Parent was found, does it have any parents?
				if ( $_parent->parent_id ) :

					//	Yes it does, repeat!
					$_return = $this->_find_parents( $_parent->parent_id, $source, $separator );

					return $_return ? $_return . $_parent->label . $separator : $_parent->label;

				else :

					//	Nope, end of the line mademoiselle
					return $_parent->label . $separator;

				endif;


			else :

				//	Did not find parent, give up.
				return '';

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_by_id( $id, $include_children = FALSE, $include_product_count = FALSE, $nest = FALSE )
	{
		//	Fetch
		$this->db->where( 'id', $id );
		$_categories = $this->get_all( $include_children, $include_product_count );

		if ( ! $_categories ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Process
		return $this->_get_by_idslug( $_categories, $nest );
	}


	// --------------------------------------------------------------------------


	public function get_by_slug( $slug, $include_children = FALSE, $include_product_count = FALSE, $nest = FALSE )
	{
		//	Fetch
		$this->db->where( 'slug', $slug );
		$_categories = $this->get_all( $include_children, $include_product_count );

		if ( ! $_categories ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Process
		return $this->_get_by_idslug( $_categories, $nest );
	}


	// --------------------------------------------------------------------------


	protected function _get_by_idslug( $categories, $nest )
	{
		//	Are we nesting this category amongst it's parents?
		if ( $nest ) :

			$_family_tree	= array();
			$_family_tree[]	= $categories[0];
			$_current		= $categories[0];

			while( $_current && $_current->parent_id ) :

				$_current = $this->get_by_id( $_current->parent_id );

				if ( $_current ) :

					$_family_tree[] = $_current;

				endif;

			endwhile;

			//	Now stitch them together in reverse
			$_family_tree	= array_reverse( $_family_tree );

			if ( count( $_family_tree ) > 1 ) :

				$_out			= $_family_tree[0];
				$_pointer		=& $_out;
				foreach ( $_family_tree AS $category ) :

					//	Place the category
					$_pointer->children = array( $category );

					//	Update the pointer
					$_pointer =& $_pointer->children[0];


				endforeach;

			else :

				$_out = $_family_tree[0];

			endif;

			return $_out;

		else :

			//	Nope, just return
			return $categories[0];

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_top_level( $include_children = FALSE, $include_product_count = FALSE )
	{
		$this->db->where( 'parent_id', NULL );
		return $this->get_all( $include_children, $include_product_count );
	}


	// --------------------------------------------------------------------------


	public function get_ids_of_all_children( $category_id )
	{
		$_out = array();

		$this->db->select( 'id' );
		$this->db->where( 'parent_id', $category_id );
		$_result = $this->db->get( NAILS_DB_PREFIX . 'shop_category' )->result();

		if ( $_result ) :

			foreach( $_result AS $result ) :

				$_out[] = $result->id;

				//	Look for children of this dude
				$_children = $this->get_ids_of_all_children( $result->id );

				if ( $_children ) :

					$_out = array_merge( $_out, $_children );

				endif;

			endforeach;

		endif;

		return $_out;
	}


	// --------------------------------------------------------------------------

	protected function _get_ids_of_all_children( $category_id )
	{
		$result = array();

		for ( $i = 0, $c = count( $list ); $i < $c; $i++ ) :

			if ( $list[$i]->parent_id == $parent ) :

				$list[$i]->children	= $this->_nest_categories( $list, $list[$i]->id );
				$result[]			= $list[$i];

			endif;

		endfor;

		return $result;
	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$object )
	{
		//	Type casting
		$object->id				= (int) $object->id;
		$object->parent_id		= $object->parent_id ? (int) $object->parent_id : NULL;
		$object->order			= (int) $object->order;
		$object->created_by		= $object->created_by ? (int) $object->created_by : NULL;
		$object->modified_by	= $object->modified_by ? (int) $object->modified_by : NULL;
		$object->children		= array();
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
 * CodeIgniter  instanciate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
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
/* Location: ./application/models/shop_category_model.php */