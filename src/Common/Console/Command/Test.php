<?php

namespace Nails\Common\Console\Command;

use Nails\Console\Command\Base;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends Base
{
    /**
     * Configures the app
     * @return void
     */
    protected function configure()
    {
        $this->setName('test');
        $this->setDescription('[WIP] Runs PHPUnit tests for the application');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     * @param  InputInterface $oInput The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $oOutput->writeln('');
        $oOutput->writeln('<info>-------------</info>');
        $oOutput->writeln('<info>Nails Testing</info>');
        $oOutput->writeln('<info>-------------</info>');
        $oOutput->writeln('Beginning...');

        $oOutput->writeln('');
        $oOutput->writeln('');
        $oOutput->writeln('<comment>@todo</comment>');
        $oOutput->writeln('');
        $oOutput->writeln('');

        //  Cleaning up
        $oOutput->writeln('');
        $oOutput->writeln('<comment>Cleaning up...</comment>');

        //  And we're done!
        $oOutput->writeln('');
        $oOutput->writeln('Complete!');

        return static::EXIT_CODE_SUCCESS;
    }
}
