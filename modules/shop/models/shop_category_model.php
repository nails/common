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


	public function get_all( $include_children = FALSE, $include_product_count = FALSE, $order_style = 'NORMAL' )
	{
		switch( $order_style ) :

			case 'NORMAL' :

				$this->db->order_by( 'order,label' );

			break;

			case 'NESTED' :

				$this->db->order_by( 'slug' );

			break;

		endswitch;


		$_all = parent::get_all();

		if ( $include_children ) :

			foreach( $_all AS $cat ) :

				$this->db->where( 'parent_id', $cat->id );
				$cat->children = parent::get_all();

				if ( $include_product_count ) :

					foreach( $cat->children AS $child ) :

						$_ids = array_merge( array( $child->id ), $this->get_ids_of_all_children( $child->id ) );

						$this->db->select( 'COUNT( DISTINCT( `spc`.`product_id` ) ) total' );
						$this->db->where_in( 'spc.category_id', $_ids );
						$this->db->where( 'p.is_active', TRUE );
						$this->db->join( NAILS_DB_PREFIX . 'shop_product p', 'p.id = spc.product_id' );
						$child->product_count = $this->db->get( NAILS_DB_PREFIX . 'shop_product_category spc' )->row()->total;

					endforeach;

				endif;

			endforeach;

		endif;

		if ( $include_product_count ) :

			foreach( $_all AS $cat ) :

				$_ids = array_merge( array( $cat->id ), $this->get_ids_of_all_children( $cat->id ) );
				$this->db->select( 'COUNT( DISTINCT( `spc`.`product_id` ) ) total' );
				$this->db->where_in( 'category_id', $_ids );
				$this->db->where( 'p.is_active', TRUE );
				$this->db->join( NAILS_DB_PREFIX . 'shop_product p', 'p.id = spc.product_id' );
				$cat->product_count = $this->db->get( NAILS_DB_PREFIX . 'shop_product_category spc' )->row()->total;

			endforeach;

		endif;

		return $_all;
	}


	// --------------------------------------------------------------------------


	public function get_all_nested()
	{
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

		asort( $_out );

		// --------------------------------------------------------------------------

		//	Remove parents from the array if they have any children
		if ( $murder_parents_of_children ) :

			foreach( $_out AS $key => &$cat ) :

				$_found		= FALSE;
				$_needle	= $cat . $separator;

				//	Hat tip - http://uk3.php.net/manual/en/function.array-search.php#90711
				foreach ( $_out as $item ) :

					if ( strpos( $item, $_needle ) !== FALSE ) :

						$_found = TRUE;
						break;

					endif;

				endforeach;

				if ( $_found ) :

					unset( $_out[$key] );

				endif;

			endforeach;

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
		//	Check the cache
		$_cache_key = 'get_ids_of_all_children-' . $category_id;
		$_cache = $this->_get_cache( $_cache_key );

		if ( $_cache ) :

			return $_cache;

		endif;

		// --------------------------------------------------------------------------

		//	Not in cache? Bugger
		$_out = array();

		$this->db->select( 'id' );
		$this->db->where( 'parent_id', $category_id );
		$_result = $this->db->get( $this->_table )->result();

		if ( $_result ) :

			foreach( $_result AS $result ) :

				$_out[] = (int) $result->id;

				//	Look for children of this dude
				$_children = $this->get_ids_of_all_children( $result->id );

				if ( $_children ) :

					$_out = array_merge( $_out, $_children );

				endif;

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Cache this
		$this->_set_cache( $_cache_key, $_out );

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	public function create( $data, $return_obj = FALSE )
	{
		$_data = new stdClass();

		if ( isset( $data->label ) ) :

			$_data->label = strip_tags( $data->label );

		else :

			$this->_set_error( 'Label is required.' );
			return FALSE;

		endif;

		if ( isset( $data->parent_id ) ) :

			$_data->parent_id = ! empty( $data->parent_id ) ? (int) $data->parent_id : NULL;

			//	Work out the slug, prefix it with nested parents
			$_parent = $this->get_by_id( $_data->parent_id );

			if ( $_data->parent_id && ! $_parent ) :

				$this->_set_error( 'Invalid Parent ID.' );
				return FALSE;

			endif;

			if ( $_data->parent_id ) :

				$_data->slug			= $this->_generate_slug( $data->label, $this->_table, 'slug', $_parent->slug . '/' );

				//	Do it like this as _generate_slug() may have added some numbers or something after (i.e. can't use url_title())
				$_data->slug_end		= preg_replace( '#^' . str_replace( '-', '\-', $_parent->slug ) . '/#', '', $_data->slug );
				$_data->label_nested	= $_parent->label_nested . '|' . $_data->label;

			else :

				//	No parent, slug is just the label
				$_data->slug			= $this->_generate_slug( $data->label, $this->_table, 'slug' );
				$_data->slug_end		= $_data->slug;
				$_data->label_nested	= $_data->label;

			endif;

		else :

			//	No parent, slug is just the label
			$_data->slug			= $this->_generate_slug( $data->label, $this->_table, 'slug' );
			$_data->label_nested	= $_data->label;

		endif;

		if ( isset( $data->description ) ) :

			$_data->description = strip_tags( $data->description, '<a><strong><em><img>' );

		endif;

		if ( isset( $data->seo_description ) ) :

			$_data->seo_description = strip_tags( $data->seo_description );

		endif;

		if ( isset( $data->seo_keywords ) ) :

			$_data->seo_keywords = strip_tags( $data->seo_keywords );

		endif;

		// --------------------------------------------------------------------------

		return parent::create( $_data, $return_obj );
	}


	// --------------------------------------------------------------------------


	public function update( $id, $data )
	{
		//	Fetch the current category, incldue the children - we may need to
		//	update them if the label changes

		$_current = $this->get_by_id( $id );
		$_children = $this->get_ids_of_all_children( $_current->id );

		if ( ! $_current ) :

			$this->_set_error( 'Invalid Category ID.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_data			= new stdClass();
		$_regenerate	= FALSE;

		if ( isset( $data->label ) ) :

			if ( $_current->label !== $data->label ) :

				$_data->label	= strip_tags( $data->label );
				$_regenerate	= TRUE;

			endif;

		else :

			$this->_set_error( 'Label is required.' );
			return FALSE;

		endif;

		if ( isset( $data->parent_id ) ) :

			if ( $_current->parent_id != $data->parent_id ) :

				$_data->parent_id	= ! empty( $data->parent_id ) ? (int) $data->parent_id : NULL;
				$_regenerate		= TRUE;

			endif;

		endif;

		//	If we need to regenerate the slug then do so here
		if ( $_regenerate ) :

			//	Get the new slug prefix
			if ( ! empty( $_data->parent_id ) ) :

				$_parent = $this->get_by_id( $_data->parent_id );
				if ( ! $_parent ) :

					$this->_set_error( 'Invalid Parent ID.' );
					return FALSE;

				endif;

				$_parent_slug			= $_parent->slug . '/';
				$_parent_label_nested	= $_parent->label_nested . '|';

			else :

				$_parent_slug			= '';
				$_parent_label_nested	= '';

			endif;

			//	Get new slug details
			$_label					= isset( $_data->label ) ? $_data->label : $_current->label;
			$_data->slug			= $this->_generate_slug( $_label, $this->_table, 'slug', $_parent_slug );

			//	Do it like this as _generate_slug() may have added some numbers or something after (i.e. can't use url_title())
			$_data->slug_end		= preg_replace( '#^' . str_replace( '-', '\-', $_parent_slug ) . '#', '', $_data->slug );
			$_data->label_nested	= $_parent_label_nested . $_label;

		endif;

		if ( isset( $data->description ) ) :

			$_data->description = strip_tags( $data->description, '<a><strong><em><img>' );

		endif;

		if ( isset( $data->seo_description ) ) :

			$_data->seo_description = strip_tags( $data->seo_description );

		endif;

		if ( isset( $data->seo_keywords ) ) :

			$_data->seo_keywords = strip_tags( $data->seo_keywords );

		endif;

		if ( ! empty( $_data ) ) :

			//	Start the transaction
			$this->db->trans_begin();

			$this->db->set( $_data );
			$this->db->set( 'modified', 'NOW()', FALSE );
			$this->db->where( 'id', $id );

			if ( active_user( 'id' ) ) :

				$this->db->set( 'modified_by', active_user( 'id' ) );

			endif;

			if ( $this->db->update( $this->_table ) ) :

				//	Regenerate anything? This may seem like magic, future Pabs, but we're just
				//	gluing stuff together from the other stuffs we made up there^^

				if ( $_regenerate ) :

					//	Update children's slugs and nested labels
					$_children = $this->get_ids_of_all_children( $id );

					if ( $_children ) :

						$this->db->set( 'label_nested', 'CONCAT( "' . $_data->label_nested . '|", label )', FALSE );
						$this->db->set( 'slug', 'CONCAT( "' . $_data->slug . '/", slug_end )', FALSE );
						$this->db->where_in( 'id', $_children );
						$this->db->update( $this->_table );

						if ( $this->db->trans_status() !== FALSE ) :

							$this->db->trans_commit();
							return TRUE;

						else :

							$this->db->trans_rollback();
							return FALSE;

						endif;

					else :

						$this->db->trans_commit();
						return TRUE;

					endif;

				else :

					//	Complete, commit transaction
					$this->db->trans_commit();

				endif;

				return TRUE;

			else :

				//	Failed, roll back
				$this->db->trans_rollback();

				return FALSE;

			endif;

		else :

			return TRUE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function delete( $id )
	{
		//	Turn off DB Errors
		$_previous = $this->db->db_debug;
		$this->db->db_debug = FALSE;

		$this->db->trans_begin();
		$this->db->where( 'id', $id );
		$this->db->delete( $this->_table );
		$_affected_rows = $this->db->affected_rows();

		if ( $this->db->trans_status() === FALSE ) :

		    $this->db->trans_rollback();
			$_return = FALSE;

		else :

		    $this->db->trans_commit();
			$_return = (bool) $_affected_rows;

		endif;

		//	Put DB errors back as they were
		$this->db->db_debug = $_previous;

		return $_return;
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

		$object->depth			= count( explode( '/', $object->slug ) ) - 1;
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
/* Location: ./application/models/shop_category_model.php */