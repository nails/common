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
                        list($mObject, $sMethod) = $oSubscriber->callback;
                        if (is_object($mObject)) {
                            $sClass = get_class($mObject);
                        } else {
                            $sClass = $mObject;
                        }
                        $sListener = '<comment>' . $sClass . '</comment>::<info>' . $sMethod . '</info>';
                    } elseif (is_string($oSubscriber->callback)) {
                        $sListener = '<info>' . $oSubscriber->callback . '</info>';
                    } elseif ($oSubscriber->callback instanceof \Closure) {
                        //  Hack some info about the closure
                        $sDetails = print_r($oSubscriber->callback, true);
                        preg_match('/\[this\] => (.+) Object/', $sDetails, $aMatches);
                        $sClass    = getFromArray(1, $aMatches, 'Unknown');
                        $sListener = '<comment>' . $sClass . '</comment>::<info>{{Closure}}</info>';
                    } else {
                        $sListener = '<info>' . serialize($oSubscriber->callback) . '</info>';
                    }
                    $this->oOutput->writeln($sListener);
                }
                $this->oOutput->writeln('');
            }
        }

        $this->oOutput->writeln('');
        return static::EXIT_CODE_SUCCESS;
    }
}
