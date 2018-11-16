<?php

/* Load the base config file from the CodeIgniter package */
require NAILS_CI_APP_PATH . 'config/autoload.php';

/**
 * System Routes
 */
$route['default_controller'] = 'home/index';

/**
 * App Routes
 */
if (!defined('NAILS_STARTUP_GENERATE_APP_ROUTES') || !NAILS_STARTUP_GENERATE_APP_ROUTES) {
    include_once CACHE_PATH . 'routes_app.php';
}
