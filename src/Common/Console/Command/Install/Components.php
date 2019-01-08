<?php

namespace Nails\Common\Console\Command\Install;

use Nails\Console\Command\Base;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Components extends Base
{
    /**
     * Configures the app
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('install:components');
        $this->setDescription('Executes any post install commands for installed components.');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the command
     *
     * @param  InputInterface  $oInput  The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);
        //  @todo (Pablo - 2019-01-08) - Loop components and execute any scripts they define
        return static::EXIT_CODE_SUCCESS;
    }
}
