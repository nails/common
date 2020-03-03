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

$oView   = Factory::service('View');
$oUri    = Factory::service('Uri');
$oConfig = Factory::service('Config');
$oRouter = Factory::service('Router');

// --------------------------------------------------------------------------

if (isset($footerOverride)) {

    //  Manual override
    $oView->load($footerOverride);

} elseif (isset($footer_override)) {

    //  Manual override
    $oView->load($footer_override);

} else {

    //  Auto-detect footer if there is a config file
    if (file_exists(NAILS_APP_PATH . 'application/config/footer_views.php')) {

        $oConfig->load('footer_views');
        $match     = false;
        $uriString = $oUri->uri_string();

        if (!$uriString) {
            //  We're at the homepage, get the name of the default controller
            $uriString = $oRouter->routes['default_controller'];
        }

        if ($oConfig->item('alt_footer')) {

            foreach ($oConfig->item('alt_footer') as $pattern => $template) {

                //  Prep the regex
                $key = str_replace(':any', '.+', str_replace(':num', '\d+', $pattern));

                //  Match found?
                if (preg_match('#^' . preg_quote($key, '#') . '$#', $uriString)) {
                    $match = $template;
                    break;
                }
            }
        }

        //  Load the appropriate footer view
        if ($match) {
            $oView->load($match);
        } else {
            $oView->load($oConfig->item('default_footer'));
        }

    } elseif (file_exists(NAILS_APP_PATH . 'application/views/structure/footer/default.php')) {

        //  No config file, but the app has a default footer
        $oView->load('structure/footer/default');

    } else {

        //  No config file or app default, fall back to the default Nails. footer
        $oView->load('structure/footer/nails-default');
    }
}
