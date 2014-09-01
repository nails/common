<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Returns the content of the basket
 *
 * @access	public
 * @param	none
 * @return	object
 */
if ( ! function_exists( 'get_basket' ) )
{
	function get_basket()
	{
		get_instance()->load->model( 'shop/shop_model' );
		get_instance()->load->model( 'shop/shop_basket_model' );

		// --------------------------------------------------------------------------

		return get_instance()->shop_basket_model->get();
	}
}


// --------------------------------------------------------------------------


/**
 * Gets the number of items of the basket
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'get_basket_count' ) )
{
	function get_basket_count( $respect_quantity = TRUE )
	{
		get_instance()->load->model( 'shop/shop_model' );
		get_instance()->load->model( 'shop/shop_basket_model' );

		// --------------------------------------------------------------------------

		return get_instance()->shop_basket_model->get_count( $respect_quantity );
	}
}


// --------------------------------------------------------------------------


/**
 * Gets the current basket total
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'get_basket_total' ) )
{
	function get_basket_total( $include_symbol = FALSE, $include_thousands = FALSE )
	{
		get_instance()->load->model( 'shop/shop_model' );
		get_instance()->load->model( 'shop/shop_basket_model' );

		// --------------------------------------------------------------------------

		return get_instance()->shop_basket_model->get_total( $include_symbol, $include_thousands );
	}
}


/* End of file shop_helper.php */
/* Location: ./modules/shop/helpers/shop_helper.php */