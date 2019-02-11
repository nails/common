<?php

namespace Nails\Common\Console\Command\Make\Database;

use Nails\Common\Exception\Console\SeederExistsException;
use Nails\Console\Command\BaseMaker;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Seed extends BaseMaker
{
    const RESOURCE_PATH = NAILS_COMMON_PATH . 'resources/console/';
    const SEEDER_PATH   = NAILS_APP_PATH . 'src/Seed/';

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this
            ->setName('make:db:seeder')
            ->setDescription('Creates a new Database seeder')
            ->addArgument(
                'modelName',
                InputArgument::REQUIRED,
                'Define the name of the model on which to base the seeder'
            )
            ->addArgument(
                'modelProvider',
                InputArgument::OPTIONAL,
                'Define the provider of the model',
                'app'
            )
            ->addOption(
                'skip-check',
                null,
                InputOption::VALUE_OPTIONAL,
                'Skip model check'
            );
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param  InputInterface  $oInput  The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput): int
    {
        parent::execute($oInput, $oOutput);

        // --------------------------------------------------------------------------

        try {
            //  Ensure the paths exist
            $this->createPath(self::SEEDER_PATH);
            //  Create the seeder
            $this->createSeeder();
        } catch (\Exception $e) {
            return $this->abort(
                self::EXIT_CODE_FAILURE,
                $e->getMessage()
            );
        }

        // --------------------------------------------------------------------------

        //  Cleaning up
        $oOutput->writeln('');
        $oOutput->writeln('<comment>Cleaning up...</comment>');

        // --------------------------------------------------------------------------

        //  And we're done
        $oOutput->writeln('');
        $oOutput->writeln('Complete!');

        return self::EXIT_CODE_SUCCESS;
    }

    // --------------------------------------------------------------------------

    /**
     * Create the Seeder
     *
     * @throws \Exception
     * @return void
     */
    private function createSeeder(): void
    {
        $aFields  = $this->getArguments();
        $aCreated = [];

        try {

            $aModels = array_filter(explode(',', $aFields['MODEL_NAME']));
            sort($aModels);

            foreach ($aModels as $sModel) {

                $aFields['MODEL_NAME'] = $sModel;
                $this->oOutput->write('Creating seeder <comment>' . $sModel . '</comment>... ');

                //  Validate model exists by attempting to load it
                if (!stringToBoolean($this->oInput->getOption('skip-check'))) {
                    Factory::model($sModel, $aFields['MODEL_PROVIDER']);
                }

                //  Check for existing seeder
                $sPath = static::SEEDER_PATH . $sModel . '.php';
                if (file_exists($sPath)) {
                    throw new SeederExistsException(
                        'Seeder "' . $sModel . '" exists already at path "' . $sPath . '"'
                    );
                }

                $this->createFile($sPath, $this->getResource('template/seeder.php', $aFields));
                $aCreated[] = $sPath;
                $this->oOutput->writeln('<info>done!</info>');
            }

        } catch (\Exception $e) {
            $this->oOutput->writeln('<error>failed!</error>');
            //  Clean up created seeders
            if (!empty($aCreated)) {
                $this->oOutput->writeln('<error>Cleaning up - removing newly created files</error>');
                foreach ($aCreated as $sPath) {
                    @unlink($sPath);
                }
            }
            throw new \Exception($e->getMessage());
        }
    }
}
