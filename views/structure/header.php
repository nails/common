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

if (isset($headerOverride)) {

    //  Manual override
    $oView->load($headerOverride);

} elseif (isset($header_override)) {

    //  Manual override
    $oView->load($header_override);

} else {

    //  Auto-detect header if there is a config file
    if (file_exists(NAILS_APP_PATH . 'application/config/header_views.php')) {

        $oConfig->load('header_views');
        $match     = false;
        $uriString = $oUri->uri_string();

        if (!$uriString) {
            //  We're at the homepage, get the name of the default controller
            $uriString = $oRouter->routes['default_controller'];
        }

        if ($oConfig->item('alt_header')) {

            foreach ($oConfig->item('alt_header') as $pattern => $template) {

                //  Prep the regex
                $key = str_replace(':any', '.+', str_replace(':num', '\d+', $pattern));

                //  Match found?
                if (preg_match('#^' . preg_quote($key, '#') . '$#', $uriString)) {
                    $match = $template;
                    break;
                }
            }
        }

        //  Load the appropriate header view
        if ($match) {
            $oView->load($match);
        } else {
            $oView->load($oConfig->item('default_header'));
        }

    } elseif (file_exists(NAILS_APP_PATH . 'application/views/structure/header/default.php')) {

        //  No config file, but the app has a default header
        $oView->load('structure/header/default');

    } else {

        //  No config file or app default, fall back to the default Nails. header
        $oView->load('structure/header/nails-default');
    }
}
