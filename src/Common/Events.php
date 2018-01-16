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
     * Fired when the system starts
     */
    const SYSTEM_STARTUP = 'SYSTEM:STARTUP';

    /**
     * Fired when the system is ready
     */
    const SYSTEM_READY = 'SYSTEM:READY';
}
