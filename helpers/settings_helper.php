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
	function app_setting( $key = NULL, $grouping = 'app', $force_refresh = FALSE )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'settings' ) ) :

			get_instance()->load->model( 'system/settings_model', 'settings' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->settings->get_settings( $key, $grouping, $force_refresh );
	}
}