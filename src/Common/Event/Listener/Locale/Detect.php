<?php

namespace Nails\Common\Event\Listener\Locale;

use Nails\Common\Events;
use Nails\Common\Events\Subscription;

/**
 * Class Detect
 *
 * @package Nails\Common\Event\Listener\Locale
 */
class Detect extends Subscription
{
    /**
     * Detect constructor.
     */
    public function __construct()
    {
        $this
            ->setEvent(Events::SYSTEM_STARTUP)
            ->setCallback([$this, 'execute']);
    }

    // --------------------------------------------------------------------------

    public function execute(): void
    {
        /** @var Locale $oLocale */
        $oLocale = Factory::service('Locale');

        /**
         * Manipulate the URL
         * Remove the /{language} element from the URL once the Locale service has initialsied
         * so that this doesn't affect normal routing of the application.
         *
         * @todo (Pablo - 2019-03-08) - Check if this breaks on systems where the app isn't at the root of the domain
         */

        if (array_key_exists('PATH_INFO', $_SERVER)) {
            $_SERVER['PATH_INFO'] = preg_replace($oLocale::URL_REGEX, '$2', ltrim($_SERVER['PATH_INFO'], '/'));
        }

        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $_SERVER['REQUEST_URI'] = preg_replace($oLocale::URL_REGEX, '$2', ltrim($_SERVER['REQUEST_URI'], '/'));
        }
    }
}
