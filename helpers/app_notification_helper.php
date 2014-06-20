<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Helper for quickly accessing notifications
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'app_notification' ) )
{
	function app_notification( $key = NULL, $grouping = 'app', $force_refresh = FALSE )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'app_notification_model' ) ) :

			get_instance()->load->model( 'system/app_notification_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->app_notification_model->get( $key, $grouping, $force_refresh );
	}
}