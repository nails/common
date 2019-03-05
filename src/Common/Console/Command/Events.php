<?php

namespace Nails\Common\Console\Command;

use Nails\Common\Exception\EventException;
use Nails\Components;
use Nails\Console\Command\Base;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Events extends Base
{
    /**
     * Configures the app
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('events')
            ->setDescription('Lists the events which are available to subscribe to')
            ->addArgument('component', InputArgument::OPTIONAL, 'Filter by component');
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

        $sFilter     = $this->oInput->getArgument('component');
        $aEvents     = [];
        $aComponents = array_merge(
            [
                (object) [
                    'namespace' => 'App\\',
                    'slug'      => 'app',
                ],
            ],
            Components::available()
        );

        foreach ($aComponents as $oComponent) {

            if (!empty($sFilter) && $sFilter !== $oComponent->slug) {
                continue;
            }

            $sClass = '\\' . $oComponent->namespace . 'Events';
            if (class_exists($sClass)) {

                if (!classExtends($sClass, \Nails\Common\Events\Base::class)) {
                    throw new EventException($sClass . ' must extend ' . \Nails\Common\Events\Base::class);
                }

                if (method_exists($sClass, 'info')) {
                    $aComponentEvents = call_user_func([$sClass, 'info']);
                    if (!empty($aComponentEvents)) {
                        $aEvents[$oComponent->slug] = $aComponentEvents;
                    }
                }
            }
        }

        $oOutput->writeln('');

        if (!empty($sFilter)) {
            $oOutput->writeln('The following events are available for <info>' . $sFilter . '</info>:');
        } else {
            $oOutput->writeln('The following events are available in this application:');
        }

        foreach ($aEvents as $sComponent => $aComponentEvents) {
            $oOutput->writeln('');
            if (empty($sFilter)) {
                $oOutput->writeln('<comment>' . $sComponent . '</comment>');
                $oOutput->writeln('<comment>' . str_repeat('-', strlen($sComponent)) . '</comment>');
                $oOutput->writeln('');
            }
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
