<?php

namespace Nails\Common\Console\Command;

use Nails\Environment;
use Nails\Console\Command\Base;
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
        $this->setName('seed');
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
     * @param  InputInterface  $input  The Input Interface proivided by Symfony
     * @param  OutputInterface $output The Output Interface proivided by Symfony
     * @throws \Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<info>----------------------</info>');
        $output->writeln('<info>Nails Database Seeder </info>');
        $output->writeln('<info>----------------------</info>');
        $output->writeln('Beginning...');
        $output->writeln('');

        // --------------------------------------------------------------------------

        //  Check environment
        if (Environment::is('PRODUCTION')) {

            $output->writeln('');
            $output->writeln('--------------------------------------');
            $output->writeln('| <info>WARNING: The app is in PRODUCTION.</info> |');
            $output->writeln('--------------------------------------');
            $output->writeln('');
            $output->writeln('Aborting seed.');
            return;
        }

        // --------------------------------------------------------------------------


        //  Prep arguments
        $sComponent = strtolower($input->getArgument('component'));
        $sClass     = strtolower($input->getArgument('class'));
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
                    $aSeedClasses = $aSeeds;
                } else {
                    foreach ($aClasses as $sClass) {
                        if (!in_array($sClass, $aSeeds)) {
                            throw new \Exception('"' . $sClass . '" is not a valid seed class.');
                        } else {
                            $aSeedClasses[] = $sClass;
                        }
                    }
                }

                //  Get confirmation
                $output->writeln('The following seeds will be executed in this order:');
                $output->writeln('');
                foreach ($aSeedClasses as $sSeedClass) {
                    //  Execute migration
                    $output->writeln(
                        ' - <comment>' . $oComponent->slug . ' [' . $sSeedClass . ']</comment>'
                    );
                }
                $output->writeln('');

                $bDoSeed = $this->confirm('Does this look OK?', true, $input, $output);

                if ($bDoSeed) {
                    $output->writeln('');
                    $output->writeln('Executing seeds...');
                    $output->writeln('');
                    foreach ($aSeedClasses as $sSeedClass) {
                        //  Execute seed
                        $output->write(
                            str_pad(
                                ' - <comment>' . $oComponent->slug . ' [' . $sSeedClass . ']</comment>... ',
                                50,
                                ' '
                            )
                        );
                        $sClassName = $oComponent->namespace . 'Seed\\' . $sSeedClass;
                        $oClass = new $sClassName();
                        $oClass->pre();
                        $oClass->execute();
                        $oClass->post();
                        $output->writeln('<info>DONE</info>');
                    }
                    $output->writeln('');
                } else {
                    $output->writeln('');
                    $output->writeln('No seeds were executed');
                    $output->writeln('');
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Cleaning up
        $output->writeln('');
        $output->writeln('<comment>Cleaning up...</comment>');

        // --------------------------------------------------------------------------

        //  And we're done
        $output->writeln('');
        $output->writeln('Complete!');
    }
}
