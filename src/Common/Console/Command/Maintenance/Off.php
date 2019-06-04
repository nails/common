<?php

namespace Nails\Common\Console\Command\Maintenance;

use Nails\Console\Command\Base;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Off
 *
 * @package Nails\Common\Console\Command\Maintenance
 */
class Off extends Base
{
    /**
     * Configures the command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('maintenance:off')
            ->setDescription('Turns maintenance mode off');
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

        if (is_file(On::MAINTENANCE_FILE)) {
            unlink(On::MAINTENANCE_FILE);
        }

        $oOutput->writeln('Maintenance mode turned <comment>OFF</comment>');

        return static::EXIT_CODE_SUCCESS;
    }
}
