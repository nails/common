<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Helper for quickly accessing app settings
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'app_setting' ) )
{
	function app_setting( $key = NULL, $grouping = 'app', $force_refresh = FALSE )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'app_setting_model' ) ) :

			get_instance()->load->model( 'system/app_setting_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->app_setting_model->get( $key, $grouping, $force_refresh );
	}
}


// --------------------------------------------------------------------------


/**
 * Helper for quickly setting app settings
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'set_app_setting' ) )
{
	function set_app_setting( $key, $grouping = 'app', $value = NULL, $encrypt = FALSE )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'app_setting_model' ) ) :

			get_instance()->load->model( 'system/app_setting_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->app_setting_model->set( $key, $grouping, $value, $encrypt );
	}
}

