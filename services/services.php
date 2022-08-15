<?php

use MimeTyper\Repository\MimeDbRepository;
use Nails\Common\Interfaces;
use Nails\Common\Resource;
use Nails\Common\Service;
use Nails\Common\Factory;
use Nails\Config;
use Symfony\Component\Mime\MimeTypes;

return [
    'services' => [
        'AppSetting'                     => function (): Service\AppSetting {
            if (class_exists('\App\Common\Service\AppSetting')) {
                return new \App\Common\Service\AppSetting();
            } else {
                return new Service\AppSetting();
            }
        },
        'Asset'                          => function (): Service\Asset {

            //  @todo (Pablo - 2020-03-08) - Remove DEPLOY_REVISION
            $sCacheBuster         = Config::get('ASSET_REVISION', Config::get('DEPLOY_REVISION'));
            $sBaseUrl             = Config::get('ASSET_URL', '/assets');
            $sBaseUrlSecure       = Config::get('ASSET_URL_SECURE', $sBaseUrl);
            $sBaseModuleUrl       = Config::get('ASSET_MODULE_URL', '/vendor');
            $sBaseModuleUrlSecure = Config::get('ASSET_MODULE_URL_SECURE', $sBaseModuleUrl);
            $sCssDir              = Config::get('ASSET_CSS_DIR', 'build/css');
            $sJsDir               = Config::get('ASSET_JS_DIR', 'build/js');

            if (class_exists('\App\Common\Service\Asset')) {
                return new \App\Common\Service\Asset(
                    $sCacheBuster,
                    $sBaseUrl,
                    $sBaseUrlSecure,
                    $sBaseModuleUrl,
                    $sBaseModuleUrlSecure,
                    $sCssDir,
                    $sJsDir
                );
            } else {
                return new Service\Asset(
                    $sCacheBuster,
                    $sBaseUrl,
                    $sBaseUrlSecure,
                    $sBaseModuleUrl,
                    $sBaseModuleUrlSecure,
                    $sCssDir,
                    $sJsDir
                );
            }
        },
        'CodeIgniter'                    => function (): \CI_Controller {
            return get_instance();
        },
        'Config'                         => function (): Service\Config {
            if (class_exists('\App\Common\Service\Config')) {
                return new \App\Common\Service\Config();
            } else {
                return new Service\Config();
            }
        },
        'Cookie'                         => function (): Service\Cookie {
            if (class_exists('\App\Common\Service\Cookie')) {
                return new \App\Common\Service\Cookie();
            } else {
                return new Service\Cookie();
            }
        },
        'Country'                        => function (): Service\Country {
            if (class_exists('\App\Common\Service\Country')) {
                return new \App\Common\Service\Country();
            } else {
                return new Service\Country();
            }
        },
        'Database'                       => function (): Service\Database {
            if (class_exists('\App\Common\Service\Database')) {
                return new \App\Common\Service\Database();
            } else {
                return new Service\Database();
            }
        },
        'DateTime'                       => function (): Service\DateTime {
            if (class_exists('\App\Common\Service\DateTime')) {
                return new \App\Common\Service\DateTime();
            } else {
                return new Service\DateTime();
            }
        },
        'Encrypt'                        => function (): Service\Encrypt {
            if (class_exists('\App\Common\Service\Encrypt')) {
                return new \App\Common\Service\Encrypt();
            } else {
                require_once BASEPATH . 'libraries/Encrypt.php';
                return new Service\Encrypt();
            }
        },
        'ErrorHandler'                   => function (): Service\ErrorHandler {
            if (class_exists('\App\Common\Service\ErrorHandler')) {
                return new \App\Common\Service\ErrorHandler();
            } else {
                return new Service\ErrorHandler();
            }
        },
        'Event'                          => function (): Service\Event {
            if (class_exists('\App\Common\Service\Event')) {
                return new \App\Common\Service\Event();
            } else {
                return new Service\Event();
            }
        },
        'FileCache'                      => function (
            Interfaces\Service\FileCache\Driver $oPrivate = null,
            Interfaces\Service\FileCache\Driver\AccessibleByUrl $oPublic = null
        ): Service\FileCache {
            if (class_exists('\App\Common\Service\FileCache')) {
                /** @var Service\FileCache $oFileCache */
                $oFileCache = new \App\Common\Service\FileCache(
                    $oPrivate ?? \Nails\Factory::service('FileCacheDriver'),
                    $oPublic ?? \Nails\Factory::service('FileCacheDriverAccessibleByUrl')
                );
            } else {
                /** @var Service\FileCache $oFileCache */
                $oFileCache = new Service\FileCache(
                    $oPrivate ?? \Nails\Factory::service('FileCacheDriver'),
                    $oPublic ?? \Nails\Factory::service('FileCacheDriverAccessibleByUrl')
                );
            }

            return $oFileCache;
        },
        'FileCacheDriver'                => function (
            string $sDir = null
        ): Service\FileCache\Driver {

            $sDir = $sDir
                ?? Config::get(
                    'CACHE_PRIVATE_DIR',
                    Config::get('NAILS_APP_PATH') . implode(DIRECTORY_SEPARATOR, ['cache', 'private', ''])
                );

            if (class_exists('\App\Common\Service\FileCache\Driver')) {
                /** @var Service\FileCache\Driver $oDriver */
                $oDriver = new \App\Common\Service\FileCache\Driver($sDir);
            } else {
                /** @var Service\FileCache\Driver $oDriver */
                $oDriver = new Service\FileCache\Driver($sDir);
            }

            return $oDriver;
        },
        'FileCacheDriverAccessibleByUrl' => function (
            string $sDir = null,
            string $sUrl = null
        ): Service\FileCache\Driver\AccessibleByUrl {

            $sDir = $sDir
                ?? Config::get(
                    'CACHE_PUBLIC_DIR',
                    Config::get('NAILS_APP_PATH') . implode(DIRECTORY_SEPARATOR, ['cache', 'public', ''])
                );
            $sUrl = $sUrl
                ?? Config::get(
                    'CACHE_PUBLIC_URL',
                    Config::get('BASE_URL') . 'cache/public'
                );

            if (class_exists('\App\Common\Service\FileCache\Driver\AccessibleByUrl')) {
                /** @var Service\FileCache\Driver\AccessibleByUrl $oDriver */
                $oDriver = new \App\Common\Service\FileCache\Driver\AccessibleByUrl($sDir, $sUrl);
            } else {
                /** @var Service\FileCache\Driver\AccessibleByUrl $oDriver */
                $oDriver = new Service\FileCache\Driver\AccessibleByUrl($sDir, $sUrl);
            }

            return $oDriver;
        },
        'Form'                           => function (): Service\Form {
            if (class_exists('\App\Common\Service\Form')) {
                return new \App\Common\Service\Form();
            } else {
                return new Service\Form();
            }
        },
        'FormValidation'                 => function (): Service\FormValidation {
            if (class_exists('\App\Common\Service\FormValidation')) {
                return new \App\Common\Service\FormValidation();
            } else {
                return new Service\FormValidation();
            }
        },
        'HttpCodes'                      => function (): Service\HttpCodes {
            if (class_exists('\App\Common\Service\HttpCodes')) {
                return new \App\Common\Service\HttpCodes();
            } else {
                return new Service\HttpCodes();
            }
        },
        'Input'                          => function (): Service\Input {
            if (class_exists('\App\Common\Service\Input')) {
                return new \App\Common\Service\Input();
            } else {
                return new Service\Input();
            }
        },
        'Language'                       => function (): Service\Language {
            if (class_exists('\App\Common\Service\Language')) {
                return new \App\Common\Service\Language();
            } else {
                return new Service\Language();
            }
        },
        'Locale'                         => function (
            \Nails\Common\Factory\Locale $oLocale = null,
            Service\Input $oInput = null
        ): Service\Locale {

            $oInput = $oInput ?? \Nails\Factory::service('Input');

            if (class_exists('\App\Common\Service\Locale')) {
                return new \App\Common\Service\Locale(
                    $oInput,
                    $oLocale
                );
            } else {
                return new Service\Locale(
                    $oInput,
                    $oLocale
                );
            }
        },
        'Logger'                         => function (): Service\Logger {
            if (class_exists('\App\Common\Service\Logger')) {
                return new \App\Common\Service\Logger();
            } else {
                return new Service\Logger();
            }
        },
        'Meta'                           => function (): Service\Meta {
            if (class_exists('\App\Common\Service\Meta')) {
                return new \App\Common\Service\Meta();
            } else {
                return new Service\Meta();
            }
        },
        'MetaData'                       => function ($oObj = []): Service\MetaData {
            if (class_exists('\App\Common\Service\MetaData')) {
                return new \App\Common\Service\MetaData($oObj);
            } else {
                return new Service\MetaData($oObj);
            }
        },
        'Mime'                           => function ($oDatabase = null, $oDetector = null): Service\Mime {

            if (!$oDatabase) {
                $oDatabase = new MimeDbRepository();
            }

            if (!$oDetector) {
                $oDetector = new MimeTypes();
            }

            if (class_exists('\App\Common\Service\Mime')) {
                return new \App\Common\Service\Mime($oDatabase, $oDetector);
            } else {
                return new Service\Mime($oDatabase, $oDetector);
            }
        },
        'Mustache'                       => function (): Mustache_Engine {
            if (class_exists('\App\Common\Service\Mustache')) {
                return new \App\Common\Service\Mustache();
            } else {
                return new Mustache_Engine();
            }
        },
        'Output'                         => function (): Service\Output {
            if (class_exists('\App\Common\Service\Output')) {
                return new \App\Common\Service\Output();
            } else {
                return new Service\Output();
            }
        },
        'PDODatabase'                    => function (): Service\PDODatabase {
            if (class_exists('\App\Common\Service\PDODatabase')) {
                return new \App\Common\Service\PDODatabase();
            } else {
                return new Service\PDODatabase();
            }
        },
        'Profiler'                       => function (): Service\Profiler {
            if (class_exists('\App\Common\Service\Profiler')) {
                return new \App\Common\Service\Profiler();
            } else {
                return new Service\Profiler();
            }
        },
        'Routes'                         => function (): Service\Routes {
            if (class_exists('\App\Common\Service\Routes')) {
                return new \App\Common\Service\Routes();
            } else {
                return new Service\Routes();
            }
        },
        'Router'                         => function () {
            //  @todo - remove dependency on CI
            if (class_exists('CI_Router')) {
                $oCi = get_instance();
                return $oCi->router;
            } else {
                return new \Nails\Common\CodeIgniter\Core\Router\Dummy();
            }
        },
        'Security'                       => function (): Service\Security {
            if (class_exists('\App\Common\Service\Security')) {
                return new \App\Common\Service\Security();
            } else {
                return new Service\Security();
            }
        },
        'Session'                        => function (): Service\Session {
            if (class_exists('\App\Common\Service\Session')) {
                return new \App\Common\Service\Session();
            } else {
                return new Service\Session();
            }
        },
        'Typography'                     => function (): Service\Typography {
            if (class_exists('\App\Common\Service\Typography')) {
                return new \App\Common\Service\Typography();
            } else {
                return new Service\Typography();
            }
        },
        'Uri'                            => function (): Service\Uri {
            if (class_exists('\App\Common\Service\Uri')) {
                return new \App\Common\Service\Uri();
            } else {
                return new Service\Uri();
            }
        },
        'UserFeedback'                   => function (): Service\UserFeedback {
            if (class_exists('\App\Common\Service\UserFeedback')) {
                return new \App\Common\Service\UserFeedback();
            } else {
                return new Service\UserFeedback();
            }
        },
        'View'                           => function (): Service\View {
            if (class_exists('\App\Common\Service\View')) {
                return new \App\Common\Service\View();
            } else {
                return new Service\View();
            }
        },
        'Zip'                            => function (): Service\Zip {
            if (class_exists('\App\Common\Service\Zip')) {
                return new \App\Common\Service\Zip();
            } else {
                return new Service\Zip();
            }
        },
    ],

    'factories' => [
        'AssetCriticalCss'           => function (Service\Asset $oAsset): Factory\Asset\CriticalCss {
            if (class_exists('\App\Common\Factory\Asset\CriticalCss')) {
                return new \App\Common\Factory\Asset\CriticalCss($oAsset);
            } else {
                return new Factory\Asset\CriticalCss($oAsset);
            }
        },
        'ComponentSetting'           => function (): \Nails\Components\Setting {
            return new \Nails\Components\Setting();
        },
        'DatabaseForeignKeyCheck'    => function (Service\Database $oDatabase): Factory\Database\ForeignKeyCheck {
            if (class_exists('\App\Common\Factory\Database\ForeignKeyCheck')) {
                return new \App\Common\Factory\Database\ForeignKeyCheck($oDatabase);
            } else {
                return new Factory\Database\ForeignKeyCheck($oDatabase);
            }
        },
        'DatabaseTransaction'        => function (Service\Database $oDatabase): Factory\Database\Transaction {
            if (class_exists('\App\Common\Factory\Database\Transaction')) {
                return new \App\Common\Factory\Database\Transaction($oDatabase);
            } else {
                return new Factory\Database\Transaction($oDatabase);
            }
        },
        'DateTime'                   => function ($sTime = null, DateTimeZone $oTimeZone = null): \DateTime {
            return new \DateTime(
                $sTime ?? Config::get('NAILS_TIME_NOW', 'now'),
                $oTimeZone
            );
        },
        'FormValidationValidator'    => function (
            array $aRules = [],
            array $aMessages = [],
            array $aData = []
        ): Factory\Service\FormValidation\Validator {
            if (class_exists('\App\Common\Factory\Service\FormValidation\Validator')) {
                return new \App\Common\Factory\Service\FormValidation\Validator($aRules, $aMessages, $aData);
            } else {
                return new Factory\Service\FormValidation\Validator($aRules, $aMessages, $aData);
            }
        },
        'HttpClient'                 => function (array $aConfig = []): \GuzzleHttp\Client {
            if (class_exists('\App\Common\HttpClient')) {
                return new \App\Common\HttpClient($aConfig);
            } else {
                return new \GuzzleHttp\Client($aConfig);
            }
        },
        'HttpRequestDelete'          => function (
            string $sBaseUri = '',
            string $sPath = '',
            array $aHeaders = []
        ): Factory\HttpRequest\Delete {
            if (class_exists('\App\Common\Factory\HttpRequest\Delete')) {
                return new \App\Common\Factory\HttpRequest\Delete($sBaseUri, $sPath, $aHeaders);
            } else {
                return new Factory\HttpRequest\Delete($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpRequestGet'             => function (
            string $sBaseUri = '',
            string $sPath = '',
            array $aHeaders = []
        ): Factory\HttpRequest\Get {
            if (class_exists('\App\Common\Factory\HttpRequest\Get')) {
                return new \App\Common\Factory\HttpRequest\Get($sBaseUri, $sPath, $aHeaders);
            } else {
                return new Factory\HttpRequest\Get($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpRequestPatch'           => function (
            string $sBaseUri = '',
            string $sPath = '',
            array $aHeaders = []
        ): Factory\HttpRequest\Patch {
            if (class_exists('\App\Common\Factory\HttpRequest\Patch')) {
                return new \App\Common\Factory\HttpRequest\Patch($sBaseUri, $sPath, $aHeaders);
            } else {
                return new Factory\HttpRequest\Patch($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpRequestPost'            => function (
            string $sBaseUri = '',
            string $sPath = '',
            array $aHeaders = []
        ): Factory\HttpRequest\Post {
            if (class_exists('\App\Common\Factory\HttpRequest\Post')) {
                return new \App\Common\Factory\HttpRequest\Post($sBaseUri, $sPath, $aHeaders);
            } else {
                return new Factory\HttpRequest\Post($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpRequestPut'             => function (
            string $sBaseUri = '',
            string $sPath = '',
            array $aHeaders = []
        ): Factory\HttpRequest\Put {
            if (class_exists('\App\Common\Factory\HttpRequest\Put')) {
                return new \App\Common\Factory\HttpRequest\Put($sBaseUri, $sPath, $aHeaders);
            } else {
                return new Factory\HttpRequest\Put($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpRequestOptions'         => function (
            string $sBaseUri = '',
            string $sPath = '',
            array $aHeaders = []
        ): Factory\HttpRequest\Options {
            if (class_exists('\App\Common\Factory\HttpRequest\Options')) {
                return new \App\Common\Factory\HttpRequest\Options($sBaseUri, $sPath, $aHeaders);
            } else {
                return new Factory\HttpRequest\Options($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpRequestHead'            => function (
            string $sBaseUri = '',
            string $sPath = '',
            array $aHeaders = []
        ): Factory\HttpRequest\Head {
            if (class_exists('\App\Common\Factory\HttpRequest\Head')) {
                return new \App\Common\Factory\HttpRequest\Head($sBaseUri, $sPath, $aHeaders);
            } else {
                return new Factory\HttpRequest\Head($sBaseUri, $sPath, $aHeaders);
            }
        },
        'HttpResponse'               => function (
            GuzzleHttp\Psr7\Response $oClient,
            \Nails\Common\Factory\HttpRequest $oRequest
        ): Factory\HttpResponse {
            if (class_exists('\App\Common\Factory\HttpResponse')) {
                return new \App\Common\Factory\HttpResponse($oClient, $oRequest);
            } else {
                return new Factory\HttpResponse($oClient, $oRequest);
            }
        },
        'Locale'                     => function (
            Factory\Locale\Language $oLanguage = null,
            Factory\Locale\Region $oRegion = null,
            Factory\Locale\Script $oScript = null
        ): Factory\Locale {
            if (class_exists('\App\Common\Factory\Locale')) {
                return new \App\Common\Factory\Locale($oLanguage, $oRegion, $oScript);
            } else {
                return new Factory\Locale($oLanguage, $oRegion, $oScript);
            }
        },
        'LocaleLanguage'             => function (
            string $sLabel = ''
        ): Factory\Locale\Language {
            if (class_exists('\App\Common\Factory\Locale\Language')) {
                return new \App\Common\Factory\Locale\Language($sLabel);
            } else {
                return new Factory\Locale\Language($sLabel);
            }
        },
        'LocaleRegion'               => function (
            string $sLabel = ''
        ): Factory\Locale\Region {
            if (class_exists('\App\Common\Factory\Locale\Region')) {
                return new \App\Common\Factory\Locale\Region($sLabel);
            } else {
                return new Factory\Locale\Region($sLabel);
            }
        },
        'LocaleScript'               => function (
            string $sLabel = ''
        ): Factory\Locale\Script {
            if (class_exists('\App\Common\Factory\Locale\Script')) {
                return new \App\Common\Factory\Locale\Script($sLabel);
            } else {
                return new Factory\Locale\Script($sLabel);
            }
        },
        'Logger'                     => function (): Factory\Logger {
            if (class_exists('\App\Common\Factory\Logger')) {
                return new \App\Common\Factory\Logger();
            } else {
                return new Factory\Logger();
            }
        },
        'ModelField'                 => function (): Factory\Model\Field {
            if (class_exists('\App\Common\Factory\Model\Field')) {
                return new \App\Common\Factory\Model\Field();
            } else {
                return new Factory\Model\Field();
            }
        },
        'Pagination'                 => function (): Factory\Pagination {
            if (class_exists('\App\Common\Factory\Pagination')) {
                return new \App\Common\Factory\Pagination();
            } else {
                return new Factory\Pagination();
            }
        },
        'PDODatabaseForeignKeyCheck' => function (
            Service\PDODatabase $oDatabase
        ): Factory\PDODatabase\ForeignKeyCheck {
            if (class_exists('\App\Common\Factory\PDODatabase\ForeignKeyCheck')) {
                return new \App\Common\Factory\PDODatabase\ForeignKeyCheck($oDatabase);
            } else {
                return new Factory\PDODatabase\ForeignKeyCheck($oDatabase);
            }
        },
        'PDODatabaseTransaction'     => function (
            Service\PDODatabase $oDatabase
        ): Factory\PDODatabase\Transaction {
            if (class_exists('\App\Common\Factory\PDODatabase\Transaction')) {
                return new \App\Common\Factory\PDODatabase\Transaction($oDatabase);
            } else {
                return new Factory\PDODatabase\Transaction($oDatabase);
            }
        },
        'UserFeedbackMessage'        => function (
            Service\UserFeedback $oUserFeedback,
            string $sType
        ): Factory\Service\UserFeedback\Message {
            if (class_exists('\App\Common\Factory\UserFeedback\Message')) {
                return new \App\Common\Factory\Service\UserFeedback\Message($oUserFeedback, $sType);
            } else {
                return new Factory\Service\UserFeedback\Message($oUserFeedback, $sType);
            }
        },
    ],

    'resources' => [
        'AppSetting'       => function ($oObj): Resource\AppSetting {
            if (class_exists('\App\Common\Resource\AppSetting')) {
                return new \App\Common\Resource\AppSetting($oObj);
            } else {
                return new Resource\AppSetting($oObj);
            }
        },
        'Cookie'           => function ($oObj): Resource\Cookie {
            if (class_exists('\App\Common\Resource\Cookie')) {
                return new \App\Common\Resource\Cookie($oObj);
            } else {
                return new Resource\Cookie($oObj);
            }
        },
        'Country'          => function ($oObj): Resource\Country {
            if (class_exists('\App\Common\Resource\Country')) {
                return new \App\Common\Resource\Country($oObj);
            } else {
                return new Resource\Country($oObj);
            }
        },
        'CountryContinent' => function ($oObj): Resource\Country\Continent {
            if (class_exists('\App\Common\Resource\Country\Continent')) {
                return new \App\Common\Resource\Country\Continent($oObj);
            } else {
                return new Resource\Country\Continent($oObj);
            }
        },
        'CountryLanguage'  => function ($oObj): Resource\Country\Language {
            if (class_exists('\App\Common\Resource\Country\Language')) {
                return new \App\Common\Resource\Country\Language($oObj);
            } else {
                return new Resource\Country\Language($oObj);
            }
        },
        'Date'             => function ($oObj): Resource\Date {
            if (class_exists('\App\Common\Resource\Date')) {
                return new \App\Common\Resource\Date($oObj);
            } else {
                return new Resource\Date($oObj);
            }
        },
        'DateTime'         => function ($oObj): Resource\DateTime {
            if (class_exists('\App\Common\Resource\DateTime')) {
                return new \App\Common\Resource\DateTime($oObj);
            } else {
                return new Resource\DateTime($oObj);
            }
        },
        'ExpandableField'  => function (): Resource\ExpandableField {
            if (class_exists('\App\Common\Resource\ExpandableField')) {
                return new \App\Common\Resource\ExpandableField();
            } else {
                return new Resource\ExpandableField();
            }
        },
        'FileCacheItem'    => function ($oObj): Resource\FileCache\Item {
            if (class_exists('\App\Common\Resource\FileCache\Item')) {
                return new \App\Common\Resource\FileCache\Item($oObj);
            } else {
                return new Resource\FileCache\Item($oObj);
            }
        },
        'Resource'         => function ($oObj): Resource {
            if (class_exists('\App\Common\Resource')) {
                return new \App\Common\Resource($oObj);
            } else {
                return new Resource($oObj);
            }
        },
        'Entity'           => function ($oObj): Resource\Entity {
            if (class_exists('\App\Common\Resource\Entity')) {
                return new \App\Common\Resource\Entity($oObj);
            } else {
                return new Resource\Entity($oObj);
            }
        },
    ],
];
