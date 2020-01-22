<?php

namespace Nails\Common\Event\Listener\Routes;

use Nails\Common\Events;
use Nails\Common\Events\Subscription;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Service\Routes;
use Nails\Factory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Update
 *
 * @package Nails\Common\Event\Listener\Routes
 */
class Update extends Subscription
{
    /**
     * Update constructor.
     */
    public function __construct()
    {
        $this
            ->setEvent(Events::ROUTES_UPDATE)
            ->setCallback([$this, 'execute']);
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the listener
     *
     * @param string          $sModule For which module to restrict the route update
     * @param OutputInterface $oOutput A Symfony OutputInterface to write logs to
     *
     * @throws NailsException
     * @throws FactoryException
     */
    public function execute(string $sModule = null, OutputInterface $oOutput = null): void
    {
        /** @var Routes $oRoutesService */
        $oRoutesService = Factory::service('Routes');
        if (!$oRoutesService->update($sModule, $oOutput)) {
            throw new NailsException($oRoutesService->lastError());
        }
    }
}
