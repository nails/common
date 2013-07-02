<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Helper for quickly accessing blog settings
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'site_setting' ) )
{
	function site_setting( $key = NULL, $force_refresh = FALSE )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'site' ) ) :
		
			get_instance()->load->model( 'system/site_model', 'site' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return get_instance()->site->settings( $key, $force_refresh );
	}
}