<?php

namespace Nails\Common\Console\Command;

use Nails\Console\Command\Base;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Events extends Base
{
    /**
     * Configures the app
     * @return void
     */
    protected function configure()
    {
        $this->setName('events');
        $this->setDescription('Lists the events which are available to subscribe to');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param  InputInterface  $oInput  The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $aEvents     = [];
        $aComponents = array_merge(
            [
                (object) [
                    'namespace' => 'App\\',
                    'slug'      => 'app',
                ],
            ],
            [
                (object) [
                    'namespace' => 'Nails\\Common\\',
                    'slug'      => 'nailsapp/common',
                ],
            ],
            _NAILS_GET_COMPONENTS()
        );

        foreach ($aComponents as $oComponent) {
            $sClass = '\\' . $oComponent->namespace . 'Events';
            if (class_exists($sClass)) {
                $aComponentEvents = call_user_func([$sClass, 'info']);
                if (!empty($aComponentEvents)) {
                    $aEvents[$oComponent->slug] = $aComponentEvents;
                }
            }
        }

        $oOutput->writeln('The following events are available in this application:');

        foreach ($aEvents as $sComponent => $aComponentEvents) {
            $oOutput->writeln('');
            $oOutput->writeln('<comment>' . $sComponent . '</comment>');
            $oOutput->writeln('<comment>' . str_repeat('-', strlen($sComponent)) . '</comment>');
            $oOutput->writeln('');
            foreach ($aComponentEvents as $aEvent) {
                $oOutput->writeln('  <info>' . $aEvent->constant . '</info>');
                $oOutput->writeln('  ' . $aEvent->description);

                if (!empty($aEvent->arguments)) {
                    $sParamPrefix = '  ↳ <comment>@param</comment> ';
                    $oOutput->writeln($sParamPrefix . implode("\n" . $sParamPrefix, $aEvent->arguments));
                }
                $oOutput->writeln('');
            }
        }
        $oOutput->writeln('');

        return static::EXIT_CODE_SUCCESS;
    }
}
