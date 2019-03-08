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

class Events extends Base
{
    /**
     * Fired when the system initialises, after basic configuration is in place.
     * This is the earliest event which can be bound to
     */
    const SYSTEM_INIT = 'SYSTEM:INIT';

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
}
