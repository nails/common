<?php

namespace Nails\Common\Console\Command\Events;

use Nails\Common\Exception\EventException;
use Nails\Common\Service\Event;
use Nails\Components;
use Nails\Console\Command\Base;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListListeners extends Base
{
    /**
     * Configures the command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('events:list:listeners')
            ->setDescription('Lists all autoloaded subscriptions');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        /** @var Event $oService */
        $oService = Factory::service('Event');
        $aSubs    = $oService->getSubscriptions();
        foreach ($aSubs as $sNamespace => $aEvents) {

            $this->banner('Namespace: <comment>' . $sNamespace . '</comment>');
            foreach ($aEvents as $sEvent => $aSubscribers) {

                $oOutput->writeln('Event:  <info>' . $sEvent . '</info>');

                foreach ($aSubscribers as $oSubscriber) {
                    if (is_array($oSubscriber->callback)) {
                        list($oObject, $sMethod) = $oSubscriber->callback;
                        if (is_object($oObject)) {
                            $this->oOutput->writeln('Class:  <comment>' . get_class($oObject) . '</comment>');
                        } else {
                            $this->oOutput->writeln('Class:  <comment>' . $oObject . '</comment>');
                        }
                        $this->oOutput->writeln('Method: <comment>' . $sMethod . '</comment>');
                    } else {
                        d($oSubscriber);
                    }
                }
                $this->oOutput->writeln('');
            }
        }

        $this->oOutput->writeln('');
        return static::EXIT_CODE_SUCCESS;
    }
}
