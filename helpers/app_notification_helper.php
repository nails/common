<?php

if (!function_exists('app_notification'))
{
	/**
	 * Get's emails associated with a particular group/key
	 * @param  string  $key          The key to retrieve
	 * @param  string  $grouping     The group the key belongs to
	 * @param  boolean $forceRefresh Whether to force a group refresh
	 * @return array
	 */
	function app_notification($key = NULL, $grouping = 'app', $forceRefresh = FALSE)
	{
		//	Load the model if it's not already loaded
		if (!get_instance()->load->isModelLoaded('app_notification_model')) {

			get_instance()->load->model('app_notification_model');
		}

		// --------------------------------------------------------------------------

		return get_instance()->app_notification_model->get($key, $grouping, $forceRefresh);
	}
}

// --------------------------------------------------------------------------

if (!function_exists('app_notification_notify'))
{
	/**
	 * Sends a notification to the email addresses associated with a particular key/grouping
	 * @param  string $key      The key to send to
	 * @param  string $grouping The key's grouping
	 * @param  array  $data     An array of values to pass to the email template
	 * @param  array  $override Override any of the definition values (this time only). Useful for defining custom email templates etc.
	 * @return boolean
	 */
	function app_notification_notify($key = NULL, $grouping = 'app', $data = array(), $override = array())
	{
		//	Load the model if it's not already loaded
		if (!get_instance()->load->isModelLoaded('app_notification_model')) {

			get_instance()->load->model('app_notification_model');
		}

		// --------------------------------------------------------------------------

		return get_instance()->app_notification_model->notify($key, $grouping, $data, $override);
	}
}
