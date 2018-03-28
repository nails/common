<?php

return [
    'properties'  => [
        'DB_HOST'     => DEPLOY_DB_HOST,
        'DB_USERNAME' => DEPLOY_DB_USERNAME,
        'DB_PASSWORD' => DEPLOY_DB_PASSWORD,
        'DB_DATABASE' => DEPLOY_DB_DATABASE,
    ],
    'services'  => [
        'Asset' => function () {
            if (class_exists('\App\Common\Service\Asset')) {
                return new \App\Common\Service\Asset();
            } else {
                return new \Nails\Common\Service\Asset();
            }
        },
        'CodeIgniter' => function () {
            return get_instance();
        },
        'Config' => function () {
            if (class_exists('\App\Common\Service\Config')) {
                return new \App\Common\Service\Config();
            } else {
                return new \Nails\Common\Service\Config();
            }
        },
        'Database' => function () {
            if (class_exists('\App\Common\Service\Database')) {
                return new \App\Common\Service\Database();
            } else {
                return new \Nails\Common\Service\Database();
            }
        },
        'Encrypt' => function () {
            if (class_exists('\App\Common\Service\Encrypt')) {
                return new \App\Common\Service\Encrypt();
            } else {
                require_once BASEPATH . 'libraries/Encrypt.php';
                return new \Nails\Common\Service\Encrypt();
            }
        },
        'ErrorHandler' => function () {
            if (class_exists('\App\Common\Service\ErrorHandler')) {
                return new \App\Common\Service\ErrorHandler();
            } else {
                return new \Nails\Common\Service\ErrorHandler();
            }
        },
        'Event' => function () {
            if (class_exists('\App\Common\Service\Event')) {
                return new \App\Common\Service\Event();
            } else {
                return new \Nails\Common\Service\Event();
            }
        },
        'FormValidation' => function () {
            if (class_exists('\App\Common\Service\FormValidation')) {
                return new \App\Common\Service\FormValidation();
            } else {
                return new \Nails\Common\Service\FormValidation();
            }
        },
        'HttpCodes' => function () {
            if (class_exists('\App\Common\Service\HttpCodes')) {
                return new \App\Common\Service\HttpCodes();
            } else {
                return new \Nails\Common\Service\HttpCodes();
            }
        },
        'Input' => function () {
            if (class_exists('\App\Common\Service\Input')) {
                return new \App\Common\Service\Input();
            } else {
                return new \Nails\Common\Service\Input();
            }
        },
        'Logger' => function () {
            if (class_exists('\App\Common\Service\Logger')) {
                return new \App\Common\Service\Logger();
            } else {
                return new \Nails\Common\Service\Logger();
            }
        },
        'Meta' => function () {
            if (class_exists('\App\Common\Service\Meta')) {
                return new \App\Common\Service\Meta();
            } else {
                return new \Nails\Common\Service\Meta();
            }
        },
        'Mustache' => function () {
            if (class_exists('\App\Common\Service\Mustache')) {
                return new \App\Common\Service\Mustache();
            } else {
                return new Mustache_Engine();
            }
        },
        'Output' => function () {
            if (class_exists('\App\Common\Service\Output')) {
                return new \App\Common\Service\Output();
            } else {
                return new \Nails\Common\Service\Output();
            }
        },
        'PDODatabase' => function () {
            if (class_exists('\App\Common\Service\PDODatabase')) {
                return new \App\Common\Service\PDODatabase();
            } else {
                return new \Nails\Common\Service\PDODatabase();
            }
        },
        'Router' => function () {
            //  @todo - remove dependency on CI
            $oCi = get_instance();
            return $oCi->router;
        },
        'Security' => function () {
            if (class_exists('\App\Common\Service\Security')) {
                return new \App\Common\Service\Security();
            } else {
                return new \Nails\Common\Service\Security();
            }
        },
        'Uri' => function () {
            if (class_exists('\App\Common\Service\Uri')) {
                return new \App\Common\Service\Uri();
            } else {
                return new \Nails\Common\Service\Uri();
            }
        },
        'UserFeedback' => function () {
            if (class_exists('\App\Common\Service\UserFeedback')) {
                return new \App\Common\Service\UserFeedback();
            } else {
                return new \Nails\Common\Service\UserFeedback();
            }
        },
        'View' => function () {
            if (class_exists('\App\Common\Service\View')) {
                return new \App\Common\Service\View();
            } else {
                return new \Nails\Common\Service\View();
            }
        },
        'Zip' => function () {
            if (class_exists('\App\Common\Service\Zip')) {
                return new \App\Common\Service\Zip();
            } else {
                return new \Nails\Common\Service\Zip();
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
