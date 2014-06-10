<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Helper for quickly accessing notifications
 *
 * @access	public
 * @param	none
 * @return	void
 */
if ( ! function_exists( 'notification' ) )
{
	function notification( $key = NULL, $grouping = 'app', $force_refresh = FALSE )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'notification_model' ) ) :

			get_instance()->load->model( 'notification/notification_model' );

		endif;

		// --------------------------------------------------------------------------

		return get_instance()->notification_model->get( $key, $grouping, $force_refresh );
	}
}