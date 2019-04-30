<?php

/**
 * This file is the template for the contents of event listeners
 * Used by the console command when creating event listeners.
 */

return <<<'EOD'
<?php

/**
 * The {{CLASS_NAME}} event listener
 *
 * @package  App
 * @category events
 */

namespace {{NAMESPACE}};

use Nails\Common\Events\Subscription;

class {{CLASS_NAME}} extends Subscription
{
    /**
     * {{CLASS_NAME}} constructor.
     */
    public function __construct()
    {
        //  @todo - Complete the event stub
        //$this
        //    ->setEvent(\MyClass\Event::EVENT_NAME)
        //    ->setNamespace('app')
        //    ->setCallback([$this, 'execute']);
    }

    // --------------------------------------------------------------------------

    /**
     * Called when the event is triggered
     **/
    public function execute(): void
    {
    }
}

EOD;
