<?php

if (!function_exists('app_setting')) {

    /**
     * Helper for quickly accessing app settings
     * @return  mixed
     */
	function app_setting($key = null, $grouping = 'app', $force_refresh = false)
	{
        get_instance()->load->model('app_setting_model');
		return get_instance()->app_setting_model->get($key, $grouping, $force_refresh);
	}
}

// --------------------------------------------------------------------------

if (!function_exists('set_app_setting')) {

    /**
     * Helper for quickly setting app settings
     * @return  void
     */
	function set_app_setting($key, $grouping = 'app', $value = null, $encrypt = false)
	{
        get_instance()->load->model('app_setting_model');
		return get_instance()->app_setting_model->set($key, $grouping, $value, $encrypt);
	}
}

