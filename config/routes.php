<?php

use Nails\Common\Service\Routes;
use Nails\Config;
use Nails\Factory;

/* Load the base config file from the CodeIgniter package */
require NAILS_CI_APP_PATH . 'config/autoload.php';

/**
 * System Routes
 */
$route['default_controller'] = 'home/index';

/**
 * App Routes
 */
if (!Config::get('NAILS_STARTUP_GENERATE_APP_ROUTES')) {

    /** @var Routes $oRouesService */
    $oRouesService = Factory::service('Routes');
    include_once $oRouesService->getRoutesFile();
}
