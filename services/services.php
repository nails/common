<?php

use MimeType\MimeType;
use MimeTyper\Repository\MimeDbRepository;
use Nails\Environment;

return [
    'properties' => [
        'DB_HOST'     => DEPLOY_DB_HOST,
        'DB_USERNAME' => DEPLOY_DB_USERNAME,
        'DB_PASSWORD' => DEPLOY_DB_PASSWORD,
        'DB_DATABASE' => function () {
            if (Environment::is([Environment::ENV_TEST, Environment::ENV_HTTP_TEST])) {
                return \Nails\Testing::DB_NAME;
            } else {
                return DEPLOY_DB_DATABASE;
            }
        },
    ],
    'services'   => [
        'AppSetting'     => function () {
            if (class_exists('\App\Common\Service\AppSetting')) {
                return new \App\Common\Service\AppSetting();
            } else {
                return new \Nails\Common\Service\AppSetting();
            }
        },
        'Asset'          => function () {
            if (class_exists('\App\Common\Service\Asset')) {
                return new \App\Common\Service\Asset();
            } else {
                return new \Nails\Common\Service\Asset();
            }
        },
        'CodeIgniter'    => function () {
            return get_instance();
        },
        'Config'         => function () {
            if (class_exists('\App\Common\Service\Config')) {
                return new \App\Common\Service\Config();
            } else {
                return new \Nails\Common\Service\Config();
            }
        },
        'Cookie'         => function () {
            if (class_exists('\App\Common\Service\Cookie')) {
                return new \App\Common\Service\Cookie();
            } else {
                return new \Nails\Common\Service\Cookie();
            }
        },
        'Database'       => function () {
            if (class_exists('\App\Common\Service\Database')) {
                return new \App\Common\Service\Database();
            } else {
                return new \Nails\Common\Service\Database();
            }
        },
        'DateTime'       => function () {
            if (class_exists('\App\Common\Service\DateTime')) {
                return new \App\Common\Service\DateTime();
            } else {
                return new \Nails\Common\Service\DateTime();
            }
        },
        'Encrypt'        => function () {
            if (class_exists('\App\Common\Service\Encrypt')) {
                return new \App\Common\Service\Encrypt();
            } else {
                require_once BASEPATH . 'libraries/Encrypt.php';
                return new \Nails\Common\Service\Encrypt();
            }
        },
        'ErrorHandler'   => function () {
            if (class_exists('\App\Common\Service\ErrorHandler')) {
                return new \App\Common\Service\ErrorHandler();
            } else {
                return new \Nails\Common\Service\ErrorHandler();
            }
        },
        'Event'          => function () {
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
        'HttpCodes'      => function () {
            if (class_exists('\App\Common\Service\HttpCodes')) {
                return new \App\Common\Service\HttpCodes();
            } else {
                return new \Nails\Common\Service\HttpCodes();
            }
        },
        'Input'          => function () {
            if (class_exists('\App\Common\Service\Input')) {
                return new \App\Common\Service\Input();
            } else {
                return new \Nails\Common\Service\Input();
            }
        },
        'Language'       => function () {
            if (class_exists('\App\Common\Service\Language')) {
                return new \App\Common\Service\Language();
            } else {
                return new \Nails\Common\Service\Language();
            }
        },
        'Locale'         => function (
            \Nails\Common\Factory\Locale $oLocale = null,
            \Nails\Common\Service\Input $oInput = null
        ) {
            $oInput = $oInput ?? \Nails\Factory::service('Input');

            if (class_exists('\App\Common\Service\Locale')) {
                return new \App\Common\Service\Locale(
                    $oInput,
                    $oLocale
                );
            } else {
                return new \Nails\Common\Service\Locale(
                    $oInput,
                    $oLocale
                );
            }
        },
        'Logger'         => function () {
            if (class_exists('\App\Common\Service\Logger')) {
                return new \App\Common\Service\Logger();
            } else {
                return new \Nails\Common\Service\Logger();
            }
        },
        'Meta'           => function () {
            if (class_exists('\App\Common\Service\Meta')) {
                return new \App\Common\Service\Meta();
            } else {
                return new \Nails\Common\Service\Meta();
            }
        },
        'Mime'           => function ($oDatabase = null, $oDetector = null) {

            if (!$oDatabase) {
                $oDatabase = new MimeDbRepository();
            }

            if (!$oDetector) {
                $oDetector = new MimeType();
            }

            if (class_exists('\App\Common\Service\Mime')) {
                return new \App\Common\Service\Mime($oDatabase, $oDetector);
            } else {
                return new \Nails\Common\Service\Mime($oDatabase, $oDetector);
            }
        },
        'Mustache'       => function () {
            if (class_exists('\App\Common\Service\Mustache')) {
                return new \App\Common\Service\Mustache();
            } else {
                return new Mustache_Engine();
            }
        },
        'Output'         => function () {
            if (class_exists('\App\Common\Service\Output')) {
                return new \App\Common\Service\Output();
            } else {
                return new \Nails\Common\Service\Output();
            }
        },
        'PDODatabase'    => function () {
            if (class_exists('\App\Common\Service\PDODatabase')) {
                return new \App\Common\Service\PDODatabase();
            } else {
                return new \Nails\Common\Service\PDODatabase();
            }
        },
        'Routes'         => function () {
            if (class_exists('\App\Common\Service\Routes')) {
                return new \App\Common\Service\Routes();
            } else {
                return new \Nails\Common\Service\Routes();
            }
        },
        'Router'         => function () {
            //  @todo - remove dependency on CI
            if (class_exists('CI_Router')) {
                $oCi = get_instance();
                return $oCi->router;
            } else {
                return new \Nails\Common\CodeIgniter\Core\Router\Dummy();
            }
        },
        'Security'       => function () {
            if (class_exists('\App\Common\Service\Security')) {
                return new \App\Common\Service\Security();
            } else {
                return new \Nails\Common\Service\Security();
            }
        },
        'Typography'     => function () {
            if (class_exists('\App\Common\Service\Typography')) {
                return new \App\Common\Service\Typography();
            } else {
                return new \Nails\Common\Service\Typography();
            }
        },
        'Uri'            => function () {
            if (class_exists('\App\Common\Service\Uri')) {
                return new \App\Common\Service\Uri();
            } else {
                return new \Nails\Common\Service\Uri();
            }
        },
        'UserFeedback'   => function () {
            if (class_exists('\App\Common\Service\UserFeedback')) {
                return new \App\Common\Service\UserFeedback();
            } else {
                return new \Nails\Common\Service\UserFeedback();
            }
        },
        'View'           => function () {
            if (class_exists('\App\Common\Service\View')) {
                return new \App\Common\Service\View();
            } else {
                return new \Nails\Common\Service\View();
            }
        },
        'Zip'            => function () {
            if (class_exists('\App\Common\Service\Zip')) {
                return new \App\Common\Service\Zip();
            } else {
                return new \Nails\Common\Service\Zip();
            }
        },
    ],
    'models'     => [
        'AppNotification' => function () {
            if (class_exists('\App\Common\Model\AppNotification')) {
                return new \App\Common\Model\AppNotification();
            } else {
                return new \Nails\Common\Model\AppNotification();
            }
        },
        'Country'         => function () {
            if (class_exists('\App\Common\Model\Country')) {
                return new \App\Common\Model\Country();
            } else {
                return new \Nails\Common\Model\Country();
            }
        },
    ],
    'factories'  => [
        'DateTime'          => function ($sTime = 'now', DateTimeZone $oTimeZone = null) {
            return new \DateTime($sTime, $oTimeZone);
        },
        'EventSubscription' => function () {
            if (class_exists('\App\Common\Events\Subscription')) {
                return new \App\Common\Events\Subscription();
            } else {
                return new \Nails\Common\Events\Subscription();
            }
        },
        'HttpClient'        => function (array $aConfig = []) {
            if (class_exists('\App\Common\HttpClient')) {
                return new \App\Common\HttpClient($aConfig);
            } else {
                return new \GuzzleHttp\Client($aConfig);
            }
        },
        'HttpRequestDelete' => function (array $aConfig = []) {
            if (class_exists('\App\Common\Factory\HttpRequest\Delete')) {
                return new \App\Common\Factory\HttpRequest\Delete($aConfig);
            } else {
                return new \Nails\Common\Factory\HttpRequest\Delete($aConfig);
            }
        },
        'HttpRequestGet'    => function ($sBaseUri = null, $sPath = null, array $aHeaders = []) {
            if (class_exists('\App\Common\Factory\HttpRequest\Get')) {
                return new \App\Common\Factory\HttpRequest\Get($sBaseUri, $sPath, $aHeaders);
            } else {
                return new \Nails\Common\Factory\HttpRequest\Get($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpRequestPatch'  => function ($sBaseUri = null, $sPath = null, array $aHeaders = []) {
            if (class_exists('\App\Common\Factory\HttpRequest\Patch')) {
                return new \App\Common\Factory\HttpRequest\Patch($sBaseUri, $sPath, $aHeaders);
            } else {
                return new \Nails\Common\Factory\HttpRequest\Patch($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpRequestPost'   => function ($sBaseUri = null, $sPath = null, array $aHeaders = []) {
            if (class_exists('\App\Common\Factory\HttpRequest\Post')) {
                return new \App\Common\Factory\HttpRequest\Post($sBaseUri, $sPath, $aHeaders);
            } else {
                return new \Nails\Common\Factory\HttpRequest\Post($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpRequestPut'    => function ($sBaseUri = null, $sPath = null, array $aHeaders = []) {
            if (class_exists('\App\Common\Factory\HttpRequest\Put')) {
                return new \App\Common\Factory\HttpRequest\Put($sBaseUri, $sPath, $aHeaders);
            } else {
                return new \Nails\Common\Factory\HttpRequest\Put($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpResponse'      => function (GuzzleHttp\Psr7\Response $oClient) {
            if (class_exists('\App\Common\Factory\HttpResponse')) {
                return new \App\Common\Factory\HttpResponse($oClient);
            } else {
                return new \Nails\Common\Factory\HttpResponse($oClient);
            }
        },
        'Locale'            => function (
            \Nails\Common\Factory\Locale\Language $oLanguage = null,
            \Nails\Common\Factory\Locale\Region $oRegion = null,
            \Nails\Common\Factory\Locale\Script $oScript = null
        ) {
            if (class_exists('\App\Common\Factory\Locale')) {
                return new \App\Common\Factory\Locale($oLanguage, $oRegion, $oScript);
            } else {
                return new \Nails\Common\Factory\Locale($oLanguage, $oRegion, $oScript);
            }
        },
        'LocaleLanguage'    => function (string $sLabel = '') {
            if (class_exists('\App\Common\Factory\Locale\Language')) {
                return new \App\Common\Factory\Locale\Language($sLabel);
            } else {
                return new \Nails\Common\Factory\Locale\Language($sLabel);
            }
        },
        'LocaleRegion'      => function (string $sLabel = '') {
            if (class_exists('\App\Common\Factory\Locale\Region')) {
                return new \App\Common\Factory\Locale\Region($sLabel);
            } else {
                return new \Nails\Common\Factory\Locale\Region($sLabel);
            }
        },
        'LocaleScript'      => function (string $sLabel = '') {
            if (class_exists('\App\Common\Factory\Locale\Script')) {
                return new \App\Common\Factory\Locale\Script($sLabel);
            } else {
                return new \Nails\Common\Factory\Locale\Script($sLabel);
            }
        },
        'Pagination'        => function () {
            if (class_exists('\App\Common\Factory\Pagination')) {
                return new \App\Common\Factory\Pagination();
            } else {
                return new \Nails\Common\Factory\Pagination();
            }
        },
    ],
    'resources'  => [
        'Cookie'          => function ($oObj) {
            if (class_exists('\App\Common\Resource\Cookie')) {
                return new \App\Common\Resource\Cookie($oObj);
            } else {
                return new \Nails\Common\Resource\Cookie($oObj);
            }
        },
        'ExpandableField' => function () {
            if (class_exists('\App\Common\Resource\ExpandableField')) {
                return new \App\Common\Resource\ExpandableField();
            } else {
                return new \Nails\Common\Resource\ExpandableField();
            }
        },
        'Resource'        => function ($oObj) {
            if (class_exists('\App\Common\Resource')) {
                return new \App\Common\Resource($oObj);
            } else {
                return new \Nails\Common\Resource($oObj);
            }
        },
    ],
];
