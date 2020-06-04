<?php

/**
 * --------------------------------------------------------------------------
 * HEADER CONTROLLER
 * --------------------------------------------------------------------------
 *
 * This view controls which header should be rendered. It will use the URI to
 * determine the appropriate header file (against the header config file).
 *
 * Override this automatic behaviour by specifying the header_override
 * variable in the data supplied to the view.
 *
 **/

use Nails\Common\Service\View;
use Nails\Config;
use Nails\Factory;

// --------------------------------------------------------------------------

//  Catch 404
$bIs404 = (bool) Config::get('NAILS_IS_404');

// --------------------------------------------------------------------------

/** @var View $oView */
$oView = Factory::service('View');

if (isset($headerOverride) || isset($header_override)) {

    //  Manual override
    $oView->load($headerOverride ?? $header_override);

} elseif (file_exists(NAILS_APP_PATH . 'application/views/structure/header/default.php')) {

    //  No config file, but the app has a default header
    $oView->load(NAILS_APP_PATH . 'application/views/structure/header/default.php');

} else {

    //  No manual override or app default, fall back to the default Nails header
    $oView->load('structure/header/nails-default');
}
