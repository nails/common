<?php

namespace Nails\Common\Console\Command\Database;

use Nails\Components;
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
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:seed');
        $this->setDescription('Seed the database');

        $this->addArgument(
            'component',
            InputArgument::OPTIONAL,
            'Which component to seed from; use "list" to show all available seeders'
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
     * @param  InputInterface  $oInput  The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @throws \Exception
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $oOutput->writeln('');
        $oOutput->writeln('<info>----------------------</info>');
        $oOutput->writeln('<info>Nails Database Seeder </info>');
        $oOutput->writeln('<info>----------------------</info>');
        $oOutput->writeln('Beginning...');

        // --------------------------------------------------------------------------

        if ($oInput->getArgument('component') == 'list') {

            $oOutput->writeln('');
            $aSeeders = $this->getSeeders();
            if (empty($aSeeders)) {
                $oOutput->writeln('No seeders were discovered. Make one using <comment>make:db:seed</comment>'
                );
            } else {
                $oOutput->writeln('The following seeders are available:');
                $oOutput->writeln('');
                foreach ($aSeeders as $oSeeder) {
                    $oOutput->writeln(' - <comment>' . $oSeeder->component . ' [' . $oSeeder->class . ']</comment>');
                }
            }
            $oOutput->writeln('');

            return static::EXIT_CODE_SUCCESS;
        }

        // --------------------------------------------------------------------------

        //  Check environment
        if (Environment::is(Environment::ENV_PROD)) {

            $oOutput->writeln('');
            $oOutput->writeln('--------------------------------------');
            $oOutput->writeln('| <info>WARNING: The app is in PRODUCTION.</info> |');
            $oOutput->writeln('--------------------------------------');
            $oOutput->writeln('');
            $oOutput->writeln('Aborting seed.');

            return static::EXIT_CODE_FAILURE;
        }

        // --------------------------------------------------------------------------

        //  Find seeds
        $aSeedersAll = $this->getSeeders();

        //  Filter out seeders
        $sComponent = strtolower($oInput->getArgument('component'));
        $aClasses   = explode(',', $oInput->getArgument('class'));
        $aClasses   = array_filter($aClasses);
        $aClasses   = array_unique($aClasses);
        $aSeeders   = [];

        /**
         * If component and class arguments are empty then execute all seeds
         * If component is defined and class is empty, execute all seeds for that component
         * If both are defined then only execute seeds which match both
         */

        if (empty($sComponent) && empty($aClasses)) {
            $aSeeders = $aSeedersAll;
        } elseif (!empty($sComponent) && empty($aClasses)) {
            foreach ($aSeedersAll as $oSeeder) {
                if ($oSeeder->component === $sComponent) {
                    $aSeeders[] = $oSeeder;
                }
            }
        } else {
            foreach ($aSeedersAll as $oSeeder) {
                if ($oSeeder->component === $sComponent && in_array($oSeeder->class, $aClasses)) {
                    $aSeeders[] = $oSeeder;
                }
            }
        }

        if (empty($aSeeders)) {
            return $this->abort(
                static::EXIT_CODE_SUCCESS,
                [
                    'No seeders were discovered. Make one using <comment>make:db:seed</comment>',
                ]
            );
        }

        //  Get confirmation
        $oOutput->writeln('The following seeds will be executed in this order:');
        $oOutput->writeln('');
        foreach ($aSeeders as $oSeeder) {
            //  Execute migration
            $oOutput->writeln(
                ' - <comment>' . $oSeeder->component . ' [' . $oSeeder->class . ']</comment>'
            );
        }
        $oOutput->writeln('');

        $bDoSeed = $this->confirm('Does this look OK?', true);

        if ($bDoSeed) {
            $oOutput->writeln('');
            $oOutput->writeln('Executing seeds...');
            $oOutput->writeln('');

            $oDb = Factory::service('PDODatabase');

            foreach ($aSeeders as $oSeeder) {
                //  Execute seed
                $oOutput->write(
                    str_pad(
                        ' - <comment>' . $oSeeder->component . ' [' . $oSeeder->class . ']</comment>... ',
                        50,
                        ' '
                    )
                );
                $sClassName = $oSeeder->namespace . 'Seed\\' . $oSeeder->class;
                $oClass     = new $sClassName($oDb);
                $oClass->pre();
                $oClass->execute();
                $oClass->post();
                $oOutput->writeln('<info>done!</info>');
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

        return static::EXIT_CODE_SUCCESS;
    }

    // --------------------------------------------------------------------------

    protected function getSeeders()
    {
        $aSeedClasses   = [];
        $aAllComponents = Components::available();

        foreach ($aAllComponents as $oComponent) {
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

                foreach ($aSeeds as $sClass) {
                    $aSeedClasses[] = (object) [
                        'component' => $oComponent->slug,
                        'namespace' => $oComponent->namespace,
                        'class'     => $sClass,
                    ];
                }
            }
        }

        return $aSeedClasses;
    }
}
