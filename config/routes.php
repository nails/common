<?php

/* Load the base config file from the CodeIgniter package */
require FCPATH . 'vendor/codeigniter/framework/application/config/autoload.php';

/**
 * System Routes
 */
$route['default_controller'] = 'home/index';
$route['404_override']       = 'system/render_404';

/**
 * App Routes
 */
if (!defined( 'NAILS_STARTUP_GENERATE_APP_ROUTES') || ! NAILS_STARTUP_GENERATE_APP_ROUTES) {
    include_once DEPLOY_CACHE_DIR . 'routes_app.php';
}
