<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_inform_product_available_model.php
 *
 * Description:		This model handles informing customers when products are back in stock
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Shop_inform_product_available_model extends NAILS_Model
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table			= NAILS_DB_PREFIX . 'shop_inform_product_available';
		$this->_table_prefix	= 'sipa';
	}


	// --------------------------------------------------------------------------


	public function inform( $product_id, $variation_ids )
	{
		$variation_ids = (array) $variation_ids;
		$variation_ids = array_filter( $variation_ids );
		$variation_ids = array_unique( $variation_ids );

		if ( $variation_ids ) :

			if ( ! $this->load->model_is_loaded( 'shop_product_model' ) ) :

				$this->load->model( 'shop/shop_product_model' );

			endif;

			$_product = $this->shop_product_model->get_by_id( $product_id );

			if ( $_product && $_product->is_active && ! $_product->is_deleted ) :

				foreach ( $variation_ids AS $variation_id ) :

					$this->db->select( $this->_table_prefix . '.*' );
					$this->db->where( $this->_table_prefix . '.product_id', $product_id );
					$this->db->where( $this->_table_prefix . '.variation_id', $variation_id );
					$_result = $this->db->get( $this->_table . ' ' . $this->_table_prefix )->result();

					foreach( $_result AS $result ) :

						$_email							= new stdClass();
						$_email->to_email				= $result->email;
						$_email->type					= 'shop_inform_product_available';
						$_email->data					= array();
						$_email->data['product']		= $_product;
						$_email->data['variation']		= $variation_id;
						$_email->data['email_subject']	= $_product->label . ' is back in stock.';

						$this->load->library( 'emailer' );
						$this->emailer->send( $_email );

					endforeach;

				endforeach;

				//	Delete requests
				$this->db->where( 'product_id', $product_id );
				$this->db->where_in( 'variation_id', $variation_ids );
				$this->db->delete( $this->_table );

			endif;

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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP_INFORM_PRODUCT_AVAILABLE_MODEL' ) ) :

	class Shop_inform_product_available_model extends NAILS_Shop_inform_product_available_model
	{
	}

endif;

/* End of file shop_inform_product_available_model.php */
/* Location: ./modules/shop/models/shop_inform_product_available_model.php */