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
 * Does something
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'get_basket_count' ) )
{
	function get_basket_count()
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'basket' ) ) :
		
			get_instance()->load->model( 'shop/shop_basket_model', 'basket' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return get_instance()->basket->get_basket_count();
	}
}


/* End of file shop_helper.php */
/* Location: ./modules/shop/helpers/shop_helper.php */