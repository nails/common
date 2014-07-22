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
		//	Load the shop model, if not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop_model' ) ) :

			get_instance()->load->model( 'shop/shop_model' );

		endif;

		// --------------------------------------------------------------------------

		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop_basket_model' ) ) :

			get_instance()->load->model( 'shop/shop_basket_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->shop_basket_model->get_basket();
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
		//	Load the shop model, if not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop_model' ) ) :

			get_instance()->load->model( 'shop/shop_model' );

		endif;

		// --------------------------------------------------------------------------

		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop_basket_model' ) ) :

			get_instance()->load->model( 'shop/shop_basket_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->shop_basket_model->get_basket_count( $respect_quantity );
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
		//	Load the shop model, if not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop_model' ) ) :

			get_instance()->load->model( 'shop/shop_model' );

		endif;

		// --------------------------------------------------------------------------

		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop_basket_model' ) ) :

			get_instance()->load->model( 'shop/shop_basket_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->shop_basket_model->get_basket_total( $include_symbol, $include_thousands );
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
 * Helper for quickly formatting shop prices
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'shop_format_price' ) )
{
	function shop_format_price( $price, $include_symbol = FALSE, $include_thousands = FALSE, $for_currency = NULL, $decode_symbol = FALSE )
	{
		//	Load the shop model, if not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop_model' ) ) :

			get_instance()->load->model( 'shop/shop_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->shop_model->format_price( $price, $include_symbol, $include_thousands, $for_currency, $decode_symbol );
	}
}


// --------------------------------------------------------------------------


/**
 * Helper for quickly converting shop prices
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'shop_convert_to_user' ) )
{
	function shop_convert_to_user( $price )
	{
		//	Load the shop model, if not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop_currency_model' ) ) :

			get_instance()->load->model( 'shop/shop_currency_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->shop_currency_model->convert_to_user( $price );
	}
}


// --------------------------------------------------------------------------


/**
 * Helper for quickly converting shop prices
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'shop_convert_using_rate' ) )
{
	function shop_convert_using_rate( $price, $rate )
	{
		//	Load the shop model, if not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'shop_currency_model' ) ) :

			get_instance()->load->model( 'shop/shop_currency_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->shop_currency_model->convert_using_rate( $price, $rate );
	}
}


// --------------------------------------------------------------------------


/**
 * Helper for quickly converting shop prices
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( '_shop_sidebar_nested_categories_html' ) )
{
	function _shop_sidebar_nested_categories_html( $categories, $include_count = TRUE, $return = TRUE,  $level = 0 )
	{
		if ( ! is_array( $categories ) ) :

			$categories = (array) $categories;

		endif;

		// --------------------------------------------------------------------------

		$_styled = empty( $level ) ? 'list-unstyled' : '';
		$_out = '<ul class="categories nested level-' . $level . ' ' . $_styled . '">';

		foreach( $categories AS $category ) :

			//	Don't show if we know there's no products in it
			if ( isset( $category->product_count ) && ! $category->product_count ) :

				continue;

			endif;

			$_out .= '<li class="category">';
			$_out .= '<p><a href="' . site_url( app_setting( 'url', 'shop' ) ) . '/category/' . $category->slug . '">';
			$_out .= $category->label;

			if ( $include_count && isset( $category->product_count ) ) :

				$_out .= '&nbsp;<span class="badge">' . $category->product_count . '</span>';

			endif;

			$_out .= '</a></p>';

			if ( $category->children ) :

				$_out .= _shop_sidebar_nested_categories_html( $category->children, $include_count, $return, $level+1 );

			endif;

			$_out .= '</li>';

		endforeach;

		$_out .= '</ul>';

		// --------------------------------------------------------------------------

		//	Handle output
		if ( $return ) :

			return $_out;

		else :

			echo $_out;

		endif;
	}
}


// --------------------------------------------------------------------------


if ( ! function_exists( '_shop_sidebar_render_category' ) )
{
	function _shop_sidebar_render_category( $category )
	{
	}
}


/* End of file shop_helper.php */
/* Location: ./modules/shop/helpers/shop_helper.php */