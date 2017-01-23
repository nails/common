<?php

namespace Nails\Common\Console\Command;

use Nails\Console\Command\Base;
use Symfony\Component\Console\Command\Command;
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
     * @param  InputInterface  $input  The Input Interface provided by Symfony
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<info>-------------</info>');
        $output->writeln('<info>Nails Testing</info>');
        $output->writeln('<info>-------------</info>');
        $output->writeln('Beginning...');

        $output->writeln('');
        $output->writeln('');
        $output->writeln('<comment>@todo</comment>');
        $output->writeln('');
        $output->writeln('');

        //  Cleaning up
        $output->writeln('');
        $output->writeln('<comment>Cleaning up...</comment>');

        //  And we're done!
        $output->writeln('');
        $output->writeln('Complete!');
    }
}
