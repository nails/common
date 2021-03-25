<?php

namespace Nails\Common\Console\Command\Install;

use Nails\Common\Factory\Component;
use Nails\Common\Interfaces;
use Nails\Common\Service\AppSetting;
use Nails\Console\Command\Base;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Components
 *
 * @package Nails\Common\Console\Command\Install
 */
class Components extends Base
{
    /**
     * Configures the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('install:components');
        $this->setDescription('Executes any post install commands for components');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the command
     *
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $this->banner('Install: Components');

        foreach (\Nails\Components::available() as $oComponent) {
            $this
                ->executePostInstallScripts($oComponent);
        }

        return static::EXIT_CODE_SUCCESS;
    }

    // --------------------------------------------------------------------------

    /**
     * Executes any post install scripts for a component
     *
     * @param Component $oComponent
     *
     * @return $this
     */
    protected function executePostInstallScripts(Component $oComponent): self
    {
        if (!empty($oComponent->scripts->install)) {
            $this->oOutput->writeln(sprintf(
                'Executing post-install scripts for: <comment>%s</comment>',
                $oComponent->slug
            ));

            $this->oOutput->writeln(sprintf(
                '> <info>cd %s</info>',
                $oComponent->path
            ));

            chdir($oComponent->path);

            $this->executeCommand($oComponent->scripts->install);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Executes a command
     *
     * @param string|iterable $sCommand The command to execute
     */
    protected function executeCommand($sCommand)
    {
        if (is_iterable($sCommand)) {
            foreach ($sCommand as $sCommand) {
                $this->executeCommand($sCommand);
            }

        } elseif (is_string($sCommand)) {

            $this->oOutput->writeln(sprintf(
                '> <info>cd %s</info>',
                $sCommand
            ));
            exec($sCommand, $aOutput, $iReturnVal);

            if ($iReturnVal) {
                throw new \RuntimeException('Failed to execute command: ' . $sCommand, $iReturnVal);
            }
        }
    }
}
