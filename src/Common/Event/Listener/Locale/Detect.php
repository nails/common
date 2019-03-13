<?php

namespace Nails\Common\Event\Listener\Locale;

use Nails\Common\Events;
use Nails\Common\Events\Subscription;
use Nails\Common\Service\HttpCodes;
use Nails\Common\Service\Locale;
use Nails\Factory;

/**
 * Class Detect
 *
 * @package Nails\Common\Event\Listener\Locale
 */
class Detect extends Subscription
{
    /**
     * The Locale service
     *
     * @var Locale
     */
    protected $oLocaleService;

    /**
     * Detect constructor.
     */
    public function __construct()
    {
        $this
            ->setEvent(Events::SYSTEM_STARTUP)
            ->setCallback([$this, 'execute']);

        $this->oLocaleService = Factory::service('Locale');
    }

    // --------------------------------------------------------------------------

    public function execute(): void
    {
        if ($this->oLocaleService::ENABLE_SNIFF_URL) {

            /**
             * Manipulate the URL
             * Remove the /{language} element from the URL once the Locale service has initialsied
             * so that this doesn't affect normal routing of the application.
             *
             * @todo (Pablo - 2019-03-08) - Check if this breaks on systems where the app isn't at the root of the domain
             */

            $oUrl = $this->parseUrl($this->getUrl());

            //  If the detected language is the same as the default language then remove it form the URL and repeat
            if ($oUrl->language === $this->oLocaleService::DEFAULT_LANGUAGE) {
                header(
                    'Location: /' . $oUrl->url,
                    true,
                    HttpCodes::STATUS_TEMPORARY_REDIRECT
                );
                exit(0);
            }

            //  Update the $_SERVER values so the rest of the system continues as normal
            foreach (['PATH_INFO', 'REQUEST_URL'] as $sKey) {
                if (array_key_exists($sKey, $_SERVER)) {
                    $_SERVER[$sKey] = '/' . $oUrl->url;
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current URL
     *
     * @return string
     */
    protected function getUrl(): string
    {
        return ltrim(getFromArray('PATH_INFO', $_SERVER) ?? getFromArray('REQUEST_URI', $_SERVER), '/');
    }

    // --------------------------------------------------------------------------

    /**
     * Parse the URL for supported languages
     *
     * @param string $sUrl The URL to parse
     *
     * @return \stdClass
     */
    protected function parseUrl(string $sUrl): \stdClass
    {
        preg_match($this->oLocaleService->getUrlRegex(), $sUrl, $aMatches);

        if (!empty($aMatches)) {
            return (object) [
                'language' => !empty($aMatches[1]) ? $aMatches[1] : '',
                'url'      => ltrim(!empty($aMatches[2]) ? $aMatches[2] : '', '/'),
            ];
        } else {
            return (object) [
                'language' => null,
                'url'      => ltrim($sUrl, '/'),
            ];
        }
    }
}
