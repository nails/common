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

use Nails\Factory;

// --------------------------------------------------------------------------

//  Catch 404
$bIs404 = defined('NAILS_IS_404') && NAILS_IS_404 ? true : false;

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
    if (file_exists(FCPATH . APPPATH . 'config/header_views.php')) {

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
                $key = str_replace(':any', '.*', str_replace(':num', '[0-9]*', $pattern));

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

        } elseif ($oUri->segment(1) == 'admin') {

            //  No match, but in admin, load the appropriate admin view
            if ($bIs404) {

                //  404 with no route, show the default header
                $oView->load($oConfig->item('default_header'));

            } else {

                //  Admin has no route and it's not a 404, load up the Nails admin header
                $oView->load('structure/header/nails-admin');
            }

        } else {

            $oView->load($oConfig->item('default_header'));
        }

    } elseif ($oUri->segment(1) == 'admin' && !$bIs404) {

        /**
         * Loading admin header and no config file. This isn't a 404 so go ahead and
         * load the normal Nails admin header
         */

        $oView->load('structure/header/nails-admin');

    } elseif (file_exists(FCPATH . APPPATH . 'views/structure/header/default.php')) {

        //  No config file, but the app has a default header
        $oView->load('structure/header/default');

    } else {

        //  No config file or app default, fall back to the default Nails. header
        $oView->load('structure/header/nails-default');
    }
}
