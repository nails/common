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
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
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

            $sPattern = '/' . str_replace('/', '\/', $sFilter) . '/';
            if (!empty($sFilter) && !preg_match($sPattern, $oComponent->slug)) {
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
            $oOutput->writeln('<comment>' . $sComponent . '</comment>');
            $oOutput->writeln('<comment>' . str_repeat('-', strlen($sComponent)) . '</comment>');
            $oOutput->writeln('');
            foreach ($aComponentEvents as $oEvent) {
                $oOutput->writeln('  <info>' . $oEvent->constant . '</info>');

                if (strlen($oEvent->description) > 100) {
                    $sDescription = wordwrap($oEvent->description, 100);
                    $aDescription = explode("\n", $sDescription);
                    $aDescription = array_map('trim', $aDescription);
                } else {
                    $aDescription = [$oEvent->description];
                }

                foreach ($aDescription as $sLine) {
                    $oOutput->writeln('  ' . $sLine);
                }

                $oOutput->writeln('  <comment>@namespace</comment> ' . $oEvent->namespace);

                if (!empty($oEvent->arguments)) {
                    $sParamPrefix = '  <comment>@param</comment> ';
                    $oOutput->writeln($sParamPrefix . implode("\n" . $sParamPrefix, $oEvent->arguments));
                }
                $oOutput->writeln('');
            }
        }
        $oOutput->writeln('');

        return static::EXIT_CODE_SUCCESS;
    }
}
