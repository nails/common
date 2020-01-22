<?php

namespace Nails\Common\Console\Command\Database;

use Nails\Common\Console\Seed\DefaultSeed;
use Nails\Common\Helper\Directory;
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
     * Configures the command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('db:seed')
            ->setDescription('Seed the database')
            ->addArgument(
                'component',
                InputArgument::OPTIONAL,
                'Which component to seed from; use "list" to show all available seeders'
            )
            ->addArgument(
                'class',
                InputArgument::OPTIONAL,
                'The seed class to execute'
            );
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $this->banner('Nails Database Seeder');

        // --------------------------------------------------------------------------

        if ($oInput->getArgument('component') == 'list') {

            $oOutput->writeln('');
            $aSeeders = $this->getSeeders();
            if (empty($aSeeders)) {
                $oOutput->writeln('No seeders were discovered. Make one using <comment>make:db:seed</comment>');
                $oOutput->writeln('');
            } else {
                $oOutput->writeln('The following seeders are available:');
                $this->listSeeders($aSeeders);
            }

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
                if ($oSeeder->component->slug === $sComponent) {
                    $aSeeders[] = $oSeeder;
                }
            }
        } else {
            foreach ($aSeedersAll as $oSeeder) {
                if ($oSeeder->component->slug === $sComponent && in_array($this->stripNamespace($oSeeder), $aClasses)) {
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
        $this->listSeeders($aSeeders);

        $bDoSeed = $this->confirm('Does this look OK?', true);

        if ($bDoSeed) {
            $oOutput->writeln('');
            $oOutput->writeln('Executing seeds...');
            $oOutput->writeln('');

            $oDb = Factory::service('PDODatabase');

            foreach ($aSeeders as $oSeeder) {

                $oOutput->write(
                    str_pad($this->renderLine($oSeeder) . '... ', 50, ' ')
                );

                $sClassName = $oSeeder->class;
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

    /**
     * Returns the discovered seeders
     *
     * @return \stdClass[]
     */
    protected function getSeeders(): array
    {
        $aSeedClasses = [];
        foreach (Components::available() as $oComponent) {

            $aSeeds = $oComponent
                ->findClasses('Seed')
                ->whichExtend(DefaultSeed::class);

            foreach ($aSeeds as $sClass) {
                $aSeedClasses[] = (object) [
                    'component' => $oComponent,
                    'class'     => $sClass,
                    'priority'  => constant($sClass . '::CONFIG_PRIORITY'),
                ];
            }
        }

        //  Sort by priority, then class name
        array_multisort(
            array_column($aSeedClasses, 'priority'), SORT_ASC,
            array_column($aSeedClasses, 'class'), SORT_ASC,
            $aSeedClasses
        );

        return $aSeedClasses;
    }

    // --------------------------------------------------------------------------

    protected function listSeeders(array $aSeeders): void
    {
        $this->oOutput->writeln('');
        foreach ($aSeeders as $oSeeder) {
            $this->oOutput->writeln(
                $this->renderLine($oSeeder)
            );
        }
        $this->oOutput->writeln('');
    }

    // --------------------------------------------------------------------------

    protected function renderLine($oSeeder): string
    {
        return sprintf(
            ' - [<comment>%s</comment>] <info>%s</info>',
            $oSeeder->component->slug,
            $this->stripNamespace($oSeeder)
        );
    }

    // --------------------------------------------------------------------------

    protected function stripNamespace($oSeeder): string
    {
        return preg_replace(
            '/^' . preg_quote($oSeeder->component->namespace . 'Seed\\', '/') . '/',
            '',
            $oSeeder->class
        );
    }
}
