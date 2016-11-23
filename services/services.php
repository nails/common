<?php

return array(
    'services' => array(
        'Asset' => function () {
            if (class_exists('\App\Common\Library\Asset')) {
                return new \App\Common\Library\Asset();
            } else {
                return new \Nails\Common\Library\Asset();
            }
        },
        'CodeIgniter' => function () {
            return get_instance();
        },
        'Config' => function () {
            $oCi = get_instance();
            return $oCi->config;
        },
        'Database' => function () {
            return new \Nails\Common\Library\Database();
        },
        'Encrypt' => function () {
            $oCi = get_instance();
            $oCi->load->library('encrypt');
            return $oCi->encrypt;
        },
        'ErrorHandler' => function () {
            if (class_exists('\App\Common\Library\ErrorHandler')) {
                return new \App\Common\Library\ErrorHandler();
            } else {
                return new \Nails\Common\Library\ErrorHandler();
            }
        },
        'Event' => function () {
            if (class_exists('\App\Common\Library\Event')) {
                return new \App\Common\Library\Event();
            } else {
                return new \Nails\Common\Library\Event();
            }
        },
        'HttpCodes' => function () {
            if (class_exists('\App\Common\Library\HttpCodes')) {
                return new \App\Common\Library\HttpCodes();
            } else {
                return new \Nails\Common\Library\HttpCodes();
            }
        },
        'FormValidation' => function () {
            $oCi = get_instance();
            $oCi->load->library('form_validation');
            return $oCi->form_validation;
        },
        'Input' => function () {
            $oCi = get_instance();
            return $oCi->input;
        },
        'Logger' => function () {
            if (class_exists('\App\Common\Library\Logger')) {
                return new \App\Common\Library\Logger();
            } else {
                return new \Nails\Common\Library\Logger();
            }
        },
        'Meta' => function () {
            if (class_exists('\App\Common\Library\Meta')) {
                return new \App\Common\Library\Meta();
            } else {
                return new \Nails\Common\Library\Meta();
            }
        },
        'Mustache' => function () {
            if (class_exists('\App\Common\Library\Mustache')) {
                return new \App\Common\Library\Mustache();
            } else {
                return new Mustache_Engine();
            }
        },
        'Output' => function () {
            $oCi = get_instance();
            return $oCi->output;
        },
        'Uri' => function () {
            $oCi = get_instance();
            return $oCi->uri;
        },
        'UserFeedback' => function () {
            if (class_exists('\App\Common\Library\UserFeedback')) {
                return new \App\Common\Library\UserFeedback();
            } else {
                return new \Nails\Common\Library\UserFeedback();
            }
        },
        'View' => function () {
            if (class_exists('\App\Common\Library\View')) {
                return new \App\Common\Library\View();
            } else {
                return new \Nails\Common\Library\View();
            }
        }
    ),
    'models' => array(
        'AppNotification' => function () {
            if (class_exists('\App\Common\Model\AppNotification')) {
                return new \App\Common\Model\AppNotification();
            } else {
                return new \Nails\Common\Model\AppNotification();
            }
        },
        'AppSetting' => function () {
            if (class_exists('\App\Common\Model\AppSetting')) {
                return new \App\Common\Model\AppSetting();
            } else {
                return new \Nails\Common\Model\AppSetting();
            }
        },
        'Country' => function () {
            if (class_exists('\App\Common\Model\Country')) {
                return new \App\Common\Model\Country();
            } else {
                return new \Nails\Common\Model\Country();
            }
        },
        'DateTime' => function () {
            if (class_exists('\App\Common\Model\DateTime')) {
                return new \App\Common\Model\DateTime();
            } else {
                return new \Nails\Common\Model\DateTime();
            }
        },
        'Language' => function () {
            if (class_exists('\App\Common\Model\Language')) {
                return new \App\Common\Model\Language();
            } else {
                return new \Nails\Common\Model\Language();
            }
        },
        'Router' => function () {
            $oCi = get_instance();
            return $oCi->router;
        },
        'Routes' => function () {
            if (class_exists('\App\Common\Model\Routes')) {
                return new \App\Common\Model\Routes();
            } else {
                return new \Nails\Common\Model\Routes();
            }
        }
    ),
    'factories' => array(
        'DateTime' => function () {
            return new \DateTime();
        },
        'HttpClient' => function () {
            if (class_exists('\App\Common\HttpClient')) {
                return new \App\Common\HttpClient();
            } else {
                return new \GuzzleHttp\Client();
            }
        }
    )
);
