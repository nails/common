<?php

namespace Nails\Common\Console\Command\Maintenance;

use Nails\Console\Command\Base;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class On
 *
 * @package Nails\Common\Console\Command\Maintenance
 */
class On extends Base
{
    const MAINTENANCE_FILE = NAILS_APP_PATH . '.MAINTENANCE';

    // --------------------------------------------------------------------------

    /**
     * Configures the command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('maintenance:on')
            ->setDescription('Turns maintenance mode on');
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

        touch(static::MAINTENANCE_FILE);

        $oOutput->writeln('Maintenance mode turned <comment>ON</comment>');

        return static::EXIT_CODE_SUCCESS;
    }
}
