<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_brand_model.php
 *
 * Description:		This model handles interfacing with shop brands
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_brand_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		$this->_table = NAILS_DB_PREFIX . 'shop_brand';
	}


	// --------------------------------------------------------------------------


	public function get_all( $include_count  = FALSE)
	{
		$this->db->select( 'b.id,b.slug,b.label,b.logo_id,b.description,b.seo_description,b.seo_keywords,b.created,b.modified' );

		if ( $include_count ) :

			$this->db->select( '(SELECT COUNT(*) FROM ' . NAILS_DB_PREFIX .  'shop_product_brand WHERE brand_id = b.id) product_count' );

		endif;

		$this->db->order_by( 'label' );
		$_result = $this->db->get( $this->_table . ' b' )->result();

		foreach( $_result AS &$r ) :

			$this->_format_object( $r );

		endforeach;

		return $_result;
	}


	// --------------------------------------------------------------------------


	protected function _format_object( &$obj )
	{
		$obj->id				= (int) $obj->id;
		$obj->product_count		= isset( $obj->product_count ) ? (int) $obj->product_count : NULL;
	}


	// --------------------------------------------------------------------------


	public function create( $data )
	{
		$_data = new stdClass();

		if ( isset( $data->label ) ) :

			$_data->label = $data->label;

		else :

			$this->db->_set_error( 'Label is required.' );
			return FALSE;

		endif;

		if ( isset( $data->logo_id ) ) :

			$_data->logo_id = ! empty( $data->logo_id ) ? (int) $data->logo_id : NULL;

		endif;

		if ( isset( $data->description ) ) :

			$_data->description = $data->description;

		endif;

		if ( isset( $data->seo_description ) ) :

			$_data->seo_description = $data->seo_description;

		endif;

		if ( isset( $data->seo_keywords ) ) :

			$_data->seo_keywords = $data->seo_keywords;

		endif;

		if ( ! empty( (array) $_data ) ) :

			//	Generate a slug
			$_data->slug = $this->_generate_slug( $data->label, NAILS_DB_PREFIX . 'shop_brand', 'slug' );
			$this->db->set( $_data );
			$this->db->set( 'created', 'NOW()', FALSE );
			$this->db->set( 'modified', 'NOW()', FALSE );

			if ( active_user( 'id' ) ) :

				$this->db->set( 'created_by', active_user( 'id' ) );
				$this->db->set( 'modified_by', active_user( 'id' ) );

			endif;

			$this->db->insert( NAILS_DB_PREFIX . 'shop_brand' );

			if ( $this->db->affected_rows() ) :

				return $this->db->insert_id();

			else :

				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function update( $id, $data )
	{
		$_data = new stdClass();

		if ( isset( $data->label ) ) :

			$_data->label = $data->label;

		else :

			$this->db->_set_error( 'Label is required.' );
			return FALSE;

		endif;

		if ( isset( $data->logo_id ) ) :

			$_data->logo_id = ! empty( $data->logo_id ) ? (int) $data->logo_id : NULL;

		endif;

		if ( isset( $data->description ) ) :

			$_data->description = $data->description;

		endif;

		if ( isset( $data->seo_description ) ) :

			$_data->seo_description = $data->seo_description;

		endif;

		if ( isset( $data->seo_keywords ) ) :

			$_data->seo_keywords = $data->seo_keywords;

		endif;

		if ( ! empty( (array) $_data ) ) :

			//	Generate a slug
			$this->db->set( $_data );
			$this->db->set( 'modified', 'NOW()', FALSE );
			$this->db->where( 'id', $id );

			if ( active_user( 'id' ) ) :

				$this->db->set( 'modified_by', active_user( 'id' ) );

			endif;

			if ( $this->db->update( NAILS_DB_PREFIX . 'shop_brand' ) ) :

				return TRUE;

			else :

				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_BRAND_MODEL' ) ) :

	class Shop_brand_model extends NAILS_Shop_brand_model
	{
	}

endif;

/* End of file shop_brand_model.php */
/* Location: ./application/models/shop_brand_model.php */