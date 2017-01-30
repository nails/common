<?php

namespace Nails\Common\Console\Command\Make\Database;

use Nails\Common\Exception\Console\MigrationExistsException;
use Nails\Console\Command\BaseMaker;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migration extends BaseMaker
{
    const RESOURCE_PATH  = NAILS_COMMON_PATH . 'resources/console/';
    const MIGRATION_PATH = FCPATH . 'application/migrations/';

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('make:db:migration');
        $this->setDescription('Creates a new Database Migration');
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
            $this->createPath(self::MIGRATION_PATH);
            //  Create the controller
            $this->createMigration();
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
     * Create the Model
     *
     * @throws \Exception
     * @return void
     */
    private function createMigration()
    {
        try {

            $oNow    = Factory::factory('DateTime');
            $aFields = [
                'INDEX'      => 0,
                'DATE_START' => $oNow->format('Y-m-d'),
            ];

            //  Get the most recent migration and increment it by 1
            $sPattern    = \Nails\Common\Console\Command\Database\Migrate::VALID_MIGRATION_PATTERN;
            $aMigrations = [];
            foreach (new \DirectoryIterator(static::MIGRATION_PATH) as $oFileInfo) {

                if ($oFileInfo->isDot()) {
                    continue;
                }

                //  In the correct format?
                if (preg_match($sPattern, $oFileInfo->getFilename(), $aMatches)) {
                    $aMigrations[] = (int) $aMatches[1];
                }
            }

            if (!empty($aMigrations)) {
                $aFields['INDEX'] = end($aMigrations) + 1;
            } else {
                $aFields['INDEX'] = 0;
            }
            $this->oOutput->write('Creating migration <comment>' . $aFields['INDEX'] . '</comment>... ');

            //  Check for existing controller
            $sPath = static::MIGRATION_PATH . $aFields['INDEX'] . '.php';
            if (file_exists($sPath)) {
                throw new MigrationExistsException(
                    'Migration "' . $aFields['INDEX'] . '" exists already at path "' . $sPath . '"'
                );
            }

            $this->createFile($sPath, $this->getResource('template/migration.php', $aFields));
            $aCreated[] = $sPath;
            $this->oOutput->writeln('<info>done!</info>');

        } catch (\Exception $e) {
            $this->oOutput->writeln('<error>failed!</error>');
            //  Clean up created models
            if (!empty($aCreated)) {
                $this->oOutput->writeln('<error>Cleaning up - removing newly created migration</error>');
                foreach ($aCreated as $sPath) {
                    @unlink($sPath);
                }
            }
            throw new \Exception($e->getMessage());
        }
    }
}
