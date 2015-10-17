<?php

return array(
    'services' => array(
        'Asset' => function() {
            if (class_exists('\App\Common\Library\Asset')) {
                return new \App\Common\Library\Asset();
            } else {
                return new \Nails\Common\Library\Asset();
            }
        },
        'Meta' => function() {
            if (class_exists('\App\Common\Library\Meta')) {
                return new \App\Common\Library\Meta();
            } else {
                return new \Nails\Common\Library\Meta();
            }
        },
        'UserFeedback' => function() {
            if (class_exists('\App\Common\Library\UserFeedback')) {
                return new \App\Common\Library\UserFeedback();
            } else {
                return new \Nails\Common\Library\UserFeedback();
            }
        },
        'ErrorHandler' => function() {
            if (class_exists('\App\Common\Library\ErrorHandler')) {
                return new \App\Common\Library\ErrorHandler();
            } else {
                return new \Nails\Common\Library\ErrorHandler();
            }
        },
        'Logger' => function() {
            if (class_exists('\App\Common\Library\Logger')) {
                return new \App\Common\Library\Logger();
            } else {
                return new \Nails\Common\Library\Logger();
            }
        },
        'Mustache' => function() {
            if (class_exists('\App\Common\Library\Asset')) {
                return new \App\Common\Library\Mustache();
            } else {
                return new Mustache_Engine();
            }
        },
        'Session' => function() {

            $oCi = get_instance();

            /**
             * STOP! Before we load the session library, we need to check if we're using
             * the database. If we are then check if `sess_table_name` is "nails_session".
             * If it is, and NAILS_DB_PREFIX != nails_ then replace 'nails_' with NAILS_DB_PREFIX
             */

            $sSessionTable = $oCi->config->item('sess_table_name');

            if ($sSessionTable === 'nails_session' && NAILS_DB_PREFIX !== 'nails_') {

                $sSessionTable = str_replace('nails_', NAILS_DB_PREFIX, $sSessionTable);
                $oCi->config->set_item('sess_table_name', $sSessionTable);
            }

            /**
             * Test that $_SERVER is available, the session library needs this
             * Generally not available when running on the command line. If it's
             * not available then load up the faux session which has the same methods
             * as the session library, but behaves as if logged out - comprende?
             */

            if ($oCi->input->server('REMOTE_ADDR')) {

                $oCi->load->library('session');

            } else {

                $oCi->load->library('auth/faux_session', 'session');
            }

            return $oCi->session;
        },
        'Encrypt' => function() {

            $oCi = get_instance();
            $oCi->load->library('encrypt');

            return $oCi->encrypt;
        },
        'Database' => function() {
            return new \Nails\Common\Library\Database();
        },
    ),
    'models' => array(
        'AppNotification' => function() {
            if (class_exists('\App\Common\Model\AppNotification')) {
                return new \App\Common\Model\AppNotification();
            } else {
                return new \Nails\Common\Model\AppNotification();
            }
        },
        'AppSetting' => function() {
            if (class_exists('\App\Common\Model\AppSetting')) {
                return new \App\Common\Model\AppSetting();
            } else {
                return new \Nails\Common\Model\AppSetting();
            }
        },
        'Country' => function() {
            if (class_exists('\App\Common\Model\Country')) {
                return new \App\Common\Model\Country();
            } else {
                return new \Nails\Common\Model\Country();
            }
        },
        'DateTime' => function() {
            if (class_exists('\App\Common\Model\DateTime')) {
                return new \App\Common\Model\DateTime();
            } else {
                return new \Nails\Common\Model\DateTime();
            }
        },
        'Language' => function() {
            if (class_exists('\App\Common\Model\Language')) {
                return new \App\Common\Model\Language();
            } else {
                return new \Nails\Common\Model\Language();
            }
        },
        'Routes' => function() {
            if (class_exists('\App\Common\Model\Routes')) {
                return new \App\Common\Model\Routes();
            } else {
                return new \Nails\Common\Model\Routes();
            }
        }
    ),
    'factories' => array(
        'DateTime' => function() {
            return new \DateTime();
        },
        'HttpClient' => function() {
            if (class_exists('\App\Common\HttpClient')) {
                return new \App\Common\HttpClient();
            } else {
                return new \GuzzleHttp\Client();
            }
        }
    )
);
