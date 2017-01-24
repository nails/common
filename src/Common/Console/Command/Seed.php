<?php

namespace Nails\Common\Console\Command;

use Nails\Console\Command\Base;
use Nails\Environment;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Seed extends Base
{
    /**
     * Configures the app
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:seed');
        $this->setDescription('Seed the database');

        $this->addArgument(
            'component',
            InputArgument::OPTIONAL,
            'Which component to seed from'
        );

        $this->addArgument(
            'class',
            InputArgument::OPTIONAL,
            'The seed class to execute'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param  InputInterface $oInput The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     * @throws \Exception
     * @return void
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $oOutput->writeln('');
        $oOutput->writeln('<info>----------------------</info>');
        $oOutput->writeln('<info>Nails Database Seeder </info>');
        $oOutput->writeln('<info>----------------------</info>');
        $oOutput->writeln('Beginning...');
        $oOutput->writeln('');

        // --------------------------------------------------------------------------

        //  Check environment
        if (Environment::is('PRODUCTION')) {

            $oOutput->writeln('');
            $oOutput->writeln('--------------------------------------');
            $oOutput->writeln('| <info>WARNING: The app is in PRODUCTION.</info> |');
            $oOutput->writeln('--------------------------------------');
            $oOutput->writeln('');
            $oOutput->writeln('Aborting seed.');

            return;
        }

        // --------------------------------------------------------------------------


        //  Prep arguments
        $sComponent = strtolower($oInput->getArgument('component'));
        $sClass     = strtolower($oInput->getArgument('class'));
        $aClasses   = explode(',', $sClass);
        $aClasses   = array_filter($aClasses);
        $aClasses   = array_unique($aClasses);
        $aClasses   = array_map('ucfirst', $aClasses);

        //  Find seeds
        $aAllComponents = _NAILS_GET_COMPONENTS();
        array_unshift(
            $aAllComponents,
            (object) [
                'slug'      => 'app',
                'namespace' => 'App\\',
                'path'      => FCPATH,
            ],
            (object) [
                'slug'      => 'nailsapp/common',
                'namespace' => 'Nails\\Common\\',
                'path'      => NAILS_COMMON_PATH,
            ]
        );
        $aComponents = [];

        if (!empty($sComponent)) {
            foreach ($aAllComponents as $oComponent) {
                if ($sComponent == $oComponent->slug) {
                    $aComponents[] = $oComponent;
                    break;
                }
            }
            if (empty($aComponents)) {
                throw new \Exception('"' . $sComponent . '"  is not a valid component');
            }
        } else {
            $aComponents = $aAllComponents;
        }

        // --------------------------------------------------------------------------

        //  Find seeds, if they exist
        $aSeedClasses = [];
        foreach ($aComponents as $oComponent) {
            $sPath = $oComponent->path . 'src/Seed/';
            if (is_dir($sPath)) {

                $aSeeds = directory_map($sPath);
                $aSeeds = array_map(
                    function ($sClass) {
                        return basename($sClass, '.php');
                    },
                    $aSeeds
                );

                if (empty($aSeeds)) {
                    continue;
                }

                if (empty($aClasses)) {
                    foreach ($aSeeds as $sClass) {
                        $aSeedClasses[] = (object) [
                            'component' => $oComponent->slug,
                            'namespace' => $oComponent->namespace,
                            'class'     => $sClass,
                        ];
                    }
                } else {
                    foreach ($aClasses as $sClass) {
                        if (!in_array($sClass, $aSeeds)) {
                            throw new \Exception('"' . $sClass . '" is not a valid seed class.');
                        } else {
                            $aSeedClasses[] = (object) [
                                'component' => $oComponent->slug,
                                'namespace' => $oComponent->namespace,
                                'class'     => $sClass,
                            ];
                        }
                    }
                }
            }
        }

        //  Get confirmation
        $oOutput->writeln('The following seeds will be executed in this order:');
        $oOutput->writeln('');
        foreach ($aSeedClasses as $oSeedClass) {
            //  Execute migration
            $oOutput->writeln(
                ' - <comment>' . $oSeedClass->component . ' [' . $oSeedClass->class . ']</comment>'
            );
        }
        $oOutput->writeln('');

        $bDoSeed = $this->confirm('Does this look OK?', true);

        if ($bDoSeed) {
            $oOutput->writeln('');
            $oOutput->writeln('Executing seeds...');
            $oOutput->writeln('');

            $oDb = Factory::service('ConsoleDatabase', 'nailsapp/module-console');

            foreach ($aSeedClasses as $oSeedClass) {
                //  Execute seed
                $oOutput->write(
                    str_pad(
                        ' - <comment>' . $oSeedClass->component . ' [' . $oSeedClass->class . ']</comment>... ',
                        50,
                        ' '
                    )
                );
                $sClassName = $oSeedClass->namespace . 'Seed\\' . $oSeedClass->class;
                $oClass     = new $sClassName($oDb);
                $oClass->pre();
                $oClass->execute();
                $oClass->post();
                $oOutput->writeln('<info>DONE</info>');
            }
            $oOutput->writeln('');
        } else {
            $oOutput->writeln('');
            $oOutput->writeln('No seeds were executed');
            $oOutput->writeln('');
        }

        // --------------------------------------------------------------------------

        //  Cleaning up
        $oOutput->writeln('');
        $oOutput->writeln('<comment>Cleaning up...</comment>');

        // --------------------------------------------------------------------------

        //  And we're done
        $oOutput->writeln('');
        $oOutput->writeln('Complete!');
    }
}
