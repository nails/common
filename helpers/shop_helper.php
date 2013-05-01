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
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'basket' ) ) :
		
			get_instance()->load->model( 'shop/shop_basket_model', 'basket' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return get_instance()->basket->get_basket();
	}
}


// --------------------------------------------------------------------------


/**
 * Gets the number of items of the absket
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'get_basket_count' ) )
{
	function get_basket_count( $respect_quantity = TRUE )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'basket' ) ) :
		
			get_instance()->load->model( 'shop/shop_basket_model', 'basket' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return get_instance()->basket->get_basket_count( $respect_quantity );
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
if ( ! function_exists( 'add_to_basket_button' ) )
{
	function add_to_basket_button( $product_id, $button_text = NULL, $attr = 'class="add-to-basket awesome small"', $return_to = NULL )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'basket' ) ) :
		
			get_instance()->load->model( 'shop/shop_basket_model', 'basket' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_in_basket = get_instance()->basket->is_in_basket( $product_id );
		
		// --------------------------------------------------------------------------
		
		if ( ! $button_text ) :
		
			get_instance()->lang->load( 'shop/shop', RENDER_LANG );
			
			if ( $_in_basket ) :
			
				return anchor( remove_from_basket_url( $product_id, $return_to ), lang( 'button_remove_from_basket' ), $attr );
				
			else :
			
				return anchor( add_to_basket_url( $product_id, $return_to ), lang( 'button_add_to_basket' ), $attr );
			
			endif;
		
		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * Get's the URL for adding to the basket
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'add_to_basket_url' ) )
{
	function add_to_basket_url( $product_id, $return_to = NULL )
	{
		$_return = $return_to ? '?return=' . urlencode( $return_to ) : ''; 
		return site_url( 'shop/basket/add/' . $product_id . $_return );
	}
}


// --------------------------------------------------------------------------


/**
 * Get's the URL for removing from the basket
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'remove_from_basket_url' ) )
{
	function remove_from_basket_url( $product_id, $return_to = NULL )
	{
		$_return = $return_to ? '?return=' . urlencode( $return_to ) : ''; 
		return site_url( 'shop/basket/remove/' . $product_id . $_return );
	}
}


// --------------------------------------------------------------------------


/**
 * Get's the URL for adding to the basket
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'round_to_precision' ) )
{
	function round_to_precision( $in, $prec )
	{
		$fact = pow( 10, $prec );
		return ceil( $fact * $in ) / $fact;
	}
}


// --------------------------------------------------------------------------


/**
 * Get's the URL for adding to the basket
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'shop_setting' ) )
{
	function shop_setting( $key = NULL )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop' ) ) :
		
			get_instance()->load->model( 'shop/shop_model', 'shop' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return get_instance()->shop->settings( $key );
	}
}


/* End of file shop_helper.php */
/* Location: ./modules/shop/helpers/shop_helper.php */