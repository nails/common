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

use Nails\Common\Event\Listener\Locale;
use Nails\Common\Events\Base;
use Nails\Common\Events\Subscription;
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
     * Fired when the system shutsdown, this is the last event to be fired
     */
    const SYSTEM_SHUTDOWN = 'SYSTEM:SHUTDOWN';

    // --------------------------------------------------------------------------

    /**
     * Subscribe to events
     *
     * @return Subscription[]
     */
    public function autoload(): array
    {
        return [
            new Locale\Detect(),
        ];
    }
}
