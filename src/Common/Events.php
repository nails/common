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
     */
    public function detectLocale()
    {
        /** @var Locale $oLocale */
        $oLocale = Factory::service('Locale');
        //  @todo (Pablo - 2019-03-08) - Manipulate the URL?
    }
}
