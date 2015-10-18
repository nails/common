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
