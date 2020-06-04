<?php

/**
 * --------------------------------------------------------------------------
 * FOOTER CONTROLLER
 * --------------------------------------------------------------------------
 *
 * This view controls which footer should be rendered. It will use the URI to
 * determine the appropriate footer file (against the footer config file).
 *
 * Override this automatic behaviour by specifying the footer_override
 * variable in the data supplied to the view.
 *
 **/

use Nails\Config;
use Nails\Factory;

// --------------------------------------------------------------------------

//  Catch 404
$bIs404 = (bool) Config::get('NAILS_IS_404');

// --------------------------------------------------------------------------

/** @var View $oView */
$oView = Factory::service('View');

if (isset($footerOverride) || isset($footer_override)) {

    //  Manual override
    $oView->load($footerOverride ?? $footer_override);

} elseif (file_exists(NAILS_APP_PATH . 'application/views/structure/footer/default.php')) {

    //  No config file, but the app has a default footer
    $oView->load(NAILS_APP_PATH . 'application/views/structure/footer/default.php');

} else {

    //  No manual override or app default, fall back to the default Nails footer
    $oView->load('structure/footer/nails-default');
}
