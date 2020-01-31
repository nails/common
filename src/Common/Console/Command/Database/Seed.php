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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Seed
 *
 * @package Nails\Common\Console\Command\Database
 */
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
                'Filter by component'
            )
            ->addArgument(
                'class',
                InputArgument::OPTIONAL,
                'Filter by class name (comma separated)'
            )
            ->addOption(
                'list',
                'l',
                InputOption::VALUE_NONE,
                'List the seeders, rather than execute'
            )
            ->addOption(
                'fresh',
                'f',
                InputOption::VALUE_NONE,
                'Rebuild the database before seeding'
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

        $aSeeders = $this->filterSeeders(
            $this->getSeeders(),
            $oInput->getArgument('component'),
            $oInput->getArgument('class')
        );

        if (empty($aSeeders)) {
            $oOutput->writeln('No seeders were discovered. Make one using <comment>make:db:seed</comment>');
            $oOutput->writeln('');
            return static::EXIT_CODE_SUCCESS;
        }

        // --------------------------------------------------------------------------

        if ($oInput->getOption('list')) {

            $oOutput->writeln('The following seeders are available:');
            $this->listSeeders($aSeeders);

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

        //  Get confirmation
        $oOutput->writeln('The following seeds will be executed in this order:');
        $this->listSeeders($aSeeders);

        $bDoSeed = $this->confirm('Does this look OK?', true);

        if ($bDoSeed) {

            if ($oInput->getOption('fresh')) {
                $oOutput->writeln('');
                $oOutput->write('Rebuilding database... ');

                $iExitCode = $this->callCommand(
                    'db:rebuild',
                    array_filter([
                        '-v'   => $oOutput->getVerbosity() === $oOutput::VERBOSITY_VERBOSE,
                        '-vv'  => $oOutput->getVerbosity() === $oOutput::VERBOSITY_VERY_VERBOSE,
                        '-vvv' => $oOutput->getVerbosity() === $oOutput::VERBOSITY_DEBUG,
                    ]),
                    false,
                    $oOutput->getVerbosity() <= $oOutput::VERBOSITY_NORMAL
                );

                if ($iExitCode !== static::EXIT_CODE_SUCCESS) {
                    return $this->abort(
                        static::EXIT_CODE_FAILURE,
                        ['Failed to reset database.']
                    );
                }
                $oOutput->writeln('<info>done!</info>');
            } else {
                $oOutput->writeln('');
            }

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

    /**
     * Filters seeders by component and class
     *
     * @param array       $aSeeders   The seeders to filter
     * @param string|null $sComponent The component name to filter by
     * @param string|null $sClass     The classname to filter by
     *
     * @return \stdClass[]
     */
    protected function filterSeeders(array $aSeeders, string $sComponent = null, string $sClass = null): array
    {
        if ($sComponent) {
            $aSeeders = array_filter(
                $aSeeders,
                function ($oSeeder) use ($sComponent) {
                    return $sComponent === $oSeeder->component->slug;
                }
            );
        }

        if ($sClass) {

            $aClasses = array_filter(explode(',', $sClass));
            $aSeeders = array_filter(
                $aSeeders,
                function ($oSeeder) use ($aClasses) {
                    return in_array($this->stripNamespace($oSeeder), $aClasses);
                }
            );
        }

        return $aSeeders;
    }

    // --------------------------------------------------------------------------

    /**
     * Renders a styalized list of seeder class names
     *
     * @param \stdClass[] $aSeeders The seeders to render
     */
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

    /**
     * Renders a single styalised line for a seeder
     *
     * @param \stdClass $oSeeder The seeder to rendered
     *
     * @return string
     */
    protected function renderLine($oSeeder): string
    {
        return sprintf(
            ' - [<comment>%s</comment>] <info>%s</info>',
            $oSeeder->component->slug,
            $this->stripNamespace($oSeeder)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the seeder's class anme without the namespace prefix
     *
     * @param \stdClass $oSeeder The seeder to render
     *
     * @return string
     */
    protected function stripNamespace($oSeeder): string
    {
        return preg_replace(
            '/^' . preg_quote($oSeeder->component->namespace . 'Seed\\', '/') . '/',
            '',
            $oSeeder->class
        );
    }
}
