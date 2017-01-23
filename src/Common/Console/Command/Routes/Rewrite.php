<?php

namespace Nails\Common\Console\Command\Routes;

use Nails\Factory;
use Nails\Console\Command\Base;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Rewrite extends Base
{
    /**
     * Configures the app
     * @return void
     */
    protected function configure()
    {
        $this->setName('routes:rewrite');
        $this->setDescription('[WIP] Rewrites the application\'s routes file');
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
        $output->writeln('<info>--------------------</info>');
        $output->writeln('<info>Nails Routes Rewrite</info>');
        $output->writeln('<info>--------------------</info>');
        $output->writeln('Beginning...');

        $oRoutesModel = Factory::model('Routes');

        if ($oRoutesModel->update()) {

            $output->writeln('Routes rewritten successfully.');

        } else {

            $output->writeln('There was a problem writing the routes.');
            $output->writeln($oRoutesModel->lastError());
        }


        //  Cleaning up
        $output->writeln('');
        $output->writeln('<comment>Cleaning up...</comment>');

        //  And we're done!
        $output->writeln('');
        $output->writeln('Complete!');
    }
}
