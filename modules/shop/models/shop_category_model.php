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
	protected $_table = 'shop_category';
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
		public function get_all_nested()
		{
			$this->db->order_by( 'label' );
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


		public function get_all_nested_flat( $separator = ' &rsaquo; ' )
		{
			$_out			= array();
			$_categories	= $this->get_all();

			foreach ( $_categories AS $cat ) :

				$_out[$cat->id] = $this->_find_parents( $cat->parent_id, $_categories, $separator ) . $cat->label;

			endforeach;

			sort( $_out );

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
	}

endif;

/* End of file shop_category_model.php */
/* Location: ./application/models/shop_category_model.php */