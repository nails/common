<?php

namespace Nails\Common\Event\Listener\Output;

use Nails\Common\Events;
use Nails\Common\Events\Subscription;
use Nails\Common\Service\Output;
use Nails\Common\Service\MetaData;
use Nails\Factory;

/**
 * Class Pre
 *
 * @package Nails\Common\Event\Listener\Output
 */
class Pre extends Subscription
{
    /**
     * Pre constructor.
     */
    public function __construct()
    {
        $this
            ->setEvent(Events::OUTPUT_PRE)
            ->setCallback([$this, 'execute']);
    }

    // --------------------------------------------------------------------------

    public function execute(): void
    {
        /** @var Output $oOutout */
        $oOutout = Factory::service('Output');
        /** @var MetaData $oMetaData */
        $oMetaData = Factory::service('MetaData');

        if ($oMetaData->getNoIndex()) {
            $oOutout->setHeader('X-Robots-Tag: noindex');
        }
    }
}
