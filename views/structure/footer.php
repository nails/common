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

use Nails\Factory;

// --------------------------------------------------------------------------

//  Catch 404
$bIs404 = defined('NAILS_IS_404') && NAILS_IS_404 ? true : false;

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
    if (file_exists(FCPATH . APPPATH . 'config/footer_views.php')) {

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
                $key = str_replace(':any', '.*', str_replace(':num', '[0-9]*', $pattern));

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

        } elseif ($oUri->segment(1) == 'admin') {

            //  No match, but in admin, load the appropriate admin view
            if ($bIs404) {

                //  404 with no route, show the default footer
                $oView->load($oConfig->item('default_footer'));

            } else {

                //  Admin has no route and it's not a 404, load up the Nails admin footer
                $oView->load('structure/footer/nails-admin');
            }

        } else {

            $oView->load($oConfig->item('default_footer'));
        }

    } elseif ($oUri->segment(1) == 'admin' && !$bIs404) {

        /**
         * Loading admin footer and no config file. This isn't a 404 so go ahead and
         * load the normal Nails admin footer
         */

        $oView->load('structure/footer/nails-admin');

    } elseif (file_exists(FCPATH . APPPATH . 'views/structure/footer/default.php')) {

        //  No config file, but the app has a default footer
        $oView->load('structure/footer/default');

    } else {

        //  No config file or app default, fall back to the default Nails. footer
        $oView->load('structure/footer/nails-default');
    }
}
