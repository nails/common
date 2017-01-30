<?php

namespace Nails\Common\Console\Command\Make\Database;

use Nails\Common\Exception\Console\SeederExistsException;
use Nails\Console\Command\BaseMaker;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Seed extends BaseMaker
{
    const RESOURCE_PATH = NAILS_COMMON_PATH . 'resources/console/';
    const SEEDER_PATH   = FCPATH . 'src/Seed/';

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('make:db:seeder');
        $this->setDescription('Creates a new Database seeder');
        $this->addArgument(
            'seederName',
            InputArgument::OPTIONAL,
            'Define the name of the seeder to create'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param  InputInterface $oInput The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
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
    private function createSeeder()
    {
        $aFields  = $this->getArguments();
        $aCreated = [];

        try {

            $aSeeders = array_filter(explode(',', $aFields['SEEDER_NAME']));
            sort($aSeeders);

            foreach ($aSeeders as $sSeed) {

                $sSeed                  = ucfirst(strtolower($sSeed));
                $aFields['SEEDER_NAME'] = $sSeed;
                $this->oOutput->write('Creating seeder <comment>' . $sSeed . '</comment>... ');

                //  Check for existing seeder
                $sPath = static::SEEDER_PATH . $sSeed . '.php';
                if (file_exists($sPath)) {
                    throw new SeederExistsException(
                        'Seeder "' . $sSeed . '" exists already at path "' . $sPath . '"'
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
