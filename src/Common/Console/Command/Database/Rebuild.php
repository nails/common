<?php

namespace Nails\Common\Console\Command\Database;

use Nails\Console\Command\Base;
use Nails\Environment;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Rebuild extends Base
{
    /**
     * Configures the app
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:rebuild');
        $this->setDescription('Drops every table in the database and runs migrations');
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
        $oOutput->writeln('<info>Nails Database Rebuild </info>');
        $oOutput->writeln('<info>----------------------</info>');
        $oOutput->writeln('');

        // --------------------------------------------------------------------------

        //  Check environment
        if (Environment::is('PRODUCTION')) {

            $oOutput->writeln('--------------------------------------');
            $oOutput->writeln('| <info>WARNING: The app is in PRODUCTION.</info> |');
            $oOutput->writeln('--------------------------------------');
            $oOutput->writeln('');
            $oOutput->writeln('Aborting rebuild.');

            return static::EXIT_CODE_FAILURE;
        }

        // --------------------------------------------------------------------------

        //  Which tables are we going to drop; all those which match our prefixes
        $oDb      = Factory::service('Database');
        $aResults = $oDb->query('SHOW TABLES')->result_array();
        $aTables  = [];
        foreach ($aResults as $aResult) {
            $sTable = reset($aResult);
            if (preg_match('/^(' . NAILS_DB_PREFIX . '|' . APP_DB_PREFIX . ')/', $sTable)) {
                $aTables[] = $sTable;
            }
        }

        // --------------------------------------------------------------------------

        $oOutput->writeln('The following database tables will be dropped, and migrations run.');
        $oOutput->writeln('');
        foreach ($aTables as $sTable) {
            $oOutput->writeln(' - <comment>' . $sTable . '</comment>');
        }
        $oOutput->writeln('');

        if ($this->confirm('Continue?', true)) {
            $oOutput->writeln('');
            $oOutput->write('Dropping tables... ');
            $oDb->query('SET FOREIGN_KEY_CHECKS = 0;');
            foreach ($aTables as $sTable) {
                $oDb->query('DROP TABLE IF EXISTS `' . $sTable . '`;');
            }
            $oDb->query('SET FOREIGN_KEY_CHECKS = 1;');
            $oOutput->writeln('<info>done!</info>');
            $oOutput->write('Migrating database... ');
            $iExitCode = $this->callCommand(
                'db:migrate',
                [
                    '--dbHost' => DEPLOY_DB_HOST,
                    '--dbUser' => DEPLOY_DB_USERNAME,
                    '--dbPass' => DEPLOY_DB_PASSWORD,
                    '--dbName' => DEPLOY_DB_DATABASE,
                ],
                false,
                true
            );

            if ($iExitCode == static::EXIT_CODE_SUCCESS) {
                $oOutput->writeln('<info>done!</info>');
            } else {
                $oOutput->writeln('<error>fail!</error>');
                return $this->abort(
                    self::EXIT_CODE_FAILURE,
                    [
                        'The Migration tool encountered issues and aborted the migration.',
                        'You should run it manually and investigate any issues.',
                        'The exit code was ' . $iExitCode,
                    ]
                );
            }
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
}
