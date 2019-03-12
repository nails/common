<?php

/**
 * The class provides a summary of the events fired by this module
 *
 * @package     Nails
 * @subpackage  module-common
 * @category    Events
 * @author      Nails Dev Team
 */

namespace Nails\Common;

use Nails\Common\Events\Base;
use Nails\Common\Service\Locale;
use Nails\Factory;

class Events extends Base
{
    /**
     * Fired when the system starts, after Nails has initiated and routing complete,
     * but before the controller is constructed
     */
    const SYSTEM_STARTUP = 'SYSTEM:STARTUP';

    /**
     * Fired when the system is ready and the controller is about to be constructed
     */
    const SYSTEM_READY = 'SYSTEM:READY';

    /**
     * Fired when the systems shutsdown, this is the last event to be fired
     */
    const SYSTEM_SHUTOWN = 'SYSTEM:SHUTDOWN';

    // --------------------------------------------------------------------------

    /**
     * Subscribe to events
     *
     * @return array
     */
    public function autoload(): array
    {
        return [
            Factory::factory('EventSubscription')
                ->setEvent(static::SYSTEM_STARTUP)
                ->setCallback([$this, 'detectLocale']),
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Set up the Locale service and auto-detect the locale for the request
     *
     * @throws Exception\FactoryException
     */
    public function detectLocale(): void
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
