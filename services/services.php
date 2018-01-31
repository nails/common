<?php

return [
    'services'  => [
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
            if (class_exists('\App\Common\Library\Config')) {
                return new \App\Common\Library\Config();
            } else {
                return new \Nails\Common\Library\Config();
            }
        },
        'Database' => function () {
            if (class_exists('\App\Common\Library\Database')) {
                return new \App\Common\Library\Database();
            } else {
                return new \Nails\Common\Library\Database();
            }
        },
        'Encrypt' => function () {
            if (class_exists('\App\Common\Library\Encrypt')) {
                return new \App\Common\Library\Encrypt();
            } else {
                require_once BASEPATH . 'libraries/Encrypt.php';
                return new \Nails\Common\Library\Encrypt();
            }
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
            if (class_exists('\App\Common\Library\FormValidation')) {
                return new \App\Common\Library\FormValidation();
            } else {
                return new \Nails\Common\Library\FormValidation();
            }
        },
        'Input' => function () {
            if (class_exists('\App\Common\Library\Input')) {
                return new \App\Common\Library\Input();
            } else {
                return new \Nails\Common\Library\Input();
            }
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
            if (class_exists('\App\Common\Library\Output')) {
                return new \App\Common\Library\Output();
            } else {
                return new \Nails\Common\Library\Output();
            }
        },
        'Router' => function () {
            //  @todo - remove dependency on CI
            $oCi = get_instance();
            return $oCi->router;
        },
        'Security' => function () {
            if (class_exists('\App\Common\Library\Security')) {
                return new \App\Common\Library\Security();
            } else {
                return new \Nails\Common\Library\Security();
            }
        },
        'Uri' => function () {
            if (class_exists('\App\Common\Library\Uri')) {
                return new \App\Common\Library\Uri();
            } else {
                return new \Nails\Common\Library\Uri();
            }
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
        },
        'Zip' => function () {
            if (class_exists('\App\Common\Library\Zip')) {
                return new \App\Common\Library\Zip();
            } else {
                return new \Nails\Common\Library\Zip();
            }
        },
    ],
    'models'    => [
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
        'Routes' => function () {
            if (class_exists('\App\Common\Model\Routes')) {
                return new \App\Common\Model\Routes();
            } else {
                return new \Nails\Common\Model\Routes();
            }
        },
    ],
    'factories' => [
        'DateTime' => function () {
            return new \DateTime();
        },
        'EventSubscription' => function () {
            if (class_exists('\App\Common\Events\Subscription')) {
                return new \App\Common\Events\Subscription();
            } else {
                return new \Nails\Common\Events\Subscription();
            }
        },
        'HttpClient' => function () {
            if (class_exists('\App\Common\HttpClient')) {
                return new \App\Common\HttpClient();
            } else {
                return new \GuzzleHttp\Client();
            }
        },
    ],
];
